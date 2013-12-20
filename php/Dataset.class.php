<?php

include_once CLASSES . 'Link.class.php';
include_once CLASSES . 'License.class.php';
include_once CLASSES . 'Author.class.php';

/**
 * Dataset class is tha base class that every dataset should extend
 */
class Dataset {
    /*
     * Member variables are public in order
     * to be serialized properly by json_encode
     */

    public $id;
    public $updated;
    public $created;
    public $lang;
    public $author;
    public $license;
    public $link;
    public $updateFrequency;
    public $url;

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
    public function __construct($id, $updated, $created, $lang, $author, $license, $link, $updateFrequency, $url) {
        $this->id = $id;
        $this->updated = $updated;
        $this->created = $created;
        $this->lang = $lang;
        $this->author = $author;
        $this->license = $license;
        $this->link = $link;
        $this->updateFrequency = $updateFrequency;
        $this->url = $url;
    }

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

    public static function saveNewDataset($datasetName, $url, $type, $city) {

        $sql = "INSERT INTO datasets VALUES(null, :identifier, :type , :update, Now(), :lang, :updateFrequency, :url)";
        $sqlParams = array(':identifier' => $datasetName,
            ':type' => $type,
            ':update' => '',
            ':lang' => '',
            ':updateFrequency' => '',
            ':url' => $url);


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


        $sql = "INSERT INTO city_datasets VALUES(null, :city_id, :dataset_id)";
        $sqlParams = array(':city_id' => $city,
            ':dataset_id' => $id);


        try {
            $sth = Database::$dbh->prepare($sql);
            $sth->execute($sqlParams);
        } catch (Exception $e) {
            if (DEBUG)
                $sth->debugDumpParams();
            Util::throwException(__FILE__, __LINE__, __METHOD__, "insert into city_datasets failed", $e->getMessage(), $e);
            return false;
        }
    }
}

?>