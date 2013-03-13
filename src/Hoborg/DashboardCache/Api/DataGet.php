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
		$mapper = $this->container->getDataMapper();
		$data = $mapper->getByWidgetId($this->widgetId, $request->query->get('from', '-30min'));

		$this->jsonSuccess($data, $response);

		return $response;
	}

	protected function jsonSuccess(array $data, Response $response) {
		foreach ($data as &$row) {
			unset($row['widget_id']);
			$row['data'] = json_decode($row['json']);
			unset($row['json']);
		}

		parent::jsonSuccess(array(
			'widget_url' => '/api/1/widget/' . $this->widgetId,
			'datapoints' => $data
		), $response);
	}
}
