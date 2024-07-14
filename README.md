
# google-calendar plugin

Lets you manage events for your default calendar with as simple UI.

---
## Setup


Copy env file
```bash
cp .env.example .env && sudo chmod -R 777 .env
```
Copy your google client id and google client secret in the .env file

Select the following scopes while obtaining tokens:
- openid
- https://www.googleapis.com/auth/calendar.events
```
GOOGLE_CLIENT_ID=your-client-id  
GOOGLE_CLIENT_SECRET=your-client-secret
```
Register the following redirect URIs for app:
- http://localhost/oauth/callback/login.php
-  http://localhost/oauth/callback/calendar.php

Create database file
```bash
touch database.db && sudo chmod -R 777 database.db
```
Build project
```bash
sudo docker-compose build
```
Run project (Make sure port 80 is free)
```bash
sudo docker-compose up -d
```

Install composer dependencies
```bash
sudo docker exec -it intuji-assignment_app_1 /bin/bash
composer install
```

Initialize db schema
```bash
sudo docker exec -it intuji-assignment_app_1 /bin/bash
php src/Database/initDb.php 
```

The project will run at `localhost`
