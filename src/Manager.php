<?php namespace Ionut\Crud;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Ionut\Crud\Plugins\ActionsPopup\PopupPlugin;
use Ionut\Crud\Plugins\Extractor\ExtractorPlugin;
use Ionut\Crud\Plugins\Iframe\IframePlugin;
use Ionut\Crud\Plugins\PluginContract;


/**
 * Class Manager
 *
 * @method Crud table
 */
class Manager
{

	/**
	 * @var array
	 */
	protected $plugins = [
		PopupPlugin::class,
		IframePlugin::class,
		ExtractorPlugin::class
	];

	public function make($columns, $options)
	{
		$table = new Crud($columns, $options);
		$table->boot();

		return $table;
	}

	public function iframeParentRedirect($url)
	{
		return Application::app('presenter')->raw('global.pages.iframe-parent-redirect', compact('url'));
	}

	public function layout($view)
	{
		$layout = Application::app('presenter')->view('layout', [], false);
		$layout->content = $view instanceof View ? $view->render() : $view;
		return $layout;
	}

	public function __call($k, $args)
	{
		$table = new Crud();
		$table->boot();

		return call_user_func_array([$table, $k], $args);
	}

	/**
	 * @return array
	 */
	public function getDefaultPlugins()
	{
		return $this->plugins;
	}

}