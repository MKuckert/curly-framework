<?php
	
	/**
	 * Stream
	 * Stellt eine allgemeine Ansicht einer Folge von Bytes bereit.
	 * Stream ist die abstrakte Basisklasse aller Streams.
	 * 
	 * @author Martin Kuckert
	 * @version 1.0
	 * @see http://msdn2.microsoft.com/de-de/library/system.io.stream.aspx
	 */
	abstract class Stream {
		
		/**
		 * @var boolean Flag, ob die Ressourcen dieses Streams bereits
		 * freigegeben wurden.
		 * @ignore
		 */
		private $_disposed = false;
		
		/**
		 * Gibt zurück, ob die Ressourcen dieses Streams bereits freigegeben
		 * wurden.
		 * 
		 * @return boolean
		 */
		public function getHasDisposed() {
			return( $this->_disposed );
		}
		
		/**
		 * Schließt den aktuellen Stream und gibt alle dem aktuellen Stream
		 * zugeordneten Ressourcen frei (z. B. Sockets und Dateihandles).
		 * 
		 * @return void
		 */
		public function close() {
			if( !$this->getHasDisposed() ) {
				$this->dispose();
			}
		}
		
		/**
		 * Gibt alle dem aktuellen Stream zugeordneten Ressourcen frei
		 * (z. B. Sockets und Dateihandles).
		 * 
		 * @return void
		 */
		public function dispose() {
			$this->_disposed = true;
		}
		
		/**
		 * Löscht beim Überschreiben in einer abgeleiteten Klasse alle Puffer
		 * für diesen Stream und veranlasst die Ausgabe aller gepufferten Daten
		 * an das zugrunde liegende Gerät.
		 * 
		 * @throws IOException Ein Ein-/Ausgabefehler tritt auf.
		 * @return void
		 */
		abstract public function flush();
		
		/**
		 * Liest beim Überschreiben in einer abgeleiteten Klasse eine Folge von
		 * Bytes aus dem aktuellen Stream und erhöht die Position im Stream um
		 * die Anzahl der gelesenen Bytes.
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
		abstract public function read( &$buffer, $count );
		
		/**
		 * Liest ein Byte aus dem Stream und erhöht die Position im Stream um
		 * ein Byte, oder gibt -1 zurück, wenn das Ende des Streams erreicht
		 * ist.
		 * 
		 * @throws BadMethodCallException Der Stream wurde bereits geschlossen.
		 * @throws IOException Ein Ein-/Ausgabefehler tritt auf.
		 * @throws NotSupportedException Der Stream unterstützt keine
		 *  Lesevorgänge
		 * @return int Das Byte ohne Vorzeichen, oder -1, wenn das Ende des
		 *  Streams erreicht ist.
		 */
		public function readByte() {
			$buffer = '';
			if( $this->read( $buffer, 1 )!=1 ) {
				return( -1 );
			}
			else {
				$tmp = unpack( 'C', $buffer );
				return( reset( $tmp ) );
			}
		}
		
		/**
		 * Legt beim Überschreiben in einer abgeleiteten Klasse die Position im
		 * aktuellen Stream fest.
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
		abstract public function seek( $offset, $origin );
		
		/**
		 * Legt beim Überschreiben in einer abgeleiteten Klasse die Länge des
		 * aktuellen Streams fest.
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
		abstract public function setLength( $value );
		
		/**
		 * Schreibt beim Überschreiben in einer abgeleiteten Klasse eine Folge
		 * von Bytes in den aktuellen Stream und erhöht die aktuelle Position im
		 * Stream um die Anzahl der geschriebenen Bytes.
		 * 
		 * @throws BadMethodCallException Der Stream wurde bereits geschlossen.
		 * @throws IOException Ein Ein-/Ausgabefehler tritt auf.
		 * @throws NotSupportedException Der Stream unterstützt keine
		 *  Schreibvorgänge.
		 * @throws InvalidArgumentException $buffer ist kein String.
		 * @return void
		 * @param string Die Eingabedaten.
		 */
		abstract public function write( $buffer );
		
		/**
		 * Schreibt ein Byte an die aktuellen Position im Stream und erhöht die
		 * aktuelle Position im Stream um ein Byte.
		 * 
		 * @throws BadMethodCallException Der Stream wurde bereits geschlossen.
		 * @throws IOException Ein Ein-/Ausgabefehler tritt auf.
		 * @throws NotSupportedException Der Stream unterstützt keine
		 *  Schreibvorgänge.
		 * @throws InvalidArgumentException $value ist kein Integer oder
		 *  negativ.
		 * @return void
		 * @param int Das Byte, das in den Stream geschrieben werden soll. 
		 */
		public function writeByte( $value ) {
			$buffer = '';
			$this->write( pack( 'C', $buffer  ) );
		}
		
		/**
		 * Ruft beim Überschreiben in einer abgeleiteten Klasse einen Wert ab,
		 * der angibt, ob der aktuelle Stream Lesevorgänge unterstützt.
		 * 
		 * @return boolean true, wenn der Stream Lesevorgänge unterstützt,
		 *  andernfalls false.
		 */
		abstract public function getCanRead();
		
		/**
		 * Ruft beim Überschreiben in einer abgeleiteten Klasse einen Wert ab,
		 * der angibt, ob der aktuelle Stream Suchvorgänge unterstützt.
		 * 
		 * @return boolean true, wenn der Stream Suchvorgänge unterstützt,
		 *  andernfalls false.
		 */
		abstract public function getCanSeek();
		
		/**
		 * Ruft beim Überschreiben in einer abgeleiteten Klasse einen Wert ab,
		 * der angibt, ob der aktuelle Stream Schreibvorgänge unterstützt.
		 * 
		 * @return boolean true, wenn der Stream Schreibvorgänge unterstützt,
		 *  andernfalls false.
		 */
		abstract public function getCanWrite();
		
		/**
		 * Ruft beim Überschreiben in einer abgeleiteten Klasse die Länge des
		 * Streams in Bytes ab.
		 * 
		 * @throws BadMethodCallException Der Stream wurde bereits geschlossen.
		 * @throws NotSupportedException Der Stream unterstützt keine
		 *  Suchvorgänge.
		 * @return int Ein Wert, der die Länge des Streams in Bytes darstellt.
		 */
		abstract public function getLength();
		
		/**
		 * Ruft beim Überschreiben in einer abgeleiteten Klasse die Position im
		 * aktuellen Stream ab.
		 * 
		 * @throws BadMethodCallException Der Stream wurde bereits geschlossen.
		 * @throws IOException Ein Ein-/Ausgabefehler tritt auf.
		 * @throws NotSupportedException Der Stream unterstützt keine
		 *  Suchvorgänge.
		 * @return int Die aktuelle Position innerhalb des Streams.
		 */
		abstract public function getPosition();
		
		/**
		 * Legt beim Überschreiben in einer abgeleiteten Klasse die Position im
		 * aktuellen Stream fest.
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
		abstract public function setPosition( $value );
		
	}
	
?>