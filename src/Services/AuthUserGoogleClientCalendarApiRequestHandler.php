<?php

namespace App\Services;

use App\Auth\AuthFactory;
use App\Database\DBFactory;

class AuthUserGoogleClientCalendarApiRequestHandler
{
    private DBFactory $db;
    private AuthFactory $auth;
    private \Google_Client $client;

    private function __construct()
    {
        $this->db = DBFactory::getDB();
        $this->auth = AuthFactory::getAuth();

        $authUserInfo = $this->auth->getUserInfo();
        if(!$authUserInfo) {
            throw new \Exception('User not logged in.');
        }
        $this->client = GoogleClientFactory::getClient($authUserInfo['calendar_access_token']);
    }

    public static function getHandler() {
        return new static();
    }

    public function handleApiRequestForAuthUser(
        callable $apiRequestCallback,
        callable $refreshTokenRevokedErrorCallback,
        callable $generalErrorCallback,
    ): void {
        $authUserInfo = $this->auth->getUserInfo();

        try {
            $apiRequestCallback();
        } catch (\Google\Service\Exception $e) {
            if ($e->getCode() === 401) {
                // Handle access token expired error
                try {
                    $token = $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                    $this->client->setAccessToken($token);

                    $accessToken = $this->client->getAccessToken();
                    $refreshToken = $accessToken['refresh_token'];

                    // update token for user
                    $updateUserTokensQuery = 'UPDATE USERS SET calendar_access_token = :calendar_access_token, calendar_refresh_token = :calendar_refresh_token WHERE sid = :sid';
                    $updateUserTokensQueryParams = [
                        ':calendar_access_token' => json_encode($accessToken),
                        ':calendar_refresh_token' => $refreshToken,
                        ':sid' => $authUserInfo['sid'],
                    ];
                    $this->db->query($updateUserTokensQuery, $updateUserTokensQueryParams);

                    // login user again to refresh userinfo in session
                    $this->auth->loginUsingId($authUserInfo['id']);

                } catch (\Exception $e) {
                    // set refresh token to null for user since it is expired
                    $setUserRefreshTokenToNullQuery = 'UPDATE USERS SET calendar_refresh_token = null WHERE id = :id';
                    $this->db->query($setUserRefreshTokenToNullQuery, [':id' => $authUserInfo['id'],]);

                    // login user again to refresh userinfo in session
                    $this->auth->loginUsingId($authUserInfo['id']);

                    $refreshTokenRevokedErrorCallback();
                    return;
                }
                try {
                    $apiRequestCallback();
                } catch (\Exception $e) {
                    $generalErrorCallback();
                    return;
                }
            } else {
                $generalErrorCallback();
                return;
            }
        } catch (\Exception $e) {
            $generalErrorCallback();
            return;
        }
    }
}