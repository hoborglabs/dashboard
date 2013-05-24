<?php
namespace Hoborg\DashboardCache;

use Symfony\Component\HttpFoundation\Response,
	Symfony\Component\HttpFoundation\Request;

interface iHandler {

	public function setContainer(Kernel $kernel);

	public function processHttp(Request $request, Response $response);
}
