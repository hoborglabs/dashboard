<?php
namespace Hoborg\Dashboard\Client;

class Graphite {

	public function getAvgTargetValue($target, $graphiteUrl, array $options, $nullAsZero = false) {
		$url = $graphiteUrl . "/render?target={$target}";
		foreach ($options as $optName => $value) {
			$url .= "&{$optName}={$value}";
		}
		$data = $this->getJsonData($url);
		$avg = array();

		foreach ($data[0]['datapoints'] as $p) {
			$s = $p[0];
			if (null === $s) {
				if ($nullAsZero) {
					$s = 0;
				} else {
					continue;
				}
			}
			$avg[] = $s;
		}

		return array_sum($avg) / count($avg);
	}

	public function getTargetsData($graphiteUrl, array $targets, $from = '-5min', $to = 'now') {
		$url = $graphiteUrl . "/render?from={$from}&to={$to}&format=json&target=" . implode('&target=', $targets);
		$jsonData = file_get_contents($url);
		$data = json_decode($jsonData, true);

		if (empty($data)) {
			return array();
		}

		return $data;
	}

	public function getTargetsStatisticalData($graphiteUrl, array $targets, $from = '-15min', $to = 'now') {
		$data = $this->getTargetsData($graphiteUrl, $targets, $from, $to);
		$statisticalData = array();

		foreach ($data as $target) {
			$min = $max = $avg = 0;
			$avgSize = min(6, count($target['datapoints']));
			$avgIndex = count($target['datapoints']) - $avgSize;
			foreach ($target['datapoints'] as $i => $p) {
				if (null == $p[0]) {
					continue;
				}
				$min = min($min, $p[0]);
				$max = max($max, $p[0]);
				if ($i >= $avgIndex) {
					$avg += $p[0];
				}
			}
			$avg = $avg / $avgSize;

			$statisticalData[] = array(
					'target' => $target['target'],
					'min' => $min,
					'max' => $max,
					'avg' => $avg,
			);
		}

		return $statisticalData;
	}

	public function getJsonData($url) {
		$jsonData = file_get_contents($url . '&format=json');
		$data = json_decode($jsonData, true);

		if (empty($data)) {
			return array();
		}

		return $data;
	}

}
