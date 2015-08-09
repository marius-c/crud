<?php namespace Ionut\Crud\Table;

use Ionut\Crud\Application;

class Filter
{

    public $name;
    public $options = [
        'buttonable' => true,
        'label'      => null,
        'type'       => 'query',
        'query'      => null,
        'crud'       => null,
    ];

    public function __construct($name, $options)
    {
        $this->name = $name;
        $this->options = $this->prepareOptions($options) + $this->options;
    }

    private function prepareOptions($options)
    {
        if (!is_array($options)) {
            $options = [
                'query' => $options
            ];
        }

        return $options;
    }

    public function getLabel()
    {
        return value($this->options['label']) ?: $this->name;
    }


    public function isActive($request = null)
    {
        $request = $request ?: Application::app('request');

        return in_array($this->name, $request->get('filters', []));
    }

    public function getPipeCallback()
    {
        return function ($query, $next) {
            $query = $this->apply($query);

            return $next($query);
        };
    }

    public function apply($query)
    {
        return $this->options['query']($query);
    }
}