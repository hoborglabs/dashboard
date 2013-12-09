<?php
namespace Hoborg\DashboardCache\Api;

use Hoborg\DashboardCache\Api;
use Hoborg\DashboardCache\Kernel;
use Hoborg\DashboardCache\iHandler;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class WidgetGet extends Api implements iHandler {

	protected $container = null;

	public function __construct($id) {
		$this->widgetId = $id;
	}

	public function setContainer(Kernel $kernel) {
		$this->container = $kernel;
	}

	public function processHttp(Request $request, Response $response) {
		$mapper = $this->container->getWidgetMapper();
		$configJson = $request->query->get('config', '{}');
		$config = json_decode($configJson, true);

		$widget = $mapper->getById($this->widgetId, $config);

		if (empty($widget)) {
			$this->jsonError("Widget `{$this->widgetId}` not found", 404, $response);
		} else {
			$this->jsonSuccess($widget, $response);
		}

		return $response;
	}

	protected function jsonSuccess(array $data, Response $response) {
		$data['links'] = array(
			"get" => "/api/1/widget/{$this->widgetId}",
			"put" => "/api/1/widget/{$this->widgetId}"
		);

		parent::jsonSuccess($data, $response);
	}
}
