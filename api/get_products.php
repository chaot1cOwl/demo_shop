<?php 
// подключаем файл конфигурации
require_once "config.php"; 

// готовим данные, которые будем возвращать
$ret_data = ["error"=>0, "error_info"=>"", "products"=>[]]; // готовим данные, которые будем возвращать

// включем нормальное отображение отчетов об ошибках sql
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); 
// создаем объект для работы с БД
$db = new mysqli($CFG['db']['server'], $CFG['db']['username'], $CFG['db']['password'], $CFG['db']['database']);
// указываем кодировку для работы с БД
$db->set_charset('utf8mb4');
// включаем нормальную работу с числовыми типами данных
$db->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);
// собираем строку запроса с учетом переданных фильтров
$sql = "select p.*, c.name as category from products as p left join categories as c on c.id = p.id_categories where deleted=0";
if (isset($_POST["category"]) && $_POST["category"] != 'all') {
  $sql .= ' and id_categories = ?';
}
$sql .= " order by p.id desc"; // отсортируем продукты
// подготавливаем запрос
$stmt = $db->prepare($sql);
// добавляем данные к запросу
if (isset($_POST["category"]) && $_POST["category"] != 'all') {
  $stmt->bind_param('s', $_POST['category']);
}
// выполняем запрос
$stmt->execute();
// получаем результат запроса
$result = $stmt->get_result();
// разбираем результат запроса и добавляем к возвращаемым данным
while ($row = $result->fetch_assoc()) {
  $ret_data["products"][] = $row;
}

echo json_encode($ret_data); // возвращаем данные в формате json

