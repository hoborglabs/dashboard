<?php
namespace Hoborg\DashboardCache\Mapper;

use Hoborg\DashboardCache\Adapter\iAdapter;

/**
 * Access widget data
 *
 */
class Data {

	/**
	 * @var iAdapter
	 */
	protected $adapter = null;

	public function __construct(iAdapter $adapter) {
		$this->adapter = $adapter;
	}
}
