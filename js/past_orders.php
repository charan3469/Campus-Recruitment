<?php
include 'third_party/db/init.php';
session_start();
$c=$_SESSION['ID'];
$country = $_SESSION["country"];
if(!$c)
    $c=-1;
$array=array(
    ':c'=>$c
);
if($country=="india")
$query="SELECT products.PRICE_INDIA as PRICE,products.* FROM user_orders LEFT JOIN products ON user_orders.P_ID = products.ID where C_ID=:c";
if($country=="england")
$query="SELECT products.PRICE_ENGLAND as PRICE,products.* FROM user_orders LEFT JOIN products ON user_orders.P_ID = products.ID where C_ID=:c";
if($country=="australia")
$query="SELECT products.PRICE_AUSTRALIA as PRICE,products.* FROM user_orders LEFT JOIN products ON user_orders.P_ID = products.ID where C_ID=:c";
if($country=="u.s")
$query="SELECT products.PRICE_US as PRICE,products.* FROM user_orders LEFT JOIN products ON user_orders.P_ID = products.ID where C_ID=:c";
$result=db_rows($query,$array);


echo json_encode($result);
?>
