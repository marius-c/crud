<?php namespace Ionut\Crud\Table;

use ArrayAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Ionut\Crud\Application;
use Ionut\Crud\Modules\LaravelCompatibility\Pipeline;
use Ionut\Crud\Utils\ArrayProxy;

class Filters extends ArrayProxy
{

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var Request
     */
    protected $request;

    public function __construct($items, Request $request)
    {
        $items = $this->prepareItems($items);
        $this->collection = new FiltersCollection($items);

        $this->request = $request;
    }

    public function query()
    {
        $queryFilters = [];
        foreach ($this->active() as $filter) {
            $queryFilters[] = $filter->name;
        }

        return [
            'filters' => $queryFilters
        ];
    }

    public function toggle(Filter $filter)
    {
        $queryFilters = $this->query();
        foreach ($queryFilters['filters'] as $k => $queryFilter) {
            if ($queryFilter == $filter->name) {
                unset($queryFilters['filters'][$k]);

                return $queryFilters;
            }
        }
        $queryFilters['filters'][] = $filter->name;

        return $queryFilters;
    }

    public function send($query)
    {
        $pipes = $this->preparePipes($this->active()->queryable()->toArray());
        $pipeline = new Pipeline(Application::app());
        $pipeline->send($query)
            ->via('apply')
            ->through($pipes)
            ->then(function ($result) use ($query) {
                $query = $result;
            });

        return $query;
    }

    public function preparePipes($pipes)
    {
        return array_map(function ($pipe) {
            return $pipe->getPipeCallback();
        }, $pipes);
    }

    /**
     * @throws \Exception
     * @return ArrayAccess
     */
    protected function getProxifiedArray()
    {
        return $this->collection;
    }

    /**
     * @param $items
     * @return Filter
     */
    private function prepareItems($items)
    {
        foreach ($items as $name => &$item) {
            if (!$item instanceof Filter) {
                $item = new Filter($name, $item);
            }
        }

        return $items;
    }
}