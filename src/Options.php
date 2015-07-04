<?php namespace Ionut\Crud;

use ArrayAccess;
use Ionut\Crud\Table\Actions;
use Ionut\Crud\Utils\ArrayProxy;

class Options implements ArrayAccess
{

	/**
	 * @var array
	 */
	protected $query_walkers = [];

	/**
	 * @var bool
	 */
	protected $row_actions_enabled = true;

	/**
	 * @var bool
	 */
	protected $global_actions_enabled = true;

	/**
	 * @var bool
	 */
	protected $search_filters_enabled = false;

	/**
	 * @var bool
	 */
	protected $advanced_search_enabled = true;

	/**
	 * @var array
	 */
	protected $form_style = [];

	/**
	 * @var array
	 */
	protected $table_style = [];

	/**
	 * @var array
	 */
	protected $rules = [];

	/**
	 * @var array
	 */
	protected $events = [];

	/**
	 * @var array
	 */
	protected $with = [];

	/**
	 * Deprecated
	 */
	protected $editable = true;

	/**
	 * @var array
	 */
	protected $filters = [];

	/**
	 * @var int
	 */
	protected $default_order_column = 0;

	/**
	 * @var string
	 */
	protected $default_order_type = 'desc';

	/**
	 * @var bool
	 */
	protected $iframe_follower = true;

	/**
	 * @var null
	 */
	protected $title = null;

	/**
	 * @var bool
	 */
	protected $dev = false;

	/**
	 * @var bool
	 */
	protected $iframe_preload = false;

	public function __construct(Actions $actions)
	{
		$this->actions = $actions;
	}

	public function append($key, $value)
	{
		$this->$key = array_add($this->$key, $key, $value);
	}

	/**
	 * The indirect changes are made using the $crud->options the scope of this method
	 * being to mantain the backward compatibility between v1.0 and v2.0.
	 *
	 * @param string $key
	 * @param mixed  $value
	 */
	public function handleIndirectChange($key, $value)
	{
		$key = $this->normalizeKey($key);

		switch($key) {
			case 'query_walker':
				$this->query_walkers['option'] = $value;
				break;

			case 'actions':
				$this->actions->change($value);
				break;

			default:
				$this->$key = $value;
		}
	}

	/**
	 * @param $key
	 * @return mixed
	 */
	protected function normalizeKey($key)
	{
		return str_replace('.', '_', $key);
	}

	public function get($k)
	{
		return $this->{$this->normalizeKey($k)};
	}

	/**
	 * Whether a offset exists
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetexists.php
	 * @param mixed $offset <p>
	 *                      An offset to check for.
	 *                      </p>
	 * @return boolean true on success or false on failure.
	 *                      </p>
	 *                      <p>
	 *                      The return value will be casted to boolean if non-boolean was returned.
	 */
	public function offsetExists($offset)
	{
		return isset($this->{$this->normalizeKey($offset)});
	}

	/**
	 * Offset to retrieve
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetget.php
	 * @param mixed $offset <p>
	 *                      The offset to retrieve.
	 *                      </p>
	 * @return mixed Can return all value types.
	 */
	public function offsetGet($offset)
	{
		return $this->{$this->normalizeKey($offset)};
	}

	/**
	 * Offset to set
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetset.php
	 * @param mixed $offset <p>
	 *                      The offset to assign the value to.
	 *                      </p>
	 * @param mixed $value  <p>
	 *                      The value to set.
	 *                      </p>
	 * @return void
	 */
	public function offsetSet($offset, $value)
	{
		$this->handleIndirectChange($offset, $value);
	}

	/**
	 * Offset to unset
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetunset.php
	 * @param mixed $offset <p>
	 *                      The offset to unset.
	 *                      </p>
	 * @throws \Exception
	 */
	public function offsetUnset($offset)
	{
		throw new \Exception("You can't unset an option.");
	}


}