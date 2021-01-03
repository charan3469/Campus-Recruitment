<?php

$base = dirname(__FILE__);

require $base . '/config.php';
require $base . '/classes/dbcore.php';
require $base . '/classes/db.php';
require $base . '/classes/dbcontroller.php';

require $base . '/functions/db.php';
require $base . '/functions/relavent.php';

$db = new db();

