<?php

include_once CLASSES . 'Link.class.php';
include_once CLASSES . 'License.class.php';
include_once CLASSES . 'Author.class.php';

/**
 * Dataset class is the base class that every dataset should extend
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
    public $url;

    /**
     * Creates a new instance of the Dataset object
     * @param int $id the dataset id
     * @param string $identifier the dataset name
     * @param string $updated the timestamp indicating when this dataset was last updated
     * @param string $created the timestamp indicating when this dataset was created
     * @param string $lang the locale code RFC 1766
     * @param Author $author the author of the dataset
     * @param License $license the license of the dataset
     * @param Link $link the link of the source of the dataset
     * @param string $updateFrequency a description of the update frequency e.g. "semester"
     * @param string $url the dataset url, where the dataset is stored
     */
    public function __construct($id, $identifier, $updated, $created, $lang, $author, $license, $link, $updateFrequency, $url) {
        $this->id = $id;
        $this->identifier = $identifier;
        $this->updated = $updated;
        $this->created = $created;
        $this->lang = $lang;
        $this->author = $author;
        $this->license = $license;
        $this->link = $link;
        $this->updateFrequency = $updateFrequency;
        $this->url = $url;
    }

    /**
     * Fetches a Category instance from database based on the dataset id
     * @param int $datasetId the id of the dataset
     * @return Category|boolean a new Category object or false if not found
     */
    public static function getCategories($datasetId) {
        $sql = "SELECT * FROM categories where dataset_id = :datasetId";
        $sqlParams[":datasetId"] = $datasetId;

        $categories = array();
        try {
            $sth = Database::$dbh->prepare($sql);
            $sth->execute($sqlParams);
            include_once CLASSES . 'Filter.class.php';
            while ($result = $sth->fetch(PDO::FETCH_ASSOC)) {
                $categories[] = new Filter($result['text'], $result['default'] == 1);
            }
        } catch (Exception $e) {
            if (DEBUG)
                $sth->debugDumpParams();
            Util::throwException(__FILE__, __LINE__, __METHOD__, "select from categories failed", $e->getMessage(), $e);
        }
        return $categories;
    }

     /**
     * Fetches the number of datasets per user from database based on the user id
     * @param int $userId the id of the user
     * @return int the number of datasets per user
     */
    public static function getDatasetsOfUser($userId) {
        $sql = "SELECT * FROM datasets where createdBy = :userId";
        $sqlParams[":userId"] = $userId;
        $userDatasetsCount;
        try {
            $sth = Database::$dbh->prepare($sql);
            $sth->execute($sqlParams);
            $userDatasetsCount = $sth->rowCount();
            return $userDatasetsCount;
        } catch (Exception $e) {
            if (DEBUG)
                $sth->debugDumpParams();
            Util::throwException(__FILE__, __LINE__, __METHOD__, "getDatasetsOfUser query failed", $e->getMessage(), $e);
        }
    }

    /**
     * Saves the Dataset instance to the database
     * @param string $datasetName the dataset name
     * @param string $url the dataset url
     * @param string $type the dataset type
     * @param int $city the city id
     * @param int $userId the user id
     * @return true on success of false otherwise
     */
    public static function saveNewDataset($datasetName, $url, $type, $city, $userId) {

        $sql = "INSERT INTO datasets VALUES(null, :identifier, :type , :update, Now(),  :userId, :lang, :updateFrequency, :url)";
        $sqlParams = array(':identifier' => $datasetName,
            ':type' => $type,
            ':update' => '',
            ':lang' => '',
            ':updateFrequency' => '',
            ':url' => $url,
            ':userId' => $userId);


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
        $id = Database::$dbh->lastInsertId();

        $sql2 = "INSERT INTO city_datasets VALUES(null, :city_id, :dataset_id)";
        $sqlParams2 = array(':city_id' => $city,
            ':dataset_id' => $id);

        try {
            $sth2 = Database::$dbh->prepare($sql2);
            $sth2->execute($sqlParams2);
        } catch (Exception $e) {
            if (DEBUG)
                $sth2->debugDumpParams();
            Util::throwException(__FILE__, __LINE__, __METHOD__, "insert into city_datasets failed", $e->getMessage(), $e);
            return false;
        }
    }

}

?>