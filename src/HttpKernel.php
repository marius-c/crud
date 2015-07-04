<?php namespace Ionut\Crud;

use Illuminate\Http\Response;

class HttpKernel
{
	/**
	 * @var Crud
	 */
	public $crud;

	/**
	 * @param Crud $crud
	 */
	public function __construct(Crud $crud)
	{
		$this->crud = $crud;
	}

	/**
	 * Decorate the given response and throw it in the browser.
	 *
	 * @param  Response $response
	 * @return Response
	 */
	public function dispatch($response)
	{
		if ($this->fullDispatchAllowed()) {
			$response = $this->prepareResponse($response);
			$this->sendResponse($response);
		} else {
			if (method_exists($response, 'send')) {
				$this->sendResponse($response);
			}
		}

		return $response;
	}

	/**
	 * @return mixed
	 */
	public function fullDispatchAllowed()
	{
		return $this->crud->request['crud'] == $this->crud->id;
	}

	/**
	 * @param $response
	 * @return Response
	 */
	private function prepareResponse($response)
	{
		if (!method_exists($response, 'send')) {
			$response = new Response($response);
		}

		return $response;
	}

	/**
	 * @param $response
	 */
	private function sendResponse($response)
	{
		$response->send();
		$this->crud->session->save();
		exit;
	}


} 