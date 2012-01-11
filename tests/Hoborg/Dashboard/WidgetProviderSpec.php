<?php
namespace Hoborg\Dashboard;

class WidgetProviderSpec {

	public function widgetSources() {
		return array(
			array(
				array(),
				array(),
			),
			array(
				array(
					'php' => 'test.php'
				),
				array(
					array (
						'type' => 'php',
						'sources' => array(
							'test.php'
						)
					)
				)
			),
			array(
				array(
					'php' => array('test.php')
				),
				array(
					array (
						'type' => 'php',
						'sources' => array(
							'test.php'
						)
					)
				)
			),
			array(
				array(
					'php' => array('test1.php', 'test2.php')
				),
				array(
					array (
						'type' => 'php',
						'sources' => array('test1.php', 'test2.php')
					)
				)
			),
			array(
				array(
					'cgi' => array('url1', 'url2')
				),
				array(
					array (
						'type' => 'cgi',
						'sources' => array('url1', 'url2')
					)
				)
			),
		);
	}

}