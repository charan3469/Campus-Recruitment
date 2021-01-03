<?php

include 'third_party/db/init.php';
session_start();
$c=$_SESSION['ID'];
$array=array(
    ':c'=>$c
);
if($c)
{
    $query="select user_name from users where ID=:c";
    $result=db_row($query,$array);
   echo $result->user_name;
}
 

?>