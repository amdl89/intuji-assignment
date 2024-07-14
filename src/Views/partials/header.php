<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $PAGE_TITLE ?? 'Document'?></title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/app.css">

    <script defer src="/assets/js/alpine.js"></script>
    <script defer src="/assets/js/app.js"></script>
</head>
<body class="bg-body-secondary">
<?php require __DIR__ . '/flashMessage.php' ?>

<?php if(isset($_SESSION['__USER_INFO'])): ?>
    <?php require __DIR__ . '/nav.php' ?>
<?php endif; ?>