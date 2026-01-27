<?php 
if(session_status() !== PHP_SESSION_ACTIVE) session_start(); // запуск сессии если еще не начата
// подключаем файл конфигурации
require_once "config.php"; 

// готовим данные, которые будем возвращать
$ret_data = ["error"=>0, "error_info"=>""]; // готовим данные, которые будем возвращать

if (isset($_POST["id"])) {
  // включем нормальное отображение отчетов об ошибках sql
  mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); 
  // создаем объект для работы с БД
  $db = new mysqli($CFG['db']['server'], $CFG['db']['username'], $CFG['db']['password'], $CFG['db']['database']);
  // указываем кодировку для работы с БД
  $db->set_charset('utf8mb4');
  // включаем нормальную работу с числовыми типами данных
  $db->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);
  
  // в идеале записи не удаляются а обновляется поле с флагом удаления (напр.deleted, см.delete_product.php), чтобы можно было восстановить удаленное и не нарушалась целостность БД
  // в рамках демо будем удалять записи из БД, но предварительно проверять нет ли ссылок на эти записи

  // сначала проверим есть ли товары в категории
  $sql = "select * from products where id_categories=? and deleted=0";
  $stmt = $db->prepare($sql);
  // добавляем данные к запросу
  $stmt->bind_param('i', $_POST['id']);
  // выполняем запрос
  $stmt->execute();
  // получаем результат запроса
  $stmt->store_result();
  // получаем кол-во строк в результате запроса
  $count = $stmt->num_rows();
  if ($count > 0) { // если есть хотя бы 1 товар в категории, то удалить ее не можем
    $ret_data["error"] = 1;
    $ret_data["error_info"] = "В категории есть товары, невозможно безопасно удалить";
  } else { // товаров в категории нет - удаляем категорию
    $sql = "delete from categories where id=?";
    $stmt = $db->prepare($sql);
    // добавляем данные к запросу
    $stmt->bind_param('i', $_POST['id']);
    // выполняем запрос
    $stmt->execute();
  }
  
} else {
  $ret_data["error"] = 1;
  $ret_data["error_info"] = "Недостаточно данных для изменения";
}
echo json_encode($ret_data); // возвращаем данные в формате json

