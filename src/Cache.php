<?php namespace Ionut\Crud;


use Doctrine\Common\Cache\Cache as DoctrineCache;

class Cache {

	protected $crud;

	/**
	 * @var DoctrineCache
	 */
	protected $repo;

	public function __construct(Crud $crud)
	{
		$this->crud = $crud;
		$this->repo = $crud->cache;
	}

	public function start($key, $expireMinutes = 10)
	{
		if($this->crud->options['dev']) {
			return true;
		}

		$key = $this->formatKey($key);
		if($this->repo->contains($key)) {
			echo $this->repo->fetch($key);
			return false;
		}

		$expire = $expireMinutes*60;
		ob_start($this->saveKey($key, $expire));
		return true;
	}

	public function saveKey($key, $expire)
	{
		return function($contents) use($key, $expire) {
			$this->repo->save($key, $contents, $expire);
			return $contents;
		};
	}

	public function stop()
	{
		ob_end_flush();
	}

	private function formatKey($key)
	{
		return $this->crud->id.'.'.$key;
	}
}