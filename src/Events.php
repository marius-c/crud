<?php namespace Ionut\Crud;

use Illuminate\Contracts\View\View;
use Illuminate\Events\Dispatcher;
use Symfony\Component\HttpFoundation\Response;

class Events extends Dispatcher
{

	/**
	 * @var Crud
	 */
	private $crud;

	public function __construct(Crud $crud)
	{
		$this->crud = $crud;
		parent::__construct($this->crud->app);
	}

	public function fire($event, $payload = [], $halt = true)
	{
		$payload = $this->preparePayload($payload);

		$result = parent::fire($event, $payload, $halt);
		if ($result instanceOf View || $result instanceof Response) {
			$this->crud->kernel->dispatch($result);
		}

		return $result;
	}

	/**
	 * @param $payload
	 * @return array
	 */
	private function preparePayload($payload)
	{
		if (!is_array($payload)) {
			$payload = [$payload];
		}
		$payload[] = $this->crud;

		return $payload;
	}
}