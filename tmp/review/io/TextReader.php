<?php
	
	/**
	 * TextReader
	 * Repräsentiert einen Leser, der sequentiell eine Serie von Zeichen lesen
	 * kann.
	 * 
	 * @author Martin Kuckert
	 * @version 1.0
	 */
	abstract class TextReader {
		
		/**
		 * @var boolean Flag, ob die Ressourcen dieses Lesers bereits
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
		 * Gibt zurück, ob die Ressourcen dieses Lesers bereits freigegeben
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
		 * Schließt diesen Leser und gibt alle zugeordneten Ressourcen frei
		 * (z.B. verwendete Stream-Objekte).
		 * 
		 * @return void
		 */
		public function close() {
			if( !$this->getHasDisposed() ) {
				$this->dispose();
			}
		}
		
		/**
		 * Gibt alle diesem Leser zugeordneten Ressourcen frei
		 * (z. B. verwendete Stream-Objekte).
		 * 
		 * @return void
		 */
		public function dispose() {
			$this->_disposed = true;
		}
		
		/**
		 * Ließt das nächste Zeichen ohne den Zustand des Lesers oder der
		 * Datenquelle zu ändern. Gibt das nächste verfügbare Zeichen zurück,
		 * ohne es vom Eingabestrom zu lesen.
		 * 
		 * @return int Das nächste zu lesende Byte oder -1, wenn keine weiteren
		 * Zeichen verfügbar sind oder der verwendete Stream kein Ändern der
		 * Leseposition erlaubt.
		 */
		public function peek();
		
		/**
		 * Liest eine Zeichenkette aus dem aktuellen Stream und erhöht die
		 * Position im Stream um die Anzahl der gelesenen Bytes.
		 * 
		 * @throws BadMethodCallException Der Leser wurde bereits geschlossen.
		 * @throws IOException Ein Ein-/Ausgabefehler tritt auf.
		 * @throws OutOfRangeException $count ist negativ.
		 * @throws NotSupportedException Der verwendete Stream unterstützt
		 *  keine Lesevorgänge.
		 * @throws InvalidArgumentException $buffer ist kein String oder $count
		 *  ist kein Integer.
		 * @return int Die Gesamtanzahl der in den Puffer gelesenen Bytes. Dies
		 *  kann weniger als die Anzahl der angeforderten Bytes sein, wenn diese
		 *  Anzahl an Bytes derzeit nicht verfügbar ist, oder 0, wenn das Ende
		 *  des Streams erreicht ist.
		 * @param string& Nach dem Beenden dieser Methode enthält der Puffer
		 *  count Bytes, aus der aktuellen Quelle.
		 */
		public function read( &$buffer, $count );
		
		/**
		 * Liest eine Zeile von Zeichen aus dem aktuellen Stream und erhöht die
		 * Position im Stream um die Anzahl der gelesenen Bytes.
		 * Eine Zeile wird durch die Zeichen \n, \r, die Zeichenfolge \r\n oder
		 * durch das Ende des Streams bestimmt. Der Rückgabestring enthält
		 * dieses Begrenzerzeichen nicht.
		 * 
		 * @throws BadMethodCallException Der Leser wurde bereits geschlossen.
		 * @throws IOException Ein Ein-/Ausgabefehler tritt auf.
		 * @throws NotSupportedException Der verwendete Stream unterstützt
		 *  keine Lesevorgänge.
		 * @return string Die gerade gelesene Zeile oder NULL, wenn keine Daten
		 * mehr verfügbar sind
		 */
		public function readLine();
		
		/**
		 * Ließt alle verfügbaren Zeichen von der aktuellen Position bis zum
		 * Ende des Streams und gibt diese als einen String zurück.
		 * 
		 * @throws BadMethodCallException Der Leser wurde bereits geschlossen.
		 * @throws IOException Ein Ein-/Ausgabefehler tritt auf.
		 * @throws NotSupportedException Der verwendete Stream unterstützt
		 *  keine Lesevorgänge.
		 * @return string Ein String, der alle Zeichen von der aktuellen
		 *  Position bis zum Ende des Streams enthält.
		 */
		public function readToEnd();
		
	}
	
?>