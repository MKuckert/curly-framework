<?php
	
	/**
	 * TextWriter
	 * Repräsentiert einen Schreiber, der sequentiell eine Serie von Zeichen
	 * schreiben kann.
	 * 
	 * @author Martin Kuckert
	 * @version 1.0
	 */
	abstract class TextWriter {
		
		/**
		 * @var boolean Flag, ob die Ressourcen dieses Schreibers bereits
		 * freigegeben wurden.
		 * @ignore
		 */
		private $_disposed = false;
		
		/**
		 * @var Stream Das zu verwendende Stream-Objekt.
		 * @ignore
		 */
		private $_stream = null;
		
		/**
		 * @var string Das zu verwendende Neue-Zeile-Zeichen
		 */
		private $_newLine = "\n";
		
		/**
		 * Gibt zurück, ob die Ressourcen dieses Schreibers bereits freigegeben
		 * wurden.
		 * 
		 * @return boolean
		 */
		public function getHasDisposed() {
			return( $this->_disposed );
		}
		
		/**
		 * Gibt das zu verwendende Stream-Objekt zurück.
		 * 
		 * @return Stream
		 * @ignore
		 */
		protected function getStream() {
			return( $this->_stream );
		}
		
		/**
		 * Setzt das zu verwendende Stream-Objekt.
		 * 
		 * @return void
		 * @param Stream
		 * @ignore
		 */
		protected function setStream( Stream $stream ) {
			$this->_stream = $stream;
		}
		
		/**
		 * Gibt das zu verwendende Neue-Zeile-Zeichen zurück.
		 * 
		 * @return string
		 */
		public function getNewLine() {
			return( $this->_newLine );
		}
		
		/**
		 * Setzt das zu verwendende Neue-Zeile-Zeichen
		 * 
		 * @return void
		 * @param string
		 */
		public function setStream( $newLine ) {
			$this->_newLine = $newLine;
		}
		
		/**
		 * Schließt diesen Schreiber und gibt alle zugeordneten Ressourcen
		 * frei (z.B. verwendete Stream-Objekte).
		 * 
		 * @return void
		 */
		public function close() {
			if( !$this->getHasDisposed() ) {
				$this->dispose();
			}
		}
		
		/**
		 * Gibt alle diesem Schreiber zugeordneten Ressourcen frei
		 * (z. B. verwendete Stream-Objekte).
		 * 
		 * @return void
		 */
		public function dispose() {
			$this->_disposed = true;
		}
		
		/**
		 * Schreibt die übergebenen Daten in den Stream.
		 * 
		 * @throws BadMethodCallException Der Schreiber wurde bereits
		 *  geschlossen.
		 * @throws NotSupportedException Der Stream unterstützt keine
		 *  Schreibvorgänge.
		 * @throws InvalidArgumentException $buffer ist kein String.
		 * @return void
		 * @param string Die Eingabedaten.
		 */
		public function write( $buffer );
		
		/**
		 * Schreibt die übergebenen Daten in den Stream, gefolgt von einem
		 * Neue-Zeile-Zeichen
		 * 
		 * @throws BadMethodCallException Der Schreiber wurde bereits
		 *  geschlossen.
		 * @throws NotSupportedException Der Stream unterstützt keine
		 *  Schreibvorgänge.
		 * @throws InvalidArgumentException $buffer ist kein String.
		 * @return void
		 * @param string Die Eingabedaten.
		 */
		public function writeLine( $buffer );
		
	}
	
?>