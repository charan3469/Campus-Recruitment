<?php
include 'third_party/db/init.php';
session_start();
$country = $_SESSION["country"];
$c=$_SESSION['ID'];
$FNAME=$_POST['fname'];
$LNAME=$_POST['lname'];
$ADDRESS1=$_POST['address1'];
$ADDRESS2=$_POST['address2'];
$ZIP_CODE=$_POST['zip_code'];
$CITY=$_POST['city'];
$STATE=$_POST['state'];
if(!$c)
    $c=-1;
$array=array(
    ':c'=>$c
);
if($country=="india")
$query="SELECT products.PRICE_INDIA as PRICE,products.* FROM products LEFT JOIN cart ON cart.P_ID = products.ID where C_ID=:c";
if($country=="england")
$query="SELECT products.PRICE_ENGLAND as PRICE,products.* FROM products LEFT JOIN cart ON cart.P_ID = products.ID where C_ID=:c";
if($country=="australia")
$query="SELECT products.PRICE_AUSTRALIA as PRICE,products.* FROM products LEFT JOIN cart ON cart.P_ID = products.ID where C_ID=:c";
if($country=="u.s")
$query="SELECT products.PRICE_US as PRICE,products.* FROM products LEFT JOIN cart ON cart.P_ID = products.ID where C_ID=:c";
        $id=db_rows($query,$array);
for($i=0;$i<sizeof($id);$i++)
{
    $filters=array(
        'ID'=>$id[$i]->ID
    );
    $quanity=$id[$i]->quantity;
    $price=$id[$i]->PRICE;
    $fields=array(
        'quantity'=>$quanity-1
    );
    $result2=sdb_u("products",$fields,$filters);
$fields1=array(
        'P_ID'=>$id[$i]->ID,
        'C_ID'=>$c,
      'PRICE'=>$price,
        
    );

$er= sdb_i("user_orders",$fields1); 
$ship_detail=array(
    'PID'=>$er,
    'CID'=>$c,
    'FNAME'=>$FNAME,
    'LNAME'=>$LNAME,
    'ZIP_CODE'=>$ZIP_CODE,
    'ADDRESS1'=>$ADDRESS1,
    'ADDRESS2'=>$ADDRESS2,
    'CITY'=>$CITY,
    'STATE'=>$STATE  
);
sdb_i("shipping_details",$ship_detail);

}

echo json_encode($er);  
?>
