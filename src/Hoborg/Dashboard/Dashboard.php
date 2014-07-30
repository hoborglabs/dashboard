<?php
namespace Hoborg\Dashboard;

class Dashboard {

	/**
	 * @var \Hoborg\Dashboard\Kernel
	 */
	protected $kernel = null;

	/**
	* @var \Hoborg\Dashboard\IWidgetProvider
	*/
	protected $widgetProvider = null;

	protected $widgets = array();

	protected $assets = array(
		'css' => array(),
		'js' => array(),
	);

	public function __construct(Kernel $kernel, WidgetProvider $widgetProvider = null) {

		if (null == $widgetProvider) {
			$widgetProvider = new WidgetProvider($kernel);
		}

		$this->widgetProvider = $widgetProvider;
		$this->kernel = $kernel;
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

			$w = $this->widgetProvider->createRowWidget($widget);
			$this->widgets[$index] = $w;
		}

		$tpl = $config['template'] . '.phtml';
		$this->collectAssets($this->widgets);
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

		$HD_PUBLIC = $this->kernel->getParam('public', '');
		ob_start();
		include $tpl;
		return ob_get_clean();
	}

	protected function collectAssets(array $widgets) {

		foreach ($widgets as $widget) {
			$this->assets['css'] += $widget->getCSS();
			$this->assets['js'] += $widget->getJS();
		}
	}
}
