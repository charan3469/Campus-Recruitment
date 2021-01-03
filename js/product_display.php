<?php
include 'third_party/db/init.php';
session_start();
$c = $_POST['id_value'];
$g = $_POST['G'];
$country = $_SESSION["country"];
$array=array( 
  ':c'=>$c,
  ':g'=>$g
);
if($country=="india")
$query="SELECT products.PRICE_INDIA as PRICE,products.* FROM products LEFT JOIN CP ON CP.PID = products.ID where products.GENDER=:g and CP.CID=:c";
if($country=="england")
$query="SELECT products.PRICE_ENGLAND as PRICE,products.* FROM products LEFT JOIN CP ON CP.PID = products.ID where products.GENDER=:g and CP.CID=:c";
if($country=="australia")
$query="SELECT products.PRICE_AUSTRALIA as PRICE,products.* FROM products LEFT JOIN CP ON CP.PID = products.ID where products.GENDER=:g and CP.CID=:c";
if($country=="u.s")
$query="SELECT products.PRICE_US as PRICE,products.* FROM products LEFT JOIN CP ON CP.PID = products.ID where products.GENDER=:g and CP.CID=:c";
$result=db_rows($query,$array);
echo json_encode($result);
        
        

