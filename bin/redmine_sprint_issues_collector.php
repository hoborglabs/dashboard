<?php
error_reporting(0);

// options:
// h    help
// u    URL
// k    API key
// p    project id
// v    version id
// t    tracker id or "*" for all (default *)
// s    status id or "*" for all (default *)
// l    limit number (default 100)
// o    output folder (default data/)
$opt = getopt('hu:k:p:v:t:s:l:o:q:');

// show help
if (isset($opt['h'])) {
    outputHelp();
    exit();
}

// default values
$defaults = array(
    't' => '*',
    's' => '*',
    'l' => 100,
    'o' => 'data/',
);

$key = isset($opt['k']) ? $opt['k'] : null;
$redmineUrl = isset($opt['u']) ? $opt['u'] : null;
$domain = preg_replace('/https?:\/\/(.*)/', '$1', $redmineUrl);
$dataFolder = isset($opt['o']) ? $opt['o'] : $defaults['o'];
// redmine API options
$options = array(
    'query_id' => isset($opt['q']) ? $opt['q'] : null,
    'project_id' => isset($opt['p']) ? $opt['p'] : null,
    'fixed_version_id' => isset($opt['v']) ? $opt['v'] : null,
    'tracker_id' => isset($opt['t']) ? $opt['t'] : $defaults['t'],
    'status_id' => isset($opt['s']) ? $opt['s'] : $defaults['s'],
    'limit' => isset($opt['l']) ? $opt['l'] : $defaults['l'],
);

// show help end exit if required field are not set
if (null === $options['project_id'] || null === $options['fixed_version_id'] || null === $redmineUrl || null === $key) {
    outputHelp();
    exit();
}

$date = date('Y-m-d');
$storeFile = $dataFolder . $domain . '-p_' . $options['project_id'] . '-v_' . $options['fixed_version_id'] . '.js';
// let's make sure that file exist.
fclose(fopen($storeFile, 'a'));
$stored = json_decode(file_get_contents($storeFile), true);

// get issues from redmine
$url = $redmineUrl . "/issues.xml?key=$key";
foreach ($options as $param => $value) {
    $url .= '&' . $param . '=' . urlencode($value);
}

$issuesData = callApi($url,
    array(
        'id' => true,
        'done_ratio' => true,
        'tracker' => array('name', 'id'),
        'priority' => array('name', 'id'),
        'status' => array('name', 'id'),
        'parent' => array('id'),
    ),
    array(
        6 => 'initial_dev_estimate',
        8 => 'initial_des_estimate',
        13 => 'initial_tst_estimate',
        11 => 'remaining_dev_estimate',
        12 => 'remaining_des_estimate',
        14 => 'remaining_tst_estimate',
        17 => 'story_points',
    )
);

$stored[$date] = $issuesData;
file_put_contents($storeFile, json_encode($stored));

/**
 * Outputs Help for this script.
 */
function outputHelp() {
    global $defaults;
    echo "
Usage: redmine-issues-collector -p 39 -v 57
options:
 -h                 help
 -u URL             redmine URL (required)
 -k API_KEY         redmine API KEY (required)
 -p PROJECT_ID      project id (required)
 -v VERSION_ID      version id (required)
 -t TRACKER_ID      tracker id or \"*\" for all (default " . $defaults['t'] .")
 -s STATUS_ID       status id or \"*\" for all (default " . $defaults['s'] .")
 -l ISSUES_LIMIT    limit number (default " . $defaults['l'] .")
 -o PATH            path to output folder
";
}
function outputError($message) {
    echo "  [ERROR] $message\n";
}

function callApi($url, array $save, array $customFields) {
    var_dump($url);
    $data = file_get_contents($url);
    $issuesData = array();

    if (empty($data)) {
        outputError('No data returned from: ' . $redmineUrl);
        outputError('Full request: ' . $url);
        return $issuesData;
    }

    // save issues data
    $d = new SimpleXMLElement($data);
    foreach($d->issue as $issue) {
        $issueCopy = array();


        foreach ($save as $key => $opt) {
            if (is_array($opt)) {
                if (isset($issue->$key)) {
                    foreach ($issue->$key->attributes() as $attr => $val) {
                        if (in_array($attr, $opt)) {
                            $issueCopy[$key.'_'.$attr] = (string) $val;
                        }
                    }
                }
            }
            else {
                $issueCopy[$key] = (string) $issue->$key;
            }
        }

        foreach ($issue->custom_fields->custom_field as $customField) {
            foreach($customField->attributes() as $attr => $val) {
                if ('id' === $attr) {
                    $val = (string) $val;
                    if (isset($customFields[$val])) {
                        $issueCopy[$customFields[$val]] = (string)$customField->value;
                    }
                }
            }
        }

        $issuesData[] = $issueCopy;
    }

    $t = (int)$d['total_count'];
    $o = (int)$d['offset'];
    $l = (int)$d['limit'];
    if ($t > $o + $l) {
        $url .= '&offset=' . ($o + $l);
        $issuesData = array_merge($issuesData, callApi($url, $save, $customFields));
    }

    return $issuesData;
}
