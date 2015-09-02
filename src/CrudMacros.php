<?php namespace Ionut\Crud;

use Ionut\Crud\Table\Action;
use Ionut\Crud\Widgets\ToggleButtons\Button;
use Ionut\Crud\Widgets\ToggleButtons\CrudToggleButtons;

trait CrudMacros
{

    public function removeColumn($name)
    {
        unset($this->columns[$name]);

        return $this;
    }

    public function setEditFocused($row)
    {
        $this->actions['back'] = false;

        $this->router['default']->callback = function () use ($row) {
            $url = $this->actions['edit']->url($row->id);

            return $this->router['iframe']($url);
        };

        $this->router['table']->callback = function () use($row) {
            $url = $this->actions['edit']->url($row->id);

            return $this->app['redirect']->to($url);
        };

        $this->options['iframe_preload'] = false;

        return $this;
    }

    public function removeDefaultActions()
    {
        $this->actions['edit'] = false;
        $this->actions['create'] = false;
        $this->actions['delete'] = false;

        return $this;
    }

    /**
     * Hide the actions but do not remove them.
     *
     * @return $this
     */
    public function hideDefaultActions()
    {
        foreach (['create', 'edit', 'delete'] as $action) {
            $this->actions[$action]->show = false;
        }

        return $this;
    }


    public function onlyDeleteAction()
    {
        $this->actions['edit'] = false;
        $this->actions['create'] = false;

        return $this;
    }

    public function makeReadOnly()
    {
        $this->removeDefaultActions();
        $this->options['editable'] = false;
        foreach ($this->columns as $column) {
            $column->editable = false;
        }

        return $this;
    }

    public function action($k, $v)
    {
        if ($v === false) {
            unset($this->actions[$k]);
        }
        else {
            $this->actions[$k] = new Action($this, $k, $v);
        }

        return $this;
    }

    public function removeAction($k)
    {
        return $this->action($k, false);
    }

    public function attachToggleButtons(CrudToggleButtons $buttons)
    {
        foreach ($buttons->buttons as $button) {
            /** @var Button $button */
            $this->action($button->getActionKey(), $button->getActionProperties());
        }

        return $this;
    }
} 