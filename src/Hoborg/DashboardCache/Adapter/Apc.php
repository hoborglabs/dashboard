<?php
namespace Hoborg\DashboardCache\Adapter;

class Apc implements iAdapter {

	const QUOTE_MIXED = 1;
	const QUOTE_SQL = 2;

	/**
	 * @var \apc
	 */
	protected $connection = null;

	protected $query = array();

	protected $prefix = 'DashboardCache_';

	public function __construct() {
		// check for APC
		if (!extension_loaded("APC")) {
			throw new Error("APC extension not loaded.");
		}
	}

	public function from($table) {
		$this->reset();
		$this->query['table'] = $table;
		return $this;
	}

	public function by($field, $value) {
		$this->query['by'][] = array($field, $this->quote($value));
		return $this;
	}

	public function fetch() {
		if (empty($this->query['by'])){
			throw new Error('empty `by`.');
		}

		$key = $this->prefix . $this->query['table']
			. '_' . implode('_', array_map(function($a) { return "{$a[0]}.{$a[1]}"; }, $this->query['by']));

		$this->reset();
		return apc_fetch($key);
	}

	public function update(array $data) {

		if (empty($this->query['by'])) {
			throw new Error('Missing `by` data. Please us ->by($key, $value)');
		}
		if (empty($data)) {
			return true;
		}

		$key = $this->prefix . $this->query['table']
			. '_' . implode('_', array_map(function($a) { return "{$a[0]}.{$a[1]}"; }, $this->query['by']));

		// get current data from APC and merge with new one
		$newData = apc_fetch($key);
		if (empty($newData)) {
			$newData = $data;
		} else {
			$newData = $data + $newData;
		}
		$result = apc_store($key, $newData);

		return $newData;
	}

	protected function reset() {
		$this->query = array(
			'table' => null,
			'by' => array(),
		);
	}

	/**
	 * @see Hoborg\DashboardCache\Adapter.iAdapter::quote()
	 */
	public function quote($input, $format = self::QUOTE_MIXED) {
		// escape for APC key
		return $input;
	}
}