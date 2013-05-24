<?php
namespace Hoborg\DashboardCache\Phabric\Entity;

use Phabric\Phabric\iStorageEntity,
	Phabric\Phabric\Datasource\iDatasource,
	Phabric\Phabric\Kernel,
	Phabric\Phabric\Entity;

use Behat\Gherkin\Node\TableNode;

class Widget extends Entity implements iStorageEntity {

	public function __construct(iDatasource $ds, Kernel $bus, $config = array()) {
		parent::__construct($ds, $bus, $config);
		// do something more ...?
	}

	public function updateFromTable(TableNode $table) {

	}

	public function insertFromTable(TableNode $table) {

	}

	public function deleteFromTable(TableNode $table) {

	}
}
