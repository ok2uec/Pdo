<?php
namespace CentralApps\Pdo;

class PdoStatement extends \PDOStatement
{
	protected $pdo;
	protected $params = array();
	protected $statement;

	public function __construct($pdo, $statement)
	{
		$this->pdo = $pdo;
		$this->statement = $statement;
	}

	public function bindParam($parameter, &$variable, $data_type=\PDO::PARAM_STR, $length = 0, $driver_options=array())
	{
		$this->params[$parameter] = $variable;
		$this->statement->bindParam($parameter, $variable, $data_type, $length, $driver_options);
	}

	public function execute($input_parameters = array())
	{
		foreach ($input_parameters as $key => $value) {
			$this->params[$key] = $value;
		}
		$this->pdo->addToQueryLog($this->getReadableQueryString());
		return $this->statement->execute($input_parameters);
	}

	public function getReadableQueryString()
	{
		return str_replace(array_keys($this->params), array_values($this->params), $this->statement->queryString);
	}

	public function __call($method, $args)
	{
		return call_user_func(array($this->statement, $method), $args);
	}

	public function __get($property)
	{
		return $this->statement->$property;
	}
}