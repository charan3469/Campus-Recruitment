<?php
include 'third_party/db/init.php';
$query="select * from products where ID=1";
$result=db_rows($query);
echo json_encode($result);
?>