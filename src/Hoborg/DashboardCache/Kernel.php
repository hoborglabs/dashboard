<?php
namespace Hoborg\DashboardCache;

use Hoborg\DashboardCache\Adapter\Mysqli;

use Hoborg\DashboardCache\Mapper\Widget;

use Hoborg\DashboardCache\Router\ApiWidget;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * HTTP interface for saving and sending data.
 *
 */
class Kernel {

	protected $widgetMapper = null;

	protected $dbAdapter = null;

	protected $properties = array();

	public function __construct($propertiesFile) {
		if (is_readable($propertiesFile)) {
			$this->properties = parse_ini_file($propertiesFile);
		}
	}

	public function handle(Request $request, Response $response) {
		$url = $request->getPathInfo();
		$handler = $this->getHandler($url);

		if (null == $handler) {
			$response->setStatusCode(404);
			$response->setContent('Doh, wrong url.');
			return $response;
		}

		$handler->setContainer($this);
		$handler->process($request, $response);
		return $response;
	}

	protected function getHandler($url, $method = 'GET') {
		$urlParts = explode('/', $url);
		if (empty($urlParts[0])) {
			array_shift($urlParts);
		}

		if (isset($urlParts[0]) && isset($urlParts[1])) {
			if ('api' == $urlParts[0] && '1' == $urlParts[1]) {
				if (isset($urlParts[2])) {
					if ('widget' == $urlParts[2]) {
						$widgetRouter = new ApiWidget();
						return $widgetRouter->getHandler(implode('/', array_splice($urlParts, 3)), $method);
					} else if ('data' == $urlParts[2]) {

					}
				}
			}
		}

		return null;
	}

	public function getWidgetMapper() {
		if (null != $this->widgetMapper) {
			return $this->widgetMapper;
		}

		$this->widgetMapper = new Widget($this->getDbAdapter());
		return $this->widgetMapper;
	}

	protected function getDbAdapter() {
		if (null != $this->dbAdapter) {
			return $this->dbAdapter;
		}

		return $this->dbAdapter = new Mysqli(
			$this->properties['db.host'],
			$this->properties['db.username'],
			$this->properties['db.password'],
			$this->properties['db.database']
		);
	}
}