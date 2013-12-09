<?php
namespace Hoborg\DashboardCache\Adapter;

class File implements iAdapter {

	protected $query = array();

	protected $prefix = '/tmp';

	public function __construct($storageFolder) {
	    if (!is_readable($storageFolder)) {
			throw new Error("Folder {$storageFolder} is not readable.");
	    }

		$this->prefix = $storageFolder;
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

		$filePath = $this->getFilePath();
		$this->reset();
		return require($filePath);
	}

	public function update(array $data) {

		if (empty($this->query['by'])) {
			throw new Error('Missing `by` data. Please us ->by($key, $value)');
		}
		if (empty($data)) {
			return true;
		}

		$filePath = $this->getFilePath();

		// get current data from APC and merge with new one
		$newData = require($filePath);
		if (empty($newData)) {
			$newData = $data;
		} else {
			$newData = $data + $newData;
		}
		file_put_contents($filePath, '<php return ' . var_export($newData, true) . '; ?>');

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

	public function getFilePath() {
	    return $this->prefix . $this->query['table'] . '_' . implode(
	        '_',
	        array_map(function($a) { return "{$a[0]}.{$a[1]}"; }, $this->query['by'])
	    ) . '.php';
	}
}