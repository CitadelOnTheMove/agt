<?php

include_once CLASSES . 'Dataset.class.php';
include_once CLASSES . 'Poi.class.php';

/**
 * The PoisDataset.class represents the dataset of the pois scenario
 *
 */
class PoisDataset extends Dataset {

    public $poi;    // the array of Poi.class objects

    /**
     * Creates a new instance of the PoisDataset object
     * @param string $id the unique identifier of the dataset
     * @param string $identifier the name of the dataset
     * @param string $updated the timestamp indicating when this dataset was last updated
     * @param string $created the timestamp indicating when this dataset was created
     * @param string $lang  the locale code RFC 1766
     * @param Author $author the author of the dataset
     * @param License $license the license of the dataset
     * @param Link $link the link of the source of the dataset
     * @param string $updateFrequency a description of the update frequency e.g. "semester"
     * @param string $url the public url of the dataset
     * @param Poi[] $pois the array of @see Poi object
     */

    public function __construct($id, $identifier, $updated, $created, $lang, $author, $license, $link, $updateFrequency, $url, $pois) {
        parent::__construct($id, $identifier, $updated, $created, $lang, $author, $license, $link, $updateFrequency, $url);
        $this->poi = $pois;
    }

     /**
     * Saves the PoisDataset instance to the database
     * @return boolean true on success or fasle otherwise
     */
    public function save() {
        $sql = "INSERT INTO datasets VALUES(null, :identifier, :update, :created, :lang, :updateFrequency, :url)";
        $sqlParams = array(':identifier' => $this->identifier,
            ':update' => '',
            ':created' => $this->created,
            ':lang' => $this->lang,
            ':updateFrequency' => $this->updateFrequency,
            ':url' => $this->url);

        try {
            $sth = Database::$dbh->prepare($sql);
            $sth->execute($sqlParams);
        } catch (Exception $e) {
            if (DEBUG)
                $sth->debugDumpParams();
            Util::throwException(__FILE__, __LINE__, __METHOD__, "insert dataset failed", $e->getMessage(), $e);
            return false;
        }

        // save the db id
        $this->id = Database::$dbh->lastInsertId();

        if ($this->author != null) {
            if (!$this->author->save($this->id))
                return false;
        }

        if ($this->license != null) {
            if (!$this->license->save($this->id))
                return false;
        }

        if ($this->link != null) {
            if (!$this->link->save($this->id))
                return false;
        }


        foreach ($this->poi as $p) {
            if (!$p->save($this->id))
                return false;
        }

        return true;
    }

     /**
     * Fetches a PoisDataset instance from database based on the dataset id
     * (Initialize object from db)
     * @param int $datasetId
     * @return PoisDataset|boolean a new PoisDataset object or false if not found
     */
    
    public static function createFromDb($datasetId) {
        $sql = "SELECT * FROM datasets WHERE id = :datasetId";
        $sqlParams = array(':datasetId' => $datasetId);
        try {
            $sth = Database::$dbh->prepare($sql);
            $sth->execute($sqlParams);

            $dataset = $sth->fetch(PDO::FETCH_ASSOC);

            if ($dataset) {
                return new PoisDataset($dataset['id'], $dataset['identifier'], $dataset['update'], $dataset['created'], $dataset['lang'], Author::createFromDb($datasetId), License::createFromDb($datasetId), Link::createFromDb($datasetId), $dataset['updateFrequency'], Poi::createListFromDb($datasetId));
            }
        } catch (Exception $e) {
            if (DEBUG)
                $sth->debugDumpParams();
            Util::throwException(__FILE__, __LINE__, __METHOD__, "select failed", $e->getMessage(), $e);
            return false;
        }
    }

    // From multiple datasetIds. We are actually only using one of the datasets of the app in
    // order to retrieve the POIS of ALL the datasets of the app. This is because until now only one
    // dataset could be used at a time. In this function, eventually, we will replace PoisDataset with AppDataset
    public static function createFromDb2($datasetIds) {
        $sql = "SELECT * FROM datasets WHERE id IN (:datasetId)";
        // Poi::createListFromDb2($datasetIds);
        $datasetIdsFormatted = implode(', ', $datasetIds);
        $sqlParams = array(':datasetId' => $datasetIdsFormatted);
        try {
            $sth = Database::$dbh->prepare($sql);
            $sth->execute($sqlParams);

            $dataset = $sth->fetch(PDO::FETCH_ASSOC);

            if ($dataset) {
                return new PoisDataset($dataset['id'], $dataset['identifier'], $dataset['update'], $dataset['created'], $dataset['lang'], Author::createFromDb($dataset['id']), License::createFromDb($dataset['id']), Link::createFromDb($dataset['id']), $dataset['updateFrequency'], Poi::createListFromDb2($datasetIds));
            }
            else
                throw(new AppGeneratorException("No dataset was found!!"));
        } catch (Exception $e) {
            if (DEBUG)
                $sth->debugDumpParams();
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
        if (isset($assocArray['id']) && isset($assocArray['updated'])) {
            $pois = array();

            foreach ($assocArray['poi'] as $key => $poi) {
                /* In case the id column doesn't exist in the json files
                 * we just autogenerate the id's
                 */
                if (is_null($poi["id"]))
                    $poi["id"] = $key;

                $pois[] = Poi::createFromArray($poi);
            }
            if (array_key_exists('url', $assocArray))
                $url = $assocArray['url'];
            else
                $url = "";
            if (array_key_exists('identifier', $assocArray))
                $identifier = $assocArray['identifier'];
            else
                $identifier = null;
            return new PoisDataset($assocArray['id'], $identifier, $assocArray['updated'], $assocArray['created'], $assocArray['lang'], Author::createFromArray($assocArray['author']), License::createFromArray($assocArray['license']), Link::createFromArray($assocArray['link']), $assocArray['updatefrequency'], $url, $pois);
        }
        return false;
    }

}

?>
