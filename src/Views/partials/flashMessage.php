<?php
if(isset($_SESSION['__FLASH_MESSAGE'])) {
    $__FLASH_MESSAGE = $_SESSION['__FLASH_MESSAGE'];
    unset($_SESSION['__FLASH_MESSAGE']);
}
?>
<?php if(isset($__FLASH_MESSAGE)): ?>
    <?php
        $backGroundColorClass = '';
        switch ($__FLASH_MESSAGE['type']) {
            case 'error':
                $backGroundColorClass = 'alert-danger';
                break;
            case 'success':
                $backGroundColorClass = 'alert-success';
                break;
            default:
                $backGroundColorClass = 'alert-secondary';
        }
    ?>
    <div class="container position-fixed fixed-bottom" x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)">
        <template x-if="show">
            <div class="alert <?= $backGroundColorClass ?> d-flex justify-content-between align-items-center">
                <span><?= $__FLASH_MESSAGE['message'] ?></span>
                <button type="button" class="btn-close" @click="show = false"></button>
            </div>
        </template>
    </div>
<?php endif; ?>
