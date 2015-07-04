<?php namespace Ionut\Crud\Database;

use ArrayObject;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Ionut\Crud\Crud;
use Ionut\Crud\Table\Column;

class Generator
{

	/**
	 * @var Model
	 */
	private $model;

	/**
	 * @var Request
	 */
	private $request;

	/**
	 * @var Crud
	 */
	private $crud;

	public function __construct(Crud $crud)
	{
		$this->request = $crud->app->request;
		$this->crud = $crud;
		$this->crud->model->getConnection()->enableQueryLog();
	}

	public function getRows()
	{
		return [];
	}

	public function response()
	{
		$totalQuery = $this->crud->repo->query()->with($this->crud->options['with']);
		$this->applyOrder($totalQuery);

		$filteredQuery = clone $totalQuery;
		$this->applyFilters($filteredQuery);

		$resultsQuery = clone $filteredQuery;
		$this->applyPagination($resultsQuery);;
		$data = $this->getResponseData($resultsQuery);

		return new Response(new ArrayObject([
			'draw'            => $this->request->get('draw'),
			'recordsTotal'    => $totalQuery->count(),
			'recordsFiltered' => $filteredQuery->count(),
			'data'            => $data
		]));
	}

	public function applyOrder($query)
	{
		$columns = $this->crud->columns->tableable()->values();
		if (isset($columns[ $this->crud->request['order.0.column'] ])) {

			$column = $columns[ $this->crud->request['order.0.column'] ];
			if (!$column->relationable() && $column->database) {
				return $query->orderBy($this->qualified($column->name), $this->crud->request['order.0.dir']);
			}
		}

		return $query->orderBy($this->qualified('id'), $this->crud->request['default_order_type']);
	}

	public function qualified($name)
	{
		return $this->crud->model->getTable() . '.' . $name;
	}

	public function applyFilters($query)
	{
		$this->applySearchFilter($query);
		$this->applyColumnsSearchFilter($query);

		return $query;
	}

	/**
	 * @param $query
	 */
	private function applySearchFilter($query)
	{
		if ($s = $this->crud->request['search.value']) {
			$columns = $this->crud->columns->dbable()->nonRelationable()->names();
			$columns = array_map([$this, 'qualified'], $columns);
			$columns = implode(',', $columns);
			$query->whereRaw("CONCAT_WS('|', {$columns}) LIKE ?", ["%{$s}%"]);

			foreach ($this->crud->columns->dbable()->relationable() as $column) {
				$this->applyRelationableSearch($column, $query, $s);
			}
		}
	}

	public function applyColumnsSearchFilter($query)
	{
		foreach ($this->getColumnsSearch() as list($column, $s)) {
			$name = $column->name;
			if ($column->relationable()) {
				$this->applyRelationableSearch($column, $query, $s);
			} else {
				$this->applyColumnSearch($column, $name, $query, $s);
			}
		}
	}

	public function applyRelationableSearch(Column $column, $query, $s)
	{
		preg_match('#(.*?)\.([^\.]+)$#si', $column->name, $matches);
		$final = function ($q) use ($column, $matches, $s) {
			$this->applyColumnSearch($column, $matches[2], $q, $s);
		};
		$this->parseNestedRelation($matches[1], $query, $final);
	}

	/**
	 * Parse a nested relation and apply a Closure on it.
	 *
	 * @param string   $relation
	 * @param Builder  $query
	 * @param \Closure $final
	 * @param int      $start
	 */
	public function parseNestedRelation($relation, $query, $final, $start = 0)
	{
		$steps = explode('.', $relation);
		$step = $steps[ $start ];

		// We're going to split in chunks each relation step
		// and call the function recursively until we've touched
		// the last relation step, then appling the $final function
		if (count($steps) - 1 > $start) {
			$query->whereHas($step, function ($subquery) use ($relation, $query, $final, $start) {
				$this->parseNestedRelation($relation, $subquery, $final, ++$start);
			});
		} else {
			$query->whereHas($step, $final);
		}
	}

	public function applyColumnSearch(Column $column, $name, $query, $s)
	{
		if ($column->search_input == 'interval') {
			$s = explode('-:-', $s);
		}

		if(is_array($s)) {
			$this->applyBetweenSearch($name, $query, $s);
		} elseif ($column->isTextBased()) {
			$query->whereRaw("`{$name}` LIKE ?", ["%{$s}%"]);
		} else {
			$query->whereRaw("`{$name}`=?", [$s]);
		}
	}

