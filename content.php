<main class="container">
    <?php
        // по GET параметру определяем текущую страницу
        if (isset($_GET["catalogue"])) require_once "catalogue.php";
        else if (isset($_GET["cart"])) require_once "cart.php";
        else if (isset($_GET["admin"])) require_once "admin.php";
        else require_once "main.php";
    ?>
</main>