<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start(); // запуск сессии если еще не начата 
if (isset($_SESSION["is_admin"]) && $_SESSION["is_admin"]) { // если залогинены то показываем страницу
  ?>
<div class="row mt-2">
<div id="menu" class="col-md-3 col-sm-12">
  <ul class="nav flex-column">
  <li class="nav-item"><a class="nav-link" href="./?admin&orders">Заказы</a></li>
  <li class="nav-item"><a class="nav-link" href="./?admin&products">Товары</a></li>
  <li class="nav-item"><a class="nav-link active" href="./?admin&categories">Категории</a></li>
  <li class="nav-item"><a class="nav-link" href="./?admin" id="logoutBtn">Выйти</a></li>
  </ul>
</div>
<div id="categories" class="col-md-9 col-sm-12">
  <table class="table" id="categoriesTable">
    <thead>
      <tr>
      <th>Название</th>
      <th></th>
      </tr>
      <tr>
        <td colspan="2"><button class="btn btn-success" id="addCategoryBtn">Добавить</button></td>
      </tr>
    </thead>
    <tbody>

    </tbody>
  </table>
</div>
<div id="alertsContainer" class="toast-container position-fixed bottom-0 end-0 p-3"></div>
</div>

<!--Модальное окно для добавления/редактирования-->
<div class="modal" tabindex="-1" id="categoryModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="categoryModalTitle">...</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <input type="hidden" id="operationModal" value="">
          <input type="hidden" id="categoryId" value="">
          <label for="categoryName" class="form-label">Название категории</label>
          <input type="text" class="form-control" id="categoryName">
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
  categoryModal = new bootstrap.Modal("#categoryModal", {backdrop:'static'}); // модальное окно в переменную запоминаем

  // запрос списка категорий
  function getCategories() {
    $.ajax({
      type: "POST",
      url: "./api/get_categories.php",
      dataType: "json",
      success: function(data) {
        if (data.error==0) { // если успешно выполнен запрос, то генерируем таблицу с категориями
          // пробегаем по полученному из БД списку и добавляем элементы для каждой категории
          $("#categoriesTable > tbody").html("");
          $.each(data.categories, function(idx, item) {
            tr_item = $("<tr></tr>");
            td_item = $("<td></td>").html(item.name);  
            tr_item.append(td_item);
            edit_btn = $("<button></button>").attr("class", "btn btn-sm btn-warning").html("<i class='bi bi-pencil-fill'></i>").on("click", {id: item.id, name: item.name, operation: "edit"}, showModal);
            del_btn = $("<button></button>").attr("class", "btn btn-sm btn-danger").html("<i class='bi bi-trash-fill'></i>").on("click", {id: item.id, name: item.name}, deleteCategory);
            td_item = $("<td></td>").append(edit_btn, del_btn);  
            tr_item.append(td_item);
            $("#categoriesTable > tbody").append(tr_item);  
          })
        } else {
          $("#categoriesTable > tbody").html("Не удалось получить данные");
        }          
      },
      error: function() {
        $("#categoriesTable > tbody").html("Не удалось получить данные");
      }
    });
  }

  // добавление категории
  function addCategory(newName) {
    if (!newName) return; // если название категории не введено - завершаем работу функции
    $.ajax({
      type: "POST",
      url: "./api/add_category.php",
      data: {name: newName},
      dataType: "json",
      success: function(data) {
        if (data.error==0) { 
          getCategories(); // если успешно выполнен запрос, то генерируем таблицу с категориями
          // и показываем сообщение об успешном добавлении
          $("#alertsContainer").html('<div id="alertOk" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">Категория '+newName+' добавлена успешно</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
          const alertToast = bootstrap.Toast.getOrCreateInstance($("#alertOk"));
          alertToast.show();
        } else { // в случае ошибки показываем сообщение об ошибке
          $("#alertsContainer").html('<div id="alert" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">При добавлении категории произошла ошибка</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
          const alertToast = bootstrap.Toast.getOrCreateInstance($("#alert"));
          alertToast.show();
        }          
      },
      error: function() { // в случае ошибки показываем сообщение об ошибке
        $("#alertsContainer").html('<div id="alert" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">При добавлении категории произошла ошибка</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
        const alertToast = bootstrap.Toast.getOrCreateInstance($("#alert"));
        alertToast.show();
      }
    });
  }

  // удаление категории
  function deleteCategory(ev) {
    $.ajax({
      type: "POST",
      url: "./api/delete_category.php",
      data: {id: ev.data.id},
      dataType: "json",
      success: function(data) {
        if (data.error==0) { 
          getCategories(); // если успешно выполнен запрос, то генерируем таблицу с категориями
          // и показываем сообщение об успешном удалении
          $("#alertsContainer").html('<div id="alertOk" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">Категория успешно удалена</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
          const alertToast = bootstrap.Toast.getOrCreateInstance($("#alertOk"));
          alertToast.show();
        } else { // в случае ошибки показываем сообщение об ошибке
          $("#alertsContainer").html('<div id="alert" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">При удалении категории произошла ошибка: '+data.error_info+'</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
          const alertToast = bootstrap.Toast.getOrCreateInstance($("#alert"));
          alertToast.show();
        }          
      },
      error: function() { // в случае ошибки показываем сообщение об ошибке
        $("#alertsContainer").html('<div id="alert" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">При удалении категории произошла ошибка</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
        const alertToast = bootstrap.Toast.getOrCreateInstance($("#alert"));
        alertToast.show();
      }
    });
  }

  // редактирование категории
  function editCategory(id, name) {
    $.ajax({
      type: "POST",
      url: "./api/edit_category.php",
      data: {id: id, name: name},
      dataType: "json",
      success: function(data) {
        if (data.error==0) { 
          getCategories(); // если успешно выполнен запрос, то генерируем таблицу с категориями
          // и показываем сообщение об успешном добавлении
          $("#alertsContainer").html('<div id="alertOk" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">Название категории изменено успешно</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
          const alertToast = bootstrap.Toast.getOrCreateInstance($("#alertOk"));
          alertToast.show();
        } else { // в случае ошибки показываем сообщение об ошибке
          $("#alertsContainer").html('<div id="alert" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">При редактировании категории произошла ошибка</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
          const alertToast = bootstrap.Toast.getOrCreateInstance($("#alert"));
          alertToast.show();
        }          
      },
      error: function() { // в случае ошибки показываем сообщение об ошибке
        $("#alertsContainer").html('<div id="alert" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">При редактировании категории произошла ошибка</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
        const alertToast = bootstrap.Toast.getOrCreateInstance($("#alert"));
        alertToast.show();
      }
    });
  }

  // при загрузке страницы по умолчанию запрашиваем данные
  getCategories();
  // привязываем функцию на кнопку добавления
  $("#addCategoryBtn").on("click", {id: "", name: "", operation: "add"}, showModal); 

  function showModal(ev) {
    // заполняем поля модального окна
    $("#categoryId").val(ev.data.id);
    $("#categoryName").val(ev.data.name);
    $("#operationModal").val(ev.data.operation);
    if (ev.data.operation == "edit") {
      $("#categoryModalTitle").html("Редактирование категории");
    } else {
      $("#categoryModalTitle").html("Добавление категории");
    }
    categoryModal.show(); // показываем модальное окно
  }

  $("#confirmModal").on("click", function() { // клик на кнопку Сохранить в модальном окне
    id = $("#categoryId").val();
    name = $("#categoryName").val();
    op = $("#operationModal").val();
    if (!name) return; // если имя не введено - ничего не делаем
    if (op == "add") addCategory(name);
    if (op == "edit") editCategory(id, name);
    categoryModal.hide(); // скрываем модальное окно
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