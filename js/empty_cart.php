<?php
include 'third_party/db/init.php';
session_start();
$c=$_SESSION['ID'];
if(!$c)
    $c=-1;
$filter=array(
    'C_ID'=>$c
);
sdb_d("cart",$filter);
echo json_encode($result);
?>