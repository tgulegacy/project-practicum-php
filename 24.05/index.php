<?php
	define("HOST", "localhost");
	define("USER", "root");
	define("PASS", "root");
	define("DB", "project");

	$db = mysqli_connect(HOST, USER, PASS, DB);

	if ($_GET['action'] == 'add') {
		$id = (int)$_GET['id'];
		$cartId = mysqli_query($db, "SELECT * FROM cart where `good_id` = {$id}");
		if (mysqli_fetch_assoc($cartId)) {
			mysqli_query($db, "UPDATE `cart` SET `count` = `count` + 1 WHERE `cart`.`good_id` = {$id}");
		} else {
			mysqli_query($db, "INSERT INTO `cart`(`good_id`, `count`) VALUES ('{$id}', 1)");
		}
		header("Location: /");
		die();
	}

	if ($_GET['action'] == 'del') {
		$id = (int)$_GET['id'];
		$cartId = mysqli_query($db, "SELECT * FROM cart where `good_id` = {$id}");
		if (mysqli_fetch_assoc($cartId)['count'] > 1) {
			mysqli_query($db, "UPDATE `cart` SET `count` = `count` - 1 WHERE `cart`.`good_id` = {$id}");
		} else {
			mysqli_query($db, "DELETE FROM `cart` WHERE `cart`.`good_id` = {$id}");
		}
		header("Location: /");
		die();
	}

	$result = mysqli_query($db, "SELECT * FROM catalog");

	$catalog = [];

	while ($row = mysqli_fetch_assoc($result)) {
		$catalog[] = $row;
	}
?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport"
		  content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Document</title>
</head>
<body>
<h2>Каталог</h2>
<?php foreach ($catalog as $item): ?>
	<div>
		<a href="/catalogItem.php?id=<?=$item['id']?>"></a>
		<?= $item['name'] ?><br>
		цена: <?= $item['price'] ?><br>
		<img width="100px" src="img/<?= $item['image'] ?>" alt="<?= $item['image'] ?>"><br>
		<a href="/?action=add&id=<?= $item['id'] ?>">Купить</a>
		<a href="/?action=del&id=<?= $item['id'] ?>">[X]</a><br><br>
	</div>
<?php endforeach; ?>
</body>
</html>
