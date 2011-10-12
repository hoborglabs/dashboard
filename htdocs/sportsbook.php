<?php
$widgets = array(
    array(
        'php' => 'includes/sbih-progress.php',
    ),
    array(
        'php' => 'includes/sbih-burndown.php',
    ),
//    array(
//        'php' => 'includes/sbih-issues.php',
//    ),
    array(
        'url' => 'includes/sbih-depend.html',
    ),
    array(
        'url' => 'includes/sbih-pyramid.html',
    )
);

$data = array();
foreach ($widgets as $widget) {
    $data[] = display_widget($widget);
}

function display_widget($widgetConfig) {
    $body = '';
    $head = array();
    if (!empty($widgetConfig['url'])) {
        $body = '<div class="widget">' .
            file_get_contents($widgetConfig['url']) . 
            '</div>';
    }

    if (!empty($widgetConfig['php'])) {
        $w = include($widgetConfig['php']);
        $body = $w->getBody;
        $body = '<div class="widget">' . $body() . '</div>';
        
        $head = $w->getHead;
        $head = $head();
    }

    return array('body' => $body, 'head' => $head);
}

$head = '';
$onceOnly = array();
$onLoad = array();
foreach ($data as $widget) {
    foreach ($widget['head'] as $key => $values) {
        if ('onceOnly' === $key) {
            foreach ($values as $k => $v) { $onceOnly[$k] = $v; }
        }
        if ('onLoad' === $key) {
            foreach ($values as $k => $v) { $onLoad[$k] = $v; }
        }
    }
}
$head .= join("\n", $onceOnly);

foreach ($data as $widget) {
    foreach ($widget['head'] as $key => $values) {
        if ('always' === $key) {
            foreach ($values as $k => $v) { 
                $head .= "\n" . $v; 
            }
        }
    }
}

$head .= '<script type="text/javascript">
window.onload = function () {' . join("\n\n", $onLoad) . '};</script>';

?>

<html>
<head>
    <?php echo $head; ?> 

    <style type="text/css">
.widget {
width: 500px;
float: left;
margin: 0px 10px;
}

.widget h3 {
border: 2px #DDD solid;
border-width: 0px 0px 2px 0px;
margin: 0px 0px 20px 0px;
padding: 6px 0px 10px 0px;
}
    </style>
</head>
<body>

<?php 
foreach ($data as $display) {
    echo $display['body'];
}
?> 

</body>
