<?php
namespace Hoborg\DashboardCache;

use Hoborg\DashboardCache\Api\WidgetPut;
use Hoborg\DashboardCache\Api\WidgetGet;

use Hoborg\DashboardCache\Adapter\Apc;
use Hoborg\DashboardCache\Adapter\Mysqli;
use Hoborg\DashboardCache\Mapper\Widget;
use Hoborg\DashboardCache\Mapper\Data;
use Hoborg\DashboardCache\Router\ApiWidget;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * HTTP interface for saving and sending data.
 *
 */
class Kernel {

	protected $widgetMapper = null;

	protected $dataMapper = null;

	protected $dbAdapter = null;

	protected $properties = array();

	public function __construct($propertiesFile) {
		if (is_readable($propertiesFile)) {
			$this->properties = parse_ini_file($propertiesFile);
		}
	}

	public function handle(Request $request, Response $response) {

		// get handler for given requst
		$handler = $this->getHandler($request);

		if (null == $handler) {
			$response->setStatusCode(404);
			$response->setContent('Doh, wrong url.');
			return $response;
		}

		// something for simple DI
		$handler->setContainer($this);

		$handler->processHttp($request, $response);
		return $response;
	}

	/**
	 *
	 * @param Request $request
	 *
	 * @return iHandler
	 */
	protected function getHandler(Request $request) {
		$url = $request->getPathInfo();
		$method = $request->getMethod();

		$urlParts = explode('/', $url);
		if (empty($urlParts[0])) {
			array_shift($urlParts);
		}

		// API v1 /api/1/...
		if (isset($urlParts[0]) && isset($urlParts[1])) {
			if ('api' == $urlParts[0] && '1' == $urlParts[1]) {
				// /api/1/widget/...
				if (isset($urlParts[2])) {
					if ('widget' == $urlParts[2]) {
						return $this->getWidgetHandler($request, '/api/1/widget');
					}
				}
			}
		}

		return null;
	}

	/**
	 * Get widget handler.
	 *
	 * /api/1/widget/widget:unique:id?config={...}
	 *
	 * @param Request $request
	 * @param string $prefixUrl
	 *
	 * @return iHandler
	 */
	protected function getWidgetHandler(Request $request, $prefixUrl = '') {
		$url = $request->getPathInfo();
		$method = $request->getMethod();
		$urlParts = explode('/', preg_replace('/^' . preg_quote($prefixUrl, '/') . '\/?(.*)/', '$1', $url));

		if (!isset($urlParts[0])) {
			return null;
		}

		$widgetid = $urlParts[0];

		if (1 == count($urlParts)) {
			if ('GET' == strtoupper($method)) {
				return new WidgetGet($widgetid);
			}
			if ('PUT' == strtoupper($method)) {
				return new WidgetPut($widgetid);
			}
		}
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

		if ('apc' == $this->properties['adapter']) {
			return $this->dbAdapter = new Apc();
		}

		if ('mysqli' == $this->properties['adapter']) {
			return $this->dbAdapter = new Mysqli(
				$this->properties['db.host'],
				$this->properties['db.username'],
				$this->properties['db.password'],
				$this->properties['db.database']
			);
		}
	}
}