<?php namespace Ionut\Crud\Table;

use Illuminate\Support\Collection;
use Ionut\Crud\Utils\ArrayProxy;

class Actions extends ArrayProxy
{

    /**
     * @var array
     */
    private $items;

    private $crud;

    public function __construct($crud, array $actions)
    {
        $this->crud = $crud;
        $this->items = new Collection;

        foreach ($actions as $name => $action) {
            $this->set($name, $action);
        }
    }

    public function change($new_actions)
    {
        if ($new_actions === false) {
            $this->clear();
        }

        if (!is_array($new_actions)) {
            $new_actions = [];
        }

        foreach ($new_actions as $name => $options) {
            $this->set($name, $options);
        }
    }

    public function clear()
    {
        $this->items = new Collection;
    }

    public function check()
    {
        foreach ($this->items as $action) {
            if ($action->needsFired()) {
                return $action->fire();
            }
        }
    }

    public function html($stack, $row)
    {
        $actions_html = '';
        foreach ($this->tag($stack)->sortBy('order') as $action) {
            $actions_html .= $action->html($row);
        }

        return $actions_html;
    }

    public function tag($tag)
    {
        return $this->decorate($this->items->filter(function (Action $action) use ($tag) {
            return $action->taggedAs($tag);
        }));
    }

    public function visible()
    {
        return $this->decorate($this->items->filter(function (Action $action) {
            return $action->show && !$action->hide;
        }));
    }

    public function name($name)
    {
        return $this->items->filter(function (Action $action) use ($name) {
            return $action->name == $name;
        })->first();
    }

    public function set($name, $options)
    {
        if ($options && !($options instanceof Action)) {
            $options = new Action($this->crud, $name, $options);
        }

        $this[strtolower($name)] = $options;

        return $options;
    }

    public function getProxifiedArray()
    {
        return $this->items;
    }

    public function decorate(Collection $collection)
    {
        return new static($this->crud, $collection->all());
    }

}