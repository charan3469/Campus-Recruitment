<?php
include 'third_party/db/init.php';
session_start();
$id_value =$_POST["id_value"];
$country = $_SESSION["country"];
$table_name = "products";
$id=array(
  ':id'=>$id_value
);
if($country=="india")
$query="select products.PRICE_INDIA as PRICE,products.* from products where id=:id";
if($country=="australia")
$query="select products.PRICE_AUSTRALIA as PRICE,products.* from products where id=:id";
if($country=="england")
$query="select products.PRICE_ENGLAND as PRICE,products.* from products where id=:id";
if($country=="u.s")
$query="select products.PRICE_US as PRICE,products.* from products where id=:id";
$result=db_row($query,$id);
echo json_encode($result);
?>