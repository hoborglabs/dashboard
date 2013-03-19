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

	public function process(Request $request, Response $response) {
		$mapper = $this->container->getWidgetMapper();
		$key = $request->query->get('key', null);

		$widget = $mapper->getById($this->widgetId, $key);

		if (empty($widget)) {
			$this->jsonError('Widget not found', $response);
		} else {
			$this->jsonSuccess($widget, $response);
		}

		return $response;
	}

	protected function jsonSuccess(array $data, Response $response) {
		$data['data_url'] = "/api/1/widget/{$data['id']}/data";

		parent::jsonSuccess($data, $response);
	}
}
