<?php namespace Ionut\Crud\Table;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Columns extends Collection
{
	protected $propsCache = [];
	/**
	 * @var array
	 */
	private $overrideDefaults;

	public function __construct($columns = [], array $overrideDefaults = null)
	{
		$this->overrideDefaults = $overrideDefaults;

		foreach ($columns as $k => $v) {
			if (!is_object($v)) {
				$columns[ $k ] = new Column($k, $v, $this->overrideDefaults);
			}
		}

		parent::__construct($columns);
	}

	public function names()
	{
		return Arr::pluck($this, 'name');
	}

	public function tableable()
	{
		return $this->whereProp('table');
	}

	public function dbable()
	{
		return $this->whereProp('database');
	}

	public function nonRelationable()
	{
		return $this->filter(function ($column) {
			return !$column->relationable();
		});
	}

	public function relationable()
	{
		return $this->filter(function ($column) {
			return $column->relationable();
		});
	}

	public function editable()
	{
		return $this->whereProp('editable');
	}

	public function whereProp($k, $v = true)
	{
		if (isset($this->propsCache[ $k ])) {
			return $this->propsCache[ $k ];
		}

		return $this->propsCache[ $k ] = $this->filter(function ($column) use ($k, $v) {
			return value($column->$k) == $v;
		});
	}

}