<?php namespace Ionut\Crud\Utils;

use Illuminate\Support\Collection;

/**
 * This class serve as a helper for creating options groups. Like style, rules.
 */
class OptionsGroup extends ArrayProxy
{


    public $items;
    private $defaults;


    public function __construct(array $options, array $defaults = [])
    {
        $this->defaults = $defaults;
        $this->items = new Collection($this->formatOptions($options));
    }

    protected function formatOptions($options)
    {
        $formated = [];

        foreach ($options as $columns => $columnsOptions) {
            if (is_array($columnsOptions)) {
                $columnsOptions += $this->defaults;
            }

            $columns = array_map('trim', explode(',', $columns));
            $formated[] = [
                'columns' => $columns,
                'options' => $columnsOptions
            ];
        }

        return $formated;
    }

    public function getProxifiedArray()
    {
        return $this->items;
    }
}