<?php
namespace Hoborg\Dashboard;

class WidgetProvider implements IWidgetProvider {

	protected $kernel = null;

	public function __construct(Kernel $kernel) {
		$this->kernel = $kernel;
	}

	public function createWidget(array $widgetJson) {
		$widgetJson['data'] = array();
		$widget = new Widget($this->kernel, $widgetJson);
		$sources = $this->getWidgetSources($widget);
		$wData = $widget->get();

		foreach ($sources as $source) {
			switch ($source['type']) {
				case 'cgi':
					$wData = $this->loadWidget($widget, $source['type'], $source['sources']);
					break;

				case 'php':
					$tmp = $this->loadWidget($widget, $source['type'], $source['sources']);
					break;

				case 'static':
					foreach ($source['sources'] as $src) {
						$path = $this->kernel->findFileOnPath(
								$src, $this->kernel->getDataPath());

						if (!empty($path)) {
							$widgetJson = json_decode(file_get_contents($path), true);
							if (!empty($widgetJson)) {
								$widget->extendData($widgetJson);
							}
						}
					}
					break;

				case 'url':
					$body = $this->loadBody($source['type'], $source['sources']);
					$wData['body'] = $body;
					$widget->extendData($wData);
					break;
			}
		}

		//$widget->extendData($wData);
		return $widget;
	}

	public function createRowWidget(array $widgetJson) {

		$widget = new Widget($this->kernel, $widgetJson);
		return $widget;
	}

	/**
	 * Loads first available source.
	 *
	 * @param string $type
	 * @param array $sources
	 *
	 * @return string
	 */
	protected function loadBody($type, array $sources) {
		$body = '';
		if (empty($sources)) {
			return $body;
		}

		if ('static' ==  $type) {
			foreach ($sources as $src) {
				$body = $this->loadBodyFromStatic($src);
				if (!empty($body)) {
					return $body;
				}
			}
		} else if ('url' == $type) {
			foreach ($sources as $src) {
				$body = $this->loadBodyFromUrl($src);
				if (!empty($body)) {
					return $body;
				}
			}
		}

		return $body;
	}

	/**
	 * Loads widget data array.
	 *
	 * @param string $type
	 * @param array $sources
	 *
	 * @return array
	 */
	protected function loadWidget(Widget $widget, $type, array $sources) {
		$widgetData = array();
		if (empty($sources)) {
			return $widgetData;
		}

		if ('cgi' == $type) {
			foreach ($sources as $src) {
				$widgetData = $this->loadWidgetFromCgi($widget, $src);
				$widget->extendData($widgetData);
			}
		} else if ('php' == $type) {
			foreach ($sources as $src) {
				$widgetData = $this->loadWidgetFromPhp($widget, $src);
				$widget->extendData($widgetData);
			}
		}

		return $widgetData;
	}

	protected function getWidgetSources(Widget $widget) {
		$sources = array();
		$widgetData = $widget->get();
		$sourceKeys = array('php', 'cgi', 'url', 'static');

		foreach ($widgetData as $key => $value) {
			if (in_array($key, $sourceKeys)) {
				if (!is_array($value)) {
					$value = array($value);
				}
				$sources[] = array(
					'type' => $key,
					'sources' => $value,
				);
			}
		}

		return $sources;
	}

	protected function loadWidgetFromPhp(Widget $widget, $src) {
		$path = $this->kernel->findFileOnPath(
			$src,
			$this->kernel->getWidgetsPath()
		);

		if ($path) {
			$meta = $this->getFileMeta($path);
			$widgetData = $widget->get();

			include_once $path;
			$extWidget = new $meta['class']($this->kernel, $widgetData);
			$extWidget->bootstrap();

			return $extWidget->get();
		}

		return array();
	}

	protected function loadWidgetFromCgi($widget, $src) {
		$data = $widget->get();
		$json = null;

		if (isset($data['method']) && 'get' == strtolower($data['method'])) {
			$src .= '?widget=' . urlencode(json_encode($widget->get()));
			$json = @file_get_contents($src);
		} else {
			$curl = curl_init();
			$fields = array(
				'widget' => urlencode(json_encode($data))
			);
			$fieldsString = '';
			foreach ($fields as $key => $value) {
				$fieldsString .= $key . '=' . $value . '&';
			}
			rtrim($fieldsString, '&');

			curl_setopt($curl, CURLOPT_URL, $src);
			curl_setopt($curl, CURLOPT_POST, count($fields));
			curl_setopt($curl, CURLOPT_POSTFIELDS, $fieldsString);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

			$json = curl_exec($curl);
			curl_close($curl);
		}
		$json = empty($json) ? '{}' : $json;
		$json = json_decode($json, true);
		return $json;
	}

	protected function loadBodyFromStatic($src) {
		$body = '';
		$path = $this->kernel->findFileOnPath(
			$src,
			array_merge($this->kernel->getWidgetsPath(), $this->kernel->getDataPath())
		);

		if (!$path) {
			return $body;
		}

		$body = file_get_contents($path);
		return $body;
	}

	protected function loadBodyFromUrl($src) {
		return file_get_contents($src);
	}

	private function getFileMeta($file) {
		$fileMeta = array(
				'path' => $file,
				'class' => null,
		);

		// get class names
		$tokens = token_get_all(file_get_contents($file));
		$fullClassName = '';
		for ($i = 0; $i < count($tokens); $i++) {
			$tokenName = is_array($tokens[$i]) ? $tokens[$i][0] : null;
			if (T_NAMESPACE == $tokenName) {
				$i += 2;
				$tokenValue = is_array($tokens[$i]) ? '\\' . $tokens[$i][1] : null;
				while (is_array($tokens[++$i])) {
					$tokenValue .= $tokens[$i][1];
				}
				$fullClassName .= $tokenValue;
				continue;
			}
			if (T_CLASS == $tokenName) {
				$tokenValue = is_array($tokens[$i+2]) ? $tokens[$i+2][1] : null;
				$fullClassName .= '\\' . $tokenValue;
				break;
			}
		}
		$fileMeta['class'] = $fullClassName;

		return $fileMeta;
	}
}
