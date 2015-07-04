<?php namespace Ionut\Crud\Modules\LaravelCompatibility;

use ReflectionFunction;
use ReflectionMethod;
use ReflectionParameter;

trait ApplicationCompatibility {

	/**
	 * Call the given Closure / class@method and inject its dependencies.
	 *
	 * @param  callable|string  $callback
	 * @param  array  $parameters
	 * @param  string|null  $defaultMethod
	 * @return mixed
	 */
	public function call($callback, array $parameters = [], $defaultMethod = null)
	{
		$dependencies = $this->getMethodDependencies($callback, $parameters);

		return call_user_func_array($callback, $dependencies);
	}

	public function booted()
	{

	}

	/**
	 * Get all dependencies for a given method.
	 *
	 * @param  callable|string  $callback
	 * @param  array  $parameters
	 * @return array
	 */
	protected function getMethodDependencies($callback, $parameters = [])
	{
		$dependencies = [];

		foreach ($this->getCallReflector($callback)->getParameters() as $key => $parameter)
		{
			$this->addDependencyForCallParameter($parameter, $parameters, $dependencies);
		}

		return array_merge($dependencies, $parameters);
	}



	/**
	 * Get the proper reflection instance for the given callback.
	 *
	 * @param  callable|string  $callback
	 * @return \ReflectionFunctionAbstract
	 */
	protected function getCallReflector($callback)
	{
		if (is_string($callback) && strpos($callback, '::') !== false)
		{
			$callback = explode('::', $callback);
		}

		if (is_array($callback))
		{
			return new ReflectionMethod($callback[0], $callback[1]);
		}

		return new ReflectionFunction($callback);
	}

	/**
	 * Get the dependency for the given call parameter.
	 *
	 * @param  \ReflectionParameter  $parameter
	 * @param  array  $parameters
	 * @param  array  $dependencies
	 * @return mixed
	 */
	protected function addDependencyForCallParameter(ReflectionParameter $parameter, array &$parameters, &$dependencies)
	{
		if (array_key_exists($parameter->name, $parameters))
		{
			$dependencies[] = $parameters[$parameter->name];

			unset($parameters[$parameter->name]);
		}
		elseif ($parameter->getClass())
		{
			$dependencies[] = $this->make($parameter->getClass()->name);
		}
		elseif ($parameter->isDefaultValueAvailable())
		{
			$dependencies[] = $parameter->getDefaultValue();
		}
	}
} 