<?php require __DIR__ . '/Partials/header.php' ?>
<main class="container d-flex justify-content-center align-items-center vh-100">
    <div class="d-flex flex-column align-items-center p-3 border-3">
        <h1 class="mb-3">Google Calendar Plugin</h1>
        <h3 class="mb-3">Lets you manage your events with a simple UI</h3>
        <h5 class="mb-1">Sign in to get started</h5>
        <a href="/oauth/login.php" class="btn btn-primary text-uppercase">
            <i class="bi bi-google"></i>
            Login with google
        </a>
    </div>
</main>
<?php require __DIR__ . '/Partials/footer.php' ?>
