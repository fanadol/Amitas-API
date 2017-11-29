<?php

class db
{
	private $dbhost = 'localhost';
	private $dbname = 'amitas_db';
	private $dbuser = 'root';
	private $dbpass = '';

	public function connect(){
		try{
			$mysql_connect_str = "mysql:host=$this->dbhost;dbname=$this->dbname;";
			$dbConnection = new PDO ($mysql_connect_str,$this->dbuser,$this->dbpass);
			// set the PDO error mode to exception
			$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			return $dbConnection;
		} catch(PDOException $e) {
			echo "Connection failed : ".$e->getMessage();
		}
	}
}