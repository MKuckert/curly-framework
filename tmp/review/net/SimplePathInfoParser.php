<?php
	$s = "C:/dev/autorensysteme/1/source/";
	include $s."Collection.php";
	include $s."Component.php";
	
	/**
	 * PathInfoParser
	 * Parst die Pfadinformationen, die der Webserver an das aufgerufene
	 * Skript weitergibt und extrahiert die enthaltenen Werte aus diesem Pfad.
	 * Der Pfad wird an den {@link getSeparator Separator}-Zeichen getrennt.
	 * Ist in einem dieser Werte das {@link getIndicator Indicator}-Zeichen
	 * vorhanden, wird anhand diesem Zeichen getrennt und als Schlüssel-Werte-
	 * Paar verwendet. Wird kein solches Zeichen verwendet, wird der gesamte
	 * Wert als Schlüssel angesehen und diesem der Wert null zugeordnet.
	 * Beispiele:
	 * <code>
	 *  $parser = new SimplePathInfoParser();
	 *  $parser->Path = 'key1.value1/key2.value2/';
	 *  $parser->parse();
	 *  $info = $parser->Values;
	 * </code>
	 * 
	 * Hinweis: Da für die Anwendung des Separator-Zeichens die
	 * preg_split-Funktion verwendet wird, kann auch ein regulärer Ausdruck
	 * verwendet werden.
	 * 
	 * @author Martin Kuckert
	 * @since 25.03.2008
	 * @version $Id: $
	 */
	class SimplePathInfoParser extends Component {
		
		/**
		 * @var string Der zu parsende Pfad
		 */
		private $_path = '';
		
		/**
		 * @var string Das Trennzeichen für die Einzelteile des Pfades.
		 * Der Standardwert ist das Slash-Zeichen (/).
		 */
		private $_separator = '/';
		
		/**
		 * @var string Das Trennzeichen für die Schlüssel-Werte-Paare.
		 * Der Standardwert ist das Punkt-Zeichen (.).
		 */
		private $_indicator = '.';
		
		/**
		 * @var Collection Die geparsten Werte. Diese Collection steht erst nach
		 * dem Aufruf der {@link parse}-Methode zur Verfügung (ist vorher leer!)
		 */
		private $_values = null;
		
		/**
		 * Gibt den zu parsenden Pfad zurück.
		 * 
		 * @return string
		 */
		public function getPath() {
			return( $this->_path );
		}
		
		/**
		 * Setzt den zu parsenden Pfad.
		 * 
		 * @return void
		 * @param string
		 */
		public function setPath( $path ) {
			$this->_path = strval( $path );
		}
		
		/**
		 * Gibt das Trennzeichen für die Einzelteile des Pfades zurück.
		 * 
		 * @return string
		 */
		public function getSeparator() {
			return( $this->_separator );
		}
		
		/**
		 * Setzt das Trennzeichen für die Einzelteile des Pfades.
		 * 
		 * @return void
		 * @param string
		 */
		public function setSeparator( $separator ) {
			$this->_separator = strval( $separator );
		}
		
		/**
		 * Gibt das Trennzeichen für die Schlüssel-Werte-Paare zurück.
		 * 
		 * @return string
		 */
		public function getIndicator() {
			return( $this->_indicator );
		}
		
		/**
		 * Setzt das Trennzeichen für die Schlüssel-Werte-Paare.
		 * 
		 * @return void
		 * @param string
		 */
		public function setIndicator( $indicator ) {
			$this->_indicator = strval( $indicator );
		}
		
		/**
		 * Gibt die geparsten Werte zurück.
		 * 
		 * @return Collection
		 */
		public function getValues() {
			if( is_null( $this->_values ) ) {
				$this->_values = new Collection();
			}
			return( $this->_values );
		}
		
		/**
		 * Parst den Pfad anhand des Separators bzw. des Indicator und erstellt
		 * die Values-Collection.
		 * 
		 * @return boolean Ist das Parsing nicht erfolgreich, wird false zurück-
		 * gegeben.
		 */
		public function parse() {
			$values = $this->getValues();
			
			$regex = '/'.str_replace(
				array( '\\', '/' ),
				array( '\\\\', '\\/' ),
				$this->getSeparator()
			).'/';
			$split = preg_split( $regex, $this->getPath(), -1, PREG_SPLIT_NO_EMPTY );
			
			$indicator = $this->getIndicator();
			foreach( $split as $value ) {
				$pos = strpos( $value, $indicator );
				if( $pos<=0 or $pos>=strlen( $value ) ) {
					$values->offsetSet( $value, null );
				}
				else {
					$values->offsetSet( substr( $value, 0, $pos ), substr( $value, $pos+1 ) );
				}
			}
			
			return( true );
		}
		
	}
	
	$parser = new SimplePathInfoParser();
	$parser->Path = '/key1.value1/key2.value2/key3';
	$parser->Separator = '/';
	$parser->parse();
	echo '<pre>';
	var_dump( $parser->Values );
	
?>