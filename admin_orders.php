<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start(); // запуск сессии если еще не начата 
if (isset($_SESSION["is_admin"]) && $_SESSION["is_admin"]) { // если залогинены то показываем страницу
  ?>
<div class="row mt-2">
<div id="menu" class="col-md-3 col-sm-12">
  <ul class="nav flex-column">
  <li class="nav-item"><a class="nav-link active" href="./?admin&orders">Заказы</a></li>
  <li class="nav-item"><a class="nav-link" href="./?admin&products">Товары</a></li>
  <li class="nav-item"><a class="nav-link" href="./?admin&categories">Категории</a></li>
  <li class="nav-item"><a class="nav-link" href="./?admin" id="logoutBtn">Выйти</a></li>
  </ul>
</div>
<div id="orders" class="col-md-9 col-sm-12">
  <table class="table" id="ordersTable">
    <thead>
      <tr>
      <th>Дата и время</th>
      <th>Заказчик</th>
      <th>Адрес доставки</th>
      <th>Товары</th>
      <th>Сумма</th>
      <th></th>
      </tr>
    </thead>
    <tbody>

    </tbody>
  </table>
</div>
<div id="alertsContainer" class="toast-container position-fixed bottom-0 end-0 p-3"></div>
</div>

<script>
  // запрос списка продуктов
  function getOrders() {
    $.ajax({
      type: "POST",
      url: "./api/get_orders.php",
      dataType: "json",
      success: function(data) {
        if (data.error==0) { // если успешно выполнен запрос, то генерируем таблицу с заказами
            // пробегаем по полученному из БД списку и добавляем элементы для каждого заказа
            $("#ordersTable > tbody").html("");
            $.each(data.orders, function(idx, item) {
              tr_item = $("<tr></tr>");
              td_item = $("<td></td>").html(item.timecreated);  
              tr_item.append(td_item);
              td_item = $("<td></td>").html(item.client_name);  
              tr_item.append(td_item);
              td_item = $("<td></td>").html(item.client_address);  
              tr_item.append(td_item);
              // собираем таблицу с товарами в заказе
              total = 0;
              prod_table = $("<table></table>").attr("class", "table table-sm");
              $.each(item.products, function(idxp, itemp) {
                prod_tr_item = $("<tr></tr>");
                prod_tr_item.append($("<td></td>").attr("class", "col-2").html(itemp.count+"x"), $("<td></td>").attr("class", "col-10").html(itemp.name));
                total = total + itemp.count * itemp.price;
                prod_table.append(prod_tr_item);
              }); 
              td_item = $("<td></td>").html(prod_table);  
              tr_item.append(td_item);
              td_item = $("<td></td>").html(total +" руб.");  
              tr_item.append(td_item);
              // по нажатию на кнопку будем менять статус на 1 - выполнен
              done_btn = $("<button></button>").attr("class", "btn btn-sm").html("<i class='bi bi-check-square-fill'></i>").on("click", {id: item.id, status: 1}, changeOrderStatus);
              // кнопка не закрашена если заказ не выполнен
              if (item.status == 0) {done_btn.addClass("btn-outline-success");} else {done_btn.addClass("btn-success");}
              // по нажатию на кнопку будем менять статус на 100 - удален
              del_btn = $("<button></button>").attr("class", "btn btn-sm btn-danger").html("<i class='bi bi-trash-fill'></i>").on("click", {id: item.id, status: 100}, changeOrderStatus);
              td_item = $("<td></td>").append(done_btn, del_btn);  
              tr_item.append(td_item);
              $("#ordersTable > tbody").append(tr_item);  
            })
          } else {
          $("#alertsContainer").html('<div id="alert" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">При запросе списка продуктов произошла ошибка</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
          const alertToast = bootstrap.Toast.getOrCreateInstance($("#alert"));
          alertToast.show();
        }          
      },
      error: function() {
        $("#alertsContainer").html('<div id="alert" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">При запросе списка продуктов произошла ошибка</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
        const alertToast = bootstrap.Toast.getOrCreateInstance($("#alert"));
        alertToast.show();
      }
    });
  }

  // изменение статуса заказа
  function changeOrderStatus(ev) {
    $.ajax({
      type: "POST",
      url: "./api/change_order_status.php",
      data: {id: ev.data.id, status: ev.data.status},
      dataType: "json",
      success: function(data) {
        if (data.error==0) { 
          getOrders(); // если успешно выполнен запрос, то генерируем таблицу с заказами
          // и показываем сообщение об успешном добавлении
          $("#alertsContainer").html('<div id="alertOk" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">Статус заказа успешно изменен</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
          const alertToast = bootstrap.Toast.getOrCreateInstance($("#alertOk"));
          alertToast.show();
        } else { // в случае ошибки показываем сообщение об ошибке
          $("#alertsContainer").html('<div id="alert" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">При изменении статуса заказа произошла ошибка</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
          const alertToast = bootstrap.Toast.getOrCreateInstance($("#alert"));
          alertToast.show();
        }          
      },
      error: function() { // в случае ошибки показываем сообщение об ошибке
        $("#alertsContainer").html('<div id="alert" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">При изменении статуса заказа произошла ошибка</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
        const alertToast = bootstrap.Toast.getOrCreateInstance($("#alert"));
        alertToast.show();
      }
    });
  }

  // при загрузке страницы по умолчанию запрашиваем данные
  getOrders();
  
  // кнопка выхода
  $("#logoutBtn").on("click", function(e) {
    e.preventDefault();
    $.ajax({
      type: "POST",
      url: "./api/logout_user.php",
      dataType: "json",
      success: function(data) {
        if (data.error==0) { 
          location.reload();
        } else { // в случае ошибки показываем сообщение об ошибке
          $("#alertsContainer").html('<div id="alert" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">Не удалось выйти из системы</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
          const alertToast = bootstrap.Toast.getOrCreateInstance($("#alert"));
          alertToast.show();
        }          
      },
      error: function() { // в случае ошибки показываем сообщение об ошибке
        $("#alertsContainer").html('<div id="alert" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">Не удалось выйти из системы</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
        const alertToast = bootstrap.Toast.getOrCreateInstance($("#alert"));
        alertToast.show();
      }
    });
  })
</script>

<?php 
} else {
  echo "Для просмотра необходимо <a href='./?admin'>авторизоваться в системе</a>";
}