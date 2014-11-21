<?php
/**
 * The author of the dataset
 */
class Author{
	
	/* 
	 * Member variables are public in order 
	 * to be serialized properly by json_encode
	 */
	public $id;
	public $value;	
	
	/**
	 * Creates a new instance of the Author object
	 * @param string $id a public url to be used as the identifier of the author
	 * @param string $value the name or any other available information about the autor
	 */
	public function __construct($id, $value) {
		$this->id = $id;
		$this->value = $value;
	}
	
	/**
	 * Saves the Author instance to the database
	 * @param int $datasetId the id of the dataset
	 * @return true on success of false otherwise
	 */
	public function save($datasetId) {
		$sql = "INSERT INTO authors VALUES(null, :dataset_id, :identifier, :value)";
		$sqlParams = array(':dataset_id' => $datasetId,
				':identifier' => $this->id,
				':value' => $this->value);
		
		try {
			$sth = Database::$dbh->prepare($sql);
			$sth->execute($sqlParams);
		} catch (Exception $e) {
			if (DEBUG) $sth->debugDumpParams();
			Util::throwException(__FILE__, __LINE__, __METHOD__, "insert failed", $e->getMessage(), $e);
			return false;
		}
		return true;	
	}
	
	/**
	 *
	 * @param int $datasetId th id of the dataset
	 * @return Author|boolean a new Author object or false if not found
	 */
	public static function createFromDb($datasetId) {
		$sql = "SELECT * FROM authors where dataset_id = :datasetId";
		$sqlParams[":datasetId"] = $datasetId;
	
		try {
			$sth = Database::$dbh->prepare($sql);
			$sth->execute($sqlParams);
			if($result = $sth->fetch(PDO::FETCH_ASSOC)) {
				return new Author($result['id'], $result['value']);
			}
		} catch (Exception $e) {
			if (DEBUG) $sth->debugDumpParams();
			Util::throwException(__FILE__, __LINE__, __METHOD__, "select from links failed", $e->getMessage(), $e);
		}
		return false;
	
	}
	
	/**
	 * Factory method that returns a new instance of Author
	 * @param array $assocArray an associative array representation of the object
	 * @return Author|boolean an Author instance or false on failure
	 */
	public static function createFromArray($assocArray) {		
		if(isset($assocArray['id']) && isset($assocArray['value']))
			return new Author($assocArray['id'], $assocArray['value']);
		return false;		
	}
}

?>