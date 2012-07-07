<?php
namespace Hoborg\Dashboard;

class StaticAssetsProxy {

	/**
	 * @var \Hoborg\Dashboard\Kernel
	 */
	protected $kernel = null;

	protected $headers = array(
		'jpg' => 'image/jpeg',
		'png' => 'image/png',
		'gif' => 'image/gif',
		'css' => '',
		'js' => '',
		'xml' => 'text/xml',
	);

	public function __construct(Kernel $kernel) {

		$this->kernel = $kernel;
	}

	public function output($path) {

		$assetPath = ltrim($path, '/');
		$pathParts = explode('/', $assetPath);
		$type = array_shift($pathParts);
		$assetPath = implode(DIRECTORY_SEPARATOR, $pathParts);
		$filename = '';

		switch ($type) {
			case 'templates':
				$filename = $this->kernel->findFileOnPath($assetPath, $this->kernel->getTemplatesPath());
				break;

			case 'widgets':
				$filename = $this->kernel->findFileOnPath($assetPath, $this->kernel->getWidgetsPath());
				break;

			default:
				echo "Unknown asset type '{$type}'";
		}

		if ($filename) {
			$this->proxy($filename);
		}
	}

	public function proxy($filename) {

		$ext = substr(strrchr($filename,'.'),1);
		header('Content-Type: ' . $this->getContentType($ext));
		readfile($filename);
		exit(0);
	}

	protected function getContentType($extension) {

		if (empty($this->headers[$extension])) {
			'text/text';
		}

		return $this->headers[$extension];
	}

}