<?php namespace Ionut\Crud\Widgets\ToggleButtons;

class Button
{

    public $active;
    public $inactive;
    public $value;
    public $class = 'ui button';
    public $activeIcon = '<i class="check icon"></i> ';
    public $activeClass;
    public $callback;
    public $properties = [];

    /**
     * @var CrudToggleButtons
     */
    private $container;

    public function __construct(CrudToggleButtons $container)
    {
        $this->container = $container;
    }

    public function value($value)
    {
        $this->value = $value;

        return $this;
    }

    public function setLabels($inactive, $active)
    {
        $this->inactive = $inactive;
        $this->active = $active;

        return $this;
    }

    public function getActionKey()
    {
        return self::class.':'.$this->container->column.','.$this->value;
    }

    public function color($color)
    {
        $this->class = 'ui '.$color.' button';

        return $this;
    }

    public function activeColor($color)
    {
        $this->activeClass = 'ui '.$color.' button';

        return $this;
    }

    public function callback($callback)
    {
        $this->callback = $callback;

        return $this;
    }

    public function properties(array $properties)
    {
        $this->properties = $properties;

        return $this;
    }

    public function getActionProperties()
    {
        return [
            'label'    => function ($row) {
                return $this->isActive($row) ? $this->activeIcon.$this->active : $this->inactive;
            },
            'callback' => function ($row) {
                $row->{$this->container->column} = $this->isActive($row) ? $this->container->inactiveValue : $this->value;
                $row->save();
                if ($this->callback) {
                    call_user_func_array($this->callback, [$row]);
                }

                return redirect()->back();
            },
            'show'     => function ($row) {
                return $this->container->isInactive($row) || $this->isActive($row);
            },
            'ajax'     => true,
            'class'    => function ($row) {
                if ($this->isActive($row)) {
                    return $this->activeClass ?: $this->class;
                }

                return $this->class;
            }
        ] + $this->properties;
    }

    /**
     * @param $row
     * @return bool
     */
    protected function isActive($row)
    {
        return $row->{$this->container->column} == $this->value;
    }
}