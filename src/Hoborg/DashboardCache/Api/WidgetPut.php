<?php
namespace Hoborg\DashboardCache\Api;

use Hoborg\DashboardCache\Api;
use Hoborg\DashboardCache\Kernel;
use Hoborg\DashboardCache\iHandler;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class WidgetPut extends Api implements iHandler {

	protected $container = null;

	protected $widgetId = null;

	public function __construct($id) {
		$this->widgetId = $id;
	}

	/**
	 * @see Hoborg\DashboardCache.iHandler::setContainer()
	 */
	public function setContainer(Kernel $kernel) {
		$this->container = $kernel;
	}

	/**
	 * @see Hoborg\DashboardCache.iHandler::processHttp()
	 */
	public function processHttp(Request $request, Response $response) {
		$widgetMapper = $this->container->getWidgetMapper();
		$configJson = $request->query->get('config', '{}');
		$config = json_decode($configJson, true);

		$data = json_decode($request->getContent(), true);
		if (empty($data)) {
			$this->jsonError('Invalid JSON', 500, $response);
			return $response;
		}

		$widget = $widgetMapper->updateOrInstertById($this->widgetId, $config, $data);

		$this->jsonSuccess($widget, $response);
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
