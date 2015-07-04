<?php namespace Ionut\Crud\Database;

use Illuminate\Database\Eloquent\Relations\Relation;
use Ionut\Crud\Crud;
use ReflectionObject;

class Repository
{

	/**
	 * @var Crud
	 */
	private $crud;

	public function __construct(Crud $crud)
	{
		$this->crud = $crud;
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	public function query()
	{
		$query = $this->crud->model->newQuery();

		foreach($this->crud->options['query_walkers'] as $walker) {
			if ($new_query = $walker($query, $this->crud)) {
				$query = $new_query;
			}
		}

		$query = $this->crud->filters->send($query);

		return $query;
	}

	static public function addScope($relation, $query){
		$refObject   = new ReflectionObject($relation);
		$refProperty = $refObject->getProperty('query');
		$refProperty->setAccessible(true);
		$refProperty->setValue($relation, $query);
		$relation->addConstraints();
		return $refProperty->getValue($query);
	}

	public function __call($k, $args)
	{
		return call_user_func_array([$this->query(), $k], $args);
	}

	public function collection($items)
	{
		if(is_object($items) && method_exists($items, 'all')) {
			$items = $items->all();
		}
		return new Collection($items);
	}

}