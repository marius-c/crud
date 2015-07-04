<?php namespace Ionut\Crud\Table;

use Illuminate\Http\Request;
use Ionut\Crud\Crud;
use Ionut\Crud\Html;

class Action
{

	public $name;

	protected $options;

	protected $crud;

	protected $defaultOptions = [
		'label'         => false,
		'html'          => false,
		'tag'           => 'row',
		'mutator'       => null,
		'class'         => 'ui button',
		'label_mutator' => null,
		'hide'          => false,
		'show'          => true,
		'attr' 			=> [],
		'confirm'		=> false,
		'ajax' 			=> false,
		'decorator'     => null,
		'order' 		=> 2,
	];

	function __construct(Crud $crud, $name, $options)
	{
		$this->name = strtolower($name);
		$this->options = $this->formatOptions($options);
		$this->crud = $crud;
		$this->id = strtolower($name);
	}

	public function fire($row = null)
	{
		if(!$row) {
			$row = $this->getRowFromRequest();
		}
		$result = $this->runCallback($row);

		if($decorator = $this->options['decorator']) {
			$result = $decorator($result);
		}

		if(is_null($result)) {
			$result = $this->getLabel($row);
		}

		if($this->crud->request->ajax() && $this->options['ajax']) {
			return $this->crud->actions->html('row', $row);
		}

		return $result;
	}

	public function getRowFromRequest()
	{
		if( ! $row_id = $this->crud->app->request->get('row_id')) {
			return null;
		}

		return $this->crud->repo->where($this->crud->model->getQualifiedKeyName(), $row_id)->first();
	}

	public function runCallback($row)
	{
		if($row) {
			return $this->options['callback']($row, $this->crud);
		}

		if ($row = $this->getRowFromRequest()) {
			return $this->options['callback']($row, $this->crud);
		}

		if ($rows_ids = $this->crud->app->request->get('rows_id')) {
			$rows_ids = explode(',', $rows_ids);
			$rows = $this->crud->repo->whereIn($this->crud->model->getQualifiedKeyName(), $rows_ids)->get();

			return $this->options['callback']($rows, $this->crud);
		}

		return $this->options['callback']($this->crud);
	}

	public function needsFired()
	{
		return $this->crud->app['request']['fire_action'] == $this->id;
	}

	public function getLabel($row = null)
	{
		if ($this->options['label']) {
			return $this->value($this->options['label'], $row);
		}

		return ucfirst($this->name);
	}

	public function value($value, $row)
	{
		if (is_callable($value)) {
			return $value($row);
		}

		return $value;
	}

	public function url($row_id = null)
	{
		$params = ['fire_action' => $this->id, 'action' => 'action_fired'];
		if (is_array($row_id)) {
			$params['rows_id'] = implode(',', $row_id);
		} else {
			if ($row_id !== null) {
				$params['row_id'] = $row_id;
			}
		}

		return $this->crud->router['action']->url($params);
	}

	public function html($row = null, $attr = [])
	{
		$hide = $this->value($this->options['hide'], $row) ;
		$show = $this->value($this->options['show'], $row);
		if ($hide || !$show) {
			return '';
		}

		if ($this->options['html']) {
			return $this->options['html'];
		}

		$class = $this->value($this->options['class'], $row);
		$href  = $this->url($row ? $row->id : null);

		$attr['confirm'] = $this->options['confirm'] ? 1 : 0;
		$attr['ajax']    = $this->options['ajax'] ? 1 : 0;
		$attr = Html::attr($attr+$this->options['attr']+compact('class', 'href'));
		$html = '<a '.$attr.' crud-action="' . $this->name . '">' . $this->getLabel($row) . '</a>';
		if ($this->options['mutator']) {
			$html = $this->options['mutator']($html, $row);
		}

		return $html;
	}

	public function taggedAs($tag)
	{
		return $this->options['tag'] == $tag;
	}

	private function formatOptions($options)
	{
		if (!is_array($options)) {
			$options = [
				'callback' => $options
			];
		}

		$options += $this->defaultOptions;

		return $options;
	}

	public function __get($k)
	{
		return $this->options[$k];
	}

	public function __isset($k)
	{
		return isset($this->options[$k]);
	}

	public function __invoke()
	{
		return call_user_func_array([$this, 'fire'], func_get_args());
	}

} 