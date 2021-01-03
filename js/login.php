<?php
session_start();
include 'third_party/db/init.php';
$user_name = $_POST["user_name"];
$password = $_POST["password"];
$array=array(
    ':username'=>$user_name,
    ':password'=>$password
    
);
$query="select ID from users where user_name=:username and password=:password ";
$result=db_row($query,$array);
$_SESSION['ID']=$result->ID;
if($result)
echo son_encode("success");
?>