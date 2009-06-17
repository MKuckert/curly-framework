<?php

class Curly_Http_StatusCodes {
	
	/**
	 * @desc Statusmeldung für einen unbekannten Statuscode
	 */
	const STATUSMESSAGE_UNKNOWN='Unknown Statuscode';
	
	/**
	 * @desc Statuscode für eine unbekannte Statusmeldung
	 */
	const STATUSCODE_UNKNOWN=0;
	
	/**
	 * @var array Alle bekannten Statuscodes
	 */
	static private $statusCodes=array(
		100 => 'Continue',
		101 => 'Switching Protocols',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		307 => 'Temporary Redirect',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported'
	);
	
	/**
	 * Gibt die Meldung zu einem Statuscode zurück.
	 * 
	 * @return string
	 * @param integer
	 */
	static public function getMessage($code) {
		if(isset(self::$statusCodes[$code])) {
			return(self::$statusCodes[$code]);
		}
		else {
			return(self::STATUSMESSAGE_UNKNOWN);
		}
	}
	
	/**
	 * Gibt den Statuscode zu einer Meldung zurück.
	 * 
	 * @return integer
	 * @param string
	 */
	static public function getCode($msg) {
		$code=array_search($msg, self::$statusCodes);
		if($code===false) {
			return(self::STATUSCODE_UNKNOWN);
		}
		else {
			return($code);
		}
	}
	
}