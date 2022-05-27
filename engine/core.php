<?php

function render($page, $params) {
	return renderViews(LAYOTS_DIR . $params['layout'], [
		'content' => renderViews($page, $params),
		'title' => $params['title'],
	]);
}

function renderViews($page, $params) {
	foreach ($params as $key => $value) {
		$$key = $value;
	}
	ob_start();
	include VIEWS_DIR . $page . '.php';
	return ob_get_clean();
}
