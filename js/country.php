<?php
include 'third_party/db/init.php';
session_start();
$status= $_POST["status"];
if($status==1)
{
$country= $_POST["country"];
$_SESSION['country']=$country;
}
echo $_SESSION['country'];
?>