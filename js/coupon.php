<?php
include 'third_party/db/init.php';
$coupon = $_POST["coupon"];
$id=array(
  'coupon'=>$coupon
);
$result=sdb_row("coupons",$id);
echo json_encode($result);
?>