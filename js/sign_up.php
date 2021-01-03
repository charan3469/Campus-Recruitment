<?php

include 'third_party/db/init.php';
$user_name = $_POST["user_name"];
$email = $_POST["email"];
$password = $_POST["password"];
$id=array(
  'user_name'=>$user_name,
  'password'=>$password,
  'email'=>$email
    
);
$result=sdb_i("users",$id);
echo json_encode($result);
?>