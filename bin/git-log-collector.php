<?php
include_once __DIR__ . '/../src/command_line.php';

$git = 'git';
$dir = '/home/woledzki/projects/skybet_dev-sportsbook';
$dataDir = __DIR__ . '/../data/';

chdir($dir);
$cmd = "{$git} log -100 origin/dev-sportsbook";
$log = '';

exec($cmd, $log);

$commits = parse_log($log);
$commiters = get_commiters($commits);

var_dump($commiters);

file_put_contents($dataDir . '/git-logs.js', json_encode($commiters));


function parse_log(array $logLines) {
    $commits = array();
    $state = '-';

    $commit = '#^\s*commit (.*)$#';
    $author = '#^\s*Author: ([^<]*) <([^>]*)>#';
    $match = array();
    $commitHash = null;

    foreach ($logLines as $line) {
        if (preg_match($commit, $line, $match)) {
            $state = 'C';
            $commitHash = $match[1];
        }

        if ('C' == $state) {
            if (preg_match($author, $line, $match)) {
                $email = $match[2];
                $name = $match[1];
                $commits[$commitHash] = array(
                    'name' => $name,
                    'email' => $email,
                    'commit' => $commitHash,
                );

                $state = '-';
            }

        }
    }

    return $commits;
}

function get_commiters(array $commits) {
    $commiters = array();

    foreach ($commits as $commit) {
        if (!isset($commiters[$commit['email']])) {
            $commiters[$commit['email']] = array(
                'email' => $commit['email'],
                'name' => $commit['name'],
                'count' => 0,
            );
        }

        $commiters[$commit['email']]['count']++;
    }

    uasort($commiters, function($a, $b) {return $b['count'] - $a['count']; });

    return $commiters;
}