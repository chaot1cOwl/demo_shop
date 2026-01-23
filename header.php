<header>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
        <a class="navbar-brand" href="./"><i class="bi bi-cup-hot-fill"></i> Кофейку?</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Раскрыть">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <div class="navbar-nav d-flex justify-content-between w-100">
            <div class="d-lg-flex flex-row">
                <!-- подсветка активной страницы в шапке -->
                <a class="nav-link <?php if (isset($_GET["catalogue"])) echo "active";?>"  href="./?catalogue">Каталог</a>
            </div>
            <div  class="d-lg-flex flex-row">
                <a class="nav-link" href="./?cart" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Корзина"><i class="bi bi-cart"></i><span class="d-inline d-lg-none"> Корзина</span></a>                
            </div>
            </div>
        </div>
    </nav>
</header>