<?php
namespace Hoborg\DashboardCache\Api;

use Hoborg\DashboardCache\Kernel;

use Hoborg\DashboardCache\iHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class WidgetGet implements iHandler {

	protected $container = null;

	public function __construct($id) {
		$this->widgetId = $id;
	}

	public function setContainer(Kernel $kernel) {
		$this->container = $kernel;
	}

	public function process(Request $request, Response $response) {
		$mapper = $this->container->getWidgetMapper();
		$widget = $mapper->getById($this->widgetId);

		// json_encode($widget);
		$response->setContent('{"id": ' . $this->widgetId . '}');
	}
}