	public function getColumnsSearch()
	{
		foreach ($this->crud->columns->tableable()->values() as $i => $column) {
			if ($column->database) {
				if ($column->input == 'checkbox') {
					$s = $this->crud->request['columns'][ $i ]['search']['value'];
				} else {
					$s = $this->crud->request['columns'][ $i ]['search']['value'];
				}
				if ($s != '') {
					yield $column->name => [$column, $s];
				}
			}
		}

		if ($search = $this->crud->request->get('search')) {
			parse_str($search, $fields);
			foreach ($fields as $column => $s) {

				if(preg_match('#^interval\-to#', $column))
					continue;

				if(preg_match('#^interval\-from\-(.*?)$#', $column, $matches)) {
					$column = $this->crud->columns[ $matches[1] ];
					$s = [$s, $fields['interval-to-'.$column->name]];
				}
				else {
					$column = $this->crud->columns[ $column ];
				}

				if ($column->search) {
					if ($s != '') {
						yield $column->name => [$column, $s];
					}
				}

			}
		}
	}

	public function hightlightMatchesWords($column, $value)
	{
		$columnsSearch = iterator_to_array($this->getColumnsSearch());

		$s = isset($columnsSearch[ $column->name ]) ? $columnsSearch[ $column->name ][1] : '';

		if ($column->isTextBased()) {
			$filters = [];
			if ($s) {
				$filters[] = preg_quote($s);
			}
			if ($this->crud->request['search.value']) {
				$filters[] = preg_quote($this->crud->request['search.value']);
			}
			if ($filters) {
				$regex = '#(' . implode('|', $filters) . ')#si';
				$replace = function ($matches) {
					return '<span class="highlight">' . $matches[1] . '</span>';
				};
				$value = preg_replace_callback($regex, $replace, $value);
			}
		}

		return $value;
	}

	public function getResponseData($query)
	{
		$rows = $query->get();

		$data = [];
		foreach ($rows as $row) {
			$data_row = [];
			foreach ($this->crud->columns->tableable() as $column) {
				if ($column->expandable) {
					$value = '<a class="ui blue button expandable" data-expandable-type="' . $column->expandable_type . '" data-target="' . $column->expandable_action->url($row->id) . '"><i class="fa fa-plus-square"></i></a>';
				} elseif ($column->inline) {
					$value = $this->getInline($row, $column);
				} else {
					$value = $this->crud->getValue($row, $column);
				}

				if ($column->mutator) {
					$value = $column->mutator($row, $this->crud);
				}

				$value = $this->hightlightMatchesWords($column, (string)$value);

				$data_row[] = $value;
			}

			if ($this->crud->shouldDisplayRowsActions()) {
				$data_row[] = $this->crud->actions->html('row', $row);
			}

			$data_row['DT_RowId'] = 'row_' . $row->id;

			$data[] = $data_row;
		}

		return $data;
	}

	public function getMutated($row, Column $column)
	{
		$value = $this->crud->getValue($row, $column);

		if ($column->mutator) {
			$value = $column->mutator($row, $this->crud);
		}

		return $value;
	}

	private function getInline($row, $column)
	{
		$this->crud->form->setRow($row);
		$value = $this->crud->getValue($row, $column);
		$view = $this->crud->presenter->view('inc.inline', [
			'column' => $column,
			'crud'   => $this->crud,
			'value'  => $value
		]);

		return $view;
	}

	/**
	 * @param $resultsQuery
	 */
	private function applyPagination($resultsQuery)
	{
		$resultsQuery->take($this->request->get('length', 10));
		if ($this->request['start']) {
			$resultsQuery->skip($this->request->get('start'));
		}
	}

	/**
	 * @param $name
	 * @param $query
	 * @param $s
	 */
	protected function applyBetweenSearch($name, $query, $s)
	{
		if ($s[0]) {
			$query->where($name, '>=', new Carbon($s[0]));
		}

		if ($s[1]) {
			$query->where($name, '<=', new Carbon($s[1]));
		}

	}
}