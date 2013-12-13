<?php

include_once '../Config.php';
include_once CLASSES . 'Database.class.php';
include_once CLASSES . 'Util.class.php';


$city = '1';
//$id = '45';
        
    $datasetName = "vaggelas";
    $url = "http://vaggelas";
//    
        Database::connect();
//        
//        
        $sql = "INSERT INTO datasets VALUES(null, :identifier, :type , :update, :created, :lang, :updateFrequency, :url)";
        $sqlParams = array(':identifier' =>  $datasetName,
            ':type' => '1',
            ':update' => '',
            ':created' => '',
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

            
            echo $id;
            
            
        $sql= "INSERT INTO city_datasets VALUES(null, :city_id, :dataset_id)";
        $sqlParams = array(':city_id' => $city,
            ':dataset_id' =>$id);


        try {
            $sth = Database::$dbh->prepare($sql);
            $sth->execute($sqlParams);
        } catch (Exception $e) {
            if (DEBUG)
                $sth->debugDumpParams();
            Util::throwException(__FILE__, __LINE__, __METHOD__, "insert into city_datasets failed", $e->getMessage(), $e);
            return false;
        }
            
             Database::disconnect();
           //return $id;
     //   }
?>
