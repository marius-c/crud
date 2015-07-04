<?php namespace Ionut\Crud\Modules\LaravelCompatibility;

use ArrayAccess;

class Request extends \Illuminate\Http\Request implements ArrayAccess {

	/**
	 * Determine if the given offset exists.
	 *
	 * @param  string  $offset
	 * @return bool
	 */
	public function offsetExists($offset)
	{
		return array_key_exists($offset, $this->all());
	}

	/**
	 * Get the value at the given offset.
	 *
	 * @param  string  $offset
	 * @return mixed
	 */
	public function offsetGet($offset)
	{
		return $this->input($offset);
	}

	/**
	 * Set the value at the given offset.
	 *
	 * @param  string  $offset
	 * @param  mixed  $value
	 * @return void
	 */
	public function offsetSet($offset, $value)
	{
		return $this->getInputSource()->set($offset, $value);
	}

	/**
	 * Remove the value at the given offset.
	 *
	 * @param  string  $offset
	 * @return void
	 */
	public function offsetUnset($offset)
	{
		return $this->getInputSource()->remove($offset);
	}
}