<?php

require_once "Curly/Sourcecode/Php/ProcessorStrategy/Expression.php";

/**
 * Curly_Sourcecode_Php_ProcessorStrategy_IfBase
 *
 * Basisklasse zum Ersetzen eines IF-Expression-Tokens.
 *
 * Hinweis: Damit die findEndifToken-Methode korrekt arbeiten kann, sollte die
 * Expression jeder Unterklasse mit dem Präfix if beginnen, zum Beispiel ifdef.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Sourcecode.ProcessorStrategy
 * @since 1.0 - 20.02.2009
 * @version 1.0
 */
abstract class Curly_Sourcecode_Php_ProcessorStrategy_IfBase extends Curly_Sourcecode_Php_ProcessorStrategy_Expression {

	const IF_EXPRESSION='if';
	const ENDIF_EXPRESSION='endif';

	/**
	 * @var string Expression für den ENDIF-Token.
	 */
	protected $_endifExpression=self::ENDIF_EXPRESSION;

	/**
	 * Gibt die Expression für den ENDIF-Token zurück.
	 *
	 * @return string
	 */
	public function getEndifExpression() {
		return $this->_endifExpression;
	}

	/**
	 * Sucht nach dem ENDIF-Token zu dem aktuell geöffneten Token.
	 *
	 * @return integer Indexposition des ENDIF-Tokens oder -1
	 */
	protected function findEndifToken() {
		return $this->findToken($this->getEndifExpression());
	}

	/**
	 * Sucht nach dem Token mit der übergebenen Expression zu dem aktuell
	 * geöffneten Token.
	 *
	 * @return integer Indexposition des Tokens oder -1
	 * @param integer Start-Index. Ist dieser Parameter nicht gegeben, wird der
	 * aktuelle Index des Prozessors +1 verwendet.
	 * @param integer Ist dieser Parameter gegeben, wird maximal bis zu diesem
	 * Index gesucht.
	 */
	protected function findToken($expression, $startIndex=NULL, $maxIndex=NULL) {
		if($startIndex===NULL) {
			$oldIndex=$index=$this->getCurrentIndex()+1;
		}
		else {
			$oldIndex=$index=$startIndex;
		}

		// Bereits gefundene IF-Ausdrücke
		$openIfs=0;

		$tokens=$this->getTokens();
		$tokensCollection=$tokens->getTokens();
		for($index++; $index<$tokensCollection->count(); $index++) {
			$token=$tokensCollection->get($index);

			// Falscher Token-Typ
			if($tokens->getTokenTypeAt($index)!=$this->getTokentype()) {
				continue;
			}

			// Genügt nicht Präfix
			if(!$this->matchesPrefix($token)) {
				continue;
			}

			// Ist IF-Expression
			$value=$tokens->getTokenValueAt($index);
			if(substr($value, strlen($this->getTokenPrefix()), 2)==self::IF_EXPRESSION) {
				$openIfs++;
			}
			// Ist gesuchter Token
			else if($this->matchesExpression($token, $expression)) {
				$openIfs--;
				// Passender Token zu diesem IF-Token
				if($openIfs<0) {
					break;
				}
			}
			
			if($maxIndex!==NULL and $index==$maxIndex) {
				break;
			}
		}

		// gesuchten Token nicht gefunden
		if($openIfs>=0) {
			return -1;
		}

		return $index;
	}

}