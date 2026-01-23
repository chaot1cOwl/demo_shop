<?php 
if(session_status() !== PHP_SESSION_ACTIVE) session_start(); // запуск сессии если еще не начата
// подключаем файл конфигурации
require_once "config.php"; 

// готовим данные, которые будем возвращать
$ret_data = ["error"=>0, "error_info"=>"", "order"=>""]; // готовим данные, которые будем возвращать

if (isset($_POST["clientname"]) && isset($_POST["clientname"]) && isset($_POST["products"]) && count($_POST["products"]) > 0) {
  // включем нормальное отображение отчетов об ошибках sql
  mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); 
  // создаем объект для работы с БД
  $db = new mysqli($CFG['db']['server'], $CFG['db']['username'], $CFG['db']['password'], $CFG['db']['database']);
  // указываем кодировку для работы с БД
  $db->set_charset('utf8mb4');
  // включаем нормальную работу с числовыми типами данных
  $db->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);
  // собираем строку запроса на вставку данных в таблицу заказов
  $sql = "insert into orders (client_name, client_address) values (?, ?)";
  // подготавливаем запрос
  $stmt = $db->prepare($sql);
  // добавляем данные к запросу
  $stmt->bind_param('ss', $_POST['clientname'], $_POST["clientaddress"]);
  // выполняем запрос
  $stmt->execute();
  // получаем id последней добавленной записи
  $order_id = $db->insert_id;
  // добавляем продукты в заказ
  $sql = "insert into products_to_orders (id_products, id_orders, count) values ";
  $params = [];
  foreach ($_POST["products"] as $id => $item) {
    $sql .= "(?, ?, ?),";
    $params[] = $id;
    $params[] = $order_id;
    $params[] = $item["count"];
  }
  $sql = substr($sql, 0, strlen($sql)-1);
  $stmt = $db->prepare($sql);
  $stmt->bind_param(str_repeat("i", count($params)), ...$params);
  $stmt->execute();
  // очищаем корзину!
  unset($_SESSION["cart"]);
  $ret_data["order"] = $order_id;
} else {
  $ret_data["error"] = 1;
  $ret_data["error_info"] = "Недостаточно данных для добавления заказа";
}
echo json_encode($ret_data); // возвращаем данные в формате json

