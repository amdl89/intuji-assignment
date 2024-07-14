<?php require __DIR__ . '/../partials/header.php' ?>
<main class="container d-flex justify-content-center align-items-center vh-100">
    <div class="d-flex flex-column align-items-center p-3 border-3">
        <h1 class="mb-3">
            <?php if($calendarNotConnected_newUser): ?>
            Welcome, <?= $authUserInfo['name'] ?>
            <?php else:?>
            You have disconnected your google calendar
            <?php endif; ?>
        </h1>
        <h5 class="mb-1">Connect to calendar to manage your events</h5>
        <a href="/oauth/calendar.php" class="btn btn-danger text-uppercase">
            <i class="bi bi-calendar-event-fill"></i>
            Connect to google calendar
        </a>
    </div>
</main>
<?php require __DIR__ . '/../partials/footer.php' ?>