<?php namespace Ionut\Crud;

use ArrayAccess;
use Illuminate\Support\Str;
use Ionut\Crud\Database\Generator;
use Ionut\Crud\Database\Model;
use Ionut\Crud\Database\Repository;
use Ionut\Crud\Form\Form;
use Ionut\Crud\Plugins\Plugins;
use Ionut\Crud\Routing\Router;
use Ionut\Crud\Support\MessageBag;
use Ionut\Crud\Table\Actions;
use Ionut\Crud\Table\Column;
use Ionut\Crud\Table\Columns;
use Ionut\Crud\Table\Filters;
use Ionut\Crud\Table\Style;

class Crud
{
    use CrudMacros;

    /**
     * @var array
     */
    protected $forwardableSetters = ['with', 'actions', 'title', 'filters', 'rules', 'events'];

    /**
     * @var array
     */
    protected $overrideColumnDefaults = [];


    /**
     * @var string
     */
    public $table;

    /**
     * CSS selector of the crud table.
     *
     * @var string
     */
    public $selector;

    /**
     * @var Columns
     */
    public $columns;

    /**
     * @var Model
     */
    public $model;

    /**
     * @var Application
     */
    public $app;

    /**
     * @var string
     */
    public $id;

    /**
     * @var Router
     */
    public $router;

    /**
     * @var Actions
     */
    public $actions;

    /**
     * @var Form
     */
    public $form;

    /**
     * @var Repository
     */
    public $repo;

    /**
     * @var Events
     */
    public $events;

    /**
     * @var array
     */
    static public $staticEvents = [];

    /**
     * @var Filters
     */
    public $filters;

    /**
     * @var Style
     */
    public $tableStyle;

    /**
     * @var Plugins
     */
    public $plugins;

    /**
     * @var bool
     */
    public $booted = false;


    /**
     * @var Options
     */
    public $options;

    /**
     * @var MessageBag
     */
    public $messageBag;

    /**
     *
     */
    public function __construct()
    {
        $this->app = Application::app();
        $this->router = new Router($this);
        $this->kernel = new HttpKernel($this);
        $this->cache = new Cache($this);
        $this->actions = $this->defaultActions();
        $this->options = new Options($this->actions);
        $this->plugins = new Plugins($this, $this->app['crud.manager']->getDefaultPlugins());
        $this->events = new Events($this);
        $this->messageBag = new MessageBag();
        $this->messageBag->setPresenter($this->app['presenter']);
        $this->refreshDependencies();
        $this->register();
    }

    public function refreshDependencies()
    {
        $this->id = $this->table;
        if (isset($this->options['id'])) {
            $this->id = $this->options['id'];
        }
        $this->selector = '#crud'.$this->id;
        $this->form = new Form($this);
        $this->tableStyle = new Style($this);
        $this->filters = new Filters($this->options['filters'], $this->request);

        $GLOBALS['crud.title'] = $this->getTitle();
    }

    /**
     * @param string $table
     * @return self
     */
    public function table($table)
    {
        $this->table = $table;
        $this->refreshDependencies(); // refresh the crud
        $this->guessDefaultModel();

        return $this;
    }

    /**
     * @return Crud
     */
    public function guessDefaultModel()
    {
        $model = Str::singular(Str::studly($this->table));
        if (class_exists($model)) {
            return $this->model(new $model);
        }
        foreach (['App\\', 'App\\Models\\'] as $namespace) {
            $temp_model = $namespace.$model;
            if (class_exists($temp_model)) {
                return $this->model(new $temp_model);
            }
        }

        return $this->model(new Model());
    }

    public function events(array $events = [])
    {
        return $this->options(compact('events'));
    }

