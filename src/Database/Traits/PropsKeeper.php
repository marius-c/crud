<?php namespace Ionut\Crud\Database\Traits;

trait PropsKeeper
{

	public function newInstance($attributes = [], $exists = false)
	{
		$instance = parent::newInstance($attributes, $exists);
		$instance->setTable($this->table);
		$instance->setCasts($this->casts);

		return $instance;
	}
}