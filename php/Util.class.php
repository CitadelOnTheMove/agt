<?php

class Util {

    public static function throwException($file, $line, $method, $message, $cause = NULL, $previous = NULL) {
        $msg = basename($file) . "[line: $line]";
        if (strlen($method))
            $msg .= " at $method";
        $msg .= " --> $message";
        if ($cause && strlen($cause))
            $msg .= " | $cause";
        throw new Exception($msg, 0, $previous);
    }

    public static function startsWith($haystack, $needle) {
        return strpos($haystack, $needle) === 0;
    }

    public static function endsWith($haystack, $needle) {
        return substr($haystack, -strlen($needle)) == $needle;
    }

    public static function hasRequestVar($key) {
        return isset($_GET[$key]) && !empty($_GET[$key]);
    }

    public static function hasCookie($key) {
        return isset($_COOKIE[$key]) && !empty($_COOKIE[$key]);
    }

    public static function pathInFileSystem($subdir, $file = "") {
        return HTDOCS_ROOT . BASE_DIR . $subdir . $file;
    }

    public static function pathInDocumentRoot($subdir, $file = "") {
        return "/" . BASE_DIR . $subdir . $file;
    }

    public static function printJsonObj($obj) {
// 		header('Content-type: application/json; charset=utf-8');
        echo json_encode($obj);
    }
    
    public static function clear_input($data) {
      $data = trim($data);
      $data = stripslashes($data);
      $data = htmlspecialchars($data);
      return $data;
  }

    public static function getNextPoiID() {

        if (!Database::isConnected())
            Database::connect();
        $sql = "SHOW TABLE STATUS WHERE name = 'pois'";

        try {
            $sth = Database::$dbh->prepare($sql);
            $sth->execute();
            $result = $sth->fetch(PDO::FETCH_ASSOC);
            return $result['Auto_increment'];
        } catch (Exception $e) {
            if (DEBUG)
                $sth->debugDumpParams();
            Util::throwException(__FILE__, __LINE__, __METHOD__, "get next increment from pois", $e->getMessage(), $e);
        }
    }

}

?>