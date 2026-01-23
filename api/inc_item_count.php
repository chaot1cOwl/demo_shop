<?php 
if(session_status() !== PHP_SESSION_ACTIVE) session_start(); // запуск сессии если еще не начата
$ret_data = ["error"=>0, "error_info"=>""]; // готовим данные, которые будем возвращать
if (isset($_POST["id"])) { // если пришли все нужные данные
  if (!isset($_SESSION["cart"][$_POST["id"]])) {  // товара в корзине нет - ошибка
    $ret_data["error"] = 1; 
    $ret_data["error_info"] = "Товара нет в корзине";
  } else { // товар есть в корзине - увеличиваем количество
    $_SESSION["cart"][$_POST["id"]]["count"]++;
  }
} else {
  $ret_data["error"] = 1;
  $ret_data["error_info"] = "Нечего добавлять";
}
echo json_encode($ret_data); // возвращаем данные в формате json
?>