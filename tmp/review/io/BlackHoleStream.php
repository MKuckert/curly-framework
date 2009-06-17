<?php
	
	/**
	 * BlackHoleStream
	 * Stellt einen Stream ohne physikalisches Speichermedium dar. Alle
	 * angefügten Daten werden verworfen. Bei Lesevorgängen werden immer keine
	 * Daten zurückgegeben.
	 * 
	 * @author Martin Kuckert
	 * @version 1.0
	 */
	class BlackHoleStream extends Stream {
		
		/**
		 * Löscht alle Puffer für diesen Stream und veranlasst die Ausgabe
		 * aller gepufferten Daten an das zugrunde liegende Gerät.
		 * 
		 * @throws IOException Ein Ein-/Ausgabefehler tritt auf.
		 * @return void
		 */
		public function flush() {}
		
		/**
		 * Liest eine Folge von Bytes aus dem aktuellen Stream und erhöht die
		 * Position im Stream um die Anzahl der gelesenen Bytes.
		 * 
		 * @throws BadMethodCallException Der Stream wurde bereits geschlossen.
		 * @throws IOException Ein Ein-/Ausgabefehler tritt auf.
		 * @throws OutOfRangeException $count ist negativ.
		 * @throws NotSupportedException Der Stream unterstützt keine
		 *  Lesevorgänge.
		 * @throws InvalidArgumentException $buffer ist kein String oder $count
		 *  ist kein Integer.
		 * @return int Die Gesamtanzahl der in den Puffer gelesenen Bytes. Dies
		 *  kann weniger als die Anzahl der angeforderten Bytes sein, wenn diese
		 *  Anzahl an Bytes derzeit nicht verfügbar ist, oder 0, wenn das Ende
		 *  des Streams erreicht ist.
		 * @param string& Nach dem Beenden dieser Methode enthält der Puffer
		 *  count Bytes, aus der aktuellen Quelle.
		 */
		public function read( &$buffer, $count ) {
			return( 0 );
		}
		
		/**
		 * Legt die Position im aktuellen Stream fest.
		 * 
		 * @throws BadMethodCallException Der Stream wurde bereits geschlossen.
		 * @throws IOException Ein Ein-/Ausgabefehler tritt auf.
		 * @throws NotSupportedException Der Stream unterstützt keine
		 *  Suchvorgänge. Dies ist beispielsweise der Fall, wenn der Stream aus
		 *  einer Pipe- oder Konsolenausgabe erstellt wird.
		 * @throws InvalidArgumentException $offset oder $origin ist kein
		 *  Integer, negativ oder $origin ist keine Konstante von SeekOrigin.
		 * @return int Die neue Position innerhalb des aktuellen Streams.
		 * @param int Ein Byteoffset relativ zum origin-Parameter.
		 * @param int Ein Wert aus den Konstanten der Klasse {@link SeekOrigin},
		 *  der den Bezugspunkt angibt, von dem aus die neue Position ermittelt
		 *  wird.
		 */
		public function seek( $offset, $origin ) {
			return( 0 );
		}
		
		/**
		 * Legt die Länge des aktuellen Streams fest.
		 * 
		 * @throws BadMethodCallException Der Stream wurde bereits geschlossen
		 * @throws IOException Ein Ein-/Ausgabefehler tritt auf.
		 * @throws NotSupportedException Der Stream unterstützt keine
		 *  Lese- oder Schreibvorgänge. Dies ist beispielsweise der Fall, wenn
		 *  der Stream aus einer Pipe- oder Konsolenausgabe erstellt wird.
		 * @throws InvalidArgumentException $value ist kein Integer oder
		 *  negativ.
		 * @return void
		 * @param int Die gewünschte Länge des aktuellen Streams in Bytes.
		 */
		public function setLength( $value ) {}
		
		/**
		 * Schreibt eine Folge von Bytes in den aktuellen Stream und erhöht die
		 * aktuelle Position im Stream um die Anzahl der geschriebenen Bytes.
		 * 
		 * @throws BadMethodCallException Der Stream wurde bereits geschlossen.
		 * @throws IOException Ein Ein-/Ausgabefehler tritt auf.
		 * @throws NotSupportedException Der Stream unterstützt keine
		 *  Schreibvorgänge.
		 * @throws InvalidArgumentException $buffer ist kein String.
		 * @return void
		 * @param string Die Eingabedaten.
		 */
		public function write( $buffer ) {}
		
		/**
		 * Ruft einen Wert ab, der angibt, ob der aktuelle Stream Lesevorgänge
		 * unterstützt.
		 * 
		 * @return boolean true, wenn der Stream Lesevorgänge unterstützt,
		 *  andernfalls false.
		 */
		public function getCanRead() {
			return( true );
		}
		
		/**
		 * Ruft einen Wert ab,  angibt, ob der aktuelle Stream Suchvorgänge
		 * unterstützt.
		 * 
		 * @return boolean true, wenn der Stream Suchvorgänge unterstützt,
		 *  andernfalls false.
		 */
		public function getCanSeek() {
			return( true );
		}
		
		/**
		 * Ruft einen Wert ab, der angibt, ob der aktuelle Stream
		 * Schreibvorgänge unterstützt.
		 * 
		 * @return boolean true, wenn der Stream Schreibvorgänge unterstützt,
		 *  andernfalls false.
		 */
		public function getCanWrite() {
			return( true );
		}
		
		/**
		 * Ruft die Länge des Streams in Bytes ab.
		 * 
		 * @throws BadMethodCallException Der Stream wurde bereits geschlossen.
		 * @throws NotSupportedException Der Stream unterstützt keine
		 *  Suchvorgänge.
		 * @return int Ein Wert, der die Länge des Streams in Bytes darstellt.
		 */
		public function getLength() {
			return( 0 );
		}
		
		/**
		 * Ruft die Position im aktuellen Stream ab.
		 * 
		 * @throws BadMethodCallException Der Stream wurde bereits geschlossen.
		 * @throws IOException Ein Ein-/Ausgabefehler tritt auf.
		 * @throws NotSupportedException Der Stream unterstützt keine
		 *  Suchvorgänge.
		 * @return int Die aktuelle Position innerhalb des Streams.
		 */
		public function getPosition() {
			return( 0 );
		}
		
		/**
		 * Legt die Position im aktuellen Stream fest.
		 * 
		 * @throws BadMethodCallException Der Stream wurde bereits geschlossen.
		 * @throws IOException Ein Ein-/Ausgabefehler tritt auf.
		 * @throws NotSupportedException Der Stream unterstützt keine
		 *  Suchvorgänge.
		 * @throws InvalidArgumentException $value ist kein Integer oder
		 *  negativ.
		 * @return int Die aktuelle Position innerhalb des Streams.
		 * @param int Die gewünschte Position.
		 */
		public function setPosition( $value ) {
			return( 0 );
		}
		
	}
	
?>