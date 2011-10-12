<?php
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

foreach ($stored as $date => $issues) {
    foreach (array_keys($issueTypesData) as $key) {
        $issueTypesData[$key][$date] = 0;
    }
    foreach ($issues as $issue) {
        if (isset($issue['tracker_name'])) {
            if (!isset($issueTypesData[$issue['tracker_name']][$date])) {
                $issueTypesData[$issue['tracker_name']][$date] = 0;
            }
            $issueTypesData[$issue['tracker_name']][$date]++;
        } else {
            $issueTypesData['Story'][$date]++;
        }
    }
}
foreach ($issueTypesData as $type => $data) {
    $issueTypesData[$type] = array_values($data);
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
$graphIssueTypes = json_encode($issueTypesData);
?>
<html>
<head>
<script src="RGraph/libraries/RGraph.common.core.js" ></script>
<script src="RGraph/libraries/RGraph.common.context.js" ></script>
<script src="RGraph/libraries/RGraph.common.annotate.js" ></script>
<script src="RGraph/libraries/RGraph.common.tooltips.js" ></script>
<script src="RGraph/libraries/RGraph.common.zoom.js" ></script>
<script src="RGraph/libraries/RGraph.common.resizing.js" ></script>
<script src="RGraph/libraries/RGraph.line.js" ></script>
<script src="RGraph/libraries/RGraph.meter.js" ></script>

<script type="text/javascript">
window.onload = function () {
    var line = <?php echo $graphLine; ?>;
    var data = <?php echo $graphData; ?>;
    var issueTypes = <?php echo $graphIssueTypes; ?>;

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

    var progressChart = new RGraph.Meter("progress", 0, 100, <?php echo $done; ?>);
    var beginDate = new Date(2011, 8, 26);
    var endDate = new Date(2011, 9, 14);
    var l = endDate.getTime() - beginDate.getTime();
    var nowDate = new Date();
    var inSprint = nowDate.getTime() - beginDate.getTime();
    var greenDelta = 7;
    var yellowDelta = 20;

    var yellowStart = (inSprint/l*100 - yellowDelta) > 100 ? 100 : ((inSprint/l*100 - yellowDelta) < 0) ? 0 : (inSprint/l*100 - yellowDelta);
	var greenStart = (inSprint/l*100 - greenDelta) > 100 ? 100 : (inSprint/l*100 - greenDelta) < 0 ? 0 : (inSprint/l*100 - greenDelta);

    progressChart.Set('chart.green.start', 0); // green is red
    progressChart.Set('chart.green.end', 100);
    progressChart.Set('chart.green.color', '#9E1E1E');
    progressChart.Set('chart.yellow.start', yellowStart );
    progressChart.Set('chart.yellow.end', (inSprint/l*100 + yellowDelta) > 100 ? 100 : (inSprint/l*100 + yellowDelta) );
    progressChart.Set('chart.red.start', greenStart ); // red is green
    progressChart.Set('chart.red.end', (inSprint/l*100 + greenDelta) > 100 ? 100 : (inSprint/l*100 + greenDelta) );
    progressChart.Set('chart.red.color', '#207A20');

    progressChart.Set('chart.title', 'Sprint 15 Progress');
    progressChart.Set('chart.units.post', '%');
    progressChart.Set('chart.shadow', true);
    progressChart.Set('chart.shadow.color', 'black');
    progressChart.Set('chart.shadow.offsetx', 0);
    progressChart.Set('chart.shadow.offsety', 0);
    progressChart.Set('chart.shadow.blur', 20);


    issueTypesChart = new RGraph.Line("issueTypes");
    var keys = [];
    for (var i in issueTypes) {
        issueTypesChart.original_data.push(issueTypes[i]);
        keys.push(i);
    }
    issueTypesChart.Set('chart.key', keys);

    issueTypesChart.Set('chart.filled', true);
    issueTypesChart.Set('chart.tickmarks', null);
    issueTypesChart.Set('chart.background.barcolor1', 'white');
    issueTypesChart.Set('chart.background.barcolor2', 'white');
    issueTypesChart.Set('chart.background.grid.autofit', true);
    issueTypesChart.Set('chart.colors', ['rgba(169, 222, 244, 0.7)', 'red', '#ff0', '#000']);
    issueTypesChart.Set('chart.fillstyle', ['#daf1fa', '#faa', '#ffa', '#ccc']);
    issueTypesChart.Set('chart.yaxispos', 'right');
    issueTypesChart.Set('chart.linewidth', 5);

    burndownChart.Draw();
    progressChart.Draw();
    issueTypesChart.Draw();
};
</script>

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
    <div class="widget">
        <h3>Burdown</h3>
        <canvas id="burndown" width="475" height="250">[Please wait...]</canvas>
    </div>

    <div class="widget">
        <h3>Progress Meter</h3>
        <canvas id="progress" width="475" height="250">[Please wait...]</canvas>
    </div>

    <div class="widget">
        <h3>Issue Types</h3>
        <canvas id="issueTypes" width="475" height="250">[Please wait...]</canvas>
    </div>
</body>
</html>
