<?php
	
	/**
	 * GDImage
	 * Stellt einen OOP-Wrapper für die GDlib-Funktionen zur Verfügung
	 * 
	 * @author Martin Kuckert
	 * @package GDImage
	 * @since 18.11.2007
	 * @copyright Copyright &copy; 2007 Martin Kuckert
	 * @license New BSD License
	 * @see http://php.net/image
	 * @version 1.0
	 */
	class GDImage extends GetSetAccess {
		
		/**
		 * Gibt ein Array aller unterstützten Features der installierten GDlib zurück
		 * 
		 * @return boolean[]
		 *  Aufbau:
		 *   - freetype:		ob die Freetype-Bibliothek installiert ist
		 *   - type1:			ob die Typ 1 PostScript-Bibliothek installiert ist
		 *   - gif_read:		ob das Gif-Format gelesen werden kann
		 *   - gif_create:		ob das Gif-Format geschrieben werden kann
		 *   - jpeg:			ob das Jpeg-Format unterstützt wird
		 *   - png:				ob das Png-Format unterstützt wird
		 */
		static public function GetInformations() {
			static $supported = null;
			if( !is_null( $supported ) ) {
				$gdinfo = gd_info();
				
				$supported = array(
					'freetype'		=> $gdinfo['FreeType Support'],
					'type1'			=> $gdinfo['T1Lib Support'],
					'gif_read'		=> $gdinfo['GIF Read Support'],
					'gif_create'	=> $gdinfo['GIF Create Support'],
					'jpeg'			=> $gdinfo['JPG Support'],
					'png'			=> $gdinfo['PNG Support']
				);
			}
			
			return( $supported );
		}
		
		/**
		 * Überprüft, ob die installierte GDlib das übergebene Feature unterstützt
		 * Wirft InvalidArgumentException, wenn Parameter fehlerhaften Typs übergeben
		 * werden.
		 * 
		 * @throws InvalidArgumentException
		 * @return boolean
		 * @param string
		 */
		static public function IsSupported( $feature ) {
			static $supported = null;
			if( !is_null( $supported ) ) {
				$supported = self::GetInformations();
			}
			
			if( !is_string( $feature ) ) {
				throw new InvalidArgumentException( exceptionMessage( 'type.string', array( 'feature' ) ) );
			}
			
			return( isset( $supported[$feature] ) and $supported[$feature] );
		}
		
		/**
		 * Überprüft, ob die übergebene Ressource vom Typ GD ist
		 * 
		 * @return boolean
		 * @param resource
		 */
		static public function IsGDResource( $resource ) {
			return( is_resource( $resource ) and get_resource_type( $resource )=='gd' );
		}
		
		/** 
		 * @var resource Interner Handle für die GD-Resource des Bildes
		 */
		private $handle = null;
		
		/**
		 * @var Brush Speichert intern den zu verwendenden Pinsel für
		 * Zeichenoperationen
		 */
		private $brush = null;
		
		/**
		 * Konstruktor
		 * Erstellt bei Übergabe von zwei Integern ein TrueColor-Bild.
		 * Ansonsten muss eine der load* oder create-Funktionen verwendet werden.
		 * Wirft InvalidArgumentException, wenn Parameter fehlerhaften Typs übergeben
		 * werden.
		 * 
		 * @throws InvalidArgumentException
		 * @param integer Breite des zu erstellenden Bildes
		 * @param integer Höhe des zu erstellenden Bildes
		 * @see http://php.net/imagecreate
		 */
		public function __construct( $width=null, $height=null ) {
			if( !is_null( $width ) and !is_null( $height ) ) {
				$this->createTrueColor( $width, $height );
			}
		}
		
		/**
		 * Destruktor
		 * Gibt alle noch vorhandenen Ressourcen des Bildes frei
		 * 
		 * @see http://php.net/imagedestroy
		 */
		public function __destruct() {
			if( $this->isInitialized() ) {
				$this->destroy();
			}
		}
		
		/**
		 * Führt eine Konversion nach String durch
		 * 
		 * @return string
		 */
		public function __toString() {
			$res = array();
			if( $this->isInitialized() ) {
				$res[] = $this->isTrueColor() ? 'TrueColor' : 'Palette('.$this->getTotalColors().')';
				$res[] = 'Width '.$this->getWidth();
				$res[] = 'Height '.$this->getHeight();
			}
			if( !is_null( $this->brush ) ) {
				$res[] = 'Brush '.$this->brush->__toString();
			}
			
			return( '#'.get_class( $this ).'['.implode( ' ', $res ).']' );
		}
		
		/**
		 * Gibt zurück, ob das Bild bereits initialisiert wurde, d.h. intern ein
		 * Ressourcehandle geöffnet wurde.
		 * 
		 * @return boolean
		 */
		public function isInitialized() {
			return( !is_null( $this->handle ) );
		}
		
		/**
		 * Gibt alle Ressourcen des Bildes frei
		 * Wirft ImageException, wenn noch kein Ressourcenhandle geöffnet wurde.
		 * 
		 * @throws ImageException
		 * @return void
		 * @see http://php.net/imagedestroy
		 */
		public function destroy() {
			if( !$this->isInitialized() ) {
				throw new ImageException( exceptionMessage( 'image.not_initialized' ) );
			}
			imageDestroy( $this->handle );
			$this->handle = null;
		}
		
		/**#@+
		 * Wirft InvalidArgumentException, wenn Parameter fehlerhaften Typs übergeben
		 * werden.
		 * Wirft ImageException bei Fehlern.
		 * 
		 * @throws InvalidArgumentException, ImageException
		 * @return void
		 */
		/**
		 * Lädt eine Datei und ermittelt den zugrunde liegenden Bildtyp automatisch.
		 * 
		 * @param string Dateiname des Bildes
		 */
		public function loadFromFile( $file ) {
			if( !is_file( $file ) ) {
				throw new IOException( exceptionMessage( 'file.not_exist' ), $file );
			}
			if( !is_readable( $file ) ) {
				throw new IOException( exceptionMessage( 'file.not_readable' ), $file );
			}
			
			// Imagetyp ermitteln
			$info = @getImageSize( $file );
			if( !is_array( $info ) or !isset( $info[2] ) ) {
				throw new ImageException( exceptionMessage( 'file.not_readable_or_no_picture' ), $file );
			}
			
			if( $this->isInitialized() ) {
				$this->destroy();
			}
			
			// Entsprechend des Imagetypes Datei einlesen
			$handle = null;
			switch( $info[2] ) {
				case IMAGETYPE_GIF:
					if( !self::IsSupported( 'gif_read' ) ) {
						throw new ImageException( exceptionMessage( 'image.support.gif_read' ) );
					}
					$handle = imageCreateFromGif( $file );
					break;
				case IMAGETYPE_JPEG:
					if( !self::IsSupported( 'jpeg' ) ) {
						throw new ImageException( exceptionMessage( 'image.support.jpeg' ) );
					}
					$handle = imageCreateFromJpeg( $file );
					break;
				case IMAGETYPE_PNG:
					if( !self::IsSupported( 'png' ) ) {
						throw new ImageException( exceptionMessage( 'image.support.png' ) );
					}
					$handle = imageCreateFromPng( $file );
					break;
				default:
					throw new ImageException( exceptionMessage( 'image.wrong_type' ), $file );
			}
			
			if( !self::IsGDResource( $handle ) ) {
				throw new ImageException( exceptionMessage( 'image.create.from_file' ), $file );
			}
			
			$this->handle = $handle;
		}
		
		/**
		 * Erstellt ein Image aus einem String und ermittelt den zugrunde liegenden
		 * Bildtyp automatisch.
		 * 
		 * @param string Quellstring des Bildes
		 * @see http://php.net/imagecreatefromstring
		 */
		public function loadFromString( $string ) {
			if( !is_string( $string ) ) {
				throw new InvalidArgumentException( exceptionMessage( 'type.string', 'string' ) );
			}
			
			if( $this->isInitialized() ) {
				$this->destroy();
			}
			
			$handle = imageCreateFromString( $string );
			if( !self::IsGDResource( $handle ) ) {
				throw new ImageException( exceptionMessage( 'image.create.from_string' ) );
			}
			
			$this->handle = $handle;
		}
		
		/**
		 * Lädt ein Gif-Bild.
		 * 
		 * @param string Dateiname des Bildes
		 * @see http://php.net/imagecreatefromgif
		 */
		public function loadGif( $file ) {
			if( !self::IsSupported( 'gif_read' ) ) {
				throw new ImageException( exceptionMessage( 'image.support.gif_read' ) );
			}
			
			if( !is_file( $file ) ) {
				throw new IOException( exceptionMessage( 'file.not_exist' ), $file );
			}
			if( !is_readable( $file ) ) {
				throw new IOException( exceptionMessage( 'file.not_readable' ), $file );
			}
			
			if( $this->isInitialized() ) {
				$this->destroy();
			}
			
			$handle = imageCreateFromGif( $file );
			if( !self::IsGDResource( $handle ) ) {
				throw new ImageException( exceptionMessage( 'image.create.from_giffile', $file ) );
			}
			
			$this->handle = $handle;
		}
		
		/**
		 * Lädt ein Jpeg-Bild.
		 * 
		 * @param string Dateiname des Bildes
		 * @see http://php.net/imagecreatefromjpeg
		 */
		public function loadJpeg( $file ) {
			if( !self::IsSupported( 'jpeg' ) ) {
				throw new ImageException( exceptionMessage( 'image.support.jpeg' ) );
			}
			
			if( !is_file( $file ) ) {
				throw new IOException( exceptionMessage( 'file.not_exist' ), $file );
			}
			if( !is_readable( $file ) ) {
				throw new IOException( exceptionMessage( 'file.not_readable' ), $file );
			}
			
			if( $this->isInitialized() ) {
				$this->destroy();
			}
			
			$handle = imageCreateFromJpeg( $file );
			if( !self::IsGDResource( $handle ) ) {
				throw new ImageException( exceptionMessage( 'image.create.from_jpegfile', $file ) );
			}
			
			$this->handle = $handle;
		}
		
		/**
		 * Lädt ein Png-Bild.
		 * 
		 * @param string Dateiname des Bildes
		 * @see http://php.net/imagecreatefrompng
		 */
		public function loadPng( $file ) {
			if( !self::IsSupported( 'png' ) ) {
				throw new ImageException( exceptionMessage( 'image.support.png' ) );
			}
			
			if( !is_file( $file ) ) {
				throw new IOException( exceptionMessage( 'file.not_exist' ), $file );
			}
			if( !is_readable( $file ) ) {
				throw new IOException( exceptionMessage( 'file.not_readable' ), $file );
			}
			
			if( $this->isInitialized() ) {
				$this->destroy();
			}
			
			$handle = imageCreateFromPng( $file );
			if( !self::IsGDResource( $handle ) ) {
				throw new ImageException( exceptionMessage( 'image.create.from_pngfile', $file ) );
			}
			
			$this->handle = $handle;
		}
		
		/**
		 * Lädt ein Bild aus einer Datei oder einem String. Dies wird automatisch
		 * ermittelt. Der zugrunde liegenden Bildtyp wird ebenfalls automatisch
		 * ermittelt.
		 * Wenn die Quelle bekannt ist, ist der direkte Aufruf einer spezielleren
		 * Methode performanter.
		 * 
		 * @param string Dateiname oder Quellstring des Bildes
		 */
		public function load( $input ) {
			if( !is_string( $input ) ) {
				throw new InvalidArgumentException( exceptionMessage( 'type.string', 'input' ) );
			}
			
			// Eine Datei mit diesem Namen ist vorhanden und lesbar
			if( is_file( $input ) and is_readable( $input ) ) {
				try {
					$this->loadFromFile( $input );
					return;
				}
				catch( Exception $ex ) {}
			}
			
			try {
				$this->loadFromString( $input );
				return;
			}
			catch( Exception $ex ) {}
			
			throw new ImageException( exceptionMessage( 'image.load.unable' ) );
		}
		/**#@-*/
		/**
		 * Löscht den Inhalt dieses Bildes und erstellt es neu.
		 * Wirft InvalidArgumentException, wenn Parameter fehlerhaften Typs übergeben
		 * werden.
		 * 
		 * @throws InvalidArgumentException
		 * @return void
		 * @param integer Breite des zu erstellenden Bildes
		 * @param integer Höhe des zu erstellenden Bildes
		 * @see http://php.net/imagecreate
		 */
		public function create( $width, $height ) {
			if( !is_int( $width ) ) {
				throw new InvalidArgumentException( exceptionMessage( 'type.integer', 'width' ) );
			}
			else if( !is_int( $height ) ) {
				throw new InvalidArgumentException( exceptionMessage( 'type.integer', 'height' ) );
			}
			
			if( $this->isInitialized() ) {
				$this->destroy();
			}
			
			$handle = imageCreate( $width, $height );
			if( !self::IsGDResource( $handle ) ) {
				throw new ImageException( exceptionMessage( 'image.create.palette' ) );
			}
			
			$this->handle = $handle;
		}
		
		/**
		 * Löscht den Inhalt dieses Bildes und erstellt es als TrueColor-Bild neu.
		 * Wirft InvalidArgumentException, wenn Parameter fehlerhaften Typs übergeben
		 * werden.
		 * 
		 * @throws InvalidArgumentException
		 * @return void
		 * @param integer Breite des zu erstellenden Bildes
		 * @param integer Höhe des zu erstellenden Bildes
		 * @see http://php.net/imagecreatetruecolor
		 */
		public function createTrueColor( $width, $height ) {
			if( !is_int( $width ) ) {
				throw new InvalidArgumentException( exceptionMessage( 'type.integer', 'width' ) );
			}
			else if( !is_int( $height ) ) {
				throw new InvalidArgumentException( exceptionMessage( 'type.integer', 'height' ) );
			}
			
			if( $this->isInitialized() ) {
				$this->destroy();
			}
			
			$handle = imageCreateTrueColor( $width, $height );
			if( !self::IsGDResource( $handle ) ) {
				throw new ImageException( exceptionMessage( 'image.create.truecolor' ) );
			}
			
			$this->handle = $handle;
		}
		
		/**#@+
		 * Wird kein Dateiname übergeben, so wird das Ergebnis in den
		 * Standardausgabestrom geschrieben.
		 * Wirft InvalidArgumentException, wenn Parameter fehlerhaften Typs übergeben
		 * werden.
		 * Wirft ImageException, wenn das Bild noch nicht initialisiert wurde.
		 * 
		 * @throws InvalidArgumentException, ImageException
		 * @return boolean true, wenn das Erzeugen des Bildes erfolgreich war
		 * @param string Zieldateiname für das Bild
		 */
		/**
		 * Speichert dieses Bild als Gif-Bild ab.
		 * 
		 * @see http://php.net/imagegif
		 */
		public function saveGif( $filename=null ) {
			if( !$this->isInitialized() ) {
				throw new ImageException( exceptionMessage( 'image.not_initialized' ) );
			}
			
			if( !self::IsSupported( 'gif_create' ) ) {
				throw new ImageException( exceptionMessage( 'image.support.gif_write' ) );
			}
			
			// Parameter übergeben
			if( !is_null( $filename ) ) {
				if( !is_string( $filename ) ) {
					throw new InvalidArgumentException( exceptionMessage( 'type.string' ) );
				}
				
				// Datei existiert bereits
				if( is_file( $filename ) ) {
					// aber nicht schreibbar
					if( !is_writable( $filename ) ) {
						throw new IOException( exceptionMessage( 'file.not_writable', $filename ) );
					}
				}
				// Verzeichnis überprüfen, ob dieses Skript eine Datei in diesem erstellen darf
				else if( !is_writable( dirname( $filename ) ) ) {
					throw new IOException( exceptionMessage( 'directory.not_writable', dirname( $filename ) ) );
				}
			}
			
			return( imageGif( $this->getHandle(), $filename ) );
		}
		
		/**
		 * Speichert dieses Bild als Jpeg-Bild ab.
		 * 
		 * @param integer Qualitätswert zwischen 0 und 100
		 * @see http://php.net/imagejpeg
		 */
		public function saveJpeg( $filename=null, $quality=null ) {
			if( !$this->isInitialized() ) {
				throw new ImageException( exceptionMessage( 'image.not_initialized' ) );
			}
			
			if( !self::IsSupported( 'jpeg' ) ) {
				throw new ImageException( exceptionMessage( 'image.support.jpeg' ) );
			}
			
			// Parameter übergeben
			if( !is_null( $filename ) ) {
				if( !is_string( $filename ) ) {
					throw new InvalidArgumentException( exceptionMessage( 'type.string' ) );
				}
				
				// Datei existiert bereits
				if( is_file( $filename ) ) {
					// aber nicht schreibbar
					if( !is_writable( $filename ) ) {
						throw new IOException( exceptionMessage( 'file.not_writable', $filename ) );
					}
				}
				// Verzeichnis überprüfen, ob dieses Skript eine Datei in diesem erstellen darf
				else if( !is_writable( dirname( $filename ) ) ) {
					throw new IOException( exceptionMessage( 'directory.not_writable', dirname( $filename ) ) );
				}
			}
			
			
			if( is_null( $quality ) ) {
				return( imageJpeg( $this->getHandle(), $filename ) );
			}
			else {
				if( !is_int( $quality ) ) {
					throw new InvalidArgumentException( exceptionMessage( 'type.integer' ) );
				}
				else if( $quality<0 or $quality>100 ) {
					throw new InvalidArgumentException( exceptionMessage( 'image.jpeg.quality' ) );
				}
				return( imageJpeg( $this->getHandle(), $filename, $quality ) );
			}
		}
		
		/**
		 * Speichert dieses Bild als Png-Bild ab.
		 * 
		 * @param integer Kompressionslevel 
		 * @param integer Filter
		 * @see http://php.net/imagepng
		 */
		public function savePng( $filename=null, $quality=0 ) {
			if( !$this->isInitialized() ) {
				throw new ImageException( exceptionMessage( 'image.not_initialized' ) );
			}
			
			if( !self::IsSupported( 'jpeg' ) ) {
				throw new ImageException( exceptionMessage( 'image.support.jpeg' ) );
			}
			
			// Parameter übergeben
			if( !is_null( $filename ) ) {
				if( !is_string( $filename ) ) {
					throw new InvalidArgumentException( exceptionMessage( 'type.string' ) );
				}
				
				// Datei existiert bereits
				if( is_file( $filename ) ) {
					// aber nicht schreibbar
					if( !is_writable( $filename ) ) {
						throw new IOException( exceptionMessage( 'file.not_writable', $filename ) );
					}
				}
				// Verzeichnis überprüfen, ob dieses Skript eine Datei in diesem erstellen darf
				else if( !is_writable( dirname( $filename ) ) ) {
					throw new IOException( exceptionMessage( 'directory.not_writable', dirname( $filename ) ) );
				}
			}
			
			// Qualität überprüfen
			if( !is_int( $quality ) ) {
				throw new InvalidArgumentException( exceptionMessage( 'type.integer' ) );
			}
			else if( $quality<0 or $quality>100 ) {
				throw new InvalidArgumentException( exceptionMessage( 'image.jpeg.quality' ) );
			}
			
			return( imageJpeg( $this->getHandle(), $filename, $quality ) );
		}
		/**#@-*/
		
		/**#@+
		 * Wirft ImageException, wenn das Bild noch nicht initialisiert ist.
		 * 
		 * @throws ImageException
		 */
		/**
		 * Konvertiert dieses Bild in ein Palettenbild.
		 * Es muss bereits ein Bild erstellt oder geladen worden sein.
		 * Wirft InvalidArgumentException, wenn Parameter fehlerhaften Typs übergeben
		 * werden.
		 * 
		 * @throws InvalidArgumentException
		 * @return void
		 * @param boolean true, wenn ein Dithering auf dem Bild angewendert werden
		 * soll.
		 * @param integer maximale Anzahl Farben in der Palette
		 * @see http://php.net/imagetruecolortopalette
		 */
		public function toPalette( $dither=true, $colors=256 ) {
			if( !$this->isInitialized() ) {
				throw new ImageException( exceptionMessage( 'image.not_initialized' ) );
			}
			
			if( !$this->isTrueColor() ) {
				return( true );
			}
			
			if( !is_int( $colors ) ) {
				throw new InvalidArgumentException( exceptionMessage( 'type.integer', 'colors' ) );
			}
			
			if( $colors<1 or $colors>255 ) {
				throw new InvalidArgumentException( exceptionMessage( 'palette.interval' ) );
			}
			
			imageTrueColorToPalette( $this->handle, (bool)$dither, $colors );
		}
		
		/**
		 * Konvertiert dieses Bild in ein TrueColor-Bild.
		 * Durch diese Methode wird das Alphablending auf false und SaveAlpha auf
		 * true gesetzt.
		 * 
		 * @return void
		 * @see http://php.net/imagecreatetruecolor
		 */
		public function toTrueColor() {
			if( !$this->isInitialized() ) {
				throw new ImageException( exceptionMessage( 'image.not_initialized' ) );
			}
			
			if( !$this->isTrueColor() ) {
				return( true );
			}
			
			$w = imageSX( $this->handle );
			$h = imageSY( $this->handle );
			
			$tmp = imageCreateTrueColor( $w, $h );
			imageSaveAlpha( $tmp, true );
			imageAlphaBlending( $tmp, false );
			
			for( $x=0; $x<$w; $x++ ) {
				for( $y=0; $y<$h; $y++ ) {
					imageSetPixel( $tmp, $x, $y, imageColorAt( $this->handle, $x, $y ) );
				}
			}
			
			imageDestroy( $this->handle );
			$this->handle = $tmp;
		}
		
		/**
		 * Überprüft, ob dieses Bild bereits erstellt oder geladen wurde.
		 * 
		 * @return boolean true, wenn dieses Bild bereits erstellt oder geladen wurde.
		 */
		public function isInitialized() {
			return( !is_null( $this->handle ) );
		}
		
		/**
		 * Überprüft, ob dieses Bild ein Palettenbild ist
		 * 
		 * @return boolean true, wenn es sich um ein Palettenbild handelt
		 */
		public function isPalette() {
			if( !$this->isInitialized() ) {
				throw new ImageException( exceptionMessage( 'image.not_initialized' ) );
			}
			
			return( !imageIsTrueColor( $this->handle ) );
		}
		
		/**
		 * Überprüft, ob dieses Bild ein TrueColor-Bild ist
		 * 
		 * @return boolean true, wenn es sich um ein TrueColor-Bild handelt
		 * @see http://php.net/imageistruecolor
		 */
		public function isTrueColor() {
			if( !$this->isInitialized() ) {
				throw new ImageException( exceptionMessage( 'image.not_initialized' ) );
			}
			
			return( imageIsTrueColor( $this->handle ) );
		}
		
		/**
		 * Gibt die Breite des Bildes in Pixeln als Integer zurück
		 * 
		 * @return integer
		 * @see http://php.net/imagesx
		 */
		public function getWidth() {
			if( !$this->isInitialized() ) {
				throw new ImageException( exceptionMessage( 'image.not_initialized' ) );
			}
			
			return( imageSX( $this->handle ) );
		}
		
		/**
		 * Setzt die Breite des Bildes in Pixeln. Das Bild wird dabei skaliert.
		 * 
		 * @return void
		 * @param integer
		 */
		public function setWidth( $width ) {
			if( !$this->isInitialized() ) {
				throw new ImageException( exceptionMessage( 'image.not_initialized' ) );
			}
			
			// TODO
		}
		
		/**
		 * Gibt die Höhe des Bildes in Pixeln als Integer zurück
		 * 
		 * @return integer
		 * @see http://php.net/imagesy
		 */
		public function getHeight() {
			if( !$this->isInitialized() ) {
				throw new ImageException( exceptionMessage( 'image.not_initialized' ) );
			}
			
			return( imageSY( $this->handle ) );
		}
		
		/**
		 * Setzt die Höhe des Bildes in Pixeln. Das Bild wird dabei skaliert.
		 * 
		 * @return void
		 * @param integer
		 */
		public function setHeight( $height ) {
			if( !$this->isInitialized() ) {
				throw new ImageException( exceptionMessage( 'image.not_initialized' ) );
			}
			
			// TODO
		}
		
		/**
		 * Gibt die Maße des Bildes in Pixeln als Position-Objekt zurück
		 * 
		 * @return integer
		 */
		public function getDimensions() {
			if( !$this->isInitialized() ) {
				throw new ImageException( exceptionMessage( 'image.not_initialized' ) );
			}
			
			return( new Position( imageSX( $this->handle ), imageSY( $this->handle ) ) );
		}
		
		/**
		 * Gibt die Maße des Bildes in Pixeln als Position-Objekt zurück
		 * 
		 * @return void
		 * @param Position
		 */
		public function setDimensions( Position $dim ) {
			if( !$this->isInitialized() ) {
				throw new ImageException( exceptionMessage( 'image.not_initialized' ) );
			}
			
			// TODO
		}
		
		/**
		 * Setzt den Alphablending-Modus des Bildes.
		 * 
		 * @return boolean
		 * @param boolean true, wenn der Wert gesetzt werden konnte
		 * @see http://php.net/imagealphablending
		 */
		public function setAlphablending( $blend=true ) {
			if( !$this->isInitialized() ) {
				throw new ImageException( exceptionMessage( 'image.not_initialized' ) );
			}
			
			return( imageAlphaBlending( $this->handle, (bool)$blend ) );
		}
		
		/**
		 * Setzt den Antialiasing-Modus des Bildes.
		 * 
		 * @return boolean
		 * @param boolean true, wenn der Wert gesetzt werden konnte
		 * @see http://php.net/imageantialias
		 */
		public function setAntialiasing( $antialias=true ) {
			if( !$this->isInitialized() ) {
				throw new ImageException( exceptionMessage( 'image.not_initialized' ) );
			}
			
			return( imageAntiAlias( $this->handle, (bool)$antialias ) );
		}
		
		/**
		 * Legt fest, ob der komplette Alphakanal im Bild gespeichert werden soll.
		 * Dies macht nur Sinn, wenn das Ausgabeformat dies unterstützt. Dies ist
		 * beispielsweise bei Png der Fall.
		 * 
		 * @return boolean
		 * @param boolean true, wenn der Wert gesetzt werden konnte
		 * @see http://php.net/imagesavealpha
		 */
		public function setSaveAlpha( $savealpha=true ) {
			if( !$this->isInitialized() ) {
				throw new ImageException( exceptionMessage( 'image.not_initialized' ) );
			}
			
			return( imageSaveAlpha( $this->handle, (bool)$savealpha ) );
		}
		
		/**
		 * Setzt den Interlace-Modus des Bildes.
		 * 
		 * @return boolean
		 * @param boolean true, wenn der Wert gesetzt werden konnte
		 * @see http://php.net/imageinterlace
		 */
		public function setInterlace( $interlace=true ) {
			if( !$this->isInitialized() ) {
				throw new ImageException( exceptionMessage( 'image.not_initialized' ) );
			}
			
			return( imageInterlace( $this->handle, (bool)$interlace ) );
		}
		
		/**
		 * Ermittelt die Anzahl der Farben im Bild. Handelt es sich bei diesem Bild
		 * um ein TrueColor-Bild, wird immer 0 zurückgegeben, da alle möglichen
		 * Farben im Bild vorhanden sein können.
		 * 
		 * @return integer
		 * @see http://php.net/imagecolorstotal
		 */
		public function getTotalColors() {
			if( !$this->isInitialized() ) {
				throw new ImageException( exceptionMessage( 'image.not_initialized' ) );
			}
			
			return( imageColorsTotal( $this->handle ) );
		}
		
		/**
		 * Gibt die Farbe im Bild an der übergebenen Position zurück.
		 * Wirft OutOfBoundsException, wenn die Position nicht im Bild enthalten ist.
		 * Wirft InvalidArgumentException, wenn Parameter fehlerhaften Typs übergeben
		 * werden.
		 * 
		 * @throws InvalidArgumentException, OutOfBoundsException
		 * @return Color
		 * @param integer/Position Die X-Position als Integer oder Position-Objekt
		 * @param integer Die Y-Position
		 * @see http://php.net/imagecolorat
		 */
		public function getColorAt( $x, $y=null ) {
			if( !$this->isInitialized() ) {
				throw new ImageException( exceptionMessage( 'image.not_initialized' ) );
			}
			
			if( $x instanceof Position ) {
				$x = $x->getX();
				$y = $x->getY();
			}
			
			if( !is_int( $x ) ) {
				throw new InvalidArgumentException( exceptionMessage( 'type.integer', 'x' ) );
			}
			if( !is_int( $y ) ) {
				throw new InvalidArgumentException( exceptionMessage( 'type.integer', 'y' ) );
			}
			
			$res = @imageColorAt( $this->handle, $x, $y );
			if( $res===false ) {
				throw new OutOfBoundsException( 'position.out_of_bounds', array( $x, $y ) );
			}
			
			return( $this->getColorForIndex( $res ) );
		}
		
		/**
		 * Setzt die Farbe im Bild an der übergebenen Position.
		 * Wirft ImageException, wenn die Position nicht im Bild enthalten ist.
		 * Wirft InvalidArgumentException, wenn Parameter fehlerhaften Typs übergeben
		 * werden.
		 * 
		 * @throws InvalidArgumentException
		 * @return void
		 * @param Color
		 * @param integer/Position Die X-Position als Integer oder Position-Objekt
		 * @param integer Die Y-Position
		 * @see http://php.net/imagesetpixel
		 */
		public function setColorAt( Color $color, $x, $y=null ) {
			if( !$this->isInitialized() ) {
				throw new ImageException( exceptionMessage( 'image.not_initialized' ) );
			}
			
			if( $x instanceof Position ) {
				$x = $x->getX();
				$y = $x->getY();
			}
			
			if( !is_int( $x ) ) {
				throw new InvalidArgumentException( exceptionMessage( 'type.integer', 'x' ) );
			}
			if( !is_int( $y ) ) {
				throw new InvalidArgumentException( exceptionMessage( 'type.integer', 'y' ) );
			}
			
			imageSetPixel( $this->handle, $x, $y, $this->getIndexForColor( $color ) );
		}
		
		/**
		 * Gibt die transparente Farbe des Bildes zurück.
		 * Wirft ImageException, wenn keine transparente Farbe definiert ist
		 * 
		 * @throws ImageException
		 * @return Color
		 * @see http://php.net/imagecolortransparent
		 */
		public function getTransparentColor() {
			if( !$this->isInitialized() ) {
				throw new ImageException( exceptionMessage( 'image.not_initialized' ) );
			}
			
			$res = imageColorTransparent( $this->handle );
			if( $res<=0 ) {
				throw new ImageException( 'image.no_transparent_color' );
			}
			return( $this->getColorsForIndex( $res ) );
		}
		
		/**
		 * Legt die transparente Farbe des Bildes fest.
		 * 
		 * @return boolean
		 * @param Color
		 * @see http://php.net/imagecolortransparent
		 */
		public function setTransparentColor( Color $color ) {
			if( !$this->isInitialized() ) {
				throw new ImageException( exceptionMessage( 'image.not_initialized' ) );
			}
			
			imageColorTransparent( $this->handle, $this->getIndexForColor( $color ) );
		}
		/**#@-*/
		
		/**
		 * Gibt den internen Resourcehandle zurück
		 * 
		 * @return resource null, falls noch kein Handle vorhanden ist
		 */
		public function getHandle() {
			return( $this->handle );
		}
		
		/**
		 * Gibt das Bild, welches als Pinsel für Zeichenoperationen verwendet werden
		 * soll zurück. Dieser wird immer dann verwendet, wenn eine der Farben
		 * Color::UseTiling, Color::UseStyle, Color::UseBrush,
		 * Color::UseStyleAndBrush oder keine Farbe verwendet wird. Die Strichstärke
		 * wird einmal bei Übergabe des Pinsels festgelegt
		 * Ist kein Pinsel gesetzt, wird null zurückgegeben.
		 * 
		 * @return Brush null, wenn noch kein Pinsel gesetzt wurde
		 * @see Brush
		 */
		public function getBrush() {
			return( $this->brush );
		}
		
		/**
		 * Setzt ein Bild als Pinsel für Zeichenoperationen mit der speziellen
		 * Farbe Color::Brushed
		 * 
		 * @return boolean
		 * @param Brush
		 * @see Brush
		 * @see http://php.net/imagesetbrush
		 */
		public function setBrush( Brush $brush ) {
			$this->brush = $brush;
		}
		
// 		/**
// 		 * Ersetzt eine Farbe im Bild durch eine andere.
// 		 * 
// 		 * @return boolean
// 		 * @param Color Momentane Farbe
// 		 * @param Color Neue Farbe
// 		 * @see http://php.net/imagecolorset
// 		 */
// 		public function exchangeColor( Color $source, Color $target ) {
// 			
// 		}
		
		/**
		 * Gibt die Farbe zu einem Farbhandle zurück.
		 * 
		 * @return Color
		 * @param integer
		 * @see http://php.net/imagecolorsforindex
		 */
		private function getColorForIndex( $index ) {
			return( imageColorsForIndex( $this->handle, $index ) );
		}
		
		/**
		 * Gibt den Farbhandle zu einer Farbe zurück. Ist diese Farbe noch nicht im
		 * Bild enthalten, wird diese alloziiert.
		 * 
		 * @return integer
		 * @param Color
		 * @see http://php.net/imagecolorallocatealpha
		 */
		private function getIndexForColor( Color $color ) {
			return( imageColorAllocateAlpha( $this->handle, $color->getRed(), $color->getGreen(), $color->getBlue(), $color->getAlpha() ) );
		}
		
		/**
		 * Gibt den exakten oder nächsten möglichen Farbhandle zu einer Farbe zurück.
		 * 
		 * @return integer
		 * @param Color
		 * @see http://php.net/imagecolorresolvealpha
		 */
		private function getClosestIndexForColor( Color $color ) {
			return( imageColorResolveAlpha( $this->handle, $color->getRed(), $color->getGreen(), $color->getBlue(), $color->getAlpha() ) );
		}
		
		/**
		 * Dealloziiert eine Farbe in diesem Bild
		 * 
		 * @return boolean
		 * @param integer Farbindex
		 * @see http://php.net/imagecolordeallocate
		 */
		private function deallocateColor( $color ) {
			return( imageColorDeallocate( $color ) );
		}
		
		/**
		 * Wendet einen Faltungsfilter auf das Bild an.
		 * Wirft InvalidArgumentException, wenn Parameter fehlerhaften Typs übergeben
		 * werden.
		 * 
		 * @throws InvalidArgumentException
		 * @return boolean
		 * @param array 3x3-Matrix, welche den Faltungsfilter beschreibt
		 * @param float Normalisierungswert
		 * @param float Offsetwert
		 * @see http://php.net/imageconvolution
		 */
		public function convolve( array $matrix, $div, $offset ) {
			if( !$this->isInitialized() ) {
				throw new ImageException( exceptionMessage( 'image.not_initialized' ) );
			}
			
			// Die Matrix überprüfen
			if( count( $matrix )!=3 ) {
				throw new InvalidArgumentException( exceptionMessage( 'convolution.matrix.3x3' ) );
			}
			
			foreach( $matrix as $firstDimension ) {
				if( count( $firstDimension )!=3 ) {
					throw new InvalidArgumentException( exceptionMessage( 'convolution.matrix.3x3' ) );
				}
				foreach( $firstDimension as $secondDimension ) {
					if( !is_int( $secondDimension ) ) {
						throw new InvalidArgumentException( exceptionMessage( 'type.integer', 'matrix' ) );
					}
				}
			}
			
			// Normalisierungswert überprüfen
			if( !is_float( $div ) ) {
				throw new InvalidArgumentException( exceptionMessage( 'type.float', 'div' ) );
			}
			
			// Offsetwert überprüfen
			if( !is_float( $offset ) ) {
				throw new InvalidArgumentException( exceptionMessage( 'type.float', 'offset' ) );
			}
			
			return( imageConvolution( $this->handle, $matrix, $div, $offset ) );
		}
		
		/**
		 * Wendet einen Filter auf das Bild an. Mögliche Filter sind:
		 *  IMG_FILTER_NEGATE, IMG_FILTER_GRAYSCALE, IMG_FILTER_BRIGHTNESS,
		 *  IMG_FILTER_CONTRAST, IMG_FILTER_COLORIZE, IMG_FILTER_EDGEDETECT,
		 *  IMG_FILTER_EMBOSS, IMG_FILTER_GAUSSIAN_BLUR, IMG_FILTER_SELECTIVE_BLUR,
		 *  IMG_FILTER_MEAN_REMOVAL, IMG_FILTER_SMOOTH
		 * Wirft InvalidArgumentException, wenn Parameter fehlerhaften Typs übergeben
		 * werden.
		 * 
		 * @throws InvalidArgumentException
		 * @return boolean
		 * @param integer Zu verwendender Filter
		 * @param integer Optionaler Parameter für den Filter
		 * @param integer Optionaler Parameter für den Filter
		 * @param integer Optionaler Parameter für den Filter
		 * @param integer Optionaler Parameter für den Filter
		 * @see http://php.net/imagefilter
		 */
		public function filter( $filter, $arg1=0, $arg2=0, $arg3=0, $arg4=0 ) {
			if( !$this->isInitialized() ) {
				throw new ImageException( exceptionMessage( 'image.not_initialized' ) );
			}
			
			if( !is_int( $filter ) ) {
				throw new InvalidArgumentException( exceptionMessage( 'type.integer', 'filter' ) );
			}
			
			switch( $filter ) {
				// Filter ohne Parameter
				case IMG_FILTER_NEGATE:
				case IMG_FILTER_GRAYSCALE:
				case IMG_FILTER_BRIGHTNESS:
				case IMG_FILTER_EDGEDETECT:
				case IMG_FILTER_EMBOSS:
				case IMG_FILTER_GAUSSIAN_BLUR:
				case IMG_FILTER_SELECTIVE_BLUR:
				case IMG_FILTER_MEAN_REMOVAL:
					return( imageFilter( $this->handle, $filter ) );
				// Filter mit einem Parameter
				case IMG_FILTER_CONTRAST:
				case IMG_FILTER_SMOOTH:
					return( imageFilter( $this->handle, $filter, $arg1 ) );
				// Filter mit 3 oder 4 Parameter
				case IMG_FILTER_COLORIZE:
					return( imageFilter( $this->handle, $filter, $arg1, $arg2, $arg3, $arg4 ) );
				default:
					throw new InvalidArgumentException( exceptionMessage( 'filter.not_valid' ) );
			};
		}
		
		/**
		 * Kopiert das übergebene Bild in dieses Bild hinein.
		 * Wirft InvalidArgumentException, wenn Parameter fehlerhaften Typs übergeben
		 * werden.
		 * 
		 * @throws InvalidArgumentException
		 * @return boolean
		 * @param GDImage Quellbild
		 * @param Position Zielposition
		 * @param Position Quellposition
		 * @param integer Breite des Ausschnittes aus dem Quellbild
		 * @param integer Höhe des Ausschnittes aus dem Quellbild
		 * @param integer Prozentuale Transparenz des Quellbildes. Erlaubt sind Werte
		 * zwischen 0 und 100, wobei 0 für komplette Transparenz steht.
		 * @see http://php.net/imagecopy
		 * @see http://php.net/imagecopymerge
		 */
		public function copy( GDImage $sourceImg, Position $targetPos, Position $sourcePos, $sourceW=null, $sourceH=null, $transparency=100 ) {
			if( !$this->isInitialized() ) {
				throw new ImageException( exceptionMessage( 'image.not_initialized' ) );
			}
			if( !$sourceImg->isInitialized() ) {
				throw new ImageException( exceptionMessage( 'image.not_initialized' ) );
			}
			
			// Quellbild mit kompletter Transparenz?
			if( $transparency==0 ) {
				return( true );
			}
			
			// Wurden keine Quellbildmaße übergeben, volle Maße des Quellbildes verwenden
			if( is_null( $sourceW ) ) {
				$sourceW = $sourceImg->getWidth();
			}
			if( is_null( $sourceH ) ) {
				$sourceH = $sourceImg->getHeight();
			}
			
			// Quellbildmaße auf Integer überprüfen
			if( !is_int( $sourceW ) ) {
				throw new InvalidArgumentException( exceptionMessage( 'type.integer', 'sourceW' ) );
			}
			if( !is_int( $sourceH ) ) {
				throw new InvalidArgumentException( exceptionMessage( 'type.integer', 'sourceH' ) );
			}
			
			// Transparenzwert überprüfen
			if( !is_int( $transparency ) ) {
				throw new InvalidArgumentException( exceptionMessage( 'type.integer', 'transparency' ) );
			}
			else if( $transparency<0 or $transparency>100 ) {
				throw new InvalidArgumentException( exceptionMessage( 'image.copy.transparency_range' ) );
			}
			
			if( $transparency==100 ) {
				return( imageCopy( $this->handle, $sourceImg->getHandle(), $targetPos->getX(), $targetPos->getY(), $sourcePos->getX(), $sourcePos->getY(), $sourceW, $sourceH ) );
			}
			else {
				return( imageCopyMerge( $this->handle, $sourceImg->getHandle(), $targetPos->getX(), $targetPos->getY(), $sourcePos->getX(), $sourcePos->getY(), $sourceW, $sourceH, $transparency ) );
			}
		}
		
		/**
		 * Skaliert dieses Bild.
		 * Wirft InvalidArgumentException, wenn Parameter fehlerhaften Typs übergeben
		 * werden.
		 * 
		 * @throws InvalidArgumentException
		 * @return boolean
		 * @param integer Neue Breite
		 * @param integer Neue Höhe
		 */
		public function resize( $newWidth, $newHeight ) {
			$w = $this->getWidth();
			$h = $this->getHeight();
			
			$tmp = imageCreateTrueColor( $newWidth, $newHeight );
			imageSaveAlpha( $tmp, true );
			imageAlphaBlending( $tmp, false );
			
			imageFill( $tmp, imageColorAllocateAlpha( $tmp, 255, 255, 255, 127 ) );
			
			imageCopyResampled( $tmp, $this->handle, 0, 0, 0, 0, $newWidth, $newHeight, $w, $h );
			
			imageDestroy( $this->handle );
			
			$this->handle = $tmp;
		}
		
		/**
		 * Dreht dieses Bild.
		 * Wirft InvalidArgumentException, wenn Parameter fehlerhaften Typs übergeben
		 * werden.
		 * 
		 * @throws InvalidArgumentException
		 * @return boolean
		 * @param integer Rotationswinkel in Grad
		 */
		public function rotate( $degree ) {}
		
	}
	
?>