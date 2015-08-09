<?php namespace Ionut\Crud\Plugins\ActionsPopup;

use Ionut\Crud\Crud;
use Ionut\Crud\Plugins\PluginContract;

class PopupPlugin extends PluginContract
{
    public $used_at = '[crud-action=create], [crud-action=edit], [crud-popup=1]';
    public $views = [];

    protected $name = 'popup';
    protected $active = 0;

    public function __construct(Crud $crud)
    {
        parent::__construct($crud);
        $this->views['footer'] = __DIR__.'/views/footer.php';
    }

    public function boot()
    {
        $contents = $this->getFooterContent();
        $this->crud->presenter->snippets['footer'] .= $contents;
        $this->crud->actions['back'] = false;
    }

    /**
     * @return string
     */
    private function getFooterContent()
    {
        ob_start();
        include $this->views['footer'];
        $contents = ob_get_contents();
        ob_end_clean();

        return $contents;
    }
}