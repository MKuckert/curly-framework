<?php
	
	/**
	 * HttpClient
	 * Stellt einen HttpClient dar. Mit Hilfe einer Instanz dieser Klasse kann direkt
	 * über HTTP mit einem entfernten Server kommuniziert werden.
	 * 
	 * @author Martin
	 */
	class HttpClient extends Object {
		
		/**#@+
		 * Konstanten für die einzelnen Request-Methoden.
		 */
		/**
		 * Zeigt an, dass die GET-Methode verwendet werden soll.
		 */
		const MethodGet = 1;
		
		/**
		 * Zeigt an, dass die POST-Methode verwendet werden soll.
		 */
		const MethodPost = 2;
		
		/**
		 * Zeigt an, dass die HEAD-Methode verwendet werden soll.
		 */
		const MethodHead = 3;
		
		/**#@-*/
		
		/**
		 * Maximale Zeichenanzahl in einer Zeile des Response.
		 */
		const READ_PER_LINE = 4096;
		
		/**
		 * @var string Der Host, an den der Request gesendet werden soll.
		 */
		private $host = '';
		public function getHost() {
			return( $this->host );
		}
		public function setHost( $host ) {
			$this->host = strval( $host );
		}
		
		/**
		 * @var integer Der zu verwendende Port für den Request.
		 */
		private $port = 80;
		public function getPort() {
			return( $this->port );
		}
		public function setPort( $port ) {
			$this->port = intval( $port );
		}
		
		/**
		 * @var string Der zu verwendende Pfad auf dem Webserver.
		 */
		private $path = '/';
		public function getPath() {
			return( $this->path );
		}
		public function setPath( $path ) {
			$this->path = strval( $path );
		}
		
		/**
		 * @var integer Die zu verwendende Anfragemethode. Eine der Method*-Konstanten.
		 */
		private $method = self::MethodGet;
		public function getMethod() {
			return( $this->method );
		}
		public function setMethod( $method ) {
			if( $method<self::MethodGet or $method>self::MethodHead ) {
				throw new InvalidArgumentException( 'The method has to be a value of one of the method constants' );
			}
			$this->method = intval( $method );
		}
		
		/**
		 * Gibt die Übertragungsmethode für den Request als Zeichenkette zurück.
		 * 
		 * @return string
		 */
		public function getMethodAsString() {
			switch( $this->getMethod() ) {
				case self::MethodPost:
					return( "POST" );
				case self::MethodHead:
					return( "HEAD" );
				default:
					return( "GET" );
			}
		}
		
		/**
		 * @var mixed Die zu verschickenden POST-Daten. Entweder ein Array, welches URL-Encodiert
		 * gesendet wird, oder rohe Daten als String, die direkt gesendet werden.
		 */
		private $postdata = '';
		public function getPostdata() {
			return( $this->postdata );
		}
		public function setPostdata( $postdata ) {
			if( is_string( $postdata ) ) {
				$this->postdata = $postdata;
			}
			else if( $postdata instanceof Collection ) {
				$this->postdata = $postdata;
			}
			else if( is_array( $postdata ) or $postdata instanceof Traversable ) {
				$this->postdata = new Collection( $postdata );
			}
			else {
				throw new InvalidArgumentException( 'The postdata has to be a string or an object of type Collection' );
			}
		}
		public function getPostdataAsString() {
			$postdata = $this->getPostdata();
			if( is_string( $postdata ) ) {
				return( $postdata );
			}
			else {
				$query = array();
				foreach( $postdata as $key=>$value ) {
					$key = urlencode( $key );
					if( is_array( $value ) ) {
						// Nur fortlaufende nummerische Indizes
						if( array_keys( $value )==range( 0, count( $value )-1 ) ) {
							foreach( $value as $subkey=>$subvalue ) {
								$query[] = $key.'[]='.urlencode( $subvalue );
							}
						}
						// Assoziatives Array
						else {
							foreach( $value as $subkey=>$subvalue ) {
								$query[] = $key.'['.$subkey.']='.urlencode( $subvalue );
							}
						}
					}
					else {
						$query[] = $key.'='.urlencode( $value );
					}
				}
				$query = implode( '&', $query );
				$this->postdata = $query;
				return( $query );
			}
		}
		
		/**
		 * @var mixed Die zu verschickenden GET-Daten. Entweder ein Array, welches URL-Encodiert
		 * gesendet wird, oder rohe Daten als String, die direkt gesendet werden.
		 */
		private $getdata = '';
		public function getGetdata() {
			return( $this->getdata );
		}
		public function setGetdata( $getdata ) {
			if( is_string( $getdata ) ) {
				$this->getdata = $getdata;
			}
			else if( $getdata instanceof Collection ) {
				$this->getdata = $getdata;
			}
			else if( is_array( $getdata ) or $getdata instanceof Traversable ) {
				$this->getdata = new Collection( $getdata );
			}
			else {
				throw new InvalidArgumentException( 'The getdata has to be a string or an object of type Collection' );
			}
		}
		
		/**
		 * @var Collection Zu sendende oder bereits empfangene Cookies.
		 */
		private $cookies = null;
		public function getCookies() {
			if( is_null( $this->cookies ) ) {
				$this->cookies = new Collection();
			}
			return( $this->cookies );
		}
		
		/**
		 * @var string Speichert den Referer-Header, um diesen bei mehreren Requests mitzusenden.
		 */
		private $referer = '';
		public function getReferer() {
			return( $this->referer );
		}
		public function setReferer( $referer ) {
			$this->referer = strval( $referer );
		}
		
		/**
		 * @var Collection Gültige MIME-Typen, die als Antwort akzeptiert werden.
		 */
		private $accept = null;
		public function getAccept() {
			if( is_null( $this->accept ) ) {
				$this->accept = new Collection(
					array(
						'text/xml',
						'application/xml',
						'application/xhtml+xml',
						'text/html',
						'text/plain',
						'image/png',
						'image/jpeg',
						'image/gif',
						'*/*'
					)
				);
			}
			return( $this->accept );
		}
		
		/**
		 * @var Collection Gültige Codierungen, mit denen die Antwort komprimiert werden kann.
		 */
		private $acceptEncoding = null;
		public function getAcceptEncoding() {
			if( is_null( $this->acceptEncoding ) ) {
				$this->acceptEncoding = new Collection(
					array(
						'gzip'
					)
				);
			}
			return( $this->acceptEncoding );
		}
		
		/**
		 * @var Collection Gültige Sprachen, die bevorzugt werden.
		 */
		private $acceptLanguage = null;
		public function getAcceptLanguage() {
			if( is_null( $this->acceptLanguage ) ) {
				$this->acceptLanguage = new Collection(
					array(
						'en-us'
					)
				);
			}
			return( $this->acceptLanguage );
		}
		
		/**
		 * @var Collection Gültige Zeichensätze, die bevorzugt werden.
		 */
		private $acceptCharset = null;
		public function getAcceptCharset() {
			if( is_null( $this->acceptCharset ) ) {
				$this->acceptCharset = new Collection(
					array(
						'utf-8'
					)
				);
			}
			return( $this->acceptCharset );
		}
		
		/**
		 * @var string Identifikationsstring, der dem Server als Benutzerprogramm angegeben wird.
		 */
		private $userAgent = 'HttpClient';
		public function getUserAgent() {
			return( $this->userAgent );
		}
		public function setUserAgent( $userAgent ) {
			$this->userAgent = strval( $userAgent );
		}
		
		/**
		 * @var integer Zu verwendender Timeoutwert für die Socketverbindung zum Server.
		 */
		private $timeout = 20;
		public function getTimeout() {
			return( $this->timeout );
		}
		public function setTimeout( $timeout ) {
			$this->timeout = abs( intval( $timeout ) );
		}
		
		/**
		 * @var boolean Flag, ob die gzip-Komprimierung verwendet werden soll, wenn die
		 * entsprechende Extension vorhanden ist.
		 */
		private $useGzip = true;
		public function getUseGzip() {
			return( $this->useGzip );
		}
		public function setUseGzip( $useGzip ) {
			$this->useGzip = (bool)$useGzip;
		}
		
		/**
		 * @var boolean Flag, ob Cookies vom Server gespeichert und bei einem nächsten Request
		 * mitgesendet werden sollen.
		 * Vorsicht: Aktuell wird der Cookie-Pfad und die Lebenszeit komplett ignoriert. Die
		 * Lebenszeit ist nicht weiter kritisch, jedoch kann der Pfad zu Sicherheitsproblemen
		 * führen.
		 */
		private $persistCookies = true;
		public function getPersistCookies() {
			return( $this->persistCookies );
		}
		public function setPersistCookies( $persistCookies ) {
			$this->persistCookies = (bool)$persistCookies;
		}
		
		/**
		 * @var boolean Flag, ob der Referer gespeichert und beim nächsten Request mitgesendet
		 * werden soll.
		 */
		private $persistReferers = true;
		public function getPersistReferers() {
			return( $this->persistReferers );
		}
		public function setPersistReferers( $persistReferers ) {
			$this->persistReferers = (bool)$persistReferers;
		}
		
		/**
		 * @var boolean Flag, ob Weiterleitungen automatisch abgehandelt werden sollen, wenn
		 * ein Location oder URI-Header gefunden wird.
		 */
		private $handleRedirects = true;
		public function getHandleRedirects() {
			return( $this->handleRedirects );
		}
		public function setHandleRedirects( $handleRedirects ) {
			$this->handleRedirects = (bool)$handleRedirects;
		}
		
		/**
		 * @var integer Die Anzahl maximal durchzuführender Weiterleitungen.
		 */
		private $maxRedirects = 5;
		public function getMaxRedirects() {
			return( $this->maxRedirects );
		}
		public function setMaxRedirects( $maxRedirects ) {
			$this->maxRedirects = abs( intval( $maxRedirects ) );
		}
		
		/**
		 * @var boolean Flag, ob lediglich die Header-Daten gelesen werden sollen.
		 */
		private $headersOnly = false;
		public function getHeadersOnly() {
			return( $this->headersOnly );
		}
		public function setHeadersOnly( $headersOnly ) {
			$this->headersOnly = (bool)$headersOnly;
		}
		
		/**
		 * @var string Benutzername für eine BASIC-Authorisierung
		 */
		private $basicUsername = '';
		protected function getBasicUsername() {
			return( $this->basicUsername );
		}
		
		/**
		 * @var string Passwort für eine BASIC-Authorisierung
		 */
		private $basicPassword = '';
		protected function getBasicPassword() {
			return( $this->basicPassword );
		}
		
		/**
		 * Setzt die Daten für eine BASIC-Authorisierung
		 */
		public function setBasicAuthorize( $username, $password ) {
			$this->basicUsername = strval( $username );
			$this->basicPassword = strval( $password );
		}
		
		/**
		 * @var integer Der Statuscode der Antwort.
		 */
		private $statusCode = 0;
		public function getStatusCode() {
			return( $this->statusCode );
		}
		protected function setStatusCode( $statusCode ) {
			$this->statusCode = intval( $statusCode );
		}
		
		/**
		 * @var string Die Statusmeldung der Antwort.
		 */
		private $statusMessage = '';
		public function getStatusMessage() {
			return( $this->statusMessage );
		}
		protected function setStatusMessage( $statusMessage ) {
			$this->statusMessage = strval( $statusMessage );
		}
		
		/**
		 * @var Collection Die Liste der Antwort-Header.
		 */
		private $headers = null;
		public function getHeaders() {
			if( is_null( $this->headers ) ) {
				$this->headers = new Collection();
			}
			return( $this->headers );
		}
		
		/**
		 * @var string Der Inhalt der Antwort.
		 */
		private $content = '';
		public function getContent() {
			return( $this->content );
		}
		protected function setContent( $content ) {
			$this->content = $content;
		}
		
		/**
		 * @var integer Die Anzahl bisher durchgeführter Weiterleitungen. 
		 * @internal
		 */
		protected $redirects = 0;
		public function getRedirects() {
			return( $this->redirects );
		}
		
		/**
		 * @var resource Der aktuell geöffnete Dateihandle auf den Socket.
		 * @internal
		 */
		protected $socket = null;
		
		/**
		 * Führt den HTTP-Request durch.
		 * Wirft eine Exception, wenn ein Fehler auftritt.
		 * 
		 * @throws Exception
		 * @return boolean true, wenn die Anfrage erfolgreich war.
		 */
		public function send() {
			$this->openConnection();
			$request = $this->buildRequest();
			if( fwrite( $this->socket, $request )===false ) {
				$this->closeConnection();
				throw new Exception( 'Unable to send the request to the server' );
			}
			
			$this->readResponse();
			
			// Komprimierte Daten entpacken
			if( $this->getHeaders()->offsetExists( 'content-encoding' )
				and $this->getHeaders()->offsetGet( 'content-encoding' )
			) {
				$this->decodeContent();
			}
			
			// Cookies speichern
			if( $this->getPersistCookies()
				and $this->getHeaders()->offsetExists( 'set-cookie' )
			) {
				$this->persistCookies();
			}
			
			// Referer speichern
			if( $this->getPersistReferers() ) {
				$this->persistReferer();
			}
			
			// Weiterleitungen behandeln
			if( $this->getHandleRedirects() ) {
				return( $this->handleRedirect() );
			}
			
			return( true );
		}
		
		/**
		 * Öffnet den Socket zum Zielserver.
		 * 
		 * @return void
		 */
		protected function openConnection() {
			$socket = @fsockopen( $this->getHost(), $this->getPort(), $errno, $errstr, $this->getTimeout() );
			if( !is_resource( $socket ) ) {
				throw new Exception( $errstr, $errno );
			}
			if( !stream_set_timeout( $socket, $this->getTimeout() ) ) {
				throw new Exception( 'Unable to set the timeout value for the socket' );
			}
			$this->socket = $socket;
		}
		
		/**
		 * Schließt den Socket zum Zielserver.
		 * 
		 * @return void
		 */
		protected function closeConnection() {
			@fclose( $this->socket );
		}
		
		/**
		 * Erstellt den Request und gibt diesen als String zurück.
		 * 
		 * @return string
		 */
		protected function buildRequest() {
			return(
				implode( "\r\n", $this->buildHeaders() ) .
				"\r\n\r\n" .
				$this->getPostdata()
			);
		}
		
		/**
		 * Erstellt die Header-Daten für den Request und gibt diese als Array zurück.
		 * 
		 * @return array
		 */
		protected function buildHeaders() {
			return( array_merge(
				array( $this->getHttpHeader() ),
				$this->buildBaseHeaders(),
				$this->buildAcceptHeaders(),
				$this->buildCookieHeaders(),
				$this->buildAuthentificationHeaders(),
				$this->buildPostHeaders()
			) );
		}
		
		/**
		 * Erstellt die Basisheader Host und User-Agent.
		 * 
		 * @return array
		 */
		protected function buildBaseHeaders() {
			return( array(
				'Host: '.$this->getHost(),
				'User-Agent: '.$this->getUserAgent(),
				//'Keep-Alive: '.$this->getTimeout(),
				//'Connection: keep-alive'
			) );
		}
		
		/**
		 * Erstellt die Accept-Header.
		 * 
		 * @return array
		 */
		protected function buildAcceptHeaders() {
			$headers = array();
			$headers[] = 'Accept: '.$this->getAccept()->join( ',' );
			if( $this->getUseGzip() ) {
				$headers[] = 'Accept-Encoding: '.$this->getAcceptEncoding()->join( ',' );
			}
			$headers[] = 'Accept-Language: '.$this->getAcceptLanguage()->join( ',' );
			$headers[] = 'Accept-Charset: '.$this->getAcceptCharset()->join( ',' );
			if( ( $referer = $this->getReferer() )!='' ) {
				$headers[] = 'Referer: '.$referer;
			}
			return( $headers );
		}
		
		/**
		 * Erstellt den Cookie-Header.
		 * 
		 * @return array
		 */
		protected function buildCookieHeaders() {
			$headers = array();
			// Cookies
			if( $this->getCookies()->count()>0 ) {
				$cookie = 'Cookie: ';
				foreach( $this->getCookies() as $name=>$value ) {
					$cookies .= $name.'='.$value.'; ';
				}
				$headers[] = $cookie;
			}
			return( $headers );
		}
		
		/**
		 * Erstellt die Authentifizierungs-Header.
		 * 
		 * @return array
		 */
		protected function buildAuthentificationHeaders() {
			$headers = array();
			// BASIC-Authentifizierung
			if(
				( $u = $this->getBasicUsername() )!='' and
				( $p = $this->getBasicPassword() )!=''
			) {
				$headers[] = 'Authorization: BASIC '.base64_encode( $u.':'.$p );
			}
			return( $headers );
		}
		
		/**
		 * Erstellt die Header für die Übertragung von POST-Daten und gibt diese
		 * als Array zurück.
		 * 
		 * @return array
		 */
		protected function buildPostHeaders() {
			$headers = array();
			if( $this->getMethod()==self::MethodPost ) {
				$headers[] = 'Content-Type: application/x-www-form-urlencoded';
				$headers[] = 'Content-Length: '.strlen( $this->getPostdataAsString() );
			}
			return( $headers );
		}
		
		/**
		 * Gibt den HTTP-Header zurück.
		 * 
		 * @return string
		 */
		protected function getHttpHeader() {
			return( $this->getMethodAsString().' '.$this->getPath().' HTTP/1.0' );
		}
		
		/**
		 * Ließt die Antwort des Servers, füllt das Headers-Array und setzt den Content.
		 * 
		 * @return void
		 */
		protected function readResponse() {
			$this->getHeaders()->clear();
			
			$content = '';
			$inHeaders = true;
			$atStart = true;
			while( !feof( $this->socket ) ) {
				$line = fgets( $this->socket, self::READ_PER_LINE );
				if( $line===false ) {
					break;
				}
				
				if( $atStart ) {
					$atStart = false;
					if( !preg_match( '/HTTP\/(\\d\\.\\d)\\s*(\\d+)\\s*(.*)/', $line, $matches ) ) {
						$this->closeConnection();
						throw new Exception( 'The HTTP status code line is invalid. Given value is: "'.$line.'"' );
					}
					$this->setStatusCode( $matches[2] );
					$this->setStatusMessage( $matches[3] );
				}
				else if( $inHeaders ) {
					if( trim( $line )=='' ) {
						$inHeaders = false;
						if( $this->getHeadersOnly() ) {
							break;
						}
						continue;
					}
					
					$pos = strpos( $line, ':' );
					if( $pos===false ) {
						continue;
					}
					
					$key = strtolower( trim( substr( $line, 0, $pos ) ) );
					$val = trim( substr( $line, $pos+1 ) );
					
					// Mehrfache Header verarbeiten
					if( isset( $this->headers[$key] ) ) {
						if( is_array( $this->headers[$key] ) ) {
							$this->headers[$key][] = $val;
						}
						else {
							$this->headers[$key] = array( $this->headers[$key], $val );
						}
					}
					else {
						$this->headers[$key] = $val;
					}
				}
				else {
					$content .= $line;
				}
			}
			
			$this->setContent( $content );
			$this->closeConnection();
		}
		
		/**
		 * Führt eine gzip-Dekodierung des Inhaltes durch.
		 * 
		 * @return void
		 */
		protected function decodeContent() {
			$this->setContent( gzinflate( substr( $this->getContent(), 10 ) ) );
		}
		
		/**
		 * Übernimmt die empfangenen Cookies in das Array der Cookie-Eigenschaft.
		 * 
		 * @return void
		 */
		protected function persistCookies() {
			$cookies = $this->getHeaders()->offsetGet( 'set-cookie' );
			if( is_array( $cookies ) ) {
				foreach( $cookies as $cookie ) {
					$pos = strpos( $cookie, '=' );
					if( $pos===false ) {
						continue;
					}
					$this->getCookies()->offsetSet(
						substr( $cookie, 0, $pos ),
						substr( $cookie, $pos+1 )
					);
				}
			}
			else {
				$pos = strpos( $cookies, '=' );
				if( $pos===false ) {
					return;
				}
				$this->getCookies()->offsetSet(
					substr( $cookies, 0, $pos ),
					substr( $cookies, $pos+1 )
				);
			}
		}
		
		/**
		 * Übernimmt die Referer-URL, so dass diese in einem nachfolgenden Request mitgesendet
		 * werden kann.
		 * 
		 * @return void
		 */
		protected function persistReferer() {
			$url = 'http://' . $this->getHost();
			if( $this->getPort() != 80) {
				$url .= ':'.$this->getPort();
			}
			$url .= $this->getPath();
			
			$this->setReferer( $url );
		}
		
		/**
		 * Führt eine Weiterleitung durch
		 * 
		 * @return void
		 */
		protected function handleRedirect() {
			if( $this->redirects >= $this->getMaxRedirects() ) {
				$this->redirects = 0;
				throw new Exception( 'The number of redirects exceeded the maximum of '.$this->getMaxRedirects() );
			}
			
			if( $this->getHeaders()->offsetExists( 'location' ) ) {
				$location = $this->getHeaders()->offsetGet( 'location' );
			}
			else if( $this->getHeaders()->offsetExists( 'uri' ) ) {
				$location = $this->getHeaders()->offsetGet( 'uri' );
			}
			else {
				return( true );
			}
			
			if( $location=='' ) {
				return( true );
			}
			
			$url = parse_url( $location );
			$this->setPath( $url['path'] );
			return( $this->send() );
		}
		
	}
	
?>