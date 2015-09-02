<?php namespace Ionut\Crud\Table;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Columns extends Collection
{
    protected $propsCache = [];

    /**
     * @var array
     */
    protected $overrideDefaults;

    public function __construct($columns = [], array $overrideDefaults = null)
    {
        $this->overrideDefaults = $overrideDefaults;

        foreach ($columns as $k => $v) {
            if (!is_object($v)) {
                $columns[$k] = new Column($k, $v, $this->overrideDefaults);
            }
        }

        parent::__construct($columns);
    }

    public function names()
    {
        return array_pluck($this->items, 'name');
    }

    public function tableable()
    {
        return $this->ordered()->whereProp('table');
    }


    public function editable()
    {
        return $this->whereProp('editable');
    }

    public function dbable()
    {
        return $this->whereProp('database');
    }

    public function ordered()
    {
        $collection = $this;
        foreach($collection as $k => $column) {
            if ($column->insert_last) {
                unset($collection[$k]);
                $collection[$k] = $column;
            }
        }

        return $collection;
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

    public function whereProp($k, $v = true)
    {
        if (isset($this->propsCache[$k])) {
            return $this->propsCache[$k];
        }

        return $this->propsCache[$k] = $this->filter(function ($column) use ($k, $v) {
            return value($column->$k) == $v;
        });
    }

}