<?php
namespace Hoborg\DashboardCache\Api;

use Hoborg\DashboardCache\Api;
use Hoborg\DashboardCache\Kernel;
use Hoborg\DashboardCache\iHandler;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class DataGet extends Api implements iHandler {

	protected $container = null;

	protected $widgetId = null;

	public function __construct($id) {
		$this->widgetId = $id;
	}

	public function setContainer(Kernel $kernel) {
		$this->container = $kernel;
	}

	public function process(Request $request, Response $response) {
		$dataMapper = $this->container->getDataMapper();
		$widgetMapper = $this->container->getWidgetMapper();
		$key = $request->query->get('key', null);

		$widget = $widgetMapper->getById($this->widgetId, $key);
		if (empty($widget)) {
			$this->jsonError('Widget not found', 404, $response);
			return $response;
		}
		$data = array(
			$widget,
			$dataMapper->getByWidget($widget, $request->query->get('from', '-30min')),
		);

		$this->jsonSuccess($data, $response);

		return $response;
	}

	protected function jsonSuccess(array $data, Response $response) {
		foreach ($data[1] as &$row) {
			unset($row['widget_id']);
			$row['data'] = json_decode($row['json']);
			unset($row['json']);
		}

		parent::jsonSuccess(array(
			'widget_url' => '/api/1/widget/' . $this->widgetId,
			'datapoints' => $data[1],
			'widget' => $data[0],
		), $response);
	}
}
