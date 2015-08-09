<?php namespace Ionut\Crud\Widgets\ToggleButtons;

class CrudToggleButtons
{

    public $buttons = [];
    public $inactiveValue;
    public $column;

    /**
     * @return Button
     */
    public function add($inactive, $active)
    {
        $button = new Button($this);

        return $this->buttons[] = $button->setLabels($inactive, $active);
    }

    public function inactive($inactiveValue)
    {
        $this->inactiveValue = $inactiveValue;

        return $this;
    }

    public function column($column)
    {
        $this->column = $column;

        return $this;
    }

    public function isInactive($row)
    {
        return $row->{$this->column} === '' || $row->{$this->column} == $this->inactiveValue;
    }
}