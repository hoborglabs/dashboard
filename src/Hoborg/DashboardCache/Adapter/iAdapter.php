<?php
namespace Hoborg\DashboardCache\Adapter;

interface iAdapter {

	public function query($sql);

	public function fetchRow($sql);

	public function fetchAll($sql);

	public function quote($input);

	public function getConnection();
}