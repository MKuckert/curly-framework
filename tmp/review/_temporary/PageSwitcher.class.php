<?php
if( !defined( "_CORE_" ) ) die( "Unauthorisierter Zugriff!" );

if( !defined( "_CLASS_PAGESWITCHER_" ) ) {
	define( "_CLASS_PAGESWITCHER_", 1 );
	
	/**
	 * Übernimmt das Organisieren und Erstellen einer Seitenwechsel-Navigation
	 *  Bsp: << < 1 2 3 > >>
	 * 
	 * @since 0.03 - 16.04.2007
	 */
	class PageSwitcher implements Iterator {
		
		/**
		 * aktuelle Seitenanzahl
		 * 
		 * @type int
		 * @since 0.03 - 16.04.2007
		 */
		private $curpage;
		
		/**
		 * maximale Seitenzahl
		 * 
		 * @type int
		 * @since 0.03 - 16.04.2007
		 */
		private $maxpages;
		
		/**
		 * Anzahl Einträge pro Seite
		 * 
		 * @type int
		 * @since 0.03 - 16.04.2007
		 */
		private $perpage;
		
		/**
		 * Die erzeugten Links
		 * 
		 * @type array
		 * @since 0.03 - 16.04.2007
		 */
		private $links = array();
		
		/**
		 * Aktuelle Position der Iteration
		 * 
		 * @type int
		 * @since 0.03 - 16.04.2007
		 */
		private $cursor = 0;
		
		/**
		 * Vorlage für einen Link
		 * Ersetzt werden die Templates %page%, %limit%, %name%
		 * Standard ist '<a class="pageswitch-link" href="?page=%page%&limit=%limit%">%name%</a>'
		 * 
		 * @type string
		 * @since 0.03 - 16.04.2007
		 */
		public $linkTemplate = '<a class="pageswitch-link" href="?page=%page%&limit=%limit%">%name%</a>';
		
		/**
		 * Vorlage für einen aktiven Link
		 * Ersetzt werden die Templates %page%, %limit%, %name%
		 * Standard ist '<a class="pageswitch-active" href="?page=%page%&limit=%limit%">%name%</a>'
		 * 
		 * @type string
		 * @since 0.03 - 16.04.2007
		 */
		public $activeTemplate = '<a class="pageswitch-active" href="?page=%page%&limit=%limit%">%name%</a>';
		
		/**
		 * Vorlage für einen deaktivierten Link
		 * Ersetzt werden die Templates %page%, %limit%, %name%
		 * Standard ist '<span class="pageswitch-nolink">%name%</span>'
		 * 
		 * @type string
		 * @since 0.03 - 16.04.2007
		 */
		public $noLinkTemplate = '<span class="pageswitch-nolink">%name%</span>';
		
		/**
		 * Maximalanzahl zu erzeugender URIs seitlich des aktuellen Links
		 * Standard ist 2
		 * 
		 * @type int
		 */
		public $neighbours = 2;
		
		/**
		 * Konstruktor
		 * Wirft OutOfRangeException bei negativen Werten
		 * 
		 * @param int			aktuelle Seitenzahl
		 * @param int			maximale Seitenzahl
		 * @param int			Anzahl Einträge pro Seite
		 * @since 0.03 - 16.04.2007
		 */
		public function __construct( $curpage, $maxpages, $perpage ) {
			if( !is_int( $curpage ) or $curpage<1 ) {
				throw new OutOfRangeException( "Der Parameter curpage muss vom Typ Integer und positiv sein!" );
			}
			if( !is_int( $maxpages ) or $maxpages<1 ) {
				throw new OutOfRangeException( "Der Parameter maxpages muss vom Typ Integer und positiv sein!" );
			}
			if( !is_int( $perpage ) or $perpage<1 ) {
				throw new OutOfRangeException( "Der Parameter perpage muss vom Typ Integer und positiv sein!" );
			}
			
			if( $curpage > $maxpages ) {
				$curpage = $maxpages;
			}
			$this->curpage = $curpage;
			$this->maxpages = $maxpages;
			$this->perpage = $perpage;
		}
		
		/**
		 * Berechnet die Navigationselemente
		 * 
		 * @return void
		 */
		public function create() {
			// Link am weitesten Links
			$maxLeft = max( $this->curpage - $this->neighbours, 1 );
			
			// Link am weitesten Rechts
			$maxRight = min( $this->curpage + $this->neighbours, $this->maxpages );
			
			//// Links erzeugen
			$this->links = array();
			
			// vorderste Links erzeugen
			$t = $this->curpage==1 ? $this->noLinkTemplate : $this->linkTemplate;
			$this->links[] = str_replace(
				array( '%page%', '%limit%', '%name%' ),
				array( 1, $this->perpage, '<span style="letter-spacing:-3px;">&lt;</span>&lt;' ), $t
			);
			$this->links[] = str_replace(
				array( '%page%', '%limit%', '%name%' ),
				array( $this->curpage-1, $this->perpage, '&lt;' ), $t
			);
			
			// Zahlenlinks erzeugen
			for( $i=$maxLeft; $i<=$maxRight; $i++ ) {
				// Aktiver Link
				$t = $i==$this->curpage ? $this->activeTemplate : $this->linkTemplate;
				
				$this->links[] = str_replace(
					array( '%page%', '%limit%', '%name%' ),
					array( $i, $this->perpage, $i ), $t
				);
			}
			
			// hinterste Links erzeugen
			$t = $this->curpage==$this->maxpages ? $this->noLinkTemplate : $this->linkTemplate;
			$this->links[] = str_replace(
				array( '%page%', '%limit%', '%name%' ),
				array( $this->curpage+1, $this->perpage, '&gt;' ), $t
			);
			$this->links[] = str_replace(
				array( '%page%', '%limit%', '%name%' ),
				array( $this->maxpages, $this->perpage, '<span style="letter-spacing:-3px;">&gt;</span>&gt;' ), $t
			);
		}
		
		/**
		 * Setzt den Iterator-Cursor zurück auf den Anfang
		 * 
		 * @return string
		 * @since 0.03 - 16.04.2007
		 */
		public function rewind() {
			$this->cursor = 0;
			return( $this->current() );
		}
		
		/**
		 * Gibt den aktuellen Wert zurück
		 * 
		 * @return string
		 * @since 0.03 - 16.04.2007
		 */
		public function current() {
			if( $this->valid() ) {
				return( $this->links[$this->cursor] );
			}
			else {
				return( null );
			}
		}
		
		/**
		 * Gibt den aktuellen Schlüssel zurück
		 * 
		 * @return int
		 * @since 0.03 - 16.04.2007
		 */
		public function key() {
			return( $this->cursor );
		}
		
		/**
		 * Setzt den Iterator-Cursor eine Position weiter und gibt den Wert zurück
		 * 
		 * @return string
		 * @since 0.03 - 16.04.2007
		 */
		public function next() {
			$this->cursor++;
			return( $this->current() );
		}
		
		/**
		 * Überprüft, ob die aktuelle Iterator-Cursor-Position valide ist
		 * 
		 * @return bool
		 * @since 0.03 - 16.04.2007
		 */
		public function valid() {
			return( isset( $this->links[$this->cursor] ) );
		}
		
		/**
		 * Lesezugriff auf die Member
		 * 
		 * @return mixed
		 * @param string		Name des Member
		 * @since 0.10 - 20.05.2007
		 */
		public function __get( $var ) {
			return( $this->$var );
		}
		
	};
}
?>