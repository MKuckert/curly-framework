<?php

/**
 * FS_StreamWrapper
 * 
 * Stellt eine Kapselung eines Datenstromes auf einen FS_Tree dar.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package FS
 * @since 1.0 - 01.06.2009
 * @version 1.0
 */
class FS_StreamWrapper {
	
	/**#@+
	 * @desc Möglicher Modi zum Öffnen eines Streams.
	 */
	const MODE_READ=1;
	const MODE_WRITE=2;
	/**#@-*/
	
	/**
	 * @var resource Der Stream-Kontext.
	 */
	public $context=NULL;
	
	/**
	 * @var FS_Tree Der Baum, in dessen Kontext sich dieser Stream bewegt.
	 */
	protected $contextTree=NULL;
	
	/**
	 * @var FS_Node Der Knoten, in dessen Kontext sich dieser Stream bewegt.
	 */
	protected $contextNode=NULL;
	
	/**
	 * @var Curly_Stream Der gekapselte Datenstrom
	 */
	protected $contextStream=NULL;
	
	/**
	 * @var string Flag, mit dem dieser Stream geöffnet wurde.
	 */
	protected $openMode=0;
	
	/**
	 * Versucht aus dem Stream-Kontext den Kontextbaum zu ermitteln
	 * 
	 * @return boolean
	 */
	protected function resolveContext() {
		$options=stream_context_get_options($this->context);
		$options=reset($options);
		
		// Kontextoption nicht gesetzt
		if(!isset($options['tree'])) {
			return false;
		}
		
		$this->contextTree=$options['tree'];
		return true;
	}
	
	/**
	 * Öffnet diesen Wrapper.
	 * 
	 * @return boolean true, wenn der Stream erfolgreich geöffnet wurde.
	 * @param string Der zu verwendende Pfad
	 * @param string Der Modus, mit dem dieser Wrapper geöffnet wurde
	 * @param integer Weitere Optionen als Bitfeld. Folgende Werte können
	 *  enthalten sein:
	 * 	 - STREAM_USE_PATH: Wenn $path relativ ist, soll die
	 * 						include_path-Konfiguration zum Auflösen
	 * 						verwendet werden. Wurde der Stream korrekt
	 * 						geöffnet, soll der 4. Parameter auf den vollen Pfad
	 * 						gesetzt werden.
	 *   - STREAM_REPORT_ERRORS: Wenn dieses Flag gesetzt ist, sollen bei
	 * 						Fehlern mit Hilfe von {@link trigger_error}
	 * 						Fehlermeldungen ausgegeben werden. Sonst nicht.
	 * @param string 
	 */
	public function stream_open($path, $mode, $options, $opened_path) {
		$reportErrors=($options&STREAM_REPORT_ERRORS)>0;
		$reportErrors=true;
		
		// Baum über Protokoll ermitteln
		$path=new FS_Path($path);
		$tree=FS_Tree_Registry::get($path[0]);
		if($tree!==NULL) {
			$this->contextTree=$tree;
		}
		// Baum über Kontext ermitteln
		else if(!$this->resolveContext()) {
			if($reportErrors) {
				trigger_error('Unable to resolve context tree');
			}
			return false;
		}
		
		// Knoten finden
		// TODO: Knoten erstellen bei Mode w/a
		$n=$this->findNode($path);
		if($n===NULL) {
			if($reportErrors) {
				trigger_error('Unable to find node with path '.$path);
			}
			return false;
		}
		
		// Keine Blatt-Metadaten
		$meta=$n->getMeta();
		if(!($meta instanceof FS_Metadata_Item)) {
			if($reportErrors) {
				trigger_error('The found node is no node with item metadata');
			}
			return false;
		}
		
		// Erfolg
		$this->contextNode=$n;
		$this->contextStream=new Curly_Stream_String($meta->getContents());
		
		// Streamposition setzen
		if(!$this->initStream($mode)) {
			if($reportErrors) {
				trigger_error('Unable to open the stream. Invalid mode '.$mode);
			}
			return false;
		}
		
		return true;
	}
	
	/**
	 * Initialisiert den Kontextstream.
	 * 
	 * @return void
	 * @param string Der Modus, mit dem dieser Wrapper geöffnet wurde
	 */
	protected function initStream($mode) {
		static $modes=array(
			'r'		=> self::MODE_READ,
			'w'		=> self::MODE_WRITE,
			'a'		=> self::MODE_WRITE
		);
		
		$mode=trim($mode, 'b');
		
		// Öffnungsmodus
		if(isset($mode[1]) and $mode[1]=='+') {
			$this->openMode=self::MODE_READ|self::MODE_WRITE;
		}
		else if(!isset($modes[$mode])) {
			return false;
		}
		else {
			$this->openMode=$modes[$mode];
		}
		
		// Startposition
		if($mode[0]=='w') {
			$this->contextStream->truncate();
		}
		else if($mode[0]=='a') {
			$this->contextStream->setPosition(0, Curly_Stream::OriginEnd);
		}
		return true;
	}
	
	/**
	 * Versucht den Knoten zu dem übergebenen Pfad zu finden.
	 * 
	 * @return FS_Node oder NULL
	 * @param FS_Path
	 */
	protected function findNode(FS_Path $path) {
		$findNode=new FS_Visitor_FindNode($path);
		$this->contextTree->acceptVisitor($findNode);
		if($findNode->wasSuccessful()) {
			return $findNode->getFoundNode();
		}
		else {
			return NULL;
		}
	}
	
	/**
	 * Schließt diesen StreamWrapper.
	 * 
	 * @return void
	 */
	public function stream_close() {
		// Metadaten zurückschreiben
		$this->contextNode->getMeta()->setContents(
			$this->contextStream->toString()
		);
	}
	
	/**
	 * Ließt Maximal $count Bytes aus dem Stream und gibt diese zurück. Die
	 * aktuelle Position im Stream muss ebenfalls aktualisiert werden.
	 * Sind keine Daten mehr vorhanden, muss false oder eine leere Zeichenkette
	 * zurückgegeben werden.
	 * 
	 * @return string
	 * @param integer Maximal zu lesende Bytes
	 */
	public function stream_read($count) {
		if(($this->openMode&self::MODE_READ)<0) {
			trigger_error('This stream is writonly');
			return false;
		}
		return $this->contextStream->read($count);
	}
	
	/**
	 * Schreibt die übergebenen Daten in diesen Strom und gibt die Anzahl
	 * geschriebener Bytes zurück. Können keine Daten geschrieben werden, so
	 * muss 0 zurückgegeben werden.
	 * 
	 * @return integer Anzahl geschriebener Bytes
	 * @param string Zu schreibende Daten
	 */
	public function stream_write($data) {
		if(($this->openMode&self::MODE_WRITE)<0) {
			trigger_error('This stream is readonly');
			return false;
		}
		$this->contextStream->write($data);
		return strlen($data);
	}
	
	/**
	 * Gibt einen boolschen Wert zurück, ob die Lese-/Schreibposition am Ende
	 * des Datenstroms steht.
	 * 
	 * @return boolean
	 */
	public function stream_eof() {
		return !$this->contextStream->getHasMore();
	}
	
	/**
	 * Gibt die aktuelle Lese-/Schreibposition zurück.
	 * 
	 * @return integer
	 */
	public function stream_tell() {
		return $this->contextStream->getPosition();
	}
	
	/**
	 * Ändert die aktuelle Lese-/Schreibposition auf den mit den
	 * Übergabeparametern definierten Wert.
	 * 
	 * @return boolean true bei Erfolg.
	 * @param integer Offset-Wert
	 * @param integer Referenz-Position
	 */
	public function stream_seek($offset, $whence) {
		return $this->contextStream->setPosition($offset, $whence);
	}
	
	/**
	 * Leert alle Datenpuffer.
	 * 
	 * @return boolean true bei Erfolg.
	 */
	public function stream_flush() {
		$this->contextStream->flush();
		return true;
	}
	
	/**
	 * Gibt statistische Daten zu diesem Stream zurück.
	 * 
	 * @return array
	 */
	public function stream_stat() {
		$meta=$this->contextNode->getMeta();
		return array(
			0,	// Laufwerk
			0,	// inode
			0,	// inode protection mode
			0,	// Anzahl der Links
			0,	// Benutzer ID des Inhabers
			0,	// Gruppen ID des Inhabers
			0,	// Laufwerkstyp wenn Inode-Laufwerk *
			strlen($this->contextStream->toString()),	// Größe in Bytes
			$meta->getAccessTime(),	// Zeitpunkt des letzten Zugriffs
			$meta->getModificationTime(),	// Zeitpunkt der letzten Modifizierung
			0,	// Zeitpunkt der letzten Änderung
			0,	// Blockgröße für das Dateisystem I/O *
			0	// Anzahl der zugewiesenen Blöcke
		);
	}
	
	/*
	public bool dir_closedir ( void )
	public bool dir_opendir ( string $path , int $options )
	public string dir_readdir ( void )
	public bool dir_rewinddir ( void )
	public bool mkdir ( string $path , int $mode , int $options )
	public bool rename ( string $path_from , string $path_to )
	public bool rmdir ( string $path , int $options )
	public bool unlink ( string $path )
	public array url_stat ( string $path , int $flags )
	*/
}