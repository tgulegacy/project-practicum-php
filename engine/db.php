<?php

function getDb() {
	static $db = null;

	if (is_null($db)) {
		$db = @mysqli_connect('localhost:3306', 'root', 'root', 'php_practic') or die('Could not connect' . mysqli_connect_error());
	}

	return $db;
}

function closeDb() {
	mysqli_close(getDb());
}

function getAssocResult($sql) {
	$result = @mysqli_query(getDb(), $sql) or die(mysqli_error(getDb()));

	$array_result = [];

	while ($row = mysqli_fetch_assoc($result)) {
		$array_result[] = $row;
	}

	return $array_result;
}

function getOneResult($sql) {
	$result = @mysqli_query(getDb(), $sql) or die(mysqli_error(getDb()));
	return mysqli_fetch_assoc($result);
}

function executeSql($sql) {
	return @mysqli_query(getDb(), $sql) or die(mysqli_error(getDb()));
}
