<?php 
if(session_status() !== PHP_SESSION_ACTIVE) session_start(); // запуск сессии если еще не начата
// подключаем файл конфигурации
require_once "config.php"; 

// готовим данные, которые будем возвращать
$ret_data = ["error"=>0, "error_info"=>""]; // готовим данные, которые будем возвращать

if (isset($_POST["name"])) {
  // включем нормальное отображение отчетов об ошибках sql
  mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); 
  // создаем объект для работы с БД
  $db = new mysqli($CFG['db']['server'], $CFG['db']['username'], $CFG['db']['password'], $CFG['db']['database']);
  // указываем кодировку для работы с БД
  $db->set_charset('utf8mb4');
  // включаем нормальную работу с числовыми типами данных
  $db->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);
  // собираем строку запроса на вставку данных в таблицу категорий
  $sql = "insert into categories (name) values (?)";
  // подготавливаем запрос
  $stmt = $db->prepare($sql);
  // добавляем данные к запросу
  $stmt->bind_param('s', $_POST['name']);
  // выполняем запрос
  $stmt->execute();
} else {
  $ret_data["error"] = 1;
  $ret_data["error_info"] = "Недостаточно данных для добавления";
}
echo json_encode($ret_data); // возвращаем данные в формате json

