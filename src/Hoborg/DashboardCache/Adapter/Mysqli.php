<?php
namespace Hoborg\DashboardCache\Adapter;

class Mysqli implements iAdapter {

	const QUOTE_MIXED = 1;
	const QUOTE_SQL = 2;

	/**
	 * @var \mysqli
	 */
	protected $connection = null;

	protected $query = array();

	public function __construct($host, $username, $password, $dbname, $port = 3306) {
		$this->connection = new \mysqli($host, $username, $password, $dbname, $port);

		if ($this->connection->connect_error) {
			throw new Error('Mysql connection error code: ' . $this->connection->connect_error);
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
		$sql = 'SELECT * FROM ' . $this->query['table'];
		if (!empty($this->query['by'])) {
			$sql .= ' WHERE ' . implode(' AND ',
					array_map(function($a) { return "{$a[0]} = {$a[1]}"; }, $this->query['by']));
		}

		$result = $this->connection->query($sql);
		$this->reset();
		return $result->fetch_assoc();
	}

	public function update(array $data) {
		$sql = 'UPDATE ' . $this->query['table'];

		if (empty($this->query['by'])) {
			throw new Error('Missing `by` data. Please us ->by($key, $value)');
		}
		if (empty($data)) {
			return true;
		}

		$set = array();
		foreach ($data as $field => $value) {
			$set[] = array($this->quote($field, self::QUOTE_SQL), $this->quote($value));
		}
		$sql .= ' SET ' . implode(', ',
				array_map(function($a) { return "{$a[0]} = {$a[1]}"; },
				$set));

		$sql .= ' WHERE ' . implode(' AND ',
				array_map(function($a) { return "{$a[0]} = {$a[1]}"; },
				$this->query['by']));

		return $this->connection->query($sql);
	}

	protected function reset() {
		$this->query = array(
			'table' => null,
			'by' => array(),
		);
	}



	public function quote($input, $format = self::QUOTE_MIXED) {
		$escaped = $this->connection->real_escape_string($input);

		if (self::QUOTE_SQL == $format) {
			return "`{$escaped}`";
		}

		if (is_numeric($escaped)) {
			return $escaped;
		} else {
			return "\"{$escaped}\"";
		}
	}

	public function query($sql) {
		$result = $this->connection->query($sql);

		return $result->fetch_assoc();
	}

	public function fetchRow($sql) {
		$result = $this->connection->query($sql);

		return $result->fetch_assoc();
	}

	public function fetchAll($sql) {
		$result = $this->connection->query($sql);

		return $result->fetch_all(MYSQLI_ASSOC);
	}

	public function getConnection() {
		$this->connect();
		return $this->connection;
	}

}