<?php
namespace Hoborg\Dashboard\Client\Graphite;

use Hoborg\Dashboard\Client\Graphite;

class Target {

	protected $graphite;

	protected $target;

	public function __construct(Graphite $graphite, $target, array $functions, array $options) {
		$this->target = $target;
		$this->graphite = $graphite;
		$this->functions = $functions;
		$this->options = $options;
	}

	public function fcn($functionName, $functionParam) {
		$this->functions[$functionName] = $functionParam;

		return $this;
	}

	public function addFunction($functionName, $functionParam) {
		return $this->fcn($functionName, $functionParam);
	}

	public function data() {
		return $this->getData();
	}

	public function avg() {
		$nullAsZero = false;
		$data = $this->getData();
		if (empty($data) || empty($data[0]['datapoints'])) {
			return null;
		}

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

	public function stats() {
		$data = $this->getData();
		if (empty($data) || empty($data[0]['datapoints'])) {
			return array('min' => null, 'max' => null, 'avg' => null);
		}

		$min = PHP_INT_MAX;
		$max = -$min;
		$avg = array();
		$nullAsZero = false;
		foreach ($data[0]['datapoints'] as $p) {
			$s = $p[0];
			if (null === $s) {
				if ($nullAsZero) {
					$s = 0;
				} else {
					continue;
				}
			}
			$min = min($min, $p[0]);
			$max = max($max, $p[0]);
			$avg[] = $s;
		}


		return array('min' => $min, 'max' => $max, 'avg' => array_sum($avg) / count($avg));
	}

	protected function getData() {
		return $this->graphite->getData(
			$this->target,
			$this->functions,
			$this->options
		);
	}
}
