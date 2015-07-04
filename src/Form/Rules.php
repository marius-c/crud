<?php namespace Ionut\Crud\Form;

use Illuminate\Support\Collection;
use Illuminate\Translation\Translator;
use Illuminate\Validation\DatabasePresenceVerifier;
use Illuminate\Validation\Validator;
use Ionut\Crud\Crud;
use Ionut\Crud\Utils\OptionsGroup;

class Rules {

	/**
	 * @var Crud
	 */
	private $crud;

	public function __construct(Crud $crud)
	{
		$this->crud = $crud;
		$this->rules = new OptionsGroup($crud->options['rules']);
	}

	public function validate()
	{
		$validator = $this->createValidator();

		return [
			'fails' => $validator->fails(),
			'messages' => $validator->messages()
		];
	}


	private function getValidatorRules()
	{
		$columnRules = [];

		foreach($this->rules as $rule) {
			foreach($rule['columns'] as $column) {
				if( ! isset($columnRules[$column])) {
					$columnRules[$column] = [];
				}

				$columnRules[$column][] = $rule['options'];
			}
		}

		$columnRules = array_map(function($rules) {
			return implode('|', $rules);
		}, $columnRules);

		return $columnRules;
	}

	/**
	 * @return Validator
	 */
	private function createValidator()
	{
		$validator = new Validator($this->crud->app['translator'], $this->crud->request->all(), $this->getValidatorRules());
		$validator->setPresenceVerifier(new DatabasePresenceVerifier($this->crud->model->getConnectionResolver()));

		return $validator;
	}

}