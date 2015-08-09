<?php namespace Ionut\Crud\View;

use Illuminate\Http\Response;
use Ionut\Crud\Application;
use Ionut\Crud\Package;

class Presenter
{
    public $snippets = [
        'footer' => '',
        'header' => '',
    ];


    public function view($name, $vars = [], $string = true)
    {
        $name = 'themes.'.Application::app('config')->get('style.theme').'.'.$name;

        return $this->raw($name, $vars, $string);
    }

    public function raw($name, $vars = [], $string = true)
    {
        $vars['presenter'] = $this;
        $view = Application::app('view')->make($name, $vars);
        if ($string) {
            $view = $view->render();
        }

        return $view;
    }

    public function iframeParentRedirect($url)
    {
        return new Response((string)$this->view('iframe-parent-redirect', compact('url')));
    }
} 