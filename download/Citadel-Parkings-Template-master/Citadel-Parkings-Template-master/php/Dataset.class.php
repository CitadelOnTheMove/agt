<?php
include_once CLASSES.'Link.class.php';
include_once CLASSES.'License.class.php';
include_once CLASSES.'Author.class.php';

/**
 * Dataset class is tha base class that every dataset should extend
 */
class Dataset {
	
	/*
	 * Member variables are public in order
	 * to be serialized properly by json_encode
	 */	
	public $id;
	public $identifier;
	public $updated;
	public $created;  
	public $lang;
	public $author;
	public $license;
	public $link;
	public $updateFrequency;
	
	
	/**
	 * @param string $identifier the public url of the dataset identifier
	 * @param string $updated the timestamp indicating when this dataset was last updated
	 * @param string $created the timestamp indicating when this dataset was created
	 * @param string $lang  the locale code RFC 1766
	 * @param Author $author the author of the dataset
	 * @param License $license the license of the dataset
	 * @param Link $link the link of the source of the dataset
	 * @param string $updateFrequency a description of the update frequency e.g. "semester"
	 */
	public function __construct($identifier, $updated, $created, $lang, $author, $license, $link, $updateFrequency) {
		$this->identifier = $identifier;
		$this->updated = $updated;
		$this->created = $created;
		$this->lang = $lang;
		$this->author = $author;
		$this->license = $license;
		$this->link = $link;
		$this->updateFrequency = $updateFrequency;		
	}
	
	public static function getCategories($datasetId) {
		$sql = "SELECT * FROM categories where dataset_id = :datasetId";
		$sqlParams[":datasetId"] = $datasetId;
		
		$categories = array();
		try {
			$sth = Database::$dbh->prepare($sql);
			$sth->execute($sqlParams);
			include_once CLASSES.'Filter.class.php';
			while($result = $sth->fetch(PDO::FETCH_ASSOC)) {
				$categories[] = new Filter($result['text'], $result['default']==1);
			}
		} catch (Exception $e) {
			if (DEBUG) $sth->debugDumpParams();
			Util::throwException(__FILE__, __LINE__, __METHOD__, "select from categories failed", $e->getMessage(), $e);
		}
		return $categories;
		
	}
	
// 	public function getId() {
// 		return $this->id;
// 	}
	
// 	public function setId($id) {
// 		$this->id = $id;
// 	} 
	
// 	public function getUpdated() {
// 		return $this->updated;
// 	}
	
// 	public function setUpdated($updated) {
// 		$this->updated = $updated;
// 	}
	
// 	public function getCreated() {
// 		return $this->created;
// 	}
	
// 	public function setCreated($created) {
// 		$this->created = $created;
// 	}
	
// 	public function getLang() {
// 		return $this->lang;
// 	}
	
// 	public function setLang($lang) {
// 		$this->lang = $lang;
// 	}
	
// 	public function getAuthor() {
// 		return $this->author;
// 	}
	
// 	public function setAuthor($author) {
// 		$this->author = $author;
// 	}
	
// 	public function getLicense() {
// 		return $this->license;
// 	}
	
// 	public function setLicense($license) {
// 		$this->license = $license;
// 	}
	
// 	public function getLink() {
// 		return $this->link;
// 	}
	
// 	public function setLink($link) {
// 		$this->link = $link;
// 	}
	
// 	public function getUpdateFrequency() {
// 		return $this->updateFrequency;	
// 	}
	
// 	public function setUpdateFrequency($updateFrequency) {
// 		$this->updateFrequency = $updateFrequency;
// 	}
}
?>