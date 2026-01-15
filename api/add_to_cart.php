<?php 
if(session_status() !== PHP_SESSION_ACTIVE) session_start(); // запуск сессии если еще не начата
$ret_data = ["error"=>0, "error_info"=>""]; // готовим данные, которые будем возвращать
if (isset($_POST["id"]) && isset($_POST["name"]) && isset($_POST["price"])) { // если пришли все нужные данные
  $product = ["id"=>(int)$_POST["id"],"name"=>(string)$_POST["name"], "price"=>(int)$_POST["price"], "count"=>1];
  if (!isset($_SESSION["cart"])) $_SESSION["cart"]=[]; // если корзины еще нет - создать
  if (!isset($_SESSION["cart"][$product["id"]])) $_SESSION["cart"][$product["id"]]=$product; else $_SESSION["cart"][$product["id"]]["count"]++; // добавить товар в корзину
} else {
  $ret_data["error"] = 1;
  $ret_data["error_info"] = "Нечего добавлять";
}
echo json_encode($ret_data); // возвращаем данные в формате json
?>