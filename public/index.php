<?php

include $_SERVER['DOCUMENT_ROOT'] .  '/../config/config.php';

$url_array = explode('/', $_SERVER['REQUEST_URI']);

if ($url_array[1] == '') {
	$page = 'catalog';
} else {
	$page = $url_array[1];
}

//$page = $_GET['page'] ?? 'catalog';

$params = prepareVariables($page);

echo render($page, $params);