    /**
     * @param array $columns
     * @return self
     */
    public function columns($columns)
    {
        foreach ($columns as $column) {
            if ($this->options['editable'] == false) {
                $column['editable'] = false;
            }
        }

        $this->columns = new Columns($columns, $this->overrideColumnDefaults);

        foreach ($this->columns as $k => $column) {
            if ($column->expandable) {
                $this->parseExpandableColumn($columns, $column);
            }

            if ($column->isTextBased()) {
                if (in_array($column->name, $this->model->getDates()) || preg_match('/_at$/', $column->name)) {
                    $column->input = $column->search_input = 'datetime';
                }
            }

            $column->crud = $this;

            if ($column->search_input == 'select-distinct') {
                $column->search_input = 'select';
                $column->options = [
                    'cache' => function ($column) {
                        $results = $this->repo->query()->selectRaw('DISTINCT `'.$column->name.'`')->get();

                        return $this->repo->collection($results)->options($column->name, $column->name);
                    }
                ];

            }

            if ($column->isCastable()) {
                $this->model->addCast($column->name, $column->getCastType());
            }

            if (!$column->input_icon && preg_match('/\bprice\b/', $column->input_label)) {
                $column->input_icon = 'dollar';
            }

            $this->columns[$k] = $column;
        }


        return $this;
    }

    public function columnDefaults(array $override)
    {
        $this->overrideColumnDefaults += $override;

        return $this;
    }

    /**
     * @param array $options
     * @return self
     */
    public function options(array $options)
    {
        foreach ($options as $k => $v) {
            $this->options[$k] = $v;
        }
        $this->refreshDependencies(); // refresh the crud
        $this->bindEvents();

        return $this;
    }

    public function query(callable $query)
    {
        return $this->options([
            'query.walker' => $query
        ]);
    }

    public function iframe()
    {
        return $this->router->iframe();
    }

    /**
     * @param Model $model
     * @return self
     */
    public function model($model)
    {
        $this->model = $model;
        if (!$this->table) {
            $this->table = $model->getTable();
        }
        $this->repo = new Repository($this);

        $this->refreshDependencies(); // refresh the crud

        return $this;
    }

    public function bindEvents()
    {
        foreach ($this->options['events'] as $event => $callback) {
            if (is_array($callback)) {
                list($event, $callback) = $callback;
            }

            $this->events->listen($event, $callback);
        }

        foreach (static::$staticEvents as $key => $callback) {
            $this->events->listen($key, $callback);
        }
    }

    static public function booting($callback)
    {
        static::$staticEvents['booting'] = $callback;
    }

    /**
     * @return Generator
     */
    public function getDataGenerator()
    {
        $generator = new Generator($this);

        return $generator;
    }

    public function defaultActions()
    {
        $defaultActions = [
            'Edit'   => [
                'callback' => $this->router->controller->edit(),
                'tag'      => 'row',
                'label'    => '<i class="edit icon"></i>',
                'class'    => 'ui icon button',
                'order'    => 3
            ],
            'Delete' => [
                'callback' => $this->router->controller->delete(),
                'tag'      => 'row',
                'label'    => '<i class="delete icon"></i>',
                'class'    => 'ui icon button',
                'confirm'  => true,
                'order'    => 4
            ],
            'Create' => [
                'callback' => $this->router->controller->create(),
                'tag'      => 'global',
                'label'    => '<i class="plus icon"></i> Create'
            ],
            'Back'   => [
                'attr' => [
                    'href'   => $this->url(),
                    'target' => '_parent'
                ],
                'tag'  => 'form'
            ],
            'Save'   => [
                'attr'     => [
                    'onclick' => "$(this).closest('form').submit()",
                    'href'    => '#'
                ],
                'class'    => 'ui submit blue button',
                'tag'      => 'form',
                'label'    => 'Save',
                'callback' => $this->router->controller->save()
            ],
        ];

        return new Actions($this, $defaultActions);
    }

    /**
     * @param $value
     * @return mixed
     */
    public function value($value)
    {
        return $value instanceof \Closure ? $value($this) : $value;
    }

    /**
     * @param array $params
     * @return string
     */
    public function url($params = [])
    {
        return $this->request->url().'?'.http_build_query($params + [
                'crud'  => $this->id,
                'route' => 'table'
            ]);
    }

    /**
     * @return bool
     */
    public function called()
    {
        return $this->request->get('crud') == $this->id;
    }

    public function sandbox($url = null)
    {
        $html = base64_encode($this->router['iframe']($url));

        return '<iframe src="data:text/html;base64,'.$html.'"></iframe';
    }

