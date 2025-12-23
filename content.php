<main class="container my-2">
    <?php
        if (substr($uri,0,11) === "/?catalogue") require_once "catalogue.php";
        else if (substr($uri,0,6) === "/?cart") require_once "cart.php";
        else require_once "main.php";
    ?>
</main>