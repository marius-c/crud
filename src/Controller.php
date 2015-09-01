<?php namespace Ionut\Crud;

use Illuminate\Database\Eloquent\Model;
use Ionut\Crud\Form\Rules;

class Controller
{

    /**
     * @var Crud
     */
    protected $crud;

    /**
     * @var string
     */
    protected $default = 'table';


    /**
     * @param Crud $crud
     */
    public function __construct(Crud $crud)
    {
        $this->crud = $crud;
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function processing()
    {
        return $this->crud->getDataGenerator()->response();
    }

    public function disabled()
    {
        return $this->crud->app['redirect']->back();;
    }

    /**
     * @return mixed
     */
    public function table()
    {
        return $this->crud->presenter->view('pages.table', ['crud' => $this->crud]);
    }

    public function validate()
    {
        $rules = new Rules($this->crud);

        return $rules->validate();
    }

    public function action()
    {
        $actionsResponse = $this->crud->actions->check();

        return $actionsResponse;
    }

    public function save()
    {
        return function ($row) {
            $row = $row instanceof Model ? $row : null;
            $this->crud->form->saver->save($row);

            return $this->crud->redirect->to($this->crud->url());
        };
    }


    /**
     * It's an action.
     *
     * @return callable
     */
    public function delete()
    {
        return function ($row) {
            $row->delete();

            return $this->crud->app->redirect->back()->with('notice.success', 'The row was deleted!');
        };
    }

    /**
     * It's an action.
     *
     * @return callable
     */
    public function create()
    {
        return function ($crud) {
            $edit = $this->edit();

            return $edit(clone $crud->model, $crud);
        };
    }

    /**
     * It's an action.
     *
     * @return callable
     */
    public function edit()
    {
        return function ($row, $crud) {
            $crud->form->setRow($row);

            if ($crud->form->listenForSubmit()) {
                return $this->crud->redirect->to($this->crud->url());
            }

            return $this->crud->presenter->view('pages.form', ['crud' => $this->crud, 'row' => $row]);
        };
    }


}