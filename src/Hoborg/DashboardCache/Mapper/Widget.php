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

	public function getById($id, $config = array()) {

		$configHash = md5(json_encode($config));
		$widget = $this->adapter->from('widget')
			->by('id', $id)
			->by('configHash', $configHash)
			->fetch();

		if (!empty($widget['data'])) {
			$widget['data'] = json_decode($widget['data']);
		}

		return $widget;
	}

	public function updateOrInstertById($id, array $config = array(), $data) {
		$configHash = md5(json_encode($config));
		$widget = $this->adapter->from('widget')
			->by('id', $id)
			->by('configHash', $configHash)
			->update(array('data' => json_encode($data), 'timestamp' => time()));
		$widget['data'] = json_decode($widget['data']);

		return $widget;
	}
}
