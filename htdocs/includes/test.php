<?php
$widget['body'] = function() use (& $widget) {
	return 'Hello ' . $widget['name'] . '!';
};

return $widget;