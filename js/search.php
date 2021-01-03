<?php

include 'third_party/db/init.php';
session_start();
$search = $_POST["search"];
$c_id = $_POST["cid"];
$country = $_SESSION["country"];
$price = $_POST["price"];
$quantity = $_POST["quantity"];
$array = array();

$pattern = "/" . $search . "/i";
if($country=="india")
$query = "select products.PRICE_INDIA as PRICE,products.* from products LEFT JOIN CP ON CP.PID = products.ID";
if($country=="england")
$query = "select products.PRICE_ENGLAND as PRICE,products.* from products LEFT JOIN CP ON CP.PID = products.ID";
if($country=="australia")
$query = "select products.PRICE_AUSTRALIA as PRICE,products.* from products LEFT JOIN CP ON CP.PID = products.ID";
if($country=="u.s")
$query = "select products.PRICE_US as PRICE,products.* from products LEFT JOIN CP ON CP.PID = products.ID";
if ($c_id !== null && $price != null)
{
      $query .= " where CP.CID=" . $c_id . " and products.PRICE >= " . $price;
}
else if ($c_id == null && $price == null)
{
    
}
else if(c_id!==null && $price == null){
   $query .= " where CP.CID=" . $c_id;
}
else if (price != null && $c_id == null){

     $query .= " where products.PRICE >= " . $price;
}
if($quantity)
    $query .= " HAVING products.quantity > 0 "; 
$result = db_rows($query);
for ($i = 0; $i < sizeof($result); $i++) {
    if (preg_match($pattern, $result[$i]->NAME)) {
        array_push($array, $result[$i]);
    }
}
//var_dump($result);
echo json_encode($array);
?>