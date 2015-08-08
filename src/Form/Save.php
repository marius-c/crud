<?php namespace Ionut\Crud\Form;


use Ionut\Crud\Crud;

class Save
{

	/**
	 * @var Crud
	 */
	private $crud;

	protected $rules;

	public function __construct(Crud $crud)
	{
		$this->crud = $crud;
		$this->rules = new Rules($crud);
	}


	public function save($request_row = null)
	{
		$request = $this->crud->app->request;
		if( ! $request->has('inline')) {
			$validation = $this->rules->validate();
			if($validation['fails']) {
				return false;
			}
		}

		$row = $request_row ? clone $request_row : clone $this->crud->model;


		foreach ($this->crud->columns->editable() as $column) {
			if ($inline = $request->get('inline')) {
				if ($inline != $column->name) {
					continue;
				}
			}

			if ($column->allowModelBinding()) {
				if($column->input == 'checkbox') {
					$row->{$column->name} = $request->has($column->name);
				}
				elseif(in_array($column->input, ['datetime', 'date'])) {
					$row->{$column->name} = $request[ $column->name ] ? $request[ $column->name ] : null;
				}
				else {
					$row->{$column->name} = $request[ $column->name ];
				}
			}
		}


		$resultSave = $this->crud->events->fire('before:save', $row, true);

		$resultCreate = true;
		if (!$request_row || !$request_row->exists) {
			$resultCreate = $this->crud->events->fire('before:create', $row);
		}

		if ($resultSave !== false && $resultCreate !== false) {
			$this->saveRow($request_row, $row);
		}

		foreach($this->crud->columns->editable() as $column) {
			if($column->input == 'file' && $request->has($column->name)) {
				foreach($row->attachmentsByColumn($column->name) as $file) {
					$file->detach();
				}

				foreach(explode(',', $request[$column->name]) as $id){
                    $attachmentModel = $this->crud->config['attachments.model'];
					$attachmentModel::whereId($id)->update([
						'attached_id' => $row->id,
						'attached_type' => get_class($row),
						'attached_column' => $column->name,
					]);
				}
			}
		}
		$row->save();

		return $row;
	}

	/**
	 * @param $request_row
	 * @param $row
	 */
	private function saveRow($request_row, $row)
	{
		$row->save();
		$this->crud->events->fire('after:save', $row);
		if (!isset($request_row) || !$request_row->exists) {
			$this->crud->events->fire('after:create', $row);
		}
	}
}