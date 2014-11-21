<?php
/**
 * Database helper static class
 *
 */
class Database {
	
	/**
	 * 
	 * @var PDO The database handler
	 */
	public static $dbh = null;
	
	/**
	 * Opens a connection with the database
	 * @throws PDOException
	 */
	public static function connect() {
		if(self::isConnected()) return;
		
		try {
			self::$dbh = new PDO("mysql:host=".DB_HOSTNAME.";port=".DB_PORT.";dbname=".DB_NAME, DB_USERNAME, DB_PASSWORD,
					array(PDO::ATTR_PERSISTENT => true));
			self::$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			// force utf-8 encoding on database			
			self::$dbh->exec("SET NAMES utf8; SET CHARACTER SET utf8;");
		}
		catch(PDOException $e)
		{
			self::disconnect();
    		throw $e;
		}
	}
	
	/**
	 * disconnect from the database
	 */
	public static function disconnect() {
		self::$dbh = null;
	}
	
	/**
	 * check connection status
	 * @return boolean true if client is connected
	 */
	public static function isConnected() {
		return (self::$dbh != null); 
	}
	
	/**
	 * Begins a transaction
	 */
	public static function begin() {
		self::$dbh->beginTransaction();
	}
	
	/**
	 * Commits the transaction
	 */
	public static function commit() {
		self::$dbh->commit();
	}
	
	/**
	 * Rolls back the transaction
	 */
	public static function rollback() {
		if (self::$dbh) self::$dbh->rollBack();
	}
	
}

?>