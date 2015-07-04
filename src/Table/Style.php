<?php namespace Ionut\Crud\Table;

use Ionut\Crud\Crud;

class Style
{

	/**
	 * @var Crud
	 */
	private $crud;

	/**
	 * @var array
	 */
	protected $style;

	protected $default = [
		'before' => null,
		'after'  => null,
	];

	public function __construct(Crud $crud)
	{
		$this->crud = $crud;
		$this->style = $crud->options['table.style'] + $this->default;
	}

	public function getBefore()
	{
		return $this->crud->value($this->style['before']);
	}

	public function getAfter()
	{
		return $this->crud->value($this->style['after']);
	}
}