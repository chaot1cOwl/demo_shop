<?php if(session_status() !== PHP_SESSION_ACTIVE) session_start(); // запуск сессии если еще не начата ?>
<h3>Корзина</h3>
<div>
<?php 
if (!isset($_SESSION["cart"]) || (isset($_SESSION["cart"]) && count($_SESSION["cart"]) == 0)) {
  echo "В корзине пока ничего нет. <a href='/?catalogue' class='link-success'>Добавим что-нибудь?</a>";
} else {
  ?>
  <table>
  <?php
  $total = 0;
  foreach ($_SESSION["cart"] as $id => $item) {
    $item_row = "<tr>";
    $item_row .= "<td>".$item["name"]."</td>";
    $item_row .= "<td>".$item["price"]." руб.</td>";
    $item_row .= "<td>".$item["count"]."</td>";
    $item_row .= "<td>".$item["price"] * $item["count"]." руб.</td>";
    $total += $item["count"] * $item["price"];
    
    $item_row .= "</tr>";
    echo $item_row;
  }  
  ?>
  <tr><td colspan="3">Итого</td><td><?php echo $total; ?> руб.</td></tr>
  </table>
<?php 
}
?>
</div>