<!DOCTYPE html>
<html lang="en">

<head>
    <?php $pageTitle = "Profile" ?>
    <?php require_once("../src/Views/components/head.php")  ?>
</head>

<body>
    <!-- Nav Bar -->
    <?php require_once("../src/Views/components/nav.php") ?>

    <!-- Main Body -->
    <div class="main">
        <?php dd($user) ?>
    </div>

</body>

<!-- Nav Bar Toggle Js -->
<script src="./assets/js/nav-toggle.js"></script>

</html>