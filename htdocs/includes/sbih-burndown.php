<?php
$w = new stdClass;

$soreFile = '/var/www/stats/data/redmine.skybet.net-p_39-v_84.js';
$stored = json_decode(file_get_contents($soreFile), true);
$stories = array();

// get Version data
// stubbing data for now
$startDate = strtotime('26.09.2011');
$endDate = strtotime('14.10.2011');

// create data for the main red line
$day = 86400; // in sec
$currentDate = $startDate;
$burndownLine = array();
while($currentDate <= $endDate) {
    $burndownLine[date('Y-m-d', $currentDate)] = 0;
    $currentDate += $day;
}
$step = 100/(count($burndownLine) - 1);
$current = 100;
foreach ($burndownLine as $key => $val) {
    $burndownLine[$key] = round($current);
    $current = $current - $step;
}

$issueTypesData = array();
// get stories only
foreach ($stored as $date => $issues) {
    foreach ($issues as $issue) {
        if (isset($issue['tracker_id'])) {
            if (7 == $issue['tracker_id']) {
                $stories[$date][] = $issue;
            }
        } else {
            // backward compatibility
            $stories[$date][] = $issue;
        }

        if (isset($issue['tracker_name'])) {
            if (!isset($issueTypesData[$issue['tracker_name']])) {
                $issueTypesData[$issue['tracker_name']] = array();
            }
        }
    }
}


// create data for burdown
$issuesCount = count(reset($stories));
$done = 0;
$burndownData = array();
foreach ($stories as $date => $issues) {
    $done = 0;
    foreach($issues as $issue) {
        $done += $issue['done_ratio']/$issuesCount;
    }
    $burndownData[$date] = 100 - $done;
}

// expand burndown data
if (count($burndownData) != count($burndownLine)) {
    $c = reset($burndownData);
    foreach ($burndownLine as $key => $value) {
        if (empty($burndownData[$key])) {
            $burndownData[$key] = $c;
        }
        else {
            $c = $burndownData[$key];
        }
    }
}
ksort($burndownData);

$graphData = json_encode(array_values($burndownData));
$graphLine = json_encode(array_values($burndownLine));

$w->getHead = function() use ($graphLine, $graphData) {
$rgraph = '
<html>
<head>
<script src="js/RGraph/libraries/RGraph.common.core.js" ></script>
<script src="js/RGraph/libraries/RGraph.common.context.js" ></script>
<script src="js/RGraph/libraries/RGraph.common.annotate.js" ></script>
<script src="js/RGraph/libraries/RGraph.common.tooltips.js" ></script>
<script src="js/RGraph/libraries/RGraph.common.zoom.js" ></script>
<script src="js/RGraph/libraries/RGraph.common.resizing.js" ></script>
<script src="js/RGraph/libraries/RGraph.line.js" ></script>
<script src="js/RGraph/libraries/RGraph.meter.js" ></script>
';

ob_start();
?>
    var line = <?php echo $graphLine; ?>;
    var data = <?php echo $graphData; ?>;

    var burndownChart = new RGraph.Line("burndown", line, data);
    burndownChart.Set('chart.title', 'Sprint 15 Burndown');
    burndownChart.Set('chart.key', ['', 'Dev']);

    burndownChart.Set('chart.linewidth', 4);
    burndownChart.Set('chart.shadow', true);
    burndownChart.Set('chart.shadow.color', 'black');
    burndownChart.Set('chart.shadow.offsetx', 0);
    burndownChart.Set('chart.shadow.offsety', 0);
    burndownChart.Set('chart.shadow.blur', 10);
    burndownChart.Set('chart.rounded', true);

    burndownChart.Draw();
<?php
$head = ob_get_clean();

return array(
    'onceOnly' => array('RGraph' => $rgraph),
    'onLoad' => array('sbih-burndonw' => $head),
);
}; 

$w->getBody = function() use ($done) {
ob_start();
?>
<h3>Burdown (<?php echo number_format($done, 0); ?> / 100 %)</h3>
<canvas id="burndown" width="475" height="250">[Please wait...]</canvas>
<?php 
$body = ob_get_clean();
return $body;
};

return $w;
