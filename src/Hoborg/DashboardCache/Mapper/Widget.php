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

	public function getById($id) {
		$sql = 'SELECT * FROM widget WHERE id = ' . $this->adapter->quote($id);
		return $this->adapter->fetchRow($sql);
	}
}
