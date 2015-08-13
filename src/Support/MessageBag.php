<?php namespace Ionut\Crud\Support;

use Ionut\Crud\View\Presenter;

class MessageBag extends \Illuminate\Support\MessageBag
{

    /**
     * @var Presenter
     */
    protected $presenter;

    public function setPresenter(Presenter $presenter)
    {
        $this->presenter = $presenter;
    }

    public function html($tag)
    {
        if (!$this->has($tag)) {
            return '';
        }

        return collect($this->getMessages()[$tag])->map([$this, 'createNoticeJs'])->implode('');
    }

    public function createNoticeJs($message)
    {
        return $this->presenter->view('inc.notice', $message);
    }
}