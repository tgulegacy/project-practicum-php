<?php

function prepareVariables($page) {
	$params['layout'] = 'main';

	switch ($page) {
		case 'catalog':
			$params['$title'] = 'Каталог';
            $filters = getFiltersFromArray($_GET);
			$params['catalog'] = getCatalog($_GET['limit'], $_GET['page'], $_GET['sort'], $filters);
            $params['filters'] = getFilters();
			break;
		case 'api-catalog':
            $filters = getFiltersFromArray($_GET);
			echo json_encode(getCatalog($_GET['limit'], $_GET['page'], $_GET['sort'], $filters), JSON_UNESCAPED_UNICODE);
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
