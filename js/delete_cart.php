<?php

include 'third_party/db/init.php';
session_start();
$id_value = $_POST["id_value"];

$c=$_SESSION['ID'];
if(!$c)
    $c=-1;
$id=array(
  'P_ID'=>$id_value,
  'C_ID'=>$c
);

$result=sdb_row("products",$id);
sdb_d("cart",$id);

echo json_encode($result);
?>

