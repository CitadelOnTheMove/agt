<?php

class Util {

	public static function throwException($file, $line, $method, $message, $cause = NULL, $previous = NULL) {
		$msg = basename($file)."[line: $line]";
		if (strlen($method)) $msg .= " at $method";
		$msg .= " --> $message";
		if ($cause && strlen($cause)) $msg .= " | $cause";
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
}

?>