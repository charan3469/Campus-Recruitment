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
$id1=array(
  'ID'=>$id_value,
);
$result1=sdb_row("products",$id1);
if($id_value!=0)
{
if($result1->quantity>0)
{
sdb_i("cart", $id);
echo json_encode("success");
}
else {
echo json_encode("unsuccess");    
}
}
else
echo json_encode("succ");    
?>