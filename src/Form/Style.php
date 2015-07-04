<?php namespace Ionut\Crud\Form;

use Ionut\Crud\Crud;
use Ionut\Crud\Utils\OptionsGroup;

class Style
{

	/**
	 * @var Crud
	 */
	private $crud;

	protected $defaults = [
		'before' => null,
		'after'  => null,
		'wrap'   => null
	];

	public function __construct(Crud $crud)
	{

		$this->crud = $crud;
		$this->options = new OptionsGroup($crud->options['form.style'], $this->defaults);
	}

	public function getBefore($column)
	{
		$printable = '';

		foreach ($this->options->items as $options) {
			if ($options['columns'][0] == $column) {
				if ($options['options']['before']) {
					$printable .= $this->crud->form->value($options['options']['before']);
				}

				if ($options['options']['wrap']) {
					$printable .= '<div class="' . $options['options']['wrap'] . '">';
				}
			}
		}

		return $printable;
	}

	public function getAfter($column)
	{
		$printable = '';

		foreach ($this->options as $options) {
			if ($options['columns'][ count($options['columns']) - 1 ] == $column) {
				if ($options['options']['after']) {
					$printable .= $this->crud->form->value($options['options']['after']);
				}

				if ($options['options']['wrap']) {
					$printable .= '</div>';
				}
			}
		}

		return $printable;
	}

	public function getClass($column)
	{

	}

	public function formatStyles($styles)
	{
		$formated = [];

		foreach ($styles as $columns => $style) {
			$style += [
				'before' => null,
				'after'  => null,
				'wrap'   => null,
			];


			$columns = array_map('trim', explode(',', $columns));
			$formated[] = compact('columns', 'style');
		}

		return $formated;
	}
} 