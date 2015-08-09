<?php namespace Ionut\Crud\Form;

use Ionut\Crud\Crud;
use Ionut\Crud\Database\Model;

class Form
{

    /**
     * @var Crud
     */
    public $crud;

    /**
     * @var Style
     */
    public $style;

    /**
     * Current edited row.
     *
     * @var Model
     */
    public $row;

    /**
     * @var Save
     */
    public $saver;

    public function __construct(Crud $crud)
    {
        $this->crud = $crud;
        $this->style = new Style($crud, $crud->options['form.style']);
        $this->saver = new Save($crud);
    }

    public function getValue($column)
    {
        $value = $this->getRawValue($column);

        if (in_array($column->input, ['multiselect', 'chosen-multiple'])) {
            if (is_string($value)) {
                $value = explode(',', $value);
            }
            if ($value == null) {
                $value = [];
            }
        }

        if ($column->value_mutator) {
            $value = call_user_func_array($column->value_mutator, [$this->row, $value, $this->crud]);
        }

        return $value;
    }

    public function getRawValue($column)
    {

        $value = $this->crud->app->request->old($column->name);
        if ($value) {
            return $value;
        }


        if ($this->row && $this->row->exists) {
            $value = $this->crud->getRawValue($this->row, $column);
            if ($value !== null && $value !== '') {
                return $value;
            }
        }


        $value = $column->default;

        return $value instanceof \Closure ? $value($this->row) : $value;
    }


    public function listenForSubmit()
    {
        if ($this->crud->app->request->isMethod('post')) {
            return $this->saver->save($this->row);
        }
    }

    /**
     * @param Model $row
     */
    public function setRow($row)
    {
        $this->row = $row;
    }

    public function value($value)
    {
        return $value instanceof \Closure ? $value($this->row ?: $this->crud->model, $this->crud) : $value;
    }
} 