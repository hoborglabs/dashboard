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
		return $this->adapter->fetchRow('SELECT * FROM ');
	}
}