    public function preload()
    {
        $preloaded = $this->router->controller->processing();

        return $preloaded->getContent();
    }


    public function shouldDisplayRowsActions()
    {
        return $this->options['row_actions_enabled'] && $this->actions->tag('row')->count();
    }

    public function getTitle()
    {
        return $this->options['title'] ? $this->value($this->options['title']) : $this->createTitleByTable($this->model);
    }

    /**
     * @return array|mixed|string
     */
    public function createTitleByTable($table)
    {
        $class = Str::plural(get_class($table));
        $class = explode('\\', $class);
        $class = last($class);
        $class = snake_case($class);
        $class = ucwords(str_replace('_', ' ', $class));

        return $class;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        try {
            $string = $this->router->iframe();
            if (!is_string($string)) {
                // we must throw an exception manually here because if $value
                // is not a string, PHP will trigger an error right after the
                // return statement, thus escaping from our try/catch.
                dd('Non-String response received: '.$string);
            }

            return $string;
        } catch (\Exception $exception) {
            $previousHandler = set_exception_handler(function () {

            });
            restore_error_handler();
            call_user_func($previousHandler, $exception);
            die;
        }
    }

    public function boot()
    {
        if (!$this->booted) {
            $this->events->fire('booting');
            $this->bootPlugins();
            $this->bootActiveFilters();
            $this->events->fire('booted');
        }
    }

    public function register()
    {
        foreach ($this->plugins as $plugin) {
            $plugin->register();
        }
    }

    public function bootPlugins()
    {
        foreach ($this->plugins->active() as $plugin) {
            $plugin->bootSingleTime();
        }
    }

    public function bootActiveFilters()
    {
        foreach ($this->filters->active() as $filter) {
            if ($filter->options['crud']) {
                $filter->options['crud']($this);
            }
        }
    }

    public function getValue($row, Column $column)
    {

        $value = $this->getRawValue($row, $column);

        if (count($column->options)) {
            if (isset($column->options[$value])) {
                $value = $column->options[$value];
            }
        }

        if ($column->append) {
            $value .= $column->append;
        }

        if ($column->prepend) {
            $value = $column->prepend.$value;
        }

        return $value;
    }

    public function getRawValue($row, Column $column)
    {
        $value = $this->data_get($row, $column->name);

        if (is_array($value)) {
            $value = implode(',', $value);
        }

        return (string)$value;
    }

    /**
     * Get an item from an array or object using "dot" notation.
     *
     * @param  mixed  $target
     * @param  string $key
     * @param  mixed  $default
     * @return mixed
     */
    public function data_get($target, $key, $default = null)
    {
        if (is_null($key)) {
            return $target;
        }

        foreach (explode('.', $key) as $segment) {
            if (is_array($target)) {
                if (!array_key_exists($segment, $target)) {
                    return value($default);
                }

                $target = $target[$segment];
            } elseif ($target instanceof ArrayAccess) {
                $target = @$target[$segment];
            } elseif (is_object($target)) {
                $target = @$target->{$segment};
            } else {
                return value($default);
            }
        }

        return $target;
    }

    /**
     * @param $columns
     * @param $column
     */
    protected function parseExpandableColumn($columns, Column $column)
    {
        if (is_array($column->expandable)) {
            list($column->expandable_type, $column->expandable) = $column->expandable;
        }

        $action = $this->actions->set('expandable'.$column->name, [
            'callback'  => $column->expandable,
            'tag'       => 'expandable',
            'decorator' => $column->expandableDecorator()
        ]);
        $column->expandable_action = $action;
        if (!isset($columns[$column->name]['form']) || !$columns[$column->name]['form']) {
            $column->form = 0;
        }
        if (!isset($columns[$column->name]['database']) || !$columns[$column->name]['database']) {
            $column->database = 0;
        }
    }

    public function __call($k, $args)
    {
        if (in_array($k, $this->forwardableSetters)) {
            $this->options([$k => $args[0]]);
        }

        throw new \Exception("Method $k does not exists in the Crud");
    }

    /**
     * @param $k
     * @return mixed
     */
    public function __get($k)
    {
        return $this->app[$k];
    }

}