<h3 class="mt-2">Что выберешь сегодня?</h3>
<div class="container row">
  <div id="categories" class="col-md-3 col-sm-12">
    <ul class="nav flex-column"></ul>
  </div>
  <div id="products" class="col-md-9 col-sm-12 d-flex flex-wrap"></div>
  <div id="alertsContainer" class="toast-container position-fixed bottom-0 end-0 p-3"></div>
</div>

<script>
  // функция для добавления товара в корзину
  function addToCart(ev) { 
    let product = {id: ev.data.id, name: ev.data.name, price: ev.data.price}; // забираем переданную информацию о товаре
    $.ajax({
      type: "POST",
      url: "./api/add_to_cart.php",
      data: product,
      dataType: "json",
      success: function(data) {
        if (data.error==0) { // если успешно выполнен запрос, то генерируем список ссылок
          // и показываем сообщение об успешном добавлении товара
          $("#alertsContainer").html('<div id="alertOk" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">'+ev.data.name+' добавлен в корзину<br><a href="./?cart" class="link-light">Перейти в корзину</a></div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
          const alertToast = bootstrap.Toast.getOrCreateInstance($("#alertOk"));
          alertToast.show();
        } else {
          $("#alertsContainer").html('<div id="alert" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">При добавлении в корзину произошла ошибка</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
          const alertToast = bootstrap.Toast.getOrCreateInstance($("#alert"));
          alertToast.show();
        }          
      },
      error: function() {
        $("#alertsContainer").html('<div id="alert" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">При добавлении в корзину произошла ошибка</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>');
        const alertToast = bootstrap.Toast.getOrCreateInstance($("#alert"));
        alertToast.show();
      }
    });
  }

  // выбор текущих фильтров из GET-параметров
  let cat = <?php if (isset($_GET['c'])) echo '"'.$_GET['c'].'"'; else echo '"all"'; ?>;

  // запрос списка категорий товаров
  $.ajax({
    type: "POST",
    url: "./api/get_categories.php",
    dataType: "json",
    success: function(data) {
      if (data.error==0) { // если успешно выполнен запрос, то генерируем список ссылок
        tmp_uri = "./?catalogue&c="; //  базовый url для генерации ссылок
        // первым элементом списка категорий ставим "Все", чтобы получить общий список товаров без учета категории
        url_all = tmp_uri + "all";
        li_item = $("<li></li>").attr("class", "nav-item").html("<a class='nav-link' href='"+url_all+"'>Все</a>");
        if (cat == 'all') li_item.children().addClass("active"); // добавляем активный класс, чтобы выделить текущий выбор
        $("#categories > ul").append(li_item);
        // пробегаем по полученному из БД списку и добавляем элементы для каждой категории
        $.each(data.categories, function(idx, item) {
          utl_item = tmp_uri+item.id;
          li_item = $("<li></li>").attr("class", "nav-item").html("<a class='nav-link' href='"+utl_item+"'>"+item.name+"</a>");
          if (cat == item.id) li_item.children().addClass("active"); // добавляем активный класс, чтобы выделить текущий выбор
          $("#categories > ul").append(li_item);  
        })
      } else {
        $("#categories").html("Не удалось получить данные");
      }          
    },
    error: function() {
      $("#categories").html("Не удалось получить данные");
    }
  });

  // запрос списка товаров с учетом текущих фильтров
  $.ajax({
    type: "POST",
    url: "./api/get_products.php",
    data: {category: cat}, // фильтры подключаем здесь
    dataType: "json",
    success: function(data) {
      if (data.error==0) { // если успешно выполнен запрос, то генерируем карточки товаров
        images_dir = "files/images/"; // базовый каталог для картинок товаров
        $.each(data.products, function(idx, item) {
          // создаем объект карточки
          // сначала создаем части карточки
          // через .attr добавляем необходимые атрибуты
          // через .html добавляем отображаемое содержимое
          card_img = $("<img>").attr("src", images_dir+item.image).attr("class", "card-img-top"); 
          card_body = $("<div></div>").attr("class", "card-body");
          card_body.append($("<h5></h5>").attr("class", "card-title").html(item.name));
          card_body.append($("<p></p>").attr("class", "card-text").html(item.description));
          // к кнопке сразу привязываем функцию добавления товара в корзину через .on
          card_body.append($("<button></button>").attr("class", "btn btn-shop").on("click", {id: item.id, name:item.name, price:item.price}, addToCart).html("<i class='bi bi-cart-plus'></i> "+item.price+" руб."));
          // создаем объект карточки
          card = $("<div></div>").attr("class", "card product-card col-lg-3 col-md-4 col-sm-6");
          // добавляем в него части
          card.append(card_img).append(card_body);         
          // добавляем саму карточку в блок со списком товаров
          $("#products").append(card);
        });
        
      } else {
        $("#products").html("Не удалось получить данные");
      }          
    },
    error: function(jqXHR,textStatus,thrown) {
      $("#products").html("Не удалось получить данные");
    }
  });
    
</script>