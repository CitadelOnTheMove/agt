<?php

include_once CLASSES.'Dataset.class.php';
include_once CLASSES.'Poi.class.php';


/**
 * The PoisDataset.class represents the dataset of the pois scenario
 *
 */
class PoisDataset extends Dataset {

	public $poi;				// the array of Poi.class objects
	
	/**
	 * @param string $identifier the public url of the dataset identifier
	 * @param string $updated the timestamp indicating when this dataset was last updated
	 * @param string $created the timestamp indicating when this dataset was created
	 * @param string $lang  the locale code RFC 1766
	 * @param Author $author the author of the dataset
	 * @param License $license the license of the dataset
	 * @param Link $link the link of the source of the dataset
	 * @param string $updateFrequency a description of the update frequency e.g. "semester"
	 * @param Poi[] $pois the array of @see Poi object
	 */
	public function __construct($identifier, $updated, $created, $lang, $author, $license, $link, $updateFrequency, $pois) {
		parent::__construct($identifier, $updated, $created, $lang, $author, $license, $link, $updateFrequency);
		$this->poi = $pois;
	}

	public function save() {
		$sql = "INSERT INTO datasets VALUES(null, :identifier, :update, :created, :lang, :updateFrequency)";
		$sqlParams = array(':identifier' => $this->identifier,
							':update' => '',
							':created' => $this->created,
							':lang' => $this->lang,
							':updateFrequency' => $this->updateFrequency);		

		try {
			$sth = Database::$dbh->prepare($sql);
			$sth->execute($sqlParams);
		} catch (Exception $e) {
			if (DEBUG) $sth->debugDumpParams();
			Util::throwException(__FILE__, __LINE__, __METHOD__, "insert dataset failed", $e->getMessage(), $e);
			return false;
		}
			
		// save the db id
		$this->id = Database::$dbh->lastInsertId();
		
		if($this->author != null) {
			if(!$this->author->save($this->id)) return false;
		}
		
		if($this->license != null) {
			if(!$this->license->save($this->id)) return false;
		}
		
		if($this->link != null) {
			if(!$this->link->save($this->id)) return false;
		}
		
		
		foreach ($this->poi as $p) {
			if(!$p->save($this->id)) return false;
		}
		
		return true;
	}
	
	
	
	public static function createFromDb($datasetId) {
		$sql = "SELECT * FROM datasets WHERE id = :datasetId";
		$sqlParams = array(':datasetId' => $datasetId);
		try {			
			$sth = Database::$dbh->prepare($sql);
			$sth->execute($sqlParams);
			
			$dataset = $sth->fetch(PDO::FETCH_ASSOC);
				
			if($dataset) {
				return new PoisDataset($dataset['identifier'], 
										$dataset['update'], 
										$dataset['created'], 
										$dataset['lang'], 
										Author::createFromDb($datasetId), 
										License::createFromDb($datasetId), 
										Link::createFromDb($datasetId), 
										$dataset['updateFrequency'], 
										Poi::createListFromDb($datasetId));		
			
			}
		} catch (Exception $e) {
			if (DEBUG) $sth->debugDumpParams();
			Util::throwException(__FILE__, __LINE__, __METHOD__, "select failed", $e->getMessage(), $e);
			return false;
		}				
		
	}
	
	/**
	 * Factory method that returns a new instance of PoisDataset
	 * @param array $assocArray an associative array representation of the object
	 * @return PoisDataset|boolean a PoisDataset instance or false on failure
	 */
	public static function createFromArray($assocArray) {
		if(isset($assocArray['id']) && isset($assocArray['updated'])) {
			$pois = array();
			foreach($assocArray['poi'] as $poi) {
				$pois[] = Poi::createFromArray($poi);
			}
			return new PoisDataset($assocArray['id'], $assocArray['updated'], $assocArray['created'], 
					$assocArray['lang'], Author::createFromArray($assocArray['author']), 
					License::createFromArray($assocArray['license']), 
					Link::createFromArray($assocArray['link']), $assocArray['updatefrequency'], $pois);
		}
		return false;
						
	}
}

?>
