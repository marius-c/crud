<?php namespace Ionut\Crud\Modules\Extractor;

use Ionut\Crud\Application;

class DbConfigExtractor
{

	public $notRecomendedForCrudColumns = ['updated_at', 'deleted_at'];

	/**
	 * @var Application
	 */
	private $app;

	public function __construct(Application $app)
	{
		$this->app = $app;
	}

	public function routes()
	{
		if ($this->app->request->has('generate')) {
			$result = $this->generate();

			return $result;
		} else {
			return $this->form();
		}
	}

	public function generate()
	{

		$table = $this->app->request->get('table');
		$conn = $this->app->request->get('connection');
		$columnsConfig = $this->getColumnsConfig($conn, $table);
		$columnsString = str_replace('array (', 'array(', var_export($columnsConfig, true));


		return <<<CRUD
app('crud')
	->table('$table')
	->columns($columnsString)
CRUD;
	}

	public function getColumnsConfig($conn, $table)
	{
		$connection = $this->app['db.factory']->make($this->app->config['database.connections'][$conn]);
		$db_name = $connection->getDatabaseName();

		$sql = <<<CRUD
		SELECT column_name, data_type, character_maximum_length, table_name,ordinal_position, is_nullable
		FROM information_schema.COLUMNS WHERE table_name LIKE '{$table}' AND TABLE_SCHEMA='{$db_name}'
		ORDER BY ordinal_position
CRUD;
		$columns = $connection->select($sql);

		$config = [];
		foreach ($columns as $column) {
			if (in_array($column['column_name'], $this->notRecomendedForCrudColumns)) {
				continue;
			}
			$config[$column['column_name']] = $this->getColumnConfig($column);
		}

		return $config;
	}

	public function getColumnConfig($column)
	{
		$config = [];
		foreach (['input', 'table', 'inline', 'form'] as $k => $v) {
			$config[$v] = $this->getColumnConfigOption($v, $column);
			if ($config[$v] === true) {
				unset($config[$v]);
			}
		}

		return $config;
	}

	public function getColumnConfigOption($option, $column)
	{
		switch ($option) {
			case 'input':
				return $this->getColumnInputType($column);

			case 'table':

				if (in_array($column['column_name'], ['password', 'remember_token'])) {
					return false;
				}

				return true;

			case 'form':
				if (in_array($column['column_name'],
					['id', 'created_at', 'updated_at', 'deleted_at', 'remember_token'])) {
					return false;
				}

				return true;

			case 'inline':
				if (in_array($column['column_name'], ['id'])) {
					return false;
				}

				return true;
		}
	}

	public function getColumnInputType($column)
	{
		if ($column['column_name'] == 'id') {
			return 'text';
		}

		if ($column['data_type'] == 'string') {
			return 'textarea';
		}

		if ($column['data_type'] == 'datetime') {
			return 'date';
		}

		if ($column['data_type'] == 'tinyint') {
			return 'checkbox';
		}

		return 'text';
	}

	public function form()
	{
		$connections = array_keys($this->app->config['database.connections']);

		return $this->app->presenter->view('modules.extractor.form')->with('connections', $connections);
	}
}