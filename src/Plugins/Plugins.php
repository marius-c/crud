<?php namespace Ionut\Crud\Plugins;

use ArrayAccess;
use Illuminate\Support\Collection;
use Ionut\Crud\Utils\ArrayProxy;

class Plugins extends ArrayProxy
{

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var
     */
    private $crud;

    public function __construct($crud, $plugins = [])
    {
        $this->crud = $crud;
        $this->collection = new Collection();

        foreach ($this->formatPlugins($plugins) as $plugin) {
            $this->put($plugin->getName(), $plugin);
        }
    }

    public function active()
    {
        return $this->filter(function (PluginContract $plugin) {
            return $plugin->isActive();
        });
    }

    /**
     * @param $plugins
     * @return array
     */
    private function formatPlugins($plugins)
    {
        $plugins = array_map(function ($plugin) {
            return is_object($plugin) ? $plugin : new $plugin($this->crud);
        }, $plugins);

        return $plugins;
    }

    /**
     * @throws \Exception
     * @return ArrayAccess
     */
    protected function getProxifiedArray()
    {
        return $this->collection;
    }
}