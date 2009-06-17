<?php

require_once "Curly/Sourcecode/Minimizer.php";

/**
 * Curly_Sourcecode_Minimizer_Css
 * 
 * Dieser Minimizer verkleinert die Datengrˆﬂe einer CSS-Datei.
 *
 * Dazu werden alle Zeilenumbr¸che und Tabulatorzeichen durch Leerzeichen
 * ersetzt. Zus‰tzlich entfernt diese Klasse alle Kommentare.
 *
 * Vorsicht: Dadurch, das alle Kommentare entfernt werden, kˆnnten
 * eventuelle CSS-Hacks entfernt werden.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Sourcecode
 * @since 1.0 - 14.11.2008
 * @version 1.0
 */
class Curly_Sourcecode_Minimizer_Css extends Curly_Sourcecode_Minimizer {

	/**
	 * @var array Die Zeichen, die w‰hrend der Minimierung von der
	 * removeWhitespace-Methode entfernt werden.
	 */
	static private $_whitespace = array(
		" ", "\t", "\n", "\r", "\0", "\x0B"
	);

	/**
	 * @var array Die Zeichen, die CSS-Syntaxzeichen sind und keine weiﬂen
	 * Zeichen davor bzw. dahinter benˆtigen.
	 */
	static private $_specialchars = array(
		"{", "}", ":", ";", "(", ")", ",", "[", "]", "=", ">"
	);

	/**
	 * Diese Methode lieﬂt die zu minimierenden Daten aus dem Datenstrom und
	 * verkleinert diese nach dem Schema dieser Klasse.
	 *
	 * @return boolean
	 */
	public function minimize() {
		parent::minimize();
		$source=$this->getSourceStream();
		$target=$this->getTargetStream();
			
		$buffer=$source->readToEnd();
			
		$pos2=0;
		// Nach Kommentarbeginn suchen
		while(($pos=strpos($buffer, '/*', $pos2))!==false) {
			$pos2=strpos($buffer, '*/', $pos);
			// Kommentarende nicht gefunden
			if($pos2===false) {
				$buffer=substr($buffer, 0, $pos);
				$pos2=$pos;
				continue;
			}
			else {
				$buffer=substr($buffer, 0, $pos).substr($buffer, $pos2+2);
				$pos2=$pos;
			}
		}
		
		$target->write($this->removeWhitespaces(trim($buffer)));
			
		return(true);
	}

	/**
	 * Diese Methode entfernt die weiﬂen Zeichen aus dem Inhalt.
	 *
	 * @return string Bereinigte Zeichenkette
	 * @param string Zu bereinigende Zeichenkette
	 */
	private function removeWhitespaces($in) {
		$out='';
		$lastChar='';
		$lastWasWhite=false;
			
		for($i=0, $n=strlen($in)-1; $i<$n; $i++) {
			$char=$in[$i];
			$nextChar=$in[$i+1];

			if(in_array($char, self::$_whitespace)) {
				if(
					$lastWasWhite
					or in_array($nextChar, self::$_whitespace)
					or in_array($lastChar, self::$_specialchars)
					or in_array($nextChar, self::$_specialchars)
				) {
					continue;
				}
				else {
					$char=' ';
					$lastWasWhite=true;
				}
			}
			$lastWasWhite=false;
			
			$out .= $char;
			$lastChar=$char;
		}
		
		$out .= $in[$n];
		
		return($out);
	}
	
}