<?php

class DB {

	private $conn;
	private $statement;

	function __construct()
	{
		$this->conn = new PDO('mysql:host=localhost;port=8889;dbname=mzayan', 'root', 'root');
	}

	function query($sql = '', $params = array())
	{
            $this->statement = $this->conn->prepare($sql);
            $this->statement->setFetchMode(PDO::FETCH_OBJ);
            $this->statement->execute($params);
		return $this->statement;
	}

	public function getInsertId()
	{
		return (int) $this->conn->lastInsertId();
	}

}