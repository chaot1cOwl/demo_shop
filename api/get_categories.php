<?php 
// подключаем файл конфигурации
require_once "config.php"; 

// готовим данные, которые будем возвращать
$ret_data = ["error"=>0, "error_info"=>"", "categories"=>[]]; // готовим данные, которые будем возвращать

// включем нормальное отображение отчетов об ошибках sql
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); 
// создаем объект для работы с БД
$db = new mysqli($CFG['db']['server'], $CFG['db']['username'], $CFG['db']['password'], $CFG['db']['database']);
// указываем кодировку для работы с БД
$db->set_charset('utf8mb4');
// включаем нормальную работу с числовыми типами данных
$db->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);
// подготавливаем запрос
$stmt = $db->prepare("select * from categories");
// выполняем запрос
$stmt->execute();
// получаем результат запроса
$result = $stmt->get_result();
// разбираем результат запроса и добавляем к возвращаемым данным
while ($row = $result->fetch_assoc()) {
  $ret_data["categories"][] = $row;
}

echo json_encode($ret_data); // возвращаем данные в формате json

