<?php
if(session_status() !== PHP_SESSION_ACTIVE) session_start(); // запуск сессии если еще не начата

$ret_data = ["error"=>0, "error_info"=>""]; // готовим данные, которые будем возвращать
unset($_SESSION["is_admin"]); // сбрасываем данные о пользователе в сесси
echo json_encode($ret_data); // возвращаем данные в формате json