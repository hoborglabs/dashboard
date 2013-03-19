<?php
namespace Hoborg\DashboardCache\Mapper;

use Hoborg\DashboardCache\Adapter\iAdapter;

/**
 * Access widgets list
 *
 */
class Widget {

	/**
	 * @var Hoborg\DashboardCache\Adapter\iAdapter
	 */
	protected $adapter = null;

	public function __construct(iAdapter $adapter) {
		$this->adapter = $adapter;
	}

	public function getById($id, $key = null) {

		$sql = 'SELECT * FROM widget '
				. 'WHERE id = ' . $this->adapter->quote($id);

		if (null == $key) {
			$sql .= ' AND api_key IS NULL';
		} else {
			$sql .= ' AND api_key = ' . $this->adapter->quote($key);
		}
		return $this->adapter->fetchRow($sql);
	}
}
