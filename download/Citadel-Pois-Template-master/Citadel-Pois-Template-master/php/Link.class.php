<?php

/**
 * The link of the dataset
 *
 */
class Link {
	/*
	 * Member variables are public in order
	* to be serialized properly by json_encode
	*/
	public $href;
	public $term;
	
	/**
	 * @param string $href the href of the link 
	 * @param string $term the term of the link
	 */
	public function __construct($href, $term) {
		$this->href = $href;
		$this->term = $term;
	}
	
	/**
	 * Saves the Link instance to the database
	 * @param int $datasetId the id of the dataset
	 * @return true on success of false otherwise
	 */
	public function save($datasetId) {
		$sql = "INSERT INTO licenses VALUES(null, :dataset_id, :href, :term)";
		$sqlParams = array(':dataset_id' => $datasetId,
				':href' => $this->href,
				':term' => $this->term);		
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
	 * @return Link|boolean a new Link object or false if not found
	 */
	public static function createFromDb($datasetId) {
		$sql = "SELECT * FROM links where dataset_id = :datasetId";
		$sqlParams[":datasetId"] = $datasetId;
		
		try {
			$sth = Database::$dbh->prepare($sql);
			$sth->execute($sqlParams);
			if($result = $sth->fetch(PDO::FETCH_ASSOC)) {
				return new Link($result['href'], $result['term']);
			}
		} catch (Exception $e) {
			if (DEBUG) $sth->debugDumpParams();
			Util::throwException(__FILE__, __LINE__, __METHOD__, "select from links failed", $e->getMessage(), $e);
		}
		return false;
		
	}
	
	/**
	 * Factory method that returns a new instance of Link
	 * @param array $assocArray an associative array representation of the object
	 * @return Link|boolean a Link instance or false on failure
	 */
	public static function createFromArray($assocArray) {
		if(isset($assocArray['href']) && isset($assocArray['term']))
			return new Link($assocArray['href'], $assocArray['term']);
		return false;
		
	}
	
// 	public function getHref() {
// 		return $this->href;
// 	}
	
// 	public function setHref($href) {
// 		return $this->href;
// 	}
	
// 	public function getTerm() {
// 		return $this->term;
// 	}
	
// 	public function setTerm($term) {
// 		$this->term = $term;
// 	}
}

?>