<?php

/**
 * Pois Votes
 */
class Vote {
    /*
     * Member variables are public in order 
     * to be serialized properly by json_encode
     */

    public $id;
    public $poiId;
    public $voteDate;
    public $value; // true is thumbsup, false is thumbsdown

    /**
     * Creates a new instance of the Vote object
     * @param $poiId the id of the poi
     * @param $voteDate the date when the poi was voted
     * @param $value the value of the votes for a poi
     */
    public function __construct($poiId, $voteDate, $value) {
        $this->poiId = $poiId;
        $this->voteDate = $voteDate;
        $this->value = $value;
    }

    /**
     * Saves the Vote instance to the database
     * @return true on success of false otherwise
     */
    public function save() {
        $sql = "INSERT INTO votes VALUES(null, :poi_id, :voteDate, :value)";
        $sqlParams = array(':poi_id' => $this->poiId,
            ':voteDate' => $this->voteDate,
            ':value' => $this->value);

        try {
            $sth = Database::$dbh->prepare($sql);
            $sth->execute($sqlParams);
        } catch (Exception $e) {
            if (DEBUG)
                $sth->debugDumpParams();
            Util::throwException(__FILE__, __LINE__, __METHOD__, "insert failed", $e->getMessage(), $e);
            return false;
        }
        return true;
    }

    /**
     * Fetches the positive and the negative votes for a poi from the database
     * @param $poiId the id of the poi
     * @return array[][] with the positive and the negatives votes on success of false otherwise
     */
    public static function getPoiVotes($poiId) {
        $sqlUpVotes = "SELECT COUNT(*) FROM votes WHERE poiId =  :poi_id  AND value = (1)";
        $sqlDownVotes = "SELECT COUNT(*) FROM votes WHERE poiId =  :poi_id AND value = (0)";
        $sqlParams = array(':poi_id' => $poiId);
        $upVotes = 0;
        $downVotes = 0;

        try {
            // We want the result to be an array with two items. One is the negative votes and the other is the positive votes
            $sth = Database::$dbh->prepare($sqlUpVotes);
            $sth->execute($sqlParams);

            $result[0] = $sth->fetchAll(PDO::FETCH_COLUMN, 0);
            $result[0] = $result[0][0];
            $upVotes = $result[0];
            $sth = Database::$dbh->prepare($sqlDownVotes);
            $sth->execute($sqlParams);
            $result[1] = $sth->fetchAll(PDO::FETCH_COLUMN, 0);
            $result[1] = $result[1][0];

            return $result;
        } catch (Exception $e) {
            if (DEBUG)
                $sth->debugDumpParams();
            Util::throwException(__FILE__, __LINE__, __METHOD__, "select failed", $e->getMessage(), $e);
            return false;
        }
        //return true;	
    }

    /**
     * Factory method that returns a new instance of Vote
     * @param $poiId the id of the poi
     * @param $voteDate indicates when a poi was voted
     * @param $value the value of the votes for a poi
     * @return a new Vote instance
     */
    public static function createVote($poiId, $voteDate, $value) {
        return new Vote($poiId, $voteDate, $value);
    }

}

?>