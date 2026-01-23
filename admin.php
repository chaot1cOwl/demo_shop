<?php 
if (session_status() !== PHP_SESSION_ACTIVE) session_start(); // запуск сессии если еще не начата 
if (isset($_SESSION["is_admin"]) && $_SESSION["is_admin"]) { // если залогинены то подгружаем нужную страницу
  if (isset($_GET["categories"])) require_once "admin_categories.php";
  else if (isset($_GET["products"])) require_once "admin_products.php";
  else require_once "admin_orders.php";
} else { // иначе перенаправляем на страницу авторизации
  require_once "admin_login.php";
}
?>
