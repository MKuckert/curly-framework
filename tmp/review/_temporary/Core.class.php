<?php
if( !defined( "_CORE_" ) ) die( "Unauthorisierter Zugriff!" );

if( !defined( "_CLASS_CORE_" ) ) {
	define( "_CLASS_CORE_", 1 );
	
	/**
	 * Kernklasse
	 * 
	 * @since 0.01 - 24.01.2007
	 */
	class Core {
		
		/**
		 * Konfigurationsobjekt
		 * Über dieses Objekt werden die Systemvariablen geladen.
		 * 
		 * @type Configuration
		 * @since 0.01 - 24.01.2007
		 */
		static public $configuration = null;
		
		/**
		 * Templateengine
		 * Ersetzt Templates durch konkrete Skripte
		 * 
		 * @type Templateengine
		 * @since 0.02 - 21.02.2007
		 */
		static public $templateengine = null;
		
		/**
		 * Flag, ob bereits ein Activitylog geschrieben wurde
		 * 
		 * @type bool
		 * @since 0.03 - 14.04.2007
		 */
		static private $logged = false;
		
		/**
		 * Fehlerlog
		 * 
		 * @type ErrorLog
		 * @since 0.20 - 15.07.2007
		 */
		static public $log = null;
		
		/**
		 * Initialisiert den Kern
		 * Stellt Datenbank-Objekt her und startet Session
		 * Setzt destroy() als shutdown-Funktion ein
		 * Deaktiviert magic_quotes und macht diese rückgängig
		 * 
		 * @return void
		 * @since 0.01 - 24.01.2007
		 */
		static public function init() {
			error_reporting( E_ALL );
			static $called=false;
			if( $called==false ) {
				$called = true;
			}
			else {
				die( "Der Kern darf nicht doppelt initialisiert werden!" );
			}
			
			// weitere Konstanten definieren
			define( "NOW", time() );
			define( "NOW_MICRO", microtime( true ) );
			define( "CLIENT_IP_ADDRESS", isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '' );
			$protocol = isset( $_SERVER["SERVER_PROTOCOL"] ) ? ( stripos( $_SERVER["SERVER_PROTOCOL"], "HTTPS" )===false ? 'http' : 'https' ) : '';
			define( "CONNECTION_PROTOCOL", $protocol );
			unset( $protocol );
			define( "REQUEST_URI", isset( $_SERVER["REQUEST_URI"] ) ? $_SERVER["REQUEST_URI"] : '' );
			
			if( CORE_ERRORLOG ) {
				self::$log = new ErrorLog( ROOT.CORE_ERRORLOG, true );
			}
			else {
				self::$log = new ErrorLog( '' );
			}
			
			// Konfiguration laden
			if( isset( $_SESSION['_CACHE'], $_SESSION['_CACHE']['configurations'] ) ) {
				self::$configuration = $_SESSION['_CACHE']['configurations'];
			}
			else {
				self::$configuration = Configuration::loadFile( "core/include/core.configurations.php" );
			}
			
			// Templateengine initialisieren
			if( self::$configuration->use_corecache and
				isset( $_SESSION['_CACHE'], $_SESSION['_CACHE']['templateengine'] ) ) {
				self::$templateengine = $_SESSION['_CACHE']['templateengine'];
			}
			else {
				$class = self::$configuration->templateengine_classname;
				self::$templateengine = new $class( 'templates/' );
				self::$templateengine->setVar( 'ROOT', ROOT );
				self::$templateengine->setVar( 'WEBURL', WEBURL );
			}
			
			// ClassLoader initialisieren
			if( isset( $_SESSION['_CACHE'], $_SESSION['_CACHE']['classloader'] ) ) {
				$classes = $_SESSION['_CACHE']['classloader'];
			}
			else {
				$classes = FileObject::loadIni( ROOT.'core/include/core.classloader.php' );
			}
			ClassLoader::addClasses( $classes );
			
			// URL-Rewrite wieder übernehmen
			if( isset( self::$configuration->rewrite_url ) and self::$configuration->rewrite_url ) {
				durl();
			}
			
			// Wörterbuch laden
			$lang = '';
			// GET gesetzt?
			if( isset( $_GET['locale'] ) and Wordbook::languageExists( $_GET['locale'] ) ) {
				$lang = $_GET['locale'];
			}
			// Session gesetzt?
			else if( isset( $_SESSION['locale'] ) and Wordbook::languageExists( $_SESSION['locale'] ) ) {
				$lang = $_SESSION['locale'];
			}
			// Browsereinstellungen wählen
			else if( isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) and trim( $_SERVER['HTTP_ACCEPT_LANGUAGE'] )!='' ) {
				$accept = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
				$accept = str_replace( ',', ';', $accept );
				
				$explode = explode( ";", $accept );
				foreach( $explode as $l ) {
					if( Wordbook::languageExists( $l ) ) {
						$lang = $l;
						break;
					}
				}
			}
			
			// Standardsprache wählen
			if( $lang=='' ) {
				$locale = self::$configuration->locale;
				
				$langs = explode( '|', $locale );
				foreach( $langs as $l ) {
					if( Wordbook::languageExists( $l ) ) {
						$lang = $l;
						break;
					}
				}
			}
			
			// Konnte Sprachdatei nicht finden
			if( $lang=='' ) {
				self::raiseError( "No Languagefile found!" );
			}
			
			// Konnte Sprachdatei nicht laden
			if( !Wordbook::loadLanguage( $lang, 'std' ) ) {
				self::raiseError( "Unable to load Languagefile!" );
			}
			$_SESSION['locale'] = $lang;
			define( 'LANG', $lang );
			
			// Lokale Einstellungen setzen
			setlocale( LC_ALL, explode( "|", self::$configuration->locale ) );
			
			// Zeitzone setzen
			date_default_timezone_set( self::$configuration->timezone );
			
			// Session starten
			session_name( CORE_SESSIONNAME );
			session_cache_expire( CORE_SESSIONEXPIRE );
			session_start();
			
			// Aktuellen Benutzer ermitteln
			if( isset( $_SESSION['timestamp'] ) and ( $_SESSION['timestamp'] + CORE_SESSIONEXPIRE ) < time() ) {
				if( isset( $_SESSION['user'] ) ) {
					$_SESSION['user']['stat'] = 'loggedoff';
				}
				$_SESSION['logged_in'] = false;
			}
			$_SESSION['timestamp'] = time();
			
			if( !isset( $_SESSION['user'] ) or !is_array( $_SESSION['user'] ) ) {
				$_SESSION['user'] = Userprofil::getPrototype();
				$_SESSION['logged_in'] = false;
			}
			Userprofil::setCurrentUser( new Userprofil( $_SESSION['user'] ) );
			
			// Session-Cookie nicht angenommen
			if( isset( $_GET['login'] ) and $_GET['login']==1 and !isset( $_COOKIE[session_name()] ) ) {
				$_SESSION['cookie_accept'] = false;
			}
			if( !isset( $_SESSION['cookie_accept'] ) ) {
				$_SESSION['cookie_accept'] = true;
			}
			
			// destroy als Shutdownfunktion
			register_shutdown_function( array ( 'Core', 'destroy' ) );
			
			// magic_quotes entfernen
			if( get_magic_quotes_gpc() ) {
				function array_stripslashes( &$var ) {
					if(is_string($var)) {
						$var = stripslashes( $var );
					}
					else if( is_array( $var ) ) {
						foreach( $var as &$value ) {
							array_stripslashes( $value );
						}
					}
				}
				array_stripslashes( $_GET );
				array_stripslashes( $_POST );
				array_stripslashes( $_COOKIE );
			}
			
			// Fehlerausgaben einstellen
			self::setErrorReporting( CORE_ERRORREPORTING );
			
			// errorhandle anmelden
			set_error_handler( array( 'Core', 'errorhandle' ) );
			
			// exceptionhandle anmelden
			set_exception_handler( array( 'Core', 'exceptionhandle' ) );
			
			// Error-Array einrichten
			$GLOBALS['error'] = array();
			
			// Nachrichten-Array einrichten
			$GLOBALS['message'] = array();
			
			// Wenn Merker-Cookie gesetzt ist, Benutzer anmelden
			if( isset( $_COOKIE['userlogin_remember'] ) and !isLoggedIn()
				and self::$me->id!=intval( $_COOKIE['userlogin_remember'] )
			) {
				$tmp = Usermanager::getUser( intval( $_COOKIE['userlogin_remember'] ) );
				if( $tmp instanceof Userprofil ) {
					Userprofil::setCurrentUser( $tmp );
				}
				unset( $tmp );
			}
			
			// Template-Referenzen anhängen
			self::$templateengine->addReference( '_POST', $_POST );
			self::$templateengine->addReference( '_GET', $_GET );
			self::$templateengine->addReference( '_REQUEST', $_REQUEST );
			self::$templateengine->addReference( '_COOKIE', $_COOKIE );
			self::$templateengine->addReference( '_SESSION', $_SESSION );
			self::$templateengine->addReference( '_SERVER', $_SERVER );
			self::$templateengine->addReference( 'error', $GLOBALS['error'] );
			self::$templateengine->addReference( 'message', $GLOBALS['message'] );
			
			// Module nachladen
			if( isset( $_SESSION['_CACHE'], $_SESSION['_CACHE']['moduleloader'] ) ) {
				$modules = $_SESSION['_CACHE']['moduleloader'];
			}
			else {
				$modules = FileObject::loadList( ROOT.'core/include/core.modules.php' );
			}
			ModuleLoader::loadModules( $modules );
			
			// Cache
			if( self::$configuration->use_corecache ) {
				$_SESSION['_CACHE']['classloader'] = ClassLoader::getClasses();
				$_SESSION['_CACHE']['configurations'] = self::$configuration;
				$_SESSION['_CACHE']['moduleloader'] = $modules;
			}
			else {
				$_SESSION['_CACHE'] = array();
			}
		}
		
		/**
		 * Zerstört den Kern
		 * beendet Datenbankverbindung und die Session
		 * 
		 * @return void
		 * @since 0.01 - 24.01.2007
		 */
		static public function destroy() {
			$me = Userprofil::getCurrentUser();
			// aktuelles Benutzerprofil gesetzt
			// Benutzer-ID ungleich 0 -> kein Gast
			if( $me['id']!=0 ) {
				$me['last_activity'] = Database::instance()->getDateTime( time() );
				$me->store();
			}
			// Benutzer in Session speichern
			$_SESSION['user'] = $me->getArrayCopy();
			
			// Aktivitätenlog?
			if( !self::$logged ) {
				self::log( 'autolog' );
			}
		}
		
		/**
		 * Funktionshandle für das Fangen von Fehlern und ersetzen durch eigene Fehlertexte.
		 * 
		 * @return void
		 * @param int		type	Fehlertype (E_*-Konstante)
		 * @param string	msg		Fehlertext
		 * @param string	file	Datei, in der der Fehler auftrat
		 * @param int		line	Dateizeile, in der der Fehler auftrat
		 * @since 0.01 - 24.01.2007
		 */
		static public function errorhandle( $type, $msg, $file, $line ) {
			static $etypes = array(
				E_WARNING		=> 'Warnung',
				E_NOTICE		=> 'Hinweis',
				E_USER_ERROR	=> 'Fehler',
				E_USER_WARNING	=> 'Warnung',
				E_USER_NOTICE	=> 'Hinweis',
				E_STRICT		=> 'Scharfe Kontrolle'
			);
			
			
			
			$typestr = isset( $etypes[$type] ) ? $etypes[$type] : 'Fehler';
			
			self::$log->error( $msg, $line, $file, $type );
			
			if( error_reporting()>0 and $type & error_reporting() ) {
				echo "<br /><b>".$typestr.":</b> ".$msg." in ".$file." on line ".$line."<br />";
			}
		}
		
		/**
		 * Funktionshandle für das Fangen von ungefangenen Ausnahmen
		 * 
		 * @return void
		 * @param int		num		Fehlernummer (E_*-Konstante)
		 * @param string	msg		Fehlertext
		 * @param string	file	Datei, in der der Fehler auftrat
		 * @param int		line	Dateizeile, in der der Fehler auftrat
		 * @since 0.10 - 25.04.2007
		 */
		static public function exceptionhandle( $exception ) {
			ob_start();
			var_dump( $exception );
			$con = ob_get_contents();
			ob_end_clean();
			
			self::$log->exception( $exception->getMessage(), $exception->getLine(), $exception->getFile(), get_class( $exception ) );
			self::raiseErrorMail( $exception->getMessage()."\n".$con, Wordbook::getMessage( 'ERROR_UNCAUGHT_EXCEPTION' ) );
		}
		
		/**
		 * Kodiert den übergebenen String als Einweg-Hash.
		 * Es wird das "Salz" aus der Konfiguration verwendet.
		 * 
		 * @return string			Das verschlüsselte Passwort
		 * @param string	string	Der zu verschlüsselnde String
		 * @since 0.01 - 24.01.2007
		 */
		static public function encodePassword( $string ) {
			$salt = self::$configuration->pw_salt;
			$saltLen = strlen( $salt );
			$strLen = strlen( $string );
			$res = '';
			for( $i=0; $i<$strLen; $i++ ) {
				$res .= substr( $string, $i, 1 );
				$res .= substr( $salt, $i%$saltLen, 1 );
			}
			if( $saltLen>$strLen ) {
				$res .= substr( $salt, $saltLen );
			}
			return( self::hash( base64_encode( $res . $saltLen . $strLen . strrev( $res ) ), self::$configuration->password_encryption_hash ) );
		}
		
		/**
		 * Erzeugt einen zufälligen String
		 * 
		 * @return string				Ein zufälliger String
		 * @param int		len			Länge des zu erzeugenden String
		 * @param string	useLetter	Nur diese Buchstaben werden verwendet
		 * @since 0.01 - 24.01.2007
		 */
		static public function createRandString( $len, $useLetter = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789' ) {
			$max = strlen( $useLetter )-1;
			$str = '';
			for( $i=0; $i<$len; $i++ ) {
				$str .= substr( $useLetter, rand( 0, $max ), 1 );
			}
			return( $str );
		}
		
		/**
		 * Setzt den Wert für das melden von Fehlern.
		 * 
		 * @return void
		 * @param mixed		arg	Zu setzender Wert
		 * @since 0.01 - 24.01.2007
		 */
		static public function setErrorReporting( $arg ) {
			if( is_int( $arg ) or strval( intval( $arg ) )==$arg ) {
				error_reporting( intval( $arg ) );
			}
			else {
				switch( strtolower( $arg ) ) {
					case 'error':
					case 'e_error':
						$arg = E_ERROR;
						break;
					case 'warning':
					case 'e_warning':
						$arg = E_WARNING;
						break;
					case 'parse':
					case 'e_parse':
						$arg = E_PARSE;
						break;
					case 'notice':
					case 'e_notice':
						$arg = E_NOTICE;
						break;
					case 'core_error':
					case 'e_core_error':
						$arg = E_CORE_ERROR;
						break;
					case 'core_warning':
					case 'e_core_warning':
						$arg = E_CORE_WARNING;
						break;
					case 'compile_error':
					case 'e_compile_error':
						$arg = E_COMPILE_ERROR;
						break;
					case 'compile_warning':
					case 'e_compile_warning':
						$arg = E_COMPILE_WARNING;
						break;
					case 'user_error':
					case 'e_user_error':
						$arg = E_USER_ERROR;
						break;
					case 'user_warning':
					case 'e_user_warning':
						$arg = E_USER_WARNING;
						break;
					case 'user_notice':
					case 'e_user_notice':
						$arg = E_USER_NOTICE;
						break;
					case 'all':
					case 'e_all':
						$arg = E_ALL;
						break;
					case 'strict':
					case 'e_strict':
						$arg = E_STRICT;
						break;
					default:
						$arg = eval( 'return '.$arg.';' );
						break;
				}
				error_reporting( intval( $arg ) );
			}
		}
		
		/**
		 * Gibt den Wert für das melden von Fehlern zurück.
		 * 
		 * @return int
		 * @since 0.20 - 18.07.2007
		 */
		static public function getErrorReporting() {
			return( error_reporting() );
		}
		
		/**
		 * Gibt eine Fehlermeldung aus und stoppt sofort die Skriptausführung
		 * @return void
		 * @param string 	msg	Fehlermeldung
		 * @since 0.01 - 24.01.2007
		 */
		static public function raiseError( $msg ) {
			self::$log->error( 'USER('.CLIENT_IP_ADDRESS.') '.$msg );
			die( '
<div style="width:600px;margin:100px auto 0;background:#DDD;border:1px solid #AAA;font:10pt Verdana, Arial, Helvetica, sans-serif;">
	<div style="font-weight:bold;background:#900;color:#DDD;border-bottom:1px solid #AAA;width:590px;padding:0 5px;">Fehler:</div>
	<div style="width:586px;padding:10px 7px;color:#900;">'.$msg.'</div>
	<div style="font-weight:bold;background:#900;color:#DDD;border-top:1px solid #AAA;width:590px;padding:0 5px;">Die Skriptverarbeitung wurde abgebrochen!</div>
</div>' );
		}
		
		/**
		 * Führt eine Weiterleitung aus
		 * 
		 * @return void
		 * @param string	to		Zieladresse
		 * @since 0.01 - 24.01.2007
		 */
		static public function redirect( $to ) {
			header( 'Location: '.$to );
			exit;
		}
		
		/**
		 * Ersetzt bestimmte Muster im übergebenen String durch Werte von
		 * Kern-Konstanten ( Bsp: {ROOT} wird zum Inhalt der Konstanten ROOT )
		 * 
		 * @return void
		 * @param mixed		dat		Array oder String mit zu ersetzenden Daten
		 * @since 0.01 - 24.01.2007
		 */
		static public function replaceVars( $dat ) {
			$me = Userprofil::getCurrentUser();
			if( $me instanceof Userprofil ) {
				$id = $me->id;
			}
			else {
				$id = 0;
			}
			
			$search = array(
				'{ROOT}', '{WEBURL}', '{CORE_DB_PREFIX}', '{CORE_MAILADDRESS}', '{CUR_USER_ID}', '{SID}'
			);
			$replace = array(
				ROOT, WEBURL, CORE_DB_PREFIX, CORE_MAILADDRESS, $id, Templatehelper::sid()
			);
			return( str_replace( $search, $replace, $dat ) );
		}
		
		/**
		 * Gibt eine Fehlermeldung aus, sendet eine Fehlermeldung mit Variablenaussehen
		 * an den Admin und stoppt sofort die Skriptausführung
		 * 
		 * @return void
		 * @param string 	mailmsg		Fehlermeldung in der Mail
		 * @param string	clientmsg	Fehlermeldung zum Ausgeben am Browser
		 * @since 0.01 - 24.01.2007
		 */
		static public function raiseErrorMail( $mailmsg, $clientmsg ) {
			ob_start();
			var_dump( $GLOBALS );
			$globals = ob_get_contents();
			ob_end_clean();
			
			// msg bauen
			$msg = date( "d.m.Y - H:i:s" )."
".$mailmsg."

Fehlerbericht

==== Datenbankkonstanten ====
Datenbanktyp:   ".CORE_DB_TYPE."
Host:           ".CORE_DB_HOST."
Benutzer:       ".CORE_DB_USER."
Password:		".( CORE_DB_PW != '' ? "ja" : "nein" )."
Datenbank:		".CORE_DB_DATABASE."
Prefix:			".CORE_DB_PREFIX."

==== Clientdaten ====
Browser:        ".$_SERVER['HTTP_USER_AGENT']."
IP-Adresse:     ".$_SERVER['REMOTE_ADDR']."
Zugriffmethode: ".$_SERVER['REQUEST_METHOD']."
Aufrufstring:   ".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."
".( isset( $_SERVER['HTTP_VIA'] ) ? 'Proxy:          '.$_SERVER['HTTP_VIA'] : '' ).
"

==== Ausgabe des globalen Variablenbereichs ====
".$globals."

========
Diese Nachricht wurde automatisch generiert. Bitte antworten Sie nicht auf diese Nachricht!";
			// msg gebaut
			
			// header bauen
			$header = 'From: errorreport@' . WEBURL . "\r\n" .
			   'Reply-To: ' . CORE_MAILADDRESS . "\r\n" .
			   'Content-Type: text/plain' . "\r\n" .
			   'X-Mailer: PHP/' . phpversion();
			
			if( _CORE_==-1 ) {
				echo "<pre>".$header."\n".$msg."</pre>";
			}
			else {
				mail( CORE_MAILADDRESS, 'Fehlerbericht', $msg, $header );
			}
			self::raiseError( $clientmsg );
		}
		
		/**
		 * Gibt eine Fehlermeldung aus, dass der Benutzer keine nötigen Rechte für
		 * diesen Bereich besitzt und stoppt sofort die Skriptausführung
		 * 
		 * @return void
		 * @since 0.01 - 24.01.2007
		 */
		static public function raiseNoAuthError() {
			self::$log->error( 'NOT AUTH '.CLIENT_IP_ADDRESS, -1, REQUEST_URI );
			die( '
<br /><span style="font-weight:bold;">'.Wordbook::getMessage( 'ERROR_NOTAUTH' ).'</span>' );
		}
		
		/**
		 * Filtert alle unerlaubten Einträge aus einem Array heraus
		 * 
		 * @return array			Array mit den gelöschten Einträgen
		 * @param array&			Das zu filternde Array (nach dieses Methode nur noch erlaubte Einträge)
		 * @param array				Array mit den erlaubten Schlüsseln
		 * @param bool				true, wenn die Kernschlüssel erhalten bleiben sollen
		 * @since 0.03 - 10.04.2007
		 */
		static public function filterArray( &$filter, $allowed, $allowCoreKeys=false ) {
			$coreKeys = array( CORE_SESSIONNAME );
			$erased = array();
			if( $allowCoreKeys ) {
				$allowed = array_merge( $allowed, $coreKeys );
			}
			
			foreach( $filter as $key=>$val ) {
				if( !in_array( $key, $allowed, true ) ) {
					$erased[$key] = $val;
					unset( $filter[$key] );
				}
			}
			
			return( $erased );
		}
		
		/**
		 * Fügt eine Aktion zum Aktivitätenlog hinzu (wenn dies aktiviert ist)
		 * 
		 * @return bool
		 * @param string		Typ des Logs
		 * @param string		Weitere Infodaten zum Log
		 * @since 0.03 - 14.04.2007
		 */
		static public function log( $type, $parameter=null ) {
			if( !CORE_ACTIVITYLOG ) {
				return( true );
			}
			
			self::$logged = true;
			try {
				$db = Database::instance();
				$query = "INSERT INTO PRE_activities ".
						 "SET user_id=".( self::$me instanceof Userprofil ? self::$me->id : self::$me ).", ".
						 "    type='".$db->escape( $type )."', ".
						 ( is_null( $parameter ) ? '' : " parameter='".$db->escape( $parameter )."', " ).
						 "    url='".$db->escape( REQUEST_URI )."', ".
						 "    created='".$db->getDateTime( NOW )."'";
				$db->setQuery( $query );
				return( $db->execute() );
			}
			catch( Exception $ex ) {
				return( false );
			}
		}
		
		/**
		 * Erzeugt einen Hash mit einem bestimmten Verfahren
		 * 
		 * @return string			Der Hash (oder null bei Fehlern)
		 * @param string			Der zu hashende String
		 * @param string			Hashverfahren (bsp: MD5, SHA1)
		 * @since 0.03 - 18.04.2007
		 */
		static public function hash( $str, $method ) {
			$method = strtolower( $method );
			if( function_exists( 'hash' ) ) {
				$hashs = hash_algos();
				// Hash-Algorithmus existiert nicht
				if( !in_array( $method, $hashs ) ) {
					return( null );
				}
				
				return( hash( $method, $str ) );
			}
			else if( function_exists( $method ) ) {
				return( $method( $str ) );
			}
			else {
				self::$log->notice( 'Hash '.$method.' does not exist!', __LINE__, __FILE__ );
				return( null );
			}
		}
		
		/**
		 * Gibt eine Umgebungsvariable oder einen Defaultwert zurück
		 * 
		 * @return mixed
		 * @param string			Name der Umgebungsvariable
		 * @param mixed				Defaultwert
		 * @since 0.10 - 25.04.2007
		 */
		static public function getParam( $key, $default ) {
			if( isset( $_ENV[$key] ) ) {
				return( $_ENV[$key] );
			}
			else if( isset( $_SERVER[$key] ) ) {
				return( $_SERVER[$key] );
			}
			else {
				return( $default );
			}
		}
	};
	
}	
?>