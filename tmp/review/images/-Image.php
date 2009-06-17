<?php
if( !defined( "_CORE_" ) ) die( "Unauthorisierter Zugriff!" );

if( !defined( "_CLASS_IMAGE_" ) ) {
	define( "_CLASS_IMAGE_", 1 );
	/**
	 * Erster Entwurf: 02.07.2006
	 * Optimierung: 27.11.2006
	 * 
	 * Diese Klasse behandelt die Images von PHP in einer gekapselten Klasse.
	 * Es existieren neue und einfache Features zum Übergeben von
	 * Pixel-Objekten anstatt jede Farbe einzeln zu allokieren.
	 * 
	 * @author Martin Kuckert
	 * @version 1.0
	 */
	
	/**
	 * Erstellt aus einem Hexadezimalstring ein Pixelobjekt
	 * 
	 * @return Pixel
	 * @param string		Mindestlänge 6 Zeichen!
	 * @deprecated 0.20 - 26.07.2007
	 */
	function strtopixel( $str ) {
		return( Pixel::parse( $str ) );
	}
	
	/**
	 * Das Image-Module und gleichzeitig die Klasse für die Darstellung und
	 * Modifikation von Bilddateien
	 */
	class Image extends Module {
		
	    /**
		 * Initialisiert dieses Modul. Sollte nur von der ModuleLoader-Klasse
		 * verwendet werden.
		 * 
		 * @return void
		 * @param array				Einige Parameter für die Initialisierung des Modules
		 * @since 0.21 - 11.08.2007
		 */
		static public function initialize( $args ) {
			ClassLoader::addClass( 'ImageException', $args['dir'].'ImageException.class.php' );
			ClassLoader::addClass( 'Pixel', $args['dir'].'Pixel.class.php' );
			ClassLoader::addClass( 'Captcha', $args['dir'].'Captcha.class.php' );
			self::$args = $args;
		}
		
		/**
		 * Default-Maße eines Image-Objektes, wenn keine Parameter übergeben wurden
		 */
		const _def_width = 100;
		const _def_height = 100;
		
		/**
		 * Aktuelle Ausmaße des Bildes
		 */
		private $_width;
		private $_height;
		
		/**
		 * Ressourcehandle des Bildes
		 */
		private $imageHandle;
		
		/**
		 * Array der bereits alloziierten Farben
		 */
		private $_palette = array();
		
		/**
		 * Dateinamen der geladenen benutzerdefinierten Schriftarten.
		 * Die Bitmap-Schriftdateinamen sind die Schlüssel im _bmp_fonts-Array und die
		 * dazugehörigen Font-Indizes die Werte.
		 * Bei dem _ttf_fonts-Array sind die beim Laden der Schriften vergebenen Namen die
		 * Schlüssel und die Dateinamen die Werte.
		 */
		private $_bmp_fonts = array( 1, 2, 3, 4, 5 );
		private $_ttf_fonts = array();
		
		/**
		 * Ein Array aus vordefinierten Farbkonstanten
		 */
		private $_const_color = array( IMG_COLOR_TILED, IMG_COLOR_STYLED, IMG_COLOR_BRUSHED, IMG_COLOR_STYLEDBRUSHED, IMG_COLOR_TRANSPARENT );
		
		/**
		 * Konstruktor der Klasse
		 */
		public function __construct( $_1=false, $_2=false ) {
			
			// Ein String als Parameter übergeben
			if( $_2===false and is_string( $_1 ) ) {
				// evtl. Datei
				if( file_exists( $_1 ) and $type = exif_imagetype( $_1 ) ) {
					switch( $type ) {
						// GIF
						case IMAGETYPE_GIF:
							$this->imageHandle = imagecreatefromgif( $_1 );
							break;
						// JPEG
						case IMAGETYPE_JPEG:
							$this->imageHandle = imagecreatefromjpeg( $_1 );
							break;
						// PNG
						case IMAGETYPE_PNG:
							$this->imageHandle = imagecreatefrompng( $_1 );
							break;
						// unbekannt
						default:
							break;
					}
					
				}
				// Image als String
				else {
					$this->imageHandle = imagecreatefromstring( $_1 );
				}
				
				$this->_width = imagesx( $this->imageHandle );
				$this->_height = imagesy( $this->imageHandle );
				
				$tmp = $this->imageHandle;
				
				$this->imageHandle = imagecreatetruecolor( $this->_width, $this->_height );
				imagesavealpha( $this->imageHandle, true );
				imagealphablending( $this->imageHandle, false );
				
				for( $x=0; $x<$this->_width; $x++ ) {
					for( $y=0; $y<$this->_height; $y++ ) {
						$index = imagecolorat( $tmp, $x, $y );
						imagesetpixel( $this->imageHandle, $x, $y, $index );
					}
				}
				
				imagedestroy( $tmp );
				unset( $tmp );
			}
			// zwei int-Übergeben
			else if( is_int( $_1 ) and is_int( $_2 ) ) {
				$this->imageHandle = imagecreatetruecolor( $_1, $_2 );
				// Leeres Bild weiß färben
				$this->fill( new Pixel( 255, 255, 255, 100 ) );
				
				$this->_width = $_1;
				$this->_height = $_2;
				
			}
			
			if( !isset( $this->imageHandle ) or !$this->imageHandle ) {
				$this->imageHandle = imagecreatetruecolor( self::_def_width, self::_def_height );
				// Leeres Bild weiß färben
				$this->fill( new Pixel( 255, 255, 255 ) );
				
				$this->_width = self::_def_width;
				$this->_height = self::_def_height;
				
			}
			$this->setAlphablending( false );
			$this->setSaveAlphaFlag( true );
		}
		
		/**
		 * Destruktor der Klasse
		 */
		public function __destruct() {
			if( isset( $this->imageHandle ) ) {
				imagedestroy( $this->imageHandle );
			}
		}
		
		/**
		 * Gibt vom Bild belegte Ressourcen wieder frei
		 */
		public function destroy() {
			if( isset( $this->imageHandle ) ) {
				imagedestroy( $this->imageHandle );
			}
			unset( $this->imageHandle );
		}
		
		/**
		 * Wirft eine ImageException-Ausnahme bei Fehlern
		 * 
		 * @param string	Fehlertext
		 */
		protected function _trigger_error( $errmsg ) {
			throw new ImageException( "Image: ".$errmsg );
		}
		
		/**
		 * Nimmt die Farbe col mit in die Farbpalette des Bildes auf und gibt
		 * den Index zu dieser Farbe in der Palette zurück.
		 * 
		 * @return int
		 * @param Pixel		Oder eine IMG_COLOR_*-Farbe
		 */
		protected function allocate( $col ) {
			// Farbkonstante übergeben
			if( is_int( $col ) ) {
				if( in_array( $col, $this->_const_color ) ) {
					return( $col );
				}
				else {
					$this->_trigger_error( "Keine Farbkonstante ".$col." definiert!" );
				}
			}
			// Farbwert ist bereits bekannt
			if( array_key_exists( $col->str(), $this->_palette ) ) {
				return( $this->_palette[$col->str()] );
			}
			// Farbwert muss noch ermittelt werden
			else {
				return( ( $this->_palette[$col->str()] = imagecolorallocatealpha( $this->imageHandle, $col->r, $col->g, $col->b, $col->alpha ) ) );
			}
		}
		
		/**
		 * Löscht die Farbe col aus der Palette
		 * 
		 * @param Pixel
		 */
		protected function deallocate( $col ) {
			if( !in_array( $col, $this->_const_color ) ) {
				 imagecolordeallocate( $this->allocate( $col ) );
			}
		}
		
		/**
		 * Gibt den Index $in als Pixel-Objekt zurück, außer $in ist eine der IMG_COLOR_*-Farben
		 * 
		 * @return Pixel
		 */
		protected function _index( $in ) {
			if( in_array( $in, $this->_const_color ) ) {
				return( $in );
			}
			if( $in >= 170 ) {
				return( new Pixel( 0, 0, 0, 0 ) );
			}
			if( $in == -1 ) {
				return( false );
			}
			$cols = imagecolorsforindex( $this->imageHandle, $in );
			return( new Pixel( $cols['red'], $cols['green'], $cols['blue'], $cols['alpha'] ) );
		}
		
		/**
		 * Gibt die Breite des Bildes zurück
		 * 
		 * @return int
		 */
		public function width() {
			return( $this->_width );
		}
		
		/**
		 * Gibt die Höhe des Bildes zurück
		 * 
		 * @return int
		 */
		public function height() {
			return( $this->_height );
		}
		
		/*****************************************************************************************************
		 * Export in versch. Bilddateien
		 ****************************************************************************************************/
		
		/**
		 * Schreibt das Image in ein GIF-Bild.
		 * Ist output gesetzt, so wird das Bild in diese Datei geschrieben,
		 * ansonsten direkt an den Client gesendet (Content-Type wird auch gesandt!)
		 * 
		 * @param string 
		 */
		public function gif( $output=false ){
			if( $output===false ) {
				header( "Content-Type: image/gif" );
				return( imagegif( $this->imageHandle ) );
			}
			else {
				return( imagegif( $this->imageHandle, $output ) );
			}
		}
		
		/**
		 * Schreibt das Image in ein JPEG-Bild.
		 * Ist output gesetzt, so wird das Bild in diese Datei geschrieben,
		 * ansonsten direkt an den Client gesendet (Content-Type wird auch gesandt!)
		 * 
		 * @param string 
		 */
		public function jpeg( $output=false, $quality=100 ){
			if( $output===false ) {
				header( "Content-Type: image/jpeg" );
				imagejpeg( $this->imageHandle );
			}
			else {
				imagejpeg( $this->imageHandle, $output, $quality );
			}
		}
		
		/**
		 * Schreibt das Image in ein PNG-Bild.
		 * Ist output gesetzt, so wird das Bild in diese Datei geschrieben,
		 * ansonsten direkt an den Client gesendet (Content-Type wird auch gesandt!)
		 * 
		 * @param string 
		 */
		public function png( $output=false ){
			if( $output===false ) {
				header( "Content-Type: image/png" );
				imagepng( $this->imageHandle );
			}
			else {
				imagepng( $this->imageHandle, $output );
			}
		}
		
		/****************************************************************************************************
		 * Zeichenfunktionen
		 * Bei allen Zeichenfunktionen, bei denen ein Objekt mit einer Form entsteht, besteht die Möglichkeit,
		 * nur die Linie, die Ausumrahmung mit einer Füllung oder ein komplett einfarbiges Objekt zu zeichnen.
		 * Dies ist dadurch gekennzeichnet, dass die Funktion zwei Farbparameter aufweißt!
		 ****************************************************************************************************/
		
		/**
		 * Füllt das ganze Bild mit der Farbe $col oder beginnt bei dem Punkt $x/$y
		 * zu füllen
		 * 
		 * @return void
		 * @param Pixel		Die Farbe, die das Bild annehmen soll
		 * @param int		X-Koordinate, ab der das Füllen des Bildes beginnen soll
		 * @param int		Y-Koordinate, ab der das Füllen des Bildes beginnen soll
		 */
		public function fill( $col, $xs=0, $ys=0 ) {
			$col = $this->allocate( $col );
			for( $y=$ys; $y<$this->_height; $y++ ) {
				for( $x=$xs ;$x<$this->_width; $x++ ) {
					imagesetpixel( $this->imageHandle, $x, $y, $col );
				}
			}
		}
		
		/**
		 * Füllt einen Bereich des Bildes mit der Farbe $col, beginnend bei dem Punkt $x/$y.
		 * Dieser Bereich wird durch die Farbe $border begrenzt. Diese Methode bietet die
		 * Funktionalität eines Farbeimer-Werkzeuges in einigen Grafikprogrammen
		 * 
		 * @return void
		 * @param int		X-Koordinate, ab der das Füllen des Bereiches beginnen soll
		 * @param int		Y-Koordinate, ab der das Füllen des Bereiches beginnen soll
		 * @param Pixel		Die Farbe, die den Bereich umrahmt
		 * @param Pixel		Die Farbe, die der Bereich annehmen soll
		 */
		public function fillToBorder( $x, $y, $border, $col ) {
			imagefilltoborder( $this->imageHandle, $x, $y, $border, $col );
		}
		
		/**
		 * Setzt ein einzelnes Pixel im Bild auf die Farbe col
		 * 
		 * @return void
		 * @param Pixel		Die Farbe, die das Pixel annehmen soll
		 * @param int		X-Koordinate des Pixels
		 * @param int		Y-Koordinate des Pixels
		 */
		public function setPixel( $x, $y, $col ) {
			imagesetpixel( $this->imageHandle, $x, $y, $this->allocate( $col ) );
		}
		
		/**
		 * Zeichnet einen Kreis in das Bild
		 * 
		 * @return void
		 * @param int		X-Koordinate des Mittelpunkt des Kreises
		 * @param int		Y-Koordinate des Mittelpunkt des Kreises
		 * @param int		Radius des Kreises
		 * @param Pixel		Rahmenfarbe
		 * @param Pixel		Füllfarbe
		 */
		public function drawCircle( $c_x, $c_y, $r, $col, $bgcol = null ) {
			$this->drawEllipse( $c_x, $c_y, $r, $r, $col, $bgcol );
		}
		
		/**
		 * Zeichnet eine Ellipse in das Bild
		 * 
		 * @return void
		 * @param int		X-Koordinate des Mittelpunkt der Ellipse
		 * @param int		Y-Koordinate des Mittelpunkt der Ellipse
		 * @param int		Radius des Kreises auf der X-Achse
		 * @param int		Radius des Kreises auf der Y-Achse
		 * @param Pixel		Rahmenfarbe
		 * @param Pixel		Füllfarbe
		 */
		public function drawEllipse( $c_x, $c_y, $r_x, $r_y, $col, $bgcol = null ) {
			// Hintergrundfarbe definiert, dann gefülltes Element zeichnen
			if( $bgcol!==null ) {
				imagefilledellipse( $this->imageHandle, $c_x, $c_y, 2*$r_x, 2*$r_y, $this->allocate( $bgcol ) );
			}
			// Vorder- und Hintergrundfarbe sind verschieden und Vordergrundfarbe ist definiert, dann Rahmen des Elements zeichnen
			if( $col != null ) {
				imageellipse( $this->imageHandle, $c_x, $c_y, 2*$r_x, 2*$r_y, $this->allocate( $col ) );
			}
		}
		
		/**
		 * Zeichnet eine Beziérkurve mithilfe von vier Punkten in das Bild
		 * 
		 * @return void
		 * @param array	Array mit den Koordinaten der Punkte. [0]/[1] ist der
		 * 				Startpunkt, [6]/[7] der Endpunkt. [2]/[3] bzw. [4]/[5] sind
		 *				die beiden Richtungspunkte.
		 * @param Pixel Farbe der Bezierkurve
		 * @param int	Anzahl der Durchgänge zur Zeichnung der Bezierkurve
		 */
		public function drawBezier( $points, $color, $num=null ) {
			if( count( $points ) < 8 ) $this->_trigger_error( "Anzahl der Punkte muss 4 sein!!" );
			if( $num===null ) $num = sqrt( pow( max( $points[6] - $points[0], 100 ), 2 ) * pow( max( $points[7] - $points[1], 100 ), 2 ) ) * 0.3;
			for( $t=0.0; $t<1.0; $t += 1.0/($num-1) ) {
				$x=$y=0.0;
				$x = $points[0] * pow( 1-$t, 3 ) + 3 * $points[2] * pow( 1-$t, 2 ) * $t
				     + 3 * $points[4] * ( 1-$t ) * pow( $t, 2 ) + $points[6] * pow( $t, 3 );
				$y = $points[1] * pow( 1-$t, 3 ) + 3 * $points[3] * pow( 1-$t, 2 ) * $t
				     + 3 * $points[5] * ( 1-$t ) * pow( $t, 2 ) + $points[7] * pow( $t, 3 );
				$this->setPixel( $x, $y, $color );
			}
		}
		
		/**
		 * Zeichnet ein Rechteck in das Bild
		 * 
		 * @return void
		 * @param int		X-Koordinate der linken oberen Ecke des Rechtecks
		 * @param int		Y-Koordinate der linken oberen Ecke des Rechtecks
		 * @param int		X-Koordinate der rechten unteren Ecke des Rechtecks
		 * @param int		Y-Koordinate der rechten unteren Ecke des Rechtecks
		 * @param Pixel		Rahmenfarbe
		 * @param Pixel		Füllfarbe
		 */
		public function drawRectangle ( $xs, $ys, $xe, $ye, $col, $bgcol=null ) {
			// Hintergrundfarbe definiert, dann gefülltes Element zeichnen
			if( $bgcol!==null ) {
				imagefilledrectangle( $this->imageHandle, $xs, $ys, $xe, $ye, $this->allocate( $bgcol ) );
			}
			// Vorder- und Hintergrundfarbe sind verschieden und Vordergrundfarbe ist definiert, dann Rahmen des Elements zeichnen
			if( $col != $bgcol and $col != null ) {
				imagerectangle( $this->imageHandle, $xs, $ys, $xe, $ye, $this->allocate( $col ) );
			}
		}
		
		/**
		 * Zeichnet eine Linie in das Bild
		 * 
		 * @return void
		 * @param int		X-Koordinate des Linienstartpunktes
		 * @param int		Y-Koordinate des Linienstartpunktes
		 * @param int		X-Koordinate des Linienendpunktes
		 * @param int		X-Koordinate des Linienendpunktes
		 * @param Pixel		Linienfarbe
		 */
		public function drawLine( $xs, $ys, $xe, $ye, $col ) {
			imageline( $this->imageHandle, $xs, $ys, $xe, $ye, $this->allocate( $col ) );
		}
		
		/**
		 * Zeichnet eine gestrichelte Linie in das Bild
		 * 
		 * @return void
		 * @param int		X-Koordinate des Linienstartpunktes
		 * @param int		Y-Koordinate des Linienstartpunktes
		 * @param int		X-Koordinate des Linienendpunktes
		 * @param int		X-Koordinate des Linienendpunktes
		 * @param Pixel		Linienfarbe
		 */
		public function drawDashedLine( $xs, $ys, $xe, $ye, $col ) {
			imagedashedline( $this->imageHandle, $xs, $ys, $xe, $ye, $this->allocate( $col ) );
		}
		
		/**
		 * Zeichnet ein Polygon in das Bild
		 * 
		 * @return void
		 * @param array		Die Koordinaten der Eckpunkte. Diese müssen im Wechsel angegeben werden, beginnend mit X 
		 * @param int		Anzahl der Koordinatenpunkte. Entspricht count(arg_points)/2
		 * @param Pixel		Rahmenfarbe
		 * @param Pixel		Füllfarbe
		 */
		public function drawPolygon( $arg_points, $num_points, $col, $bgcol = null ) {
			// Hintergrundfarbe definiert, dann gefülltes Element zeichnen
			if( $bgcol!==null ) {
				imagefilledpolygon( $this->imageHandle, $arg_points, $num_points, $this->allocate( $bgcol ) );
			}
			// Vorder- und Hintergrundfarbe sind verschieden und Vordergrundfarbe ist definiert, dann Rahmen des Elements zeichnen
			if( $col != $bgcol and $col != null ) {
				imagepolygon( $this->imageHandle, $arg_points, $num_points, $this->allocate( $col ) );
			}
		}
		
		/**
		 * Zeichnet einen Teil einer Ellipse in das Bild
		 * 
		 * @return void
		 * @param int		X-Koordinate des Mittelpunkt der Ellipse
		 * @param int		Y-Koordinate des Mittelpunkt der Ellipse
		 * @param int		Radius des Kreises auf der X-Achse
		 * @param int		Radius des Kreises auf der Y-Achse
		 * @param int		Winkel, ab dem der Bogen beginnen soll. Dabei muss gelten 0 <= s <= 360
		 * @param int		Winkel, bei dem der Bogen enden soll
		 * @param Pixel		Rahmenfarbe
		 * @param Pixel		Füllfarbe
		 * @param int		Zeichenstil des Kreises. Hierzu müssen die IMG_ARC_*-Konstanten eingesetzt werden.
		 */
		public function drawArc( $c_x, $c_y, $r_x, $r_y, $s, $e, $col, $bgcol = null, $style=IMG_ARC_PIE ) {
			// Hintergrundfarbe definiert, dann gefülltes Element zeichnen
			if( $bgcol!==null ) {
				imagefilledarc( $this->imageHandle, $c_x, $c_y, $r_x, $r_y, $s, $e, $this->allocate( $bgcol ), $style );
			}
			// Vorder- und Hintergrundfarbe sind verschieden und Vordergrundfarbe ist definiert, dann Rahmen des Elements zeichnen
			if( $col != $bgcol and $col != null ) {
				imagearc( $this->imageHandle, $c_x, $c_y, $r_x, $r_y, $s, $e, $this->allocate( $col ) );
			}
		}
		
		/**
		 * Zeichnet einen String in das Bild
		 * 
		 * @return void
		 * @param int		Indexnummer der zu verwendenden Bitmap-Schrift
		 * @param int		X-Koordinate der linken oberen Ecke des Strings
		 * @param int		Y-Koordinate der linken oberen Ecke des Strings
		 * @param string	Der zu schreibende String
		 * @param Pixel		Schriftfarbe
		 * @param bool		Wird diesem Parameter true übergeben, so wird der String aufrecht geschrieben
		 */
		public function drawString( $font, $x, $y, $string, $col, $up=false ) {
			if( $up ) {
				imagestringup( $this->imageHandle, $font, $x, $y, $string, $this->allocate( $col ) );
			}
			else {
				imagestring( $this->imageHandle, $font, $x, $y, $string, $this->allocate( $col ) );
			}
		}
		
		/**
		 * Setzt den Pinsel des Bildes, der benutzt wird, wenn als Zeichenfarbe die Konstane
		 * IMG_COLOR_BRUSHED oder IMG_COLOR_STYLEDBRUSHED eingesetzt wird.
		 * Ein Zeichnen mit einem Pinsel ist nicht mit allen draw*-Methoden möglich
		 * 
		 * @return bool
		 * @param Image&	Der zu verwendende Pinsel
		 */
		public function setBrush( Image &$brush ) {
			return( imagesetbrush( $this->imageHandle, $brush->imageHandle ) );
		}
		
		/**
		 * Setzt den Zeichenstil zum Zeichnen von Linien für das Bild. Dieser Zeichenstil kann
		 * anschließend durch Einsatz der Zeichenfarbenkonstanen IMG_COLOR_BRUSHED und
		 * IMG_COLOR_STYLEDBRUSHED genutzt werden.
		 *  Beispiel:
		 * <example>
		 * 	$image = new Image( 100, 100 );
		 *  $red = new Pixel( 255, 0, 0 );
		 * 	$blue = new Pixel( 0, 0, 255 );
		 *  $style = array( $red, $red, $red, $blue, $blue, $red, $blue );
		 *  $image->setStyle( $style );
		 *  $image->drawLine( 0, 0, 99, 99, IMG_COLOR_BRUSHED );
		 * </example>
		 * Dieses würde eine rot-blaue Linie von oben links nach unten rechts zeichnen.
		 * 
		 * @return bool
		 * @param array		Array aus Pixel-Objekten. Jedes Objekt stellt dabei ein Pixel auf der
		 * 					Stillinie dar.
		 */
		public function setStyle( $style ) {
			foreach( $style as $key=>$val ) {
				$style[$key] = $this->allocate( $val );
			}
			return( imagesetstyle( $this->imageHandle, $style ) );
		}
		
		/**
		 * Legt die Liniendicke zum Zeichnen fest
		 * 
		 * @return bool
		 * @param int		Breite der Linien in Pixeln
		 */
		public function setThickness( $thickness ) {
			return( imagesetthickness( $this->imageHandle, $thickness ) );
		}
		
		/**
		 * Setzt ein Bild für die Füllung einer Fläche. Dieses kann mit der
		 * Farbkonstante IMG_COLOR_TILED benutzt werden
		 * 
		 * @return bool
		 * @param Image		Für die Füllung zu verwendendes Bild
		 */
		public function setTile( Image &$tile ) {
			return( imagesettile( $this->imageHandle, $tile->imageHandle ) );
		}
		
		/*****************************************************************************************************
		 * Schriftfunktionen
		 * Mit diesen Funktionen können Bitmap-Schriftdateien geladen werden, die für die Methode drawString
		 * eingesetzt wird, oder erweiterte Schriftarten, wie TrueType-Schriften. Zum Schreiben mit solchen
		 * Schriften werden weitere Methoden definiert.
		 ****************************************************************************************************/
		
		/**
		 * Lädt eine Bitmap-Schriftartdatei.
		 * <example>
		 *  $blue = new Pixel( 0, 0, 255 );
		 *  $image = new Image( 10, 100 );
		 *  // Eine Bitmap-Schriftdatei nutzen
		 *  $font = $image->loadBitmapFont( 'myFonts/myBitmap.gdb');
		 *  $image->drawString( $font, 0, 5, 'Dies ist der Text', $blue );
		 * </example>
		 * Für den Einsatz von TTF-Schriften stehen die ttf-Methoden zur Verfügung
		 * 
		 * @return int
		 * @param string	Name der Bitmap-Schriftdatei
		 */
		public function loadBitmapFont( $file ) {
			// Schriftart geladen -> Index zurückgeben
			if( array_key_exists( $this->_bmp_fonts, $file ) ) {
				return( $this->_bmp_fonts[$file] );
			}
			// Schriftart noch nicht geladen -> hinzuladen und unter index speichern
			else {
				$index = imageloadfont( $file );
				$this->_bmp_fonts[$file] = $index;
				return( $index );
			}
		}
		
		/**
		 * Gibt die Höhe der BitmapFont font zurück. font muss dabei der Rückgabewert
		 * eines vorigen Aufrufs von loadBitmapFont sein.
		 * 
		 * @return int
		 * @param int		Font-Handle, der per loadBitmapFont erzeugt wurde
		 */
		static public function getFontHeight( $font ) {
			return( imagefontheight( $font ) );
		}
		
		/**
		 * Gibt die Breite der BitmapFont font zurück. font muss dabei der Rückgabewert
		 * eines vorigen Aufrufs von loadBitmapFont sein.
		 * 
		 * @return int
		 * @param int		Font-Handle, der per loadBitmapFont erzeugt wurde
		 */
		static public function getFontWidth( $font ) {
			return( imagefontwidth( $font ) );
		}
		
		/**
		 * Lädt eine TTF-Schriftartdatei. Diese kann anschließend durch
		 * den Namen name bei Schreibfunktionen angesprochen werden.
		 * <example>
		 *  $blue = new Pixel( 0, 0, 255 );
		 *  $image = new Image( 10, 100 );
		 *  $size = 12; // Größe des Textes
		 *  // Eine Bitmap-Schriftdatei nutzen
		 *  $image->loadTTF( 'myFonts/myFont.ttf', 'myFont' );
		 *  $image->drawTextTTF( 'myFont', 0, 5, 'Dies ist der Text', $blue, $size );
		 * </example>
		 * 
		 * @return string
		 * @param string	Dateiname der TTF-Schriftdatei
		 * @param string	Name, unter dem die Schriftart bei Schreibfunktionen angesprochen werden kann
		 */
		public function loadTTF( $file, $name=null ) {
			if( $name===null ) {
				$name = basename( $file, '.ttf' );
			}
			$this->_ttf_fonts[$name] = $file;
			return( $name );
		}
		
		/**
		 * Schreibt einen String mit einer TTF-Schriftart in das Bild.
		 * 
		 * @return void
		 * @param string	Name der TTF-Schriftdatei, unter der sie mit loadTTF geladen wurde
		 * @param int		X-Koordinate der linken obere Ecke des Schriftzuges
		 * @param int		Y-Koordinate der linken obere Ecke des Schriftzuges 
		 * @param string	Zu schreibender Text 
		 * @param Pixel		Schriftfarbe 
		 * @param int		Größe der Schrift
		 * @param int		Winkel, unter dem der Text geschrieben werden soll. Es wird gegen den Uhrzeigersinn gedreht!
		 */
		public function drawTextTTF( $fontname, $x, $y, $string, $col, $size, $angle=0 ) {
			$koord = $this->getTextTTFsize( $fontname, 'W', $size, $angle );
			imagettftext( $this->imageHandle, $size, $angle, $x, $y+$koord[1]-$koord[7], $this->allocate( $col ), $this->_ttf_fonts[$fontname], $string );
		}
		
		/**
		 * Vermisst die Größe eines Strings, der mit einer TTF-Schriftart in das Bild gezeichnet werden soll.
		 * Gibt die Koordinaten eines Textes, der mit der TTF fontname geschrieben werden soll, als Array zurück.
		 * Dabei gilt die Reihenfolge:
		 *	0	untere linke Ecke, X-Position
		 *	1	untere linke Ecke, Y-Position
		 *	2	untere rechte Ecke, X-Position
		 *	3	untere rechte Ecke, Y-Position
		 *	4	obere rechte Ecke, X-Position
		 *	5	obere rechte Ecke, Y-Position
		 *	6	obere linke Ecke, X-Position
		 *	7	obere linke Ecke, Y-Position
		 * 
		 * @return void
		 * @param string	Name der TTF-Schriftdatei, unter der sie mit loadTTF geladen wurde
		 * @param int		X-Koordinate der linken unteren Ecke des Schriftzuges
		 * @param int		Y-Koordinate der linken unteren Ecke des Schriftzuges 
		 * @param string	Zu schreibender Text 
		 * @param Pixel		Schriftfarbe 
		 * @param int		Größe der Schrift
		 * @param int		Winkel, unter dem der Text geschrieben werden soll. Es wird gegen den Uhrzeigersinn gedreht!
		 */
		public function getTextTTFsize( $fontname, $string, $size, $angle=0 ) {
			return( imagettfbbox( $size, $angle, $this->_ttf_fonts[$fontname], $string ) );
		}
		
		/**
		 * Bundle aus der Methode drawString und drawTextTTF, so dass mit einer Funktion Bitmap und
		 * TTF-Schriftarten genutzt werden können.
		 * 
		 * @return void
		 * @param string	Name der TTF-Schriftdatei oder Index des Bitmap-Font
		 * @param int		X-Koordinate der linken oberen Ecke des Schriftzuges
		 * @param int		Y-Koordinate der linken oberen Ecke des Schriftzuges 
		 * @param string	Zu schreibender Text
		 * @param Pixel		Schriftfarbe 
		 * @param mixed		Größe der Schrift bei TTF, Aufrechtschreiben bei Bitmap
		 * @param int		Winkel, unter dem der Text geschrieben werden soll.
		 */
		public function drawText( $fontname, $x, $y, $string, $col, $size_up=0, $angle=0 ) {
			// TTF-Font?
			if( array_key_exists( $fontname, $this->_ttf_fonts ) ) {
				$this->drawTextTTF( $fontname, $x, $y, $string, $col, $size_up, $angle );
			}
			// Bitmap-Font?
			else if( in_array( $fontname, $this->_bmp_fonts ) ) {
				$this->drawString( $fontname, $x, $y, $string, $col, (bool)$size_up );
			}
			// Fehler
			else {
				$this->_trigger_error( "Font ".$fontname." ist noch nicht geladen worden!" );
			}
		}
		
		/*****************************************************************************************************
		 * Funktionen zum Kopieren und Modifizieren einzelner Bildausschnitte
		 * Mithilfe dieser Methoden können verschiedene andere Bildausschnitte in dieses Bild kopiert werden
		 * oder Bildausschnitte können transformiert, beispielsweise gedreht werden.
		 ****************************************************************************************************/
		
		/**
		 * Kopiert einen Teil des Bildes $second in dieses Bild. Der Quellbereich wird in der Breite und Höhe
		 * durch height und width bestimmt. Die linke obere Ecke wird dabei durch srcX und srcY bestimmt.
		 * Diese Ecke wird nach destX/destY geschrieben.
		 * Dem Parameter opacity kann ein Wert zwischen 0 und 100 übergeben werden, der die Transparenz der
		 * Kopie bestimmt. opacity=0 bedeutet dabei, das keine Veränderung durchgeführt wird, während
		 * opacity=100 eine normale Kopie durchführt. Die Werte dazwischen erzeugen eine transparente Kopie.
		 * Wird der Parameter gray auf true gesetzt, wird eine graustufen-Kopie erzeugt
		 * 
		 * @return bool
		 * @param Image		Quellbild
		 * @param int		X-Koordinate der rechten oberen Ecke des Ausschnittes im Quellbild
		 * @param int		Y-Koordinate der rechten oberen Ecke des Ausschnittes im Quellbild
		 * @param int		X-Koordinate der rechten oberen Ecke des Ausschnittes im Zielbild
		 * @param int		Y-Koordinate der rechten oberen Ecke des Ausschnittes im Zielbild
		 * @param int		Breite des Bildausschnittes
		 * @param int		Höhe des Bildausschnittes
		 * @param int		Transparenzwert, den der Bildausschnitt haben soll
		 * @param bool		Ist dieser Parameter true, so wird der Bildausschnitt in Grauwerten kopiert
		 */
		public function copyFrom( Image &$second, $srcX, $srcY, $destX, $destY, $width, $height, $opacity=100, $gray=false ) {
			// Transparent enthält falschen Wert
			if( !is_int( $opacity ) or $opacity < 0 or $opacity > 100 ) {
				$this->_trigger_error( __FUNCTION__.": Parameter opacity besitzt ungültigen Wert ".$opacity );
			}
			// volle Transparenz -> nichts zeichnen
			if( $opacity==0 ) {
				return( false );
			}
			// Grau kopieren
			if( $gray ) {
				return( imagecopymergegray( $this->imageHandle, $second->imageHandle, $destX, $destY, $srcX, $srcY, $width, $height, $opacity ) );
			}
			// volle Sichtbarkeit -> normal kopieren
			else if( $opacity==100 ) {
				return( imagecopy( $this->imageHandle, $second->imageHandle, $destX, $destY, $srcX, $srcY, $width, $height ) );
			}
			// Transparenzwert -> mit Transparenz kopieren
			else {
				return( imagecopymerge( $this->imageHandle, $second->imageHandle, $destX, $destY, $srcX, $srcY, $width, $height, $opacity ) );
			}
		}
		
		/**
		 * Kopiert einen Teil des Bildes second und vergrößert/-kleinert diesen Teil auf eine
		 * bestimmte Größe
		 * 
		 * @return bool
		 * @param Image		Quellbild
		 * @param int		X-Koordinate der rechten oberen Ecke des Ausschnittes im Quellbild
		 * @param int		Y-Koordinate der rechten oberen Ecke des Ausschnittes im Quellbild
		 * @param int		X-Koordinate der rechten oberen Ecke des Ausschnittes im Zielbild
		 * @param int		Y-Koordinate der rechten oberen Ecke des Ausschnittes im Zielbild
		 * @param int		Breite des Bildausschnittes
		 * @param int		Höhe des Bildausschnittes
		 * @param int		Transparenzwert, den der Bildausschnitt haben soll
		 * @param bool		Ist dieser Parameter true, so wird der Bildausschnitt in Grauwerten kopieren
		 */
		public function copyResized( Image &$second, $srcX, $srcY, $destX, $destY, $srcW, $srcH, $destW, $destH ) {
			if( $srcW==$destW and $srcH==$destH ) {
				return( $this->copyFrom( $second, $srcX, $srcY, $destX, $destY, $srcW, $srcH ) );
			}
			return( imagecopyresized( $this->imageHandle, $second->imageHandle, $destX, $destY, $srcX, $srcY, $srcW, $srcH, $destW, $destH ) );
		}
		
		/**
		 * Dreht das Bild um einen gegebenen Winkel angle
		 * 
		 * @return void
		 * @param int		Winkel, um den gedreht werden soll
		 */
		public function rotate( $angle, $bgcol, $ignore_transparent=null ) {
			$temp = imagerotate( $this->imageHandle, $angle, $this->allocate( $bgcol ), $ignore_transparent );
			imagedestroy( $this->imageHandle );
			$this->imageHandle = $temp;
		}
		
		/**
		 * Vergrößert/Verkleinert das Bild auf die angegebenen Werte.
		 * 
		 * @return bool
		 * @param int		Neue Breite
		 * @param int		Neue Höhe
		 */
		public function resize( $width, $height ) {
			$image = new Image( $width, $height );
			$transp = imagecolortransparent( $this->imageHandle );
			if( $this->_index( $transp ) == $this->_index( 0 ) ) {
				$transp = 0;
			}
			
			if( !imagecopyresized( $image->imageHandle, $this->imageHandle, 0, 0, 0, 0, $width, $height, $this->_width, $this->_height ) ) {
				return( false );
			}
			
			if( $transp != -1 ) {
				$image->setTransparentColor( $this->_index( $transp ) );
			}
			
			$this->imageHandle = $image->imageHandle;
			unset( $image->imageHandle );
			
			$this->_width = $width;
			$this->_height = $height;
			$this->_palette = array();
			$this->_bmp_fonts = array( 1, 2, 3, 4, 5 );
			$this->_ttf_fonts = array();
			return( true );
		}
		
		/*****************************************************************************************************
		 * Filterfunktionen, die bestimmte Effekte auf das Bild legen
		 ****************************************************************************************************/
		
		/**
		 * Legt den Filter filtertype auf das Bild. Je nach Effekt müssen
		 * die Argumente arg1, arg2 und arg3 gesetzt werden.
		 * 
		 * @return bool
		 * @param int		Konstante des zu verwendenden Filter
		 * @param mixed		Je nach Filter zu verwenden
		 * @param mixed		Je nach Filter zu verwenden
		 * @param mixed		Je nach Filter zu verwenden
		 */
		public function filter( $filtertype, $arg1=null, $arg2=null, $arg3=null ) {
			if( $arg3===null ) {
				if( $arg2===null ) {
					if( $arg1===null ) {
						// Keine Argumente
						imagefilter( $this->imageHandle, $filtertype );
					}
					else {
						// 1 Argument
						imagefilter( $this->imageHandle, $filtertype, $arg1 );
					}
				}
				else {
					// 2 Argumente
					imagefilter( $this->imageHandle, $filtertype, $arg1, $arg2 );
				}
			}
			else {
				// 3 Argumente
				imagefilter( $this->imageHandle, $filtertype, $arg1, $arg2, $arg3 );
			}
		}
		
		/**
		 * Invertiert alle Farben des Bildes
		 * 
		 * @return bool		Gibt true zurück, wenn die Invertierung erfolgreich war
		 */
		public function negate() {
			$this->filter( IMG_FILTER_NEGATE );
		}
		
		/**
		 * Konvertiert das Bild in Grauwertfarben
		 * 
		 * @return bool		Gibt true zurück, wenn die Invertierung erfolgreich war
		 */
		public function convertToGrayscale() {
			$this->filter( IMG_FILTER_GRAYSCALE );
		}
	
		/**
		 * Konvertiert das Bild in ein einfarbiges Bild mit der Farbe col. Diese Methode ist
		 * ähnlich convertToGrayscale, es wird hier jedoch in eine Farbe und nicht Grau konvertiert.
		 * 
		 * @return bool		Gibt true zurück, wenn die Invertierung erfolgreich war
		 * @param Pixel		Farbe, dessen Sättigungswerte das Bild als einziges enthalten soll
		 */
		public function convertToColor( Pixel $col ) {
			$this->filter( IMG_FILTER_COLORIZE, $col->red, $col->green, $col->blue );
		}
	
		/**
		 * Setzt den Helligkeitswert des Bildes
		 * 
		 * @return bool		Gibt true zurück, wenn die Invertierung erfolgreich war
		 * @param int		Neuer Helligkeitswert. Muss zwischen 0 und 100 liegen
		 */
		public function setBrightness( $arg ) {
			$this->filter( IMG_FILTER_BRIGHTNESS, $arg );
		}
	
		/**
		 * Setzt den Konstrastwert des Bildes
		 * 
		 * @return bool		Gibt true zurück, wenn die Invertierung erfolgreich war
		 * @param int		Neuer Konstrastwert. Muss zwischen 0 und 100 liegen
		 */
		public function setContrast( $arg ) {
			$this->filter( IMG_FILTER_CONTRAST, $arg );
		}
	
		/**
		 * Hebt die Ecken in einem Bild hervor
		 * 
		 * @return bool		Gibt true zurück, wenn die Invertierung erfolgreich war
		 */
		public function detectEdges() {
			$this->filter( IMG_FILTER_EDGEDETECT );
		}
	
		/**
		 * Gibt dem Bild eine Prägung 
		 * 
		 * @return bool		Gibt true zurück, wenn die Invertierung erfolgreich war
		 */
		public function emboss() {
			$this->filter( IMG_FILTER_EMBOSS );
		}
	
		/**
		 * Wendet einen Weichzeichner auf das Bild an
		 * 
		 * @return bool		Gibt true zurück, wenn die Invertierung erfolgreich war
		 */
		public function blur() {
			$this->filter( IMG_FILTER_SELECTIVE_BLUR );
		}
	
		/**
		 * Wendet einen Gausschen Weichzeichner auf das Bild an
		 * 
		 * @return bool		Gibt true zurück, wenn die Invertierung erfolgreich war
		 */
		public function blurGaussian() {
			$this->filter( IMG_FILTER_GAUSSIAN_BLUR );
		}
	
		/**
		 * Macht das Bild weicher
		 * 
		 * @return bool		Gibt true zurück, wenn die Invertierung erfolgreich war
		 * @param int		Wert der Weichheit. Muss zwischen 0 und 100 liegen
		 */
		public function setSmoothness( $smoothness) {
			$this->filter( IMG_FILTER_SMOOTH, $smoothness );
		}
		
		/**
		 * Führt eine Gammakorrektur durch
		 * 
		 * @return void
		 * @param double	Neuer Gammawert
		 */
		public function setGamma( $val ) {
			imagegammacorrect( $this->imageHandle, 1.0, $val );
		}
		
		/*****************************************************************************************************
		 * weitere Methoden
		 ****************************************************************************************************/
		
		/**
		 * gibt die Farbe an Position ($x/$y) als Pixel-Objekt zurück
		 * 
		 * @return Pixel
		 * @param int		X-Koordinate des Pixels
		 * @param int		Y-Koordinate des Pixels
		 */
		public function getColorAt( $x, $y ) {
			return( $this->_index( imagecolorat( $this->imageHandle, $x, $y ) ) );
		}
		public function testfunc( $x, $y ) {
			return( imagecolorat( $this->imageHandle, $x, $y ) );
		}
		
		/**
		 * Schaltet den Alphakanal des Bildes an oder aus
		 * 
		 * @return bool		Wenn das Setzen des Wertes erfolgreich war, wird true zurückgegeben, sonst false
		 * @param bool		Neuer Zustandswert
		 */
		public function setAlphablending( $blend ) {
			return( imagealphablending( $this->imageHandle, (bool)$blend ) );
		}
		
		/**
		 * Gibt die Anzahl der Farben in der Palette zurück!
		 * 
		 * @return int
		 */
		public function getTotalColors() {
			return( imagecolorstotal( $this->imageHandle ) );
		}
		
		/**
		 * Ermittelt die Farbe, die col am Nahesten kommt
		 * 
		 * @return Pixel
		 * @param Pixel
		 */
		public function getClosestColor( Pixel $col ) {
			if( $col->alpha == 0 ) {
				$res = $this->_index( imagecolorclosest( $this->imageHandle, $col->red, $col->green, $col->blue ) );
			}
			else {
				$res = $this->_index( imagecolorclosestalpha( $this->imageHandle, $col->red, $col->green, $col->blue, $col->alpha ) );
			}
			return( $res );
		}
		
		/**
		 * Die der Farbe $col naheste Farbe nach den Kriterien, Weiss-/Schwarzanteil und Sättigung
		 * 
		 * @return Pixel
		 * @param Pixel
		 */
		public function getClosestColorHWB( Pixel $col ) {
			$res = imagecolorclosesthwb( $this->imageHandle, $col->red, $col->green, $col->blue );
			return( $this->_index( $res ) );
		}
		
		/**
		 * Ersetzt die Farbe old durch die Farbe new
		 * 
		 * @return bool
		 * @param Pixel
		 * @param Pixel
		 */
		public function replaceColor( Pixel $old, Pixel $new ) {
			$new = $this->allocate( $new );
			for( $x=0; $x<$this->_width; $x++ ) {
				for( $y=0; $y<$this->_height; $y++ ) {
					if( $this->_index( imagecolorat( $this->imageHandle, $x, $y ) ) == $old ) {
						imagesetpixel( $this->imageHandle, $x, $y, $new );
					}
				}
			}
		}
		
		/**
		 * Gibt die aktuelle transparente Farbe des Bildes zurück
		 * 
		 * @return Pixel	Die momentan transparente Farbe
		 */
		public function getTransparentColor() {
			return( $this->_index( imagecolortransparent( $this->imageHandle ) ) );
		}
		
		/**
		 * Legt die transparente Farbe des Bildes fest.
		 * Vorsicht: Soll die Hintergrundfarbe transparent gemacht werden, so wird
		 * diese zunächst aus technischen Gründen durch eine Ersatzfarbe ersetzt, welche
		 * anschließend als transparente Farbe festgelegt wird.
		 * Diese Ersatzfarbe ist Pixel( 255, 255, 255, 0 )
		 * 
		 * @return void
		 * @param Pixel		Neue transparente Farbe
		 */
		public function setTransparentColor( $col ) {
			if( $col == $this->_index( 0 ) ) {
				$dummycol = new Pixel( 111, 111, 111, 111 );
				$this->replaceColor( $col, $dummycol );
				$col = $dummycol;
			}
			return( $this->_index( imagecolortransparent( $this->imageHandle, $this->allocate( $col ) ) ) );
		}
		
		/**
		 * Ermittelt den Mime-Type zu einem Image-Type
		 * 
		 * @return string	Mime-Type der type entspricht
		 * @param int		IMG_*-Konstante
		 */
		static public function getMime( $type ) {
			return( image_type_to_mime_type ( $type ) );
		}
		
		/**
		 * Gibt die von der aktuell verwendeten PHP-Version unterstützten Grafik-Formate zurück.
		 * Die folgenden Bit-Werte sind möglich: IMG_GIF, IMG_JPG, IMG_PNG oder IMG_WBMP.
		 * 
		 * @return int		Bitwert mit den verfügbaren Grafik-Formaten
		 */
		static public function getTypes() {
			return( imagetypes() );
		}
		
		/**
		 * Setzt Interlace auf an oder aus
		 * 
		 * @return bool		Momentaner Aktivitätsstatus des Interlace
		 * @param bool		Mit true wird Interlace angestellt
		 */
		public function setInterlace( $value=null ) {
			if( $value===null ) {
				return( imageinterlace( $this->imageHandle ) );
			}
			else {
				return( imageinterlace( $this->imageHandle, $value ) );
			}
		}
		
		/**
		 * Setzt den Flag, ob beim Speichern von PNG-Bildern der Alphakanal
		 * komplett mitgespeichert werden soll.
		 * 
		 * @return bool		Momentaner Wert des Flags
		 * @param bool		Neuer Wert für das Flag
		 */
		public function setSaveAlphaFlag( $value=null ) {
			if( $value===null ) {
				return( imagesavealpha( $this->imageHandle ) );
			}
			else {
				return( imagesavealpha( $this->imageHandle, $value ) );
			}
		}
		
		/**
		 * Überprüft den Typ eines Bildes
		 * 
		 * @return int				 IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_SWF oder -1
		 * @param string
		 */
		static public function getImageType( $filename ) {
			$size = getimagesize( $filename );
			if( !is_array( $size ) or !isset( $size[2] ) ) {
				return( -1 );
			}
			
			switch( $size[2] ) {
				case 1:
					return( IMAGETYPE_GIF );
				case 2:
					return( IMAGETYPE_JPEG );
				case 3:
					return( IMAGETYPE_PNG );
				case 4:
					return( IMAGETYPE_SWF );
				default:
					return( -1 );
			}
		}
	};
}
?>