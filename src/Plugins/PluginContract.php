<?php namespace Ionut\Crud\Plugins;

use Ionut\Crud\Crud;
use ReflectionClass;

abstract class PluginContract
{

    protected $active = 1;

    /**
     * @var
     */
    protected $crud = 'default';

    protected $booted = false;

    public function __construct(Crud $crud)
    {
        $this->crud = $crud;
    }

    abstract public function boot();

    public function bootSingleTime()
    {
        if (!$this->booted) {
            $this->boot();
            $this->booted = true;
        }
    }

    public function register()
    {
        $this->registerViews();
    }

    public function registerViews()
    {
        $path = $this->getViewsPath();
        $this->crud->app->view->addNamespace('plugins/iframe', $path);
    }

    public function setInactive()
    {
        $this->active = 0;

        return $this;
    }

    public function disable()
    {
        return $this->setInactive();
    }

    public function setActive()
    {
        $this->active = 1;

        return $this;
    }

    public function isActive()
    {
        return (bool)$this->active;
    }

    public function getName()
    {
        if (!isset($this->name)) {
            throw new \Exception('Did you forget the name property for '.get_class($this).'?');
        }

        return $this->name;
    }

    public function getViewsPath()
    {
        return $this->getDir().'/views';
    }

    protected function getDir()
    {
        $reflector = new ReflectionClass(get_class($this));

        return dirname($reflector->getFileName());
    }
}