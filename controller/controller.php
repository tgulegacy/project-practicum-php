<?php

function prepareVariables($page) {
	$params['layout'] = 'main';

	switch ($page) {
		case 'catalog':
			$params['$title'] = 'Каталог';
			$params['catalog'] = getCatalog();
			break;
		case 'api-catalog':
			echo json_encode(getCatalog(), JSON_UNESCAPED_UNICODE);
			die();
		case 'cart':
			$params['$title'] = 'Корзина';
			break;
		default:
			echo '404';
			break;
	}

	return $params;
}
