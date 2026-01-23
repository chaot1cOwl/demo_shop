<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start(); // запуск сессии если еще не начата 
if (isset($_SESSION["is_admin"]) && $_SESSION["is_admin"]) { // если залогинены то показываем страницу
  ?>
<div class="row mt-2">
<div id="menu" class="col-md-3 col-sm-12">
  <ul class="nav flex-column">
  <li class="nav-item"><a class="nav-link" href="./?admin&orders">Заказы</a></li>
  <li class="nav-item"><a class="nav-link active" href="./?admin&products">Товары</a></li>
  <li class="nav-item"><a class="nav-link" href="./?admin&categories">Категории</a></li>
  <li class="nav-item"><a class="nav-link" href="./?admin" id="logoutBtn">Выйти</a></li>
  </ul>
</div>
<div id="products" class="col-md-9 col-sm-12">
  <table class="table" id="productsTable">
    <thead>
      <tr>
      <th>Категория</th>
      <th>Название</th>
      <th>Описание</th>
      <th>Файл изображения</th>
      <th>Цена</th>
      <th></th>
      </tr>
      <tr>
        <td colspan="6"><button class="btn btn-success" id="addProductBtn">Добавить</button></td>
      </tr>
    </thead>
    <tbody>

    </tbody>
  </table>
</div>
<div id="alertsContainer" class="toast-container position-fixed bottom-0 end-0 p-3"></div>
</div>

<!--Модальное окно для добавления/редактирования-->
<div class="modal" tabindex="-1" id="productModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="productModalTitle">...</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <input type="hidden" id="operationModal" value="">
          <input type="hidden" id="productId" value="">
          <label for="productCategory" class="form-label">Категория товара</label>
          <select class="form-select" id="productCategory"></select>
          <label for="productName" class="form-label">Название</label>
          <input type="text" class="form-control" id="productName">
          <label for="productDescription" class="form-label">Описание</label>
          <input type="text" class="form-control" id="productDescription">
          <!-- В идеале здесь загрузка файла, но в рамках демо просто название файла+расширение, например, image.jpg -->
          <label for="productImage" class="form-label">Файл изображения</label>
          <input type="text" class="form-control" id="productImage">
          <label for="productPrice" class="form-label">Цена</label>
          <input type="number" class="form-control" id="productPrice">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelModal">Отмена</button>
        <button type="button" class="btn btn-success" id="confirmModal">Сохранить</button>
      </div>
    </div>
  </div>
</div>


<script>
  productModal = new bootstrap.Modal("#productModal", {backdrop:'static'}); // модальное окно в переменную запоминаем
  categoriesList = {};
  // запрос списка категорий
  $.ajax({
    type: "POST",
    url: "./api/get_categories.php",
    dataType: "json",
    success: function(data) {
      if (data.error==0) { // если успешно выполнен запрос, то генерируем список категорий и заполняем select в модальном окне
        $.each(data.categories, function(idx, item) {
          categoriesList[item.id] = item.name;
          new_opt = $("<option></option>").attr("value", item.id).html(item.name);
          $("#productCategory").append(new_opt);
        });
      } else {
        $("#alertsContainer").html('<div id="alert" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">При запросе списка категорий произошла ошибка</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
        const alertToast = bootstrap.Toast.getOrCreateInstance($("#alert"));
        alertToast.show();
      }          
    },
    error: function() {
      $("#alertsContainer").html('<div id="alert" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">При запросе списка категорий произошла ошибка</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
      const alertToast = bootstrap.Toast.getOrCreateInstance($("#alert"));
      alertToast.show();
    }
  });
  
  // запрос списка продуктов
  function getProducts() {
    $.ajax({
      type: "POST",
      url: "./api/get_products.php",
      dataType: "json",
      success: function(data) {
        if (data.error==0) { // если успешно выполнен запрос, то генерируем таблицу с продуктами
            // пробегаем по полученному из БД списку и добавляем элементы для каждого продукта
            $("#productsTable > tbody").html("");
            $.each(data.products, function(idx, item) {
              tr_item = $("<tr></tr>");
              td_item = $("<td></td>").html(categoriesList[item.id_categories]);  
              tr_item.append(td_item);
              td_item = $("<td></td>").html(item.name);  
              tr_item.append(td_item);
              td_item = $("<td></td>").html(item.description);  
              tr_item.append(td_item);
              td_item = $("<td></td>").html(item.image);  
              tr_item.append(td_item);
              td_item = $("<td></td>").html(item.price);  
              tr_item.append(td_item);
              edit_btn = $("<button></button>").attr("class", "btn btn-sm btn-warning").html("<i class='bi bi-pencil-fill'></i>").on("click", {id: item.id, category: item.id_categories, name: item.name, description: item.description, image: item.image, price: item.price, operation: "edit"}, showModal);
              del_btn = $("<button></button>").attr("class", "btn btn-sm btn-danger").html("<i class='bi bi-trash-fill'></i>").on("click", {id: item.id, name: item.name}, deleteProduct);
              td_item = $("<td></td>").append(edit_btn, del_btn);  
              tr_item.append(td_item);
              $("#productsTable > tbody").append(tr_item);  
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


  // добавление товара
  function addProduct(category, name, description, image, price) {
    console.log(category, name, description, image, price)
    if (!name || !category || !description || !image || !price) return; // если название что-то не введено - завершаем работу функции
    $.ajax({
      type: "POST",
      url: "./api/add_product.php",
      data: {name: name, category: category, description: description, image: image, price: price},
      dataType: "json",
      success: function(data) {
        if (data.error==0) { 
          getProducts(); // если успешно выполнен запрос, то генерируем таблицу с категориями
          // и показываем сообщение об успешном добавлении
          $("#alertsContainer").html('<div id="alertOk" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">Товар '+name+' добавлен успешно</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
          const alertToast = bootstrap.Toast.getOrCreateInstance($("#alertOk"));
          alertToast.show();
        } else { // в случае ошибки показываем сообщение об ошибке
          $("#alertsContainer").html('<div id="alert" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">При добавлении товара произошла ошибка</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
          const alertToast = bootstrap.Toast.getOrCreateInstance($("#alert"));
          alertToast.show();
        }          
      },
      error: function() { // в случае ошибки показываем сообщение об ошибке
        $("#alertsContainer").html('<div id="alert" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">При добавлении товара произошла ошибка</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
        const alertToast = bootstrap.Toast.getOrCreateInstance($("#alert"));
        alertToast.show();
      }
    });
  }

  // удаление товара
  function deleteProduct(ev) {
    $.ajax({
      type: "POST",
      url: "./api/delete_product.php",
      data: {id: ev.data.id},
      dataType: "json",
      success: function(data) {
        if (data.error==0) { 
          getProducts(); // если успешно выполнен запрос, то генерируем таблицу с товарами
          // и показываем сообщение об успешном удалении
          $("#alertsContainer").html('<div id="alertOk" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">Товар успешно удален</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
          const alertToast = bootstrap.Toast.getOrCreateInstance($("#alertOk"));
          alertToast.show();
        } else { // в случае ошибки показываем сообщение об ошибке
          $("#alertsContainer").html('<div id="alert" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">При удалении товара произошла ошибка</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
          const alertToast = bootstrap.Toast.getOrCreateInstance($("#alert"));
          alertToast.show();
        }          
      },
      error: function() { // в случае ошибки показываем сообщение об ошибке
        $("#alertsContainer").html('<div id="alert" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">При удалении товара произошла ошибка</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
        const alertToast = bootstrap.Toast.getOrCreateInstance($("#alert"));
        alertToast.show();
      }
    });
  }

  // редактирование товара
  function editProduct(id, category, name, description, image, price) {
    $.ajax({
      type: "POST",
      url: "./api/edit_product.php",
      data: {id: id, name: name, category: category, description: description, image: image, price: price},
      dataType: "json",
      success: function(data) {
        if (data.error==0) { 
          getProducts(); // если успешно выполнен запрос, то генерируем таблицу с товарами
          // и показываем сообщение об успешном добавлении
          $("#alertsContainer").html('<div id="alertOk" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">Товар успешно изменен</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
          const alertToast = bootstrap.Toast.getOrCreateInstance($("#alertOk"));
          alertToast.show();
        } else { // в случае ошибки показываем сообщение об ошибке
          $("#alertsContainer").html('<div id="alert" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">При редактировании товара произошла ошибка</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
          const alertToast = bootstrap.Toast.getOrCreateInstance($("#alert"));
          alertToast.show();
        }          
      },
      error: function() { // в случае ошибки показываем сообщение об ошибке
        $("#alertsContainer").html('<div id="alert" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">При редактировании товара произошла ошибка</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
        const alertToast = bootstrap.Toast.getOrCreateInstance($("#alert"));
        alertToast.show();
      }
    });
  }

  // при загрузке страницы по умолчанию запрашиваем данные
  getProducts();
  // привязываем функцию на кнопку добавления
  $("#addProductBtn").on("click", {id: "", category: "", name: "", description: "", image: "", price: "", operation: "add"}, showModal); 

  function showModal(ev) {
    // заполняем поля модального окна
    $("#productId").val(ev.data.id);
    $("#productCategory").val(ev.data.category);
    $("#productName").val(ev.data.name);
    $("#productDescription").val(ev.data.description);
    $("#productImage").val(ev.data.image);
    $("#productPrice").val(ev.data.price);
    $("#operationModal").val(ev.data.operation);
    if (ev.data.operation == "edit") {
      $("#productModalTitle").html("Редактирование товара");
    } else {
      $("#productModalTitle").html("Добавление товара");
    }
    productModal.show(); // показываем модальное окно
  }

  $("#confirmModal").on("click", function() { // клик на кнопку Сохранить в модальном окне
    id = $("#productId").val();
    category = $("#productCategory").val();
    name = $("#productName").val();
    description = $("#productDescription").val();
    image = $("#productImage").val();
    price = $("#productPrice").val();
    op = $("#operationModal").val();
    if (!name || !category || !description || !image || !price) return; // если что-то не введено - ничего не делаем
    if (op == "add") addProduct(category, name, description, image, price);
    if (op == "edit") editProduct(id, category, name, description, image, price);
    productModal.hide(); // скрываем модальное окно
  });

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