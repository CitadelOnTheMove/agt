<?php

include_once CLASSES . 'PoiLocation.class.php';
include_once CLASSES . 'PoiLabel.class.php';

/**
 * Describes a Poi element to be used by a Dataset.class
 */
class Poi {
    /*
     * Member variables are public in order 
     * to be serialized properly by json_encode
     */

    public $id; 
    public $title;   
    public $description; 
    public $cityId;
    public $category;  
    public $location;  
    public $attribute;  
    private $db_id;   // the database primary key id
    public $datasetId;

    /**
     * Creates a new instance of the Poi object
     * @param string $identifier the Poi identifier
     * @param string $title the title of the poi
     * @param string $description the description of the poi
     * @param int $cityId the city's id
     * @param string[] $category an array of string categories
     * @param PoiLocation $location the location object
     * @param PoiLabel[] $label an array on labels
     */
    public function __construct($identifier, $title, $description, $cityId, $category, $location, $label) {
        $this->id = $identifier;
        $this->title = $title;
        $this->description = $description;
        $this->cityId = $cityId;
        $this->category = $category;
        $this->location = $location;
        $this->attribute = $label;
    }

    /**
     * Saves the Poi instance to the database
     * @param int $datasetId the unique identifier of the dataset
     * @return true on success or false otherwise
     */
    public function save($datasetId) {
        $this->datasetId = $datasetId;
        $sql = "INSERT INTO pois VALUES (null, :identifier, :title, :description, :pointSrsName, 
										:latitude, :longitude, :address, :postal,
										:city, :dataset_id)";

        // TODO: geoCoords should be identified dynamically and not just for the first point
        //		 for now we are only focusing on points and no other geometries so this approach 
        //		 works for the basic scenarios.		
        $pointCoords = explode(' ', $this->location->point->pos->posList);
        $latitude = trim($pointCoords[0]);
        $longitude = trim($pointCoords[1]);

        $sqlParams = array(':identifier' => $this->id,
            ':title' => $this->title,
            ':description' => $this->description,
            ':pointSrsName' => $this->location->point->pos->srsName,
            ':latitude' => $latitude,
            ':longitude' => $longitude,
            ':address' => $this->location->address->value,
            ':postal' => isset($this->location->address->postal) ? $this->location->address->postal : "",
            ':city' => isset($this->location->address->city) ? $this->location->address->city : "",
            ':dataset_id' => $datasetId);
        try {
            $sth = Database::$dbh->prepare($sql);
            $sth->execute($sqlParams);
        } catch (Exception $e) {
            if (DEBUG)
                $sth->debugDumpParams();
            Util::throwException(__FILE__, __LINE__, __METHOD__, "insert pois failed", $e->getMessage(), $e);
            return false;
        }

        $this->db_id = Database::$dbh->lastInsertId();

        foreach ($this->category as $categoryText) {
            if (!($this->saveCategory($categoryText)))
                return false;
        }

        foreach ($this->attribute as $lbl) {
            if (!$lbl->save($this->db_id))
                return false;
        }

        return true;
    }

    /**
     * Save a new cateogory instance and associate it with this poi.
     * If category instance exists in db, only the association is made
     * @param string $categoryText the text describing the category
     * @return true on success or false otherwise
     */
    private function saveCategory($categoryText) {
        $sql = "SELECT * FROM categories WHERE text=:text AND dataset_id=:datasetId";
        $sqlParams = array(':text' => $categoryText,
            ':datasetId' => $this->datasetId);
        try {
            $sth = Database::$dbh->prepare($sql);
            $sth->execute($sqlParams);
        } catch (Exception $e) {
            if (DEBUG)
                $sth->debugDumpParams();
            Util::throwException(__FILE__, __LINE__, __METHOD__, "select category failed", $e->getMessage(), $e);
            return false;
        }

        $categoryId = 0;

        // check if category exists for this dataset
        if ($result = $sth->fetch(PDO::FETCH_ASSOC)) {
            $categoryId = $result['id'];
        } else {
            $sql = "INSERT INTO categories VALUES(null, :text, :default, :datasetId)";
            $sqlParams = array(':text' => $categoryText,
                ':default' => false,
                ':datasetId' => $this->datasetId);
            try {
                $sth = Database::$dbh->prepare($sql);
                $sth->execute($sqlParams);
            } catch (Exception $e) {
                if (DEBUG)
                    $sth->debugDumpParams();
                Util::throwException(__FILE__, __LINE__, __METHOD__, "insert category failed", $e->getMessage(), $e);
                return false;
            }
            $categoryId = Database::$dbh->lastInsertId();
        }
        return $this->saveCategoryAssociation($this->db_id, $categoryId);
    }

    /**
     * Add the category associations to the poi
     * @param int $poiId the id of the Poi
     * @param int $categoryId the Category id
     * @return boolean true on seccuess or false otherwise
     */
    private function saveCategoryAssociation($poiId, $categoryId) {
        $sql = "INSERT INTO pois_categories VALUES(null, :categoryId, :poiId)";
        $sqlParams = array(':categoryId' => $categoryId,
            ':poiId' => $poiId);
        try {
            $sth = Database::$dbh->prepare($sql);
            $sth->execute($sqlParams);
        } catch (Exception $e) {
            if (DEBUG)
                $sth->debugDumpParams();
            Util::throwException(__FILE__, __LINE__, __METHOD__, "insert category failed", $e->getMessage(), $e);
            return false;
        }

        return ($sth->rowCount() > 0);
    }

    /**
     * A static function to fetch all Poi objects of this dataset
     * @param unknown $datasetId the id of the dataset
     * @return Poi[] the pois of the dataset
     */
    /* public static function createListFromDb($datasetId) {
      $sql = "SELECT *
      FROM pois
      WHERE dataset_id = :datasetId";
      $sqlParams[':datasetId'] = $datasetId;

      try {
      $sth = Database::$dbh->prepare($sql);
      $sth->execute($sqlParams);
      } catch (Exception $e) {
      if (DEBUG)
      $sth->debugDumpParams();
      Util::throwException(__FILE__, __LINE__, __METHOD__, "select from pois failed", $e->getMessage(), $e);
      }

      $pois = array();

      while ($result = $sth->fetch(PDO::FETCH_ASSOC)) {
      $pois[] = new Poi($result['identifier'], $result['title'], $result['description'], Poi::getCategoriesFromDb($result['id']), new PoiLocation(new PoiLocationPoint("centroid", new PoiLocationPointPos($result['pointSrsName'], $result['latitude'] . " " . $result['longitude'])), new PoiLocationAddress($result['address'], $result['postal'], $result['city'])), Poi::getLabelsFromDb($result['id']));
      }
      return $pois;
      } */

    /**
     * A static function to fetch all Poi objects of this dataset
     * @param unknown $datasetId the id of the dataset
     * @return Poi[] the pois of the dataset
     */
    public static function createListFromDb2($datasetIds) {
        // $sqlParams = array(':datasetIds' => $datasetIdsFormatted);
        //$sqlParams[':datasetIds'] = $datasetIdsFormatted;
        try {
            // SQL IN clause not working, need to construct the query manually with multiple
            // where criteria
            $sql = "SELECT * FROM pois	WHERE ";
            $i = 0;
            foreach ($datasetIds as &$datasetId) {
                if ($i == 0) {
                    $sql .= " dataset_id = " . $datasetId;
                } else {
                    $sql .= " OR dataset_id = " . $datasetId;
                }
                $i++;
            }
            $datasetIdsFormatted = implode(', ', $datasetIds);

            $sth = Database::$dbh->prepare($sql);
            $sth->execute();
        } catch (Exception $e) {
            if (DEBUG)
                $sth->debugDumpParams();
            Util::throwException(__FILE__, __LINE__, __METHOD__, "select from pois failed", $e->getMessage(), $e);
        }

        $pois = array();

        while ($result = $sth->fetch(PDO::FETCH_ASSOC)) {
            $pois[] = new Poi($result['identifier'], $result['title'], $result['description'], Poi::getCategoriesFromDb($result['id']), new PoiLocation(new PoiLocationPoint("centroid", new PoiLocationPointPos($result['pointSrsName'], $result['latitude'] . " " . $result['longitude'])), new PoiLocationAddress($result['address'], $result['postal'], $result['city'])), Poi::getLabelsFromDb($result['id']));
        }
        return $pois;
    }

    /**
     * Retrieves the categories associated with this poi
     * @param int $poiId the id of the poi
     * @return string[] the categories of this poi
     */
    private static function getCategoriesFromDb($poiId) {
        $sql = "SELECT categories.text
				FROM categories, pois_categories
				WHERE categories.id = pois_categories.category_id
				AND poi_id = :poiId";
        $sqlParams[':poiId'] = $poiId;

        $categories = array();
        try {
            $sth = Database::$dbh->prepare($sql);
            $sth->execute($sqlParams);

            while ($result = $sth->fetch(PDO::FETCH_ASSOC)) {
                $categories[] = $result['text'];
            }
        } catch (Exception $e) {
            if (DEBUG)
                $sth->debugDumpParams();
            Util::throwException(__FILE__, __LINE__, __METHOD__, "select from categories failed", $e->getMessage(), $e);
        }
        return $categories;
    }

    /**
     * Retrieves the labels associated with this poi
     * @param unknown $poiId
     * @return multitype:PoiLabel
     */
    private static function getLabelsFromDb($poiId) {
        $sql = "SELECT * FROM poilabels WHERE poi_id = :poiId";
        $sqlParams[':poiId'] = $poiId;

        $labels = array();
        try {
            $sth = Database::$dbh->prepare($sql);
            $sth->execute($sqlParams);

            while ($result = $sth->fetch(PDO::FETCH_ASSOC)) {
                $labels[] = new PoiLabel($result['term'], $result['type'], $result['text'], $result['tplIdentifier']);
            }
        } catch (Exception $e) {
            if (DEBUG)
                $sth->debugDumpParams();
            Util::throwException(__FILE__, __LINE__, __METHOD__, "select from labels failed", $e->getMessage(), $e);
        }
        return $labels;
    }

    /**
     * Factory method that returns a new instance of Poi
     * @param array $assocArray an associative array representation of the object
     * @return Poi|boolean a Poi instance or false on failure
     */
    public static function createFromArray($assocArray) {
        if (empty($assocArray['category'])) {
            throw(new AppGeneratorException("Please make sure that all POIs have category!!"));
        }

        if (isset($assocArray['id']) && isset($assocArray['title']) && isset($assocArray['description']) && isset($assocArray['category']) && isset($assocArray['location']) && isset($assocArray['attribute'])) {
            $labels = array();
            
            $assocArray['cityId'] = "";
            foreach ($assocArray['attribute'] as $label) {
                $labels[] = PoiLabel::createFromArray($label);
            }
            return new Poi($assocArray['id'], $assocArray['title'], $assocArray['description'], $assocArray['cityId'], $assocArray['category'], PoiLocation::createFromArray($assocArray['location']), $labels);
        }
        return false;
    }

}

?>