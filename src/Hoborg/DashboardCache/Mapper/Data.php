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

	public function getByWidget(array $widget, $from = '30min', $until = 'now') {
		$fromDate = new \DateTime();
		$fromDate->modify($from);

		$sql = 'SELECT json, `numeric`, unix_timestamp(timestamp) AS timestamp FROM data_hot '
				.'WHERE widget_id = ' . $this->adapter->quote($widget['id'])
				. ' AND timestamp > ' . $this->adapter->quote($fromDate->format('Y-m-d H:i:s'));

		return $this->adapter->fetchAll($sql);
	}
}
