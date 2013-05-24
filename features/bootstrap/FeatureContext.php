<?php

use Behat\Behat\Context\ClosuredContextInterface,
	Behat\Behat\Context\TranslatedContextInterface,
	Behat\Behat\Context\BehatContext,
	Behat\Behat\Exception\PendingException,
	Behat\Behat\Exception\BehaviorException;
use Behat\Gherkin\Node\PyStringNode,
	Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use Sanpi\Behatch\Context\BehatchContext;

require_once __DIR__ . '/../../autoload.php';

use Hoborg\DashboardCache\Kernel;

use Phabric\Phabric\Kernel as PhabricKernel;
//
// Require 3rd-party libraries here:
//
//   require_once 'PHPUnit/Autoload.php';
//   require_once 'PHPUnit/Framework/Assert/Functions.php';
//

/**
 * Features context.
 */
class FeatureContext extends BehatContext
{

	protected $phabric;

	protected $app;

	/**
	 * Initializes context.
	 * Every scenario gets it's own context object.
	 *
	 * @param array $parameters context parameters (set them up through behat.yml)
	 */
	public function __construct(array $parameters) {
		// Initialize Dashboard
		$this->phabric = new PhabricKernel();
		$this->phabric->loadConfiguration(__DIR__ . '/../../phabric.properties');

		$this->app = new Kernel(__DIR__ . '/dashboardCache.properties');

		$this->useContext('mink', new MinkContext);
		$this->useContext('behatch', new BehatchContext($parameters));
	}

	/**
	 * @Given /^there is a widget$/
	 */
	public function thereIsWidget(TableNode $table) {
		$mink = $this->getSubcontext('mink');
		$client = $mink->getSession()->getDriver()->getClient();

		foreach ($table->getHash() as $widget) {
			$config = empty($widget['config']) ? array() : json_decode($widget['config'], true);
			$key = 'DashboardCache_widget_id.'. $widget['id'] .'_configHash.' . md5(json_encode($config));
			$value = urlencode(json_encode($widget + array('data' => '{}')));
			$client->request('GET', $mink->locatePath('/apc.php?k=' . $key . '&v=' . $value));
		}

// 		$key = 'DashboardCache_widget_id.test:widget_configHash.' . md5(json_encode(array()));
// 		$key = 'DashboardCache_widget_id.test:widget_configHash.d751713988987e9331980363e24189ce';
// 		apc_store('DashboardCache_widget_id.widget:test_configHash.' . md5(json_encode(array())), array('test'));

// 		return;
// 		throw new PendingException();
// 		$widgetEntity = $this->phabric->getEntity('widget');
// 		$widgetEntity->insertFromTable($table);
	}

	/**
	* @Given /^clear cache$/
	*/
	public function clearCache() {
		$mink = $this->getSubcontext('mink');
		$client = $mink->getSession()->getDriver()->getClient();
		$client->request('GET', $mink->locatePath('/apc.php?k=clear'));

		return;



		throw new PendingException();

		$widgetEntity = $this->phabric->getEntity('widget');
		$widgetEntity->insertFromTable($table);
	}

	/**
	* @Then /^I should get (\d+) response$/
	*/
	public function shouldGetResponse($statusCode) {
		$mink = $this->getSubcontext('mink');
		$actual = $mink->getSession()->getStatusCode();
		if ($mink->getSession()->getStatusCode() != $statusCode) {
			throw new BehaviorException("Expected status code {$statusCode} not equal to actual {$actual}");
		}
	}

	/**
	* @Then /^I should get a widget$/
	*/
	public function iShouldGetWidget(TableNode $table) {
		throw new PendingException();
	}

}
