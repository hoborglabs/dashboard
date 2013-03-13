<?php
namespace Hoborg\DashboardCache\Adapter;

class Mysqli implements iAdapter {

	/**
	 * @var \mysqli
	 */
	protected $connection = null;

	public function __construct($host, $username, $password, $dbname, $port = 3306) {
		$this->connection = new \mysqli($host, $username, $password, $dbname, $port);

		if ($this->connection->connect_error) {
			throw new AdapterError('Mysql connection error code: ' . $this->connection->connect_error);
		}
	}

	public function quote($input) {
		return $this->connection->real_escape_string($input);
	}

	public function query($sql) {
		$result = $this->connection->query($sql);

		return $result->fetch_assoc();
	}

	public function fetchRow($sql) {
		$result = $this->connection->query($sql);

		return $result->fetch_row();
	}

	public function fetchAll($sql) {
		$result = $this->connection->query($sql);

		return $result->fetch_assoc();
	}

	public function getConnection() {
		$this->connect();
		return $this->connection;
	}

}