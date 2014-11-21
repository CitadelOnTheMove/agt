<?php
/**
 * A label of a specific Poi
 *
 */
class PoiLabel {
	
	/*
	 * Member variables
	 */
	public $term;			// the string that will be displayed in the template (describing $text)
	public $type;			// the label type might indicate a special action on the template 
							// available actions are: 
							// - tel: translate the text into a call link
							// - email: translate the text into a mailto link
							// - url: translate the text into an http link
							// - string: display the link
	public $text;			// the actual value to be displayed
	public $tplIdentifier;  // a special id e.g. #Citadel_telephone that uniquely identifies a template element.
							// it's used from the templating mechanism to properly layout data
													
	/**
	 * @param string $term the string that will be displayed in the template (describing $text)
	 * @param string $type the label type might indicate a special action on the template
	 * @param string $text the actual value to be displayed
	 * @param string $tplIdentifier a special id e.g. #Citadel_telephone that uniquely identifies a template element. It's used from the templating mechanism to properly layout data
	 */
	public function __construct($term, $type, $text, $tplIdentifier) {
		$this->term = $term;
		$this->type = $type;
		$this->text = $text;
		$this->tplIdentifier = $tplIdentifier;
	}
	
	/**
	 * 
	 * @param int $poiId the id of the poi
	 * @return boolean true on success or fasle otherwise
	 */
	public function save($poiId) {
		$sql = "INSERT INTO poilabels VALUES(null, :poiId, :term, :type, :text, :tplIdentifier)";
		$sqlParams = array(':poiId' =>$poiId, 
							':term' => $this->term, 
							':type'=> $this->type,
							':text' => $this->text, 
							':tplIdentifier' => $this->tplIdentifier);
		try {
			$sth= Database::$dbh->prepare($sql);
			$sth->execute($sqlParams);
		} catch (Exception $e) {
			if (DEBUG) $sth->debugDumpParams();
			Util::throwException(__FILE__, __LINE__, __METHOD__, "insert label failed", $e->getMessage(), $e);
			return false;
		}
		return true;
	}
	
	/**
	 * Initialize object from db
	 * @param int $poiId
	 */
	public static function createFromDb($poiId) {
		$sql = "SELECT *
				FROM poilabels
				WHERE poi_id = :id";
		$sqlParams[':id'] = $poiId;		
		
		try {
			$sth = Database::$dbh->prepare($sql);
			$sth->execute($sqlParams);
		} catch (Exception $e) {
			if (DEBUG) $sth->debugDumpParams();
			Util::throwException(__FILE__, __LINE__, __METHOD__, "select from poiLabels failed", $e->getMessage(), $e);
		}	
		
		if(!($result = $sth->fetch(PDO::FETCH_ASSOC))) {
			Util::throwException(__FILE__, __LINE__, __METHOD__, "No poiLabels found");
			return null;
		}
		return new PoiLabel($result['term'], $result['type'], $result['text'], $result['tplIdentifier']);
	}
	
	/**
	 * Factory method that returns a new instance of Label
	 * @param array $assocArray an associative array representation of the object
	 * @return Label|boolean a Label instance or false on failure
	 */
	public static function createFromArray($assocArray) {
		if(isset($assocArray['term']) && isset($assocArray['type']) && isset($assocArray['text'])
				&& isset($assocArray['tplIdentifier']))
			return new PoiLabel($assocArray['term'], $assocArray['type'], $assocArray['text'],
					 $assocArray['tplIdentifier']);
		return false;
	}
	
// 	public function getTerm() {
// 		return $this->term;
// 	}
	
// 	public function setTerm($term) {
// 		$this->term = term;
// 	}
	
// 	public function getText() {
// 		 return $this->text;
// 	}
	
// 	public function setText($text) {
// 		$this->text = $text;
// 	}
	
}

?>