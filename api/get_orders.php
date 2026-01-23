<?php 
// подключаем файл конфигурации
require_once "config.php"; 

// готовим данные, которые будем возвращать
$ret_data = ["error"=>0, "error_info"=>"", "orders"=>[]]; // готовим данные, которые будем возвращать

// включем нормальное отображение отчетов об ошибках sql
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); 
// создаем объект для работы с БД
$db = new mysqli($CFG['db']['server'], $CFG['db']['username'], $CFG['db']['password'], $CFG['db']['database']);
// указываем кодировку для работы с БД
$db->set_charset('utf8mb4');
// включаем нормальную работу с числовыми типами данных
$db->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);
// запрашиваем не удаленные заказы
$sql = "select * from orders where status<>100 order by status asc, timecreated desc";
// подготавливаем запрос
$stmt = $db->prepare($sql);
// выполняем запрос
$stmt->execute();
// получаем результат запроса
$result = $stmt->get_result();
// разбираем результат запроса и добавляем к возвращаемым данным
while ($row = $result->fetch_assoc()) {
  $order_data = $row;
  $order_data["products"] = [];
  // собираем товары в заказе
  $sql2 = "SELECT p.*,pto.count FROM `products_to_orders` pto left join products p on pto.id_products = p.id where pto.id_orders=?";
  $stmt2 = $db->prepare($sql2);
  $stmt2->bind_param('i', $row["id"]);
  $stmt2->execute();
  $result2 = $stmt2->get_result();
  while ($row2 = $result2->fetch_assoc()) {
    $order_data["products"][] = $row2;
  }
  $ret_data["orders"][] = $order_data;

}

echo json_encode($ret_data); // возвращаем данные в формате json

