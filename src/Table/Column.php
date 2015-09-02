<?php namespace Ionut\Crud\Table;

use Ionut\Crud\Html;
use Ionut\Crud\Table\ColumnView\FileColumnView;

class Column
{

    /**
     * @var string
     */
    public $name;

    /**
     * @var array
     */
    protected $options;

    protected $textBasedTypes = ['text', 'textarea', 'html'];

    /**
     * @var Action
     */
    public $expandable_action;

    /**
     * @var array
     */
    protected $defaults = [
        'table'               => 1,
        'form'                => 1,
        'inline'              => 1,
        'database'            => 1,
        'mutator'             => false,
        'value_mutator'       => null,
        'title'               => false,
        'input'               => 'text',
        'required'            => 0,
        'editable'            => true,
        'default'             => '',
        'input_label'         => null,
        'input_attr'          => [],
        'input_icon'          => null,
        'input_right_icon'    => null,
        'input_custom_html'   => '',
        'field_attr'          => [],
        'placeholder'         => null,
        'search'              => true,
        'options'             => [],
        // Labeled
        'labeled'             => null,
        'labeled_right'       => null,
        'labeled_class'       => null,
        'input_action'        => null,
        'max_length'          => false,
        'expandable'          => false,
        /**
         * normal, iframe
         */
        'expandable_type'     => 'normal',
        'help_block'          => null,
        'input_help'          => null,
        'visible'             => null,
        'hidden'              => null,
        'allow_model_binding' => null,
        'search_input'        => null,
        'append'              => null,
        'prepend'             => null,
        /**
         * Angular shortcuts
         */
        'ng-model'            => null,
        'file_empty_slots'    => 1,

        // Highlight the column contents if it matches the search terms
        'highlight'           => true,

        // Add the column before another column.
        // @todo
        'insert_before'       => null,
        // Add the column after another column.
        // @todo
        'insert_after'        => null,

        // Insert the column at the end
        'insert_last'         => false,

        // Used on the input='date' types.
        'date_format' => 'Y-m-d'
    ];

    public function __construct($name, array $options, array $overrideDefaults = [])
    {
        $this->name = $name;
        $this->options = $options + ($overrideDefaults + $this->defaults);

        if (!$this->options['title']) {
            $this->options['title'] = $this->createLabelUsingColumn();
        }

        if (!$this->options['input_label']) {
            $this->options['input_label'] = ucfirst(str_ireplace('is ', '', $this->options['title']));
        }

        if (isset($this->options['input_help'])) {
            $this->options['help_block'] = $this->options['input_help'];
        }

        if (!$this->options['search_input']) {
            $this->options['search_input'] = $this->options['input'];
            if ($this->isTextBased()) {
                $this->options['search_input'] = 'text';
            }
        }

        if ($this->name == 'id') {
            $this->options['form'] = false;
            $this->options['inline'] = false;
        }

        if (!value($this->options['form']) && !$this->options['inline']) {
            $this->options['editable'] = false;
        }

        if (!value($this->options['editable'])) {
            $this->options['form'] = false;
            $this->options['inline'] = false;
        }

        if (!is_null($this->options['visible'])) {
            $this->options['hidden'] = !$this->options['visible'];
        }

        if ($this->options['hidden'] === true) {
            $this->options['form'] = $this->options['table'] = false;
        }
    }

    public function allowModelBinding()
    {
        return !is_null($this->options['allow_model_binding']) ? $this->allow_model_binding : $this->database;
    }

    public function isTextBased()
    {
        return in_array($this->input, $this->textBasedTypes);
    }

    public function isCastable()
    {
        return $this->getCastType() !== false;
    }

    public function getCastType()
    {
        $types = [
            'multiselect' => 'array',
            'checkbox'    => 'boolean',
        ];

        if (!isset($types[$this->input])) {
            return false;
        }

        return $types[$this->input];
    }

    public function inputAttr($value = null)
    {
        $attr = $this->options['input_attr'];

        if ($value !== null && is_string($value)) {
            $attr['value'] = $value;
            $attr['initial-value'] = $value;
        }

        if ($this->options['ng-model']) {
            $attr['ng-model'] = $this->options['ng-model'];
        }

        if ($this->options['input'] == 'date') {
            $attr['class'] = 'datepicker';
            $attr['value'] = (new \DateTime($attr['value']))->format($this->date_format);
        }

        if ($this->options['input'] == 'datetime') {
            $attr['class'] = 'datetimepicker';
        }

        $attr = array_map('e', $attr);

        return Html::attr($attr);
    }

    public function fieldAttr()
    {
        return Html::attr($this->options['field_attr']);
    }


    public function relationable()
    {
        return preg_match('/\\./', $this->name);
    }

    public function expandableDecorator()
    {
        return function ($result) {
            if ($this->isTraversable($result)) {
                $result = $this->arrayToTable($result);
            }

            return $result;
        };
    }

    public function isTraversable($v)
    {
        return is_array($v) || $v instanceof \Traversable;
    }

    public function arrayToTable($array)
    {
        if (!count($array)) {
            return 'Zero results.';
        }

        $output = '<table class="ui definition table">';
        foreach ($array as $k => $v) {
            if (is_object($v) && method_exists($v, 'toArray')) {
                $v = $v->toArray();
            }

            if ($this->isTraversable($v)) {
                $v = $this->arrayToTable($v);
            }

            if (is_int($k)) {
                $output .= '<tr><td>'.$v.'</td></tr>';
            } else {
                $output .= '<tr><td>'.$k.'</td><td>'.$v.'</td></tr>';
            }
        }
        $output .= '</table>';

        return $output;
    }

    public function __get($k)
    {
        $v = $this->options[$k];
        if (is_array($v) && isset($v['cache']) && is_callable($v['cache'])) {
            $func = $v['cache'];
            $this->options[$k] = $v = $func($this);
        }

        return $v;
    }

    public function __set($k, $v)
    {
        $this->options[$k] = $v;
    }

    public function __call($k, $args)
    {
        return call_user_func_array($this->options[$k], $args);
    }

    /**
     * @return mixed
     */
    protected function createLabelUsingColumn()
    {
        $label = ucfirst($this->name);
        $label = str_replace(['_id',], '', $label);
        $label = str_replace(['_', '.'], ' ', $label);

        return $label;
    }

    public function getColumnView()
    {
        if ($this->options['input'] == 'file') {
            return new FileColumnView($this);
        }
    }

    public function isInlineSupported()
    {
        return in_array($this->options['input'], ['checkbox']);
    }
}