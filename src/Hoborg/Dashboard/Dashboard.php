<?php
namespace Hoborg\Dashboard;

class Dashboard {

	protected $kernel = null;

	protected $widgetProvider = null;

	protected $widgets = array();

	protected $assets = array(
		'css' => array(),
		'js' => array(),
	);

	public function __construct(Kernel $kernel, WidgetProvider $widgetProvider = null) {
		$this->kernel = $kernel;

		if (null == $widgetProvider) {
			$this->widgetProvider = new WidgetProvider();
		} else {
			$this->widgetProvider = $widgetProvider;
		}
	}

	public function render() {
		$config = $this->kernel->getConfig();
		$this->widgets = array();
		$widgetDefaults = array (
			'enabled' => 1
		);

		foreach ($config['widgets'] as $index => & $widget) {
			$widget += $widgetDefaults;
			if (!$widget['enabled']) {
				continue;
			}

			$w = $this->widgetProvider->createWidget($this->kernel, $widget)
					->bootstrap();
			$this->widgets[$index] = $w;
		}

		$tpl = $config['template'] . '.phtml';
		return $this->renderTemplate($tpl);
	}

	protected function renderTemplate($templateName) {
		// include tpl file.
		$tpl = $this->kernel->findFileOnPath(
			$templateName,
			$this->kernel->getTemplatesPath()
		);

		if (!$tpl) {
			throw new Exception("Template `{$templateName}` not found");
		}

		ob_start();
		include $tpl;
		return ob_get_clean();
	}

	protected function collectAssets(array $widgets) {

		foreach ($widgets as $widget) {
			$this->assets['css'] += $widget->getAssetFiles('css');
			$this->assets['js'] += $widget->getAssetFiles('js');
		}
	}
}