<?php

include 'third_party/db/init.php';
session_start();
session_destroy();
echo "success";
?>