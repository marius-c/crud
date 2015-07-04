<?php namespace App\Admin\Crud\Scaffolding;

use Ionut\Crud\Crud;

class CLASS_NAME extends Scaffolding {

	/**
	 * @return Crud
	 */
	public function getBase(){

		return crud()
			->table('TABLE_NAME')
			->columns([

			])
			->options([

			]);
	}
}