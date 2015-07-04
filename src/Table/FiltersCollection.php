<?php namespace Ionut\Crud\Table;

use Illuminate\Support\Collection;
use Ionut\Crud\Application;

class FiltersCollection extends Collection {

	public function active()
	{
		return $this->filter(function(Filter $filter) {
			return $filter->isActive();
		});
	}

	public function queryable()
	{
		return $this->filter(function(Filter $filter) {
			return $filter->options['type'] == 'query';
		});
	}

	public function buttonable()
	{
		return $this->filter(function(Filter $filter) {
			return (bool)$filter->options['buttonable'];
		});
	}
}