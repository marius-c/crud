<?php namespace Ionut\Crud\Tests;

use Ionut\Crud\Application;
use PHPUnit_Framework_TestCase;

class TestCase extends PHPUnit_Framework_TestCase {

	/**
	 * @var Application
	 */
	protected $app;

	public function setUp()
	{
		$this->app = new Application();
		$this->app->boot();
	}
} 