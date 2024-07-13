<nav class="bg-white d-flex justify-content-between align-items-center py-2 px-4 position-fixed fixed-top shadow-sm">
    <div>
        <div>
            <img src="<?php echo htmlspecialchars($_SESSION['__USER_INFO']['picture']); ?>" alt="User Avatar" class="avatar rounded-circle object-fit-cover me-1">
            <span class="fw-bold text-secondary"><?= $_SESSION['__USER_INFO']['name'] ?></span>
        </div>
    </div>
    <div>
        <form action="/logout.php" method="post">
            <button type="submit" class="btn btn-secondary text-uppercase">
                <i class="bi bi-box-arrow-right"></i>
                Log out
            </button>
        </form>
    </div>
</nav>