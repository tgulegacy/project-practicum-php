<?php

function getCatalog($limit = null, $page = null, $sort = null, $filters = [])
{
//    $filters = [
//        [
//            'type' => 'checkbox',
//            'items'=> ['gornie', 'gorodskie'],
//            'code'=>'category'
//        ],
//        [
//            'type' => 'checkbox',
//            'items'=> ['cube'],
//            'code'=>'brand'
//        ],
//        [
//            'type' => 'range',
//            'code'=>'price',
//            'items'=> [10000, 30000],
//        ]
//    ];
//    echo '<pre>';
//    print_r($filters);
//    echo '</pre>';

    $query = 'SELECT * FROM `catalog`';

    $query_arr = [];
    foreach ($filters as $filter) {
        if (!$filter->type) {
            $result = getOneResult('SELECT categories.type FROM categories WHERE categories.code="' . $filter->code . '"');
            if (!$result) {
                break;
            }

            $filter->type = $result['type'];
        }

        if ($filter->type == 'checkbox') {
            $categoryItems = '"' . implode('", "', $filter->items) . '"';
            $categoryCode = '"' . $filter->code . '"';

            $query_arr[] = '
                `catalog`.`id` IN (
                    SELECT `category_product_list`.`product_id` FROM `category_product_list`
    		    LEFT JOIN `categories`
    			    ON `categories`.id = `category_product_list`.category_id
    		    LEFT JOIN `category_items`
    			    ON `category_product_list`.`category_item_id` = `category_items`.`id`
			    WHERE (category_items.code IN (' . $categoryItems . ') AND categories.code = ' . $categoryCode . ')
			    )
            ';
        } elseif ($filter->type == 'range') {
            $query_arr[] = '(catalog.price > ' . $filter->items[0] . ' AND catalog.price < ' . $filter->items[1] . ')';
        }
    }

    if (count($query_arr)) {
        $query .= ' WHERE ' . implode(' AND ', $query_arr);
    }

    if ($sort) {
        $order_by_value = null;
        switch ($sort) {
            case 'alp':
                $order_by_value = "description";
                break;
            case "price-down":
                $order_by_value = "price DESC";
                break;
            case "price-up":
                $order_by_value = "price ASC";
                break;
        }
        if ($order_by_value) {
            $query .= " ORDER BY " . $order_by_value;
        }
    }

    if ($limit and $page) {
        $offset = ($limit * $page) - $limit;
        $query .= " LIMIT " . $limit . " OFFSET " . $offset;
    }

    return getAssocResult($query);
}

function getProductsCostLimits()
{
    $query = "SELECT MIN(price), MAX(price) FROM `catalog`";

    return getAssocResult($query);
}

function getFilters($filters): array
{
    $query = "SELECT categories.code, categories.title, categories.type, category_items.code as itemCode, category_items.title AS itemTitle FROM `categories` LEFT JOIN `category_items` ON categories.id = category_items.parent_id";
    $data = getAssocResult($query);

    $filtersObj = [];
    foreach ($data as $val) {
        $filter_active = array_values(array_filter($filters, function ($filter) use ($val) {
            if ($filter->type === 'checkbox') {
                return $filter->code == $val['code'] && in_array($val['itemCode'], $filter->items);
            } elseif ($filter->type === 'range') {
                return $filter->code == $val['code'];
            } else return [];
        }));
        
        if ($val['type'] == "checkbox") {
            $item = [
                "code" => $val['itemCode'],
                "title" => $val['itemTitle']
            ];

            if ($filter_active) {
                $item['is_active'] = true;
            }
            
            if (array_key_exists($val['code'], $filtersObj)) {
                $filtersObj[$val['code']]['items'][] = $item;
            } else {
                $filtersObj[$val['code']] = [
                    "code" => $val['code'],
                    "title" => $val['title'],
                    "type" => $val['type'],
                    "items" => [
                        $item
                    ]
                ];
            }
        } elseif ($val['type'] == "range") {
            $data = getProductsCostLimits();
            [$min, $max] = array_values($data[0]);
            
            $item = [
                "code" => $val['code'],
                "title" => $val['title'],
                "type" => $val['type'],
                "min" => intval($min),
                "max" => intval($max),
            ];
            
            if ($filter_active) {
                $item['left'] = (int)$filter_active[0]->items[0];
                $item['right'] = (int)$filter_active[0]->items[1];
            } else {
                $item['left'] = $item['min'];
                $item['right'] = $item['max'];
            }

            $filtersObj[$val['code']] = $item;
        }
    }

    return array_values($filtersObj);
}

function getFiltersFromArray ($array) {
    $filters_params = array_filter($array, function ($param) {
        return !in_array($param, ['page', 'limit', 'sort']);
    }, ARRAY_FILTER_USE_KEY);

    $filters = [];

    foreach ($filters_params as $key => $filters_param) {
        $filters[] = [
            'code' => $key,
            'items' => explode(',', $filters_param)
        ];
    }
    
    return $filters;
}

function getPageCount($items_len, $limit) {
    return ceil($items_len/$limit);
}