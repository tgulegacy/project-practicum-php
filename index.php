<?php
date_default_timezone_set('Asia/Yekaterinburg');
header('Access-Control-Allow-Origin: *');

// Заполнение БД
function fillDb($db, $conn)
{
    function fillProducts($db, $conn)
    {
        $products = $db->products;
        foreach ($products as $product) {
            $oldPrice = is_numeric($product->oldPrice) ? $product->oldPrice : null;

            $oldPriceStringLeft = $oldPrice ? ", oldPrice" : "";
            $oldPriceStringRight = $oldPrice ? ", '" . $oldPrice . "'" : "";

            $query = "INSERT INTO products (image, description, price" . $oldPriceStringLeft . ")" . " VALUES ('" . $product->image . "', '" . $product->description . "',  '" . $product->price . "'" . $oldPriceStringRight . ")";

            $result = $conn->query($query);

            if (!$result) {
                var_dump('<br>' . $conn->error);
            }
        }
    }

    function fillCategories($db, $conn)
    {
        $categories = $db->categories;
        foreach ($categories as $category) {
            $query = "INSERT INTO categories (title, code, type) VALUES ('" . $category->title . "', '" . $category->code . "',  '" . $category->type . "')";

            $result = $conn->query($query);

            if (!$result) {
                var_dump('<br>' . $conn->error);
            }
        }
    }

    function fillCategoryItems($db, $conn)
    {
        $categoryItems = $db->categoryItems;
        foreach ($categoryItems as $categoryItem) {
            $parentId = $categoryItem->parentId + 1;
            $query = "INSERT INTO category_items (parentId, code, title) VALUES ('" . $parentId . "', '" . $categoryItem->code . "',  '" . $categoryItem->title . "')";

            $result = $conn->query($query);

            if (!$result) {
                var_dump('<br>' . $conn->error);
            }
        }
    }

    function fillCategoryProductList($db, $conn)
    {
        $categoryProductList = $db->categoryProductList;
        foreach ($categoryProductList as $categoryProductListItem) {
            $productId = $categoryProductListItem->productId + 1;
            $categoryId = $categoryProductListItem->categoryId + 1;
            $categoryItemId = $categoryProductListItem->categoryItemId + 1;

            $query = "INSERT INTO category_product_list (productId, categoryId, categoryItemId) VALUES ('" . $productId . "', '" . $categoryId . "',  '" . $categoryItemId . "')";

            $result = $conn->query($query);

            if (!$result) {
                var_dump('<br>' . $conn->error);
            }
        }
    }
}

// db connection
$conn = new mysqli("localhost", "root", "", "db");
if ($conn->connect_error) {
    die("Ошибка: " . $conn->connect_error);
}

function getProducts($conn, $page, $limit, $order, $filters): array
{
    $offset = ($limit * $page) - $limit;

    $order_by_value = null;
    switch ($order) {
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

    $sql_query = 'SELECT DISTINCT products.id, products.image, products.description, products.price, products.oldPrice FROM `products`, `category_product_list`, `category_items`, `categories`';

    if (count($filters)) {
        $sql_query .= ' WHERE products.id = category_product_list.productId AND category_product_list.categoryItemId = category_items.id AND category_product_list.categoryId = category_items.parentId AND category_items.parentId = categories.id';
    }

    $sql_query_array_or = [];
    $sql_query_array_and = [];

    foreach ($filters as $filter) {
        if ($filter->type == 'checkbox') {
            $categoryItems = '"' . implode('", "', $filter->items) . '"';
            $categoryCode = $filter->code;
            $sql_query_array_or[] = '(category_items.code IN (' . $categoryItems . ') AND categories.code = "' . $categoryCode . '")';
        } elseif ($filter->type == 'range') {
            $sql_query_array_and[] = '(products.price > ' . $filter->items[0] . ' AND products.price < ' . $filter->items[1] . ')';
        }
    }

    $sql_query .= " AND (" . implode(' OR ', $sql_query_array_or) . ")";
    $sql_query .= " AND (" . implode(' AND ', $sql_query_array_and) . ")";

    $sql_query .= " ORDER BY " . $order_by_value . " LIMIT " . $limit . " OFFSET " . $offset;

    $products = [];
    if ($result = $conn->query($sql_query)) {
        while ($obj = $result->fetch_object()) {
            $products[] = [
                "id" => intval($obj->id),
                "description" => $obj->description,
                "image" => $obj->image,
                "oldPrice" => $obj->oldPrice ? intval($obj->oldPrice) : null,
                "price" => intval($obj->price),
            ];
        }

        $result->close();
    }

    return $products;
}

function getProductsCostLimits($conn): array
{
    $query = "SELECT MIN(price), MAX(price) FROM `products`";

    return $conn->query($query)->fetch_array();
}

function getFilters($conn): array
{
    $query = "SELECT categories.code, categories.title, categories.type, category_items.code as itemCode, category_items.title AS itemTitle FROM `categories` LEFT JOIN `category_items` ON categories.id = category_items.parentId";

    $data = [];
    if ($result = $conn->query($query)) {
        while ($obj = $result->fetch_object()) {
            $data[] = $obj;
        }

        $result->close();
    }

    $filtersObj = [];

    foreach ($data as $val) {
        if ($val->type == "checkbox") {
            if (array_key_exists($val->code, $filtersObj)) {
                $filtersObj[$val->code]['items'][] = [
                    "code" => $val->itemCode,
                    "title" => $val->itemTitle
                ];
            } else {
                $filtersObj[$val->code] = [
                    "code" => $val->code,
                    "title" => $val->title,
                    "type" => $val->type,
                    "items" => [
                        [
                            "code" => $val->itemCode,
                            "title" => $val->itemTitle
                        ]
                    ]
                ];
            }
        } elseif ($val->type == "range") {
            $limits = getProductsCostLimits($conn);

            $filtersObj[$val->code] = [
                "code" => $val->code,
                "title" => $val->title,
                "type" => $val->type,
                "min" => intval($limits[0]),
                "max" => intval($limits[1]),
            ];
        }
    }

    $filters = [];

    foreach ($filtersObj as $filtersObjItem) {
        $filters[] = $filtersObjItem;
    }

    return $filters;
}

$body = json_decode(file_get_contents('php://input'));

$page = $body->page;
$limit = $body->limit;
$order = $body->sort;
$filters = $body->filters;

header('Content-Type: application/json; charset=utf-8');

//$products = getProducts($conn, $page, $limit, $order, $filters);
//echo json_encode($products);

$filters = getFilters($conn);
echo json_encode($filters);

$conn->close();
