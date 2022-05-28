<?php

function prepareVariables($page) {
	$params['layout'] = 'main';

	switch ($page) {
		case 'catalog':
			$params['$title'] = 'Каталог';
            $filters = getFiltersFromArray($_GET);
            foreach ($filters as &$filter) {
                $filter = (object) $filter;
            }
			$params['catalog'] = getCatalog($_GET['limit'], $_GET['page'], $_GET['sort'], $filters);
            $params['filters'] = getFilters();
			break;
		case 'api-catalog':
            $body = (array) json_decode(file_get_contents('php://input'));
            $items = getCatalog($body['limit'], $body['page'], $body['sort'], $body['filters']);
			echo json_encode([
                'items' => $items,
                'pageCount' => getPageCount(count($items), $body['limit'])
            ], JSON_UNESCAPED_UNICODE);
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
