<?php
if( !defined( "_CORE_" ) ) die( "Unauthorisierter Zugriff!" );

if( !defined( "_CLASS_BBCODEPARSER_" ) ) {
	define( "_CLASS_BBCODEPARSER_", 1 );
	
	/**
	 * Ersetzt verschiedene BBCodes wie [URL=foo]bar[/URL] durch HTML-Tags
	 * 
	 * Folgende Codes müssen möglich sein:
	 * -----------------------------------
	 * [hr]
	 * 		-> <hr />
	 * [b]Fetter Text[/b]
	 * 		-> <b>Fetter Text</b>
	 * [url=http://example.com/]Beispiellink[/url]
	 * 		-> <a href="http://example.com/" class="external" target="_blank">Beispiellink</a>
	 * [url]http://example.com/[/url]
	 * 		-> <a href="http://example.com/" class="external" target="_blank">http://example.com/</a>
	 * [user]Maddin[/user]
	 * 		-> <a href="http://root.to.cms/user.php?user=Maddin" class="userlink">Maddin</a>
	 * [img]http://example.com/image.jpg[/img]
	 * 		-> <img src="http://example.com/image.jpg" alt="Nicht gefundenes Bild" />
	 * [thumb=http://example.com/image_thumb.jpg]http://example.com/image.jpg[/thumb]
	 * 		-> <a href="http://example.com/image.jpg" target="_blank"><img src="http://example.com/image_thumb.jpg" alt="Nicht gefundenes Bild" /></a>
	 * [color=#F00]Roter Text[/color]
	 * 		-> <span style="color:#F00;">Roter Text</span>
	 * [bgcolor=#F00]Roter Hintergrund[/bgcolor]
	 * 		-> <span style="background-color:#F00;">Roter Hintergrund</span>
	 * [font=Arial]Arialtext[/font]
	 * 		-> <span style="font-family:Arial">Arialtext</span>
	 * [size=10pt]Schriftgröße 10[/size]
	 * 		-> <span style="font-size:10pt">Schriftgröße 10</span>
	 * [left]Text links ausgerichtet[/left]
	 * 		-> <div style="text-align:left">Text links ausgerichtet</div>
	 * [right]Text rechts ausgerichtet[/right]
	 * 		-> <div style="text-align:right">Text rechts ausgerichtet</div>
	 * [center]Text zentriert ausgerichtet[/center]
	 * 		-> <div style="text-align:center">Text zentriert ausgerichtet</div>
	 * [justify]Text zentriert ausgerichtet[/justify]
	 * 		-> <div style="text-align:justify">Text zentriert ausgerichtet</div>
	 * [indent]Eingerückter Text[/indent]
	 * 		-> <div style="text-indent:20px">Eingerückter Text</div>
	 * [email]mail@example.com[/email]
	 * 		-> <a href="mailto:mail@example.com" class="email">mail@example.com</a>
	 * [email=mail@example.com]Mail mir[/email]
	 * 		-> <a href="mailto:mail@example.com" class="email">Mail mir</a>
	 * [quote]Ein Zitat[/quote]
	 * 		-> Zitat:<br /><div class="quote">Ein Zitat</div>
	 * [quote=Maddin]Ein Zitat[/quote]
	 * 		-> Zitat von Maddin:<br /><div class="quote">Ein Zitat</div>
	 * [code]Dieser Text ist Monospaced[/code]
	 * 		-> <pre>Dieser Text ist Monospaced</pre>
	 * [php]<?php echo "bla"; ?>[/php]
	 * 		-> Syntaxhighlighting des PHP-Codes
	 * [noparse][b]Kein Fetter Text[/b][hr][/noparse]
	 * 		-> [b]Kein Fetter Text[/b][hr]
	 * 
	 * Folgende Codes könnten evtl. möglich sein:
	 * ------------------------------------------
	 * [list]
	 * 	[*]Hier ist ein Listenpunkt
	 *  [*]Hier nocheiner
	 * [/list]
	 * 		-> 
	 * <ul>
	 * 		<li>Hier ist ein Listenpunkt</li>
	 *		<li>Hier nocheiner</li>
	 * </ul>
	 * 
	 * @since 0.10 - 12.05.2007
	 */
	class BBCodeParser {
		
		/**
		 * Speichert, ob Newlines in <br /> umgewandelt werden soll
		 * 
		 * @type boolean
		 * @since 0.10 - 13.05.2007
		 */
		public $nl2br;
		
		/**
		 * Speichert, ob HTML escaped werden soll
		 * 
		 * @type boolean
		 * @since 0.10 - 13.05.2007
		 */
		public $escapeHtml;
		
		/**
		 * Konstruktor
		 * 
		 * @since 0.10 - 12.05.2007
		 */
		public function __construct( $nl2br=true, $escapeHtml=true ) {
			$this->nl2br = boolval( $nl2br );
			$this->escapeHtml = boolval( $escapeHtml );
		}	// end of public function __construct()
		
		/**
		 * Ersetzt alle definierten BBCodes im Quellstring
		 * 
		 * @return string				Der ersetzte String
		 * @param string				Quellstring mit BBCodes
		 * @param boolean				true, wenn das [noparse] und [php]-Tag erlaubt ist
		 * @since 0.10 - 12.05.2007
		 */
		public function parse( $source, $inner=true ) {
			$nl2br = boolval( $this->nl2br );
			$escapeHtml = boolval( $this->escapeHtml );
			
			$GLOBALS['CURRENT_BBCODEPARSER_INSTANCE'] = &$this;
			
			// PHP
			if( $inner and ( $pos1 = stripos( $source, '[php]' ) )!==false and ( $pos2 = stripos( $source, '[/php]' ) )!==false ) {
				$source2 = $this->parse( $source, false );
				$pos3 = stripos( $source2, '[php]' );
				$pos4 = stripos( $source2, '[/php]' );
				
				$php = new PHPSyntaxHighlighter();
				
				$source2 = substr( $source2, 0, $pos3 )
						  . $php->parse( substr( $source, $pos1 + 5, $pos2 - $pos1 - 5 ) )
						  . substr( $source2, $pos4 + 6 );
				return $source2;
			}
			
			// HTML ERSETZEN
			if( $escapeHtml ) {
				// Html-Escape abstellen, damit keine Probleme in der Rekursion entstehen
				$this->escapeHtml = false;
				$source = Templatehelper::replaceHtml( $source );
			}
			
			// NOPARSE
			if( $inner and ( $pos1 = stripos( $source, '[noparse]' ) )!==false and ( $pos2 = stripos( $source, '[/noparse]' ) )!==false ) {
				$source2 = $this->parse( $source, false );
				$pos3 = stripos( $source2, '[noparse]' );
				$pos4 = stripos( $source2, '[/noparse]' );
				$source2 = substr( $source2, 0, $pos3 )
						  . substr( $source, $pos1 + 9, $pos2 - $pos1 - 9 )
						  . substr( $source2, $pos4 + 10 );
				return $source2;
			}
			
			// HR
			if( stripos( $source, '[hr]' )!==false ) {
				$source = $this->parseSimple( $source, 'hr', '<hr />' );
			}
			// B
			if( stripos( $source, '[b]' )!==false ) {
				$source = $this->parseToContent( $source, 'b' );
			}
			// I
			if( stripos( $source, '[i]' )!==false ) {
				$source = $this->parseToContent( $source, 'i' );
			}
			// U
			if( stripos( $source, '[u]' )!==false ) {
				$source = $this->parseToContent( $source, 'u' );
			}
			// CODE
			if( stripos( $source, '[code]' )!==false ) {
				$source = $this->parseToContent( $source, 'code', 'pre' );
			}
			// URL
			if( stripos( $source, '[url]' )!==false ) {
				$source = $this->parseToAttributeAndContent( $source, 'url', 'href', 'a', 'class="external" target="_blank"', Icon::getSmall('link_external').' ' );
			}
			if( stripos( $source, '[url=' )!==false ) {
				$source = $this->parseAttributeAndContent( $source, 'url', 'href', 'a', 'class="external" target="_blank"', Icon::getSmall('link_external').' ' );
			}
			// IMG
			if( stripos( $source, '[img]' )!==false ) {
				$source = $this->parseToAttribute( $source, 'img', 'src', 'img', 'alt="Nicht gefundenes Bild"' );
			}
			// COLOR
			if( stripos( $source, '[color=' )!==false ) {
				$source = $this->parseStyle( $source, 'color', 'color' );
			}
			// BGCOLOR
			if( stripos( $source, '[bgcolor=' )!==false ) {
				$source = $this->parseStyle( $source, 'bgcolor', 'background-color' );
			}
			// FONT
			if( stripos( $source, '[font=' )!==false ) {
				$source = $this->parseStyle( $source, 'font', 'font-family' );
			}
			// SIZE
			if( stripos( $source, '[size=' )!==false ) {
				$source = $this->parseStyle( $source, 'size', 'font-size' );
			}
			// AUSRICHTUNG
			if( stripos( $source, '[left]' )!==false ) {
				$source = $this->parseToStyle( $source, 'left', 'text-align', false );
			}
			if( stripos( $source, '[right]' )!==false ) {
				$source = $this->parseToStyle( $source, 'right', 'text-align', false );
			}
			if( stripos( $source, '[center]' )!==false ) {
				$source = $this->parseToStyle( $source, 'center', 'text-align', false );
			}
			if( stripos( $source, '[justify]' )!==false ) {
				$source = $this->parseToStyle( $source, 'justify', 'text-align', false );
			}
			// EINRÜCKUNG
			if( stripos( $source, '[indent]' )!==false ) {
				$source = $this->parseIndent( $source, '20px' );
			}
			// ZITAT
			if( stripos( $source, '[quote' )!==false ) {
				$source = $this->parseQuote( $source );
			}
			// USER
			if( stripos( $source, '[user]' )!==false ) {
				$source = $this->parseUserlink( $source );
			}
			// THUMB
			if( stripos( $source, '[thumb=' )!==false ) {
				$source = $this->parseThumb( $source );
			}
			// SMILIE
			if( stripos( $source, '[smilie]' )!==false ) {
				$source = $this->parseSmilie( $source );
			}
			// EMAIL
			if( stripos( $source, '[email' )!==false ) {
				$source = $this->parseEmail( $source );
			}
			
			// NEWLINES UMWANDELN
			if( $nl2br ) {
				// Newline-Ersetzung abstellen, damit keine Probleme in der Rekursion entstehen
				$this->nl2br = false;
				$source = nl2br( $source );
			}
			
			if( $nl2br ) {
				$this->nl2br = true;
			}
			if( $escapeHtml ) {
				$this->escapeHtml = true;
			}
			
			unset( $GLOBALS['CURRENT_BBCODEPARSER_INSTANCE'] );
			
			return( $source );
		}
		
		/**
		 * Parst einen simplen BBCode in der Form [tag] in einen anderen Wert replaced
		 * Beispielsweise:
		 * 	$source = '[hr]'
		 *  $tag = 'hr'
		 *  $replaced = '<hr />'
		 * -> '<hr />'
		 * 
		 * @return string				Der ersetzte String
		 * @param string				Quellstring mit BBCodes
		 * @param string				Name des zu ersetzenden Tags (tag)
		 * @param string				Zeichenkette für das Ergebnis
		 * @since 0.10 - 12.05.2007
		 */
		protected function parseSimple( $source, $tag, $replaced=null ) {
			if( is_null( $replaced ) ) {
				$replaced = '<'.$tag.' />';
			}
			
			return( str_replace( '['.$tag.']', $replaced, $source ) );
		}
		
		/**
		 * Parst einen einfachen BBCode in der Form [tag]content[/tag] in ein HTML-Tag <newtag>content</newtag>
		 * Beispielsweise:
		 * 	$source = '[b]Fetter Text[/b]'
		 *  $tag = 'b'
		 *  $newtag = null
		 * -> '<b>Fetter Text</b>'
		 * 
		 * @return string				Der ersetzte String
		 * @param string				Quellstring mit BBCodes
		 * @param string				Name des zu ersetzenden Tags (tag)
		 * @param string				Name des neuen Tags (newtag)
		 * @since 0.10 - 12.05.2007
		 */
		protected function parseToContent( $source, $tag, $newtag=null ) {
			if( is_null( $newtag ) ) {
				$newtag = $tag;
			}
			
			// Regulärer Ausdruck zum Finden der einfachen BBCodes
			$find = '/(.*)\['.$tag.'\](.*)\[\/'.$tag.'\](.*)/iUsS';
			
			// Regulärer Ausdruck zum Ersetzen der einfachen BBCodes
			$replace = '$1<'.$newtag.'>$2</'.$newtag.'>$3';
			
			// BBCodes ersetzen
			return( preg_replace( $find, $replace, $source ) );
		}
		
		/**
		 * Parst einen einfachen BBCode in der Form [tag]content[/tag] in ein HTML-Tag <newtag=content>content</newtag>
		 * Beispielsweise:
		 * 	$source = '[url]http://www.example.com/[/url]'
		 *  $tag = 'url'
		 *  $key = 'href'
		 *  $newtag = 'a'
		 *  $morekeys = 'target="_blank"'
		 * -> '<a href="http://www.example.com/" target="_blank">http://www.example.com/</a>'
		 * 
		 * @return string				Der ersetzte String
		 * @param string				Quellstring mit BBCodes
		 * @param string				Name des zu ersetzenden Tags (tag)
		 * @param string				Name des Schlüssels (key)
		 * @param string				Name des zu neuen Tags (newtag)
		 * @param string				Weitere Schlüsselwerte (morekeys="morevalues")
		 * @since 0.10 - 12.05.2007
		 */
		protected function parseToAttributeAndContent( $source, $tag, $key, $newtag=null, $morekeys=null, $morecontent='' ) {
			if( is_null( $newtag ) ) {
				$newtag = $tag;
			}
			
			// Regulärer Ausdruck zum Finden der einfachen BBCodes
			$find = '/(.*)\['.$tag.'\](.*)\[\/'.$tag.'\](.*)/iUsS';
			
			// Regulärer Ausdruck zum Ersetzen der einfachen BBCodes
			$replace = '$1<'.$newtag.' '.$key.'="$2"'.( is_null( $morekeys ) ? '' : ' '.$morekeys ).'>'.$morecontent.'$2</'.$newtag.'>$3';
			
			// BBCodes ersetzen
			return( preg_replace( $find, $replace, $source ) );
		}
		
		/**
		 * Parst einen einfachen BBCode in der Form [tag]content[/tag] in ein HTML-Tag <newtag key="content" />
		 * Beispielsweise:
		 * 	$source = '[img]http://www.example.com/image.jpg[/img]'
		 *  $tag = 'img'
		 *  $key = 'src'
		 *  $newtag = 'img'
		 *  $morekeys = 'alt=""'
		 * -> '<img src="http://www.example.com/image.jpg" alt="" />'
		 * 
		 * @return string				Der ersetzte String
		 * @param string				Quellstring mit BBCodes
		 * @param string				Name des zu ersetzenden Tags (tag)
		 * @param string				Name des Schlüssels (key)
		 * @param string				Name des zu neuen Tags (newtag)
		 * @param string				Weitere Schlüsselwerte (morekeys="morevalues")
		 * @since 0.10 - 12.05.2007
		 */
		protected function parseToAttribute( $source, $tag, $key, $newtag=null, $morekeys=null ) {
			if( is_null( $newtag ) ) {
				$newtag = $tag;
			}
			
			// Regulärer Ausdruck zum Finden der einfachen BBCodes
			$find = '/(.*)\['.$tag.'\](.*)\[\/'.$tag.'\](.*)/iUsS';
			
			// Regulärer Ausdruck zum Ersetzen der einfachen BBCodes
			$replace = '$1<'.$newtag.' '.$key.'="$2"'.( is_null( $morekeys ) ? '' : ' '.$morekeys ).' />$3';
			
			// BBCodes ersetzen
			return( preg_replace( $find, $replace, $source ) );
		}
		
		/**
		 * Parst einen einfachen BBCode in der Form [tag=value]content[/tag] in ein HTML-Tag <span style="styling:value">content</span>
		 * 
		 * @return string				Der ersetzte String
		 * @param string				Quellstring mit BBCodes
		 * @param string				Name des zu ersetzenden Tags (tag)
		 * @param string				Name des CSS-Attributes
		 * @param boolean				true für ein span (Standard), false für ein div
		 * @param string				Weitere Schlüsselwerte (morekeys="morevalues")
		 * @since 0.10 - 12.05.2007
		 */
		protected function parseStyle( $source, $tag, $styling, $span=true, $morekeys='' ) {
			// Regulärer Ausdruck zum Finden der einfachen BBCodes
			$find = '/(.*)\['.$tag.'=(.*)\](.*)\[\/'.$tag.'\](.*)/iUsS';
			
			$span = $span ? 'span' : 'div';
			
			// Regulärer Ausdruck zum Ersetzen der einfachen BBCodes
			$replace = '$1<'.$span.' style="'.$styling.':$2"'.( $morekeys=='' ? '' : ' '.$morekeys ).'>$3</'.$span.'>$4';
			
			// BBCodes ersetzen
			return( preg_replace( $find, $replace, $source ) );
		}
		
		/**
		 * Parst einen einfachen BBCode in der Form [tag]content[/tag] in ein HTML-Tag <span style="styling:tag">content</span>
		 * 
		 * @return string				Der ersetzte String
		 * @param string				Quellstring mit BBCodes
		 * @param string				Name des zu ersetzenden Tags (tag)
		 * @param string				Name des CSS-Attributes
		 * @param boolean				true für ein span (Standard), false für ein div
		 * @param string				Weitere Schlüsselwerte (morekeys="morevalues")
		 * @since 0.10 - 12.05.2007
		 */
		protected function parseToStyle( $source, $tag, $styling, $span=true, $morekeys='' ) {
			// Regulärer Ausdruck zum Finden der einfachen BBCodes
			$find = '/(.*)\['.$tag.'\](.*)\[\/'.$tag.'\](.*)/iUsS';
			
			$span = $span ? 'span' : 'div';
			
			// Regulärer Ausdruck zum Ersetzen der einfachen BBCodes
			$replace = '$1<'.$span.' style="'.$styling.':'.$tag.'"'.( $morekeys=='' ? '' : ' '.$morekeys ).'>$2</'.$span.'>$3';
			
			// BBCodes ersetzen
			return( preg_replace( $find, $replace, $source ) );
		}
		
		/**
		 * Parst einen doppelten BBCode in der Form [tag=value]content[/tag] in ein HTML-Tag <newtag key="value" morekeys="morevalues">content</newtag>
		 * Beispielsweise: [url=http://example.com/]Beispiellink[/url] -> <a href="http://example.com/" title="Ein Link">Beispiellink</a>
		 * 
		 * @return string				Der ersetzte String
		 * @param string				Quellstring mit BBCodes
		 * @param string				Name des zu ersetzenden Tags (tag)
		 * @param string				Name des Schlüssels (key)
		 * @param string				Name des zu neuen Tags (newtag)
		 * @param string				Weitere Schlüsselwerte (morekeys="morevalues")
		 * @since 0.10 - 12.05.2007
		 */
		protected function parseAttributeAndContent( $source, $tag, $key, $newtag=null, $morekeys='', $morecontent='' ) {
			if( is_null( $newtag ) ) {
				$newtag = $tag;
			}
			
			// Regulärer Ausdruck zum Finden der einfachen BBCodes
			$find = '/(.*)\['.$tag.'=(.*)\](.*)\[\/'.$tag.'\](.*)/iUsS';
			
			// Regulärer Ausdruck zum Ersetzen der einfachen BBCodes
			$replace = '$1<'.$newtag.' '.$key.'="$2"'.( $morekeys=='' ? '' : ' '.$morekeys ).'>'.$morecontent.'$3</'.$newtag.'>$4';
			
			// BBCodes ersetzen
			return( preg_replace( $find, $replace, $source ) );
		}
		
		/**
		 * Parst einen Einrückungs-BBCode
		 * 
		 * @return string				Der ersetzte String
		 * @param string				Quellstring mit BBCodes
		 * @param string				Einzurückender Wert
		 * @since 0.10 - 12.05.2007
		 */
		protected function parseIndent( $source, $indent ) {
			// Regulärer Ausdruck zum Finden der einfachen BBCodes
			$find = '/(.*)\[indent\](.*)\[\/indent\](.*)/iUs';
			
			// Regulärer Ausdruck zum Ersetzen der einfachen BBCodes
			$replace = '$1<div style="text-indent:'.$indent.'">$2</div>$3';
			
			// BBCodes ersetzen
			return( preg_replace( $find, $replace, $source ) );
		}
		
		/**
		 * Parst einen Zitat-BBCode
		 * 
		 * @return string				Der ersetzte String
		 * @param string				Quellstring mit BBCodes
		 * @since 0.10 - 12.05.2007
		 */
		protected function parseQuote( $source ) {
			// Regulärer Ausdruck zum Finden der einfachen BBCodes
			$find = array (
				'/(.*)\[quote\](.*?)\[\/quote\](.*)/iUse',
				'/(.*)\[quote=(.*)\](.*?)\[\/quote\](.*)/iUse'
			);
			
			// Regulärer Ausdruck zum Ersetzen der einfachen BBCodes
			$replace = array (
				'"$1<br />Zitat:<div class=\"quote\">".$GLOBALS[\'CURRENT_BBCODEPARSER_INSTANCE\']->parseQuote( "$2" )."</div>$3"',
				'"$1<br />Zitat von $2:<div class=\"quote\">".$GLOBALS[\'CURRENT_BBCODEPARSER_INSTANCE\']->parseQuote( "$3" )."</div>$4"'
			);
			
			// BBCodes ersetzen
			return( preg_replace( $find, $replace, $source ) );
		}
		
		/**
		 * Parst einen Userlink-BBCode
		 * 
		 * @return string				Der ersetzte String
		 * @param string				Quellstring mit BBCodes
		 * @since 0.10 - 13.05.2007
		 */
		protected function parseUserlink( $source ) {
			// Regulärer Ausdruck zum Finden der einfachen BBCodes
			$find = '/(.*)\[user\](.*)\[\/user\](.*)/iUs';
			
			// Regulärer Ausdruck zum Ersetzen der einfachen BBCodes
			$replace = '$1<a href="'.WEBURL.'user.php?user=$2" class="userlink">'.Icon::getSmall('user').' $2</a>$3';
			
			// BBCodes ersetzen
			return( preg_replace( $find, $replace, $source ) );
		}
		
		/**
		 * Parst einen eMail-BBCode
		 * 
		 * @return string				Der ersetzte String
		 * @param string				Quellstring mit BBCodes
		 * @since 0.10 - 13.05.2007
		 */
		protected function parseEmail( $source ) {
			// Regulärer Ausdruck zum Finden der einfachen BBCodes
			$find = array (
				'/(.*)\[email\](.*)\[\/email\](.*)/iUs',
				'/(.*)\[email=(.*)\](.*)\[\/email\](.*)/iUs'
			);
			
			// Regulärer Ausdruck zum Ersetzen der einfachen BBCodes
			$replace = array (
				'$1<a href="mailto:$2" class="email">'.Icon::getSmall('email').' $2</a>$3',
				'$1<a href="mailto:$2" class="email">'.Icon::getSmall('email').' $3</a>$4'
			);
			
			// BBCodes ersetzen
			return( preg_replace( $find, $replace, $source ) );
		}
		
		/**
		 * Parst einen Thumb-BBCode
		 * 
		 * @return string				Der ersetzte String
		 * @param string				Quellstring mit BBCodes
		 * @since 0.10 - 13.05.2007
		 */
		protected function parseThumb( $source ) {
			// Regulärer Ausdruck zum Finden der einfachen BBCodes
			$find = '/(.*)\[thumb=(.*)\](.*)\[\/thumb\](.*)/iUs';
			
			// Regulärer Ausdruck zum Ersetzen der einfachen BBCodes
			$replace = '$1<a href="$3" target="_blank"><img src="$2" alt="Nicht gefunden" /></a>$4';
			
			// BBCodes ersetzen
			return( preg_replace( $find, $replace, $source ) );
		}
		
		/**
		 * Parst einen Smilie-BBCode
		 * 
		 * @return string				Der ersetzte String
		 * @param string				Quellstring mit BBCodes
		 * @since 0.10 - 13.05.2007
		 */
		protected function parseSmilie( $source ) {
			// Regulärer Ausdruck zum Finden der einfachen BBCodes
			$find = '/(.*)\[smilie\](.*)\[\/smilie\](.*)/iUs';
			
			// Regulärer Ausdruck zum Ersetzen der einfachen BBCodes
			$replace = '$1<img src="'.WEBURL.Core::$configuration->icon_dir.'smilies/$2" alt="$2" />$3';
			
			// BBCodes ersetzen
			return( preg_replace( $find, $replace, $source ) );
		}
		
	};
}
?>