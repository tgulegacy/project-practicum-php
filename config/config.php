<?php

define('ROOT', dirname(__DIR__));
define('VIEWS_DIR', ROOT . '/views/');
define('LAYOTS_DIR', '/layouts/');

include ROOT . '/engine/core.php';
include ROOT . '/engine/db.php';
include ROOT . '/models/catalog.php';
include ROOT . '/controller/controller.php';
