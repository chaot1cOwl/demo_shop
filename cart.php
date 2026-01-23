<?php if(session_status() !== PHP_SESSION_ACTIVE) session_start(); // запуск сессии если еще не начата ?>
<h3>Корзина</h3>
<div id="order_info">
<?php 
if (!isset($_SESSION["cart"]) || (isset($_SESSION["cart"]) && count($_SESSION["cart"]) == 0)) { // корзина пуста
  echo "В корзине пока ничего нет. <a href='./?catalogue' class='link-success'>Добавим что-нибудь?</a>";
} else { // в корзине есть хотя бы 1 товар
  ?>
  <table class="table table-sm"><tbody>
  <?php
  $total = 0;
  foreach ($_SESSION["cart"] as $id => $item) { // отрисовываем таблицу с товарами в корзине
    $item_row = "<tr>";
    $item_row .= "<td>".$item["name"]."</td>";
    $item_row .= "<td>".$item["price"]." руб.</td>";
    $item_row .= "<td>".$item["count"]."</td>";
    $item_row .= "<td>".$item["price"] * $item["count"]." руб.</td>";
    $total += $item["count"] * $item["price"];
    // кнопки для управления товарами в корзине
    $item_row .= '<td>
    <div class="btn-group" role="group">
      <button name="decCount" data-id="'.$id.'" type="button" class="btn btn-sm btn-outline-secondary"><i class="bi bi-dash-lg"></i></button>
      <button name="incCount" data-id="'.$id.'" type="button" class="btn btn-sm btn-outline-secondary"><i class="bi bi-plus-lg"></i></button>
      <button name="delItem" data-id="'.$id.'" type="button" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
    </div>
    </td>';
    $item_row .= "</tr>";
    echo $item_row;
  }  
  ?>
  </tbody><tfoot class="table-group-divider">
  <tr><td colspan="3">Итого</td><td colspan="2"><?php echo $total; ?> руб.</td></tr>
  </tfoot>
  </table>
  <hr>
  <div class="mb-3">
    <label for="clientName" class="form-label">Ваше имя <span class="text-danger">*</span></label>
    <input type="text" class="form-control" id="clientName">
  </div>
  <div class="mb-3">
    <label for="clientAddress" class="form-label">Ваш адрес <span class="text-danger">*</span></label>
    <input type="text" class="form-control" id="clientAddress">
  </div>
  <button id="makeOrder" class="btn btn-shop" type="submit">Оформить заказ</button>

  <div id="alertsContainer" class="toast-container position-fixed bottom-0 end-0 p-3"></div>  
  <!-- Модальное окно, которое будет отображаться при попытке оформить заказ -->
  <div class="modal" tabindex="-1" id="orderConfirmModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Подтверждение заказа</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="orderData">
          Данные заказа отобразить здесь...
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelConfirm">Отмена</button>
          <button type="button" class="btn btn-success" id="confirmOrder">Подтвердить</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    modalConfirm = new bootstrap.Modal("#orderConfirmModal", {backdrop:'static'}); // модальное окно в переменную запоминаем

    // увеличение кол-ва единиц товара в корзине
    $('button[name="incCount"]').on("click", function() {
      data = {id: $(this).data("id")} ; // получаем id из аттрибута data-id у нажатой кнопки
      $.ajax({ // выполняем запрос на увеличение кол-ва 
        type: "POST",
        url: "./api/inc_item_count.php",
        data: data,
        dataType: "json",
        success: function(data) {
          if (data.error==0) { // в случае успеха обновляем страницу
            location.reload();
          }  else { // при ошибке отображаем сообщение об ошибке
            $("#alertsContainer").html('<div id="alert" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">При добавлении в корзину произошла ошибка</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
            const alertToast = bootstrap.Toast.getOrCreateInstance($("#alert"));
            alertToast.show();  
          }
        },
        error: function() { // при ошибке отображаем сообщение об ошибке
          $("#alertsContainer").html('<div id="alert" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">При добавлении в корзину произошла ошибка</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
          const alertToast = bootstrap.Toast.getOrCreateInstance($("#alert"));
          alertToast.show();
        }
      });
    });

    // уменьшение кол-ва единиц товара в корзине
    $('button[name="decCount"]').on("click", function() {
      data = {id: $(this).data("id")} ; // получаем id из аттрибута data-id у нажатой кнопки
      $.ajax({ // выполняем запрос на уменьшение кол-ва 
        type: "POST",
        url: "./api/dec_item_count.php",
        data: data,
        dataType: "json",
        success: function(data) {
          if (data.error==0) {
            location.reload(); // в случае успеха обновляем страницу
          }  else { // при ошибке отображаем сообщение об ошибке
            $("#alertsContainer").html('<div id="alert" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">При удалении из корзины произошла ошибка</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
            const alertToast = bootstrap.Toast.getOrCreateInstance($("#alert"));
            alertToast.show();  
          }
        },
        error: function() { // при ошибке отображаем сообщение об ошибке
          $("#alertsContainer").html('<div id="alert" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">При удалении из корзины произошла ошибка</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
          const alertToast = bootstrap.Toast.getOrCreateInstance($("#alert"));
          alertToast.show();
        }
      });
    });

    // удаление товара из корзины
    $('button[name="delItem"]').on("click", function() {
      data = {id: $(this).data("id")} ; // получаем id из аттрибута data-id у нажатой кнопки
      $.ajax({ // выполняем запрос на удаление товара
        type: "POST",
        url: "./api/del_item_from_cart.php",
        data: data,
        dataType: "json",
        success: function(data) {
          if (data.error==0) {
            location.reload(); // в случае успеха обновляем страницу
          }  else { // при ошибке отображаем сообщение об ошибке
            $("#alertsContainer").html('<div id="alert" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">При удалении из корзины произошла ошибка</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
            const alertToast = bootstrap.Toast.getOrCreateInstance($("#alert"));
            alertToast.show();  
          }
        },
        error: function() { // при ошибке отображаем сообщение об ошибке
          $("#alertsContainer").html('<div id="alert" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">При удалении из корзины произошла ошибка</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
          const alertToast = bootstrap.Toast.getOrCreateInstance($("#alert"));
          alertToast.show();
        }
      });
    });
    
    // оформление заказа
    $("#makeOrder").on("click", function() {
      // простая валидация полей ввода
      name = $("#clientName").val();
      if (!name) $("#clientName").addClass("is-invalid"); else $("#clientName").removeClass("is-invalid");
      addr = $("#clientAddress").val();
      if (!addr) $("#clientAddress").addClass("is-invalid"); else $("#clientAddress").removeClass("is-invalid");
      if (name && addr) { // если поля ввода заполнены, то пробуем оформить заказ
        modalConfirm.show(); // показываем окно подтверждения
      }
    });

    $("#cancelConfirm").on("click", function() {
      modalConfirm.hide(); // скрываем окно подтверждения
    });

    // оформление заказа - заказ подтвержден
    $("#confirmOrder").on("click", function() {
      // собираем данные для запроса
      data = {clientname: $("#clientName").val(), clientaddress: $("#clientAddress").val(), products: <?php echo json_encode($_SESSION["cart"]); ?>}
      modalConfirm.hide(); // скрываем модальное окно
      $.ajax({ // выполняем запрос на добавление заказа
        type: "POST",
        url: "./api/make_order.php",
        data: data,
        dataType: "json",
        success: function(data) {
          if (data.error==0) { // в случае успеха показываем номер заказа
            $("#order_info").html("<span class='h4'>Заказ № "+data.order+" принят.</span><br>Вы будете перенаправлены на страницу каталога автоматически...").show();
            // автоматически перенаправляем в каталог через 5 секунд (5000 милисекунд)
            setTimeout(function () {
              document.location.href = "./?catalogue";
            }, 5000);
          }  else { // в случае ошибки показываем сообщение об ошибке
            $("#alertsContainer").html('<div id="alert" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">При оформлении заказа произошла ошибка</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
            const alertToast = bootstrap.Toast.getOrCreateInstance($("#alert"));
            alertToast.show();  
          }
        },
        error: function() { // в случае ошибки показываем сообщение об ошибке
          $("#alertsContainer").html('<div id="alert" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">При оформлении заказа произошла ошибка</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
          const alertToast = bootstrap.Toast.getOrCreateInstance($("#alert"));
          alertToast.show();
        }
      });
    });
  </script>
<?php 
}
?>
</div>