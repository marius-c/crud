<?php namespace Ionut\Crud\Plugins\Iframe;

use Ionut\Crud\Crud;
use Ionut\Crud\Plugins\PluginContract;

class IframePlugin extends PluginContract
{

    protected $name = 'iframe';

    /**
     * @var Crud
     */
    protected $crud;

    protected $active = 1;

    public function boot()
    {
        $this->crud->router->addRoute('iframe', $this->iframe());
    }

    /**
     * @return mixed
     */
    public function iframe()
    {
        return function ($url = null) {
            $url = $url ?: $this->crud->request->get('crud_initial_url') ?: $this->crud->url();
            $url .= '&'.http_build_query($_GET);

            return $this->crud->presenter->raw('plugins/iframe::iframe',
                ['url' => $url, 'name' => $this->crud->id, 'crud' => $this->crud]);
        };
    }
}