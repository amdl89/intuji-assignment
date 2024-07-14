<?php require __DIR__.'/../partials/header.php' ?>
    <main class="container">
        <div class="py-5"></div>

        <div class="mb-3 d-flex justify-content-between align-items-baseline">
            <h1 class="mb-0">New Event</h1>
            <a class="btn btn-outline-primary" href="/home.php">
                <i class="bi bi-house-fill"></i>
                Back to home
            </a>
        </div>
        <?php require __DIR__.'/../partials/eventForm.php' ?>
    </main>
<?php require __DIR__.'/../partials/footer.php' ?>