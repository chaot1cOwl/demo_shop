<?php 
if(session_status() !== PHP_SESSION_ACTIVE) session_start(); // запуск сессии если еще не начата
$ret_data = ["error"=>0, "error_info"=>""]; // готовим данные, которые будем возвращать
if (isset($_POST["id"])) { // если пришли все нужные данные
  if (!isset($_SESSION["cart"][$_POST["id"]])) { // такого товара в корзине нет - ошибка
    $ret_data["error"] = 1; 
    $ret_data["error_info"] = "Товара нет в корзине";
  } else { // товар есть - удаляем все о нем из корзины
    unset($_SESSION["cart"][$_POST["id"]]);
  }
} else {
  $ret_data["error"] = 1;
  $ret_data["error_info"] = "Нечего удалять";
}
echo json_encode($ret_data); // возвращаем данные в формате json
?>