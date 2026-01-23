<?php 
if (session_status() !== PHP_SESSION_ACTIVE) session_start(); // запуск сессии если еще не начата 
if (isset($_SESSION["is_admin"]) && $_SESSION["is_admin"]) { // если уже залогинены, то показываем только ссылку на админку
  echo "<a href='./?admin'>Перейти в админ-панель</a>";
} else { // если не залогинены показываем форму входа
  ?>
<div class="col-4 mx-auto">
<div class="mb-3">
  <label for="login" class="form-label">Логин</label>
  <input type="text" class="form-control" id="login">
</div>
<div class="mb-3">
  <label for="password" class="form-label">Пароль</label>
  <input type="password" class="form-control" id="password">
</div>
<button class="btn btn-shop" id="checkUser">Войти</button>
</div>
<div id="alertsContainer" class="toast-container position-fixed bottom-0 end-0 p-3"></div> 

<script>
  $("#checkUser").on("click", function() {
    login = $("#login").val();
    passw = $("#password").val();
    // простая валидация полей ввода
    if (!login) $("#login").addClass("is-invalid"); else $("#login").removeClass("is-invalid");
    if (!passw) $("#password").addClass("is-invalid"); else $("#password").removeClass("is-invalid");
    if (login && passw) { // если поля ввода заполнены, то пробуем выполнить авторизацию
      $.ajax({ // выполняем запрос на проверку данных
        type: "POST",
        url: "./api/login_user.php",
        data: {login: login, password: passw},
        dataType: "json",
        success: function(data) {
          if (data.error==0) { // в случае успеха обновляем страницу 
            location.reload();
          }  else { // при ошибке отображаем сообщение об ошибке
            $("#alertsContainer").html('<div id="alert" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">Не удалось войти: '+data.error_info+'</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
            const alertToast = bootstrap.Toast.getOrCreateInstance($("#alert"));
            alertToast.show();  
          }
        },
        error: function() { // при ошибке отображаем сообщение об ошибке
          $("#alertsContainer").html('<div id="alert" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">Не удалось войти</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
          const alertToast = bootstrap.Toast.getOrCreateInstance($("#alert"));
          alertToast.show();
        }
      });
    }
  });
</script>


<?php
}
?>