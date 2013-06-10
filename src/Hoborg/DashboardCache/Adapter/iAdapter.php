<?php
namespace Hoborg\DashboardCache\Adapter;

/**
 * SQL and NO-SQL adaptor for dashboard widget cache.
 *
 */
interface iAdapter {

	public function from($table);

	public function by($field, $value);

	public function fetch();

	public function update(array $data);

	public function quote($input);

// 	public function getConnection();
}