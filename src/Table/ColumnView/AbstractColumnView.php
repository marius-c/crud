<?php namespace Ionut\Crud\Table\ColumnView;

use Ionut\Crud\Table\Column;

abstract class AbstractColumnView {

    /**
     * @var Column
     */
    protected $column;

    public function ___construct(Column $column) {
        $this->column = $column;
    }

    /**
     * @param  mixed $value
     * @return mixed
     */
    abstract public function format($value);
}