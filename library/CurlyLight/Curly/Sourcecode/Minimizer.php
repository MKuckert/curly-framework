<?php

require_once "Curly/Stream.php";

/**
 * Curly_Sourcecode_Minimizer
 * 
 * Stellt die Basisklasse für alle Minimizer-Klassen dar.
 *
 * Ein Minimizer parst beim Aufruf der {@link minimize}-Methode den Inhalt
 * des Datenstroms {@link getSourceStream SourceStream} nach einem der
 * Klasse entsprechenden Schema und schreibt die verkleinerten Daten in den
 * {@link getTargetStream TargetStream}-Datenstrom.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Sourcecode
 * @since 1.0 - 14.11.2008
 * @version 1.0
 */
abstract class Curly_Sourcecode_Minimizer {

	/**
	 * @var Curly_Stream Datenstrom mit den zu minimierenden Daten.
	 */
	private $_sourceStream = null;

	/**
	 * @var Curly_Stream Datenstrom, in den die minimierten Daten geschrieben
	 * werden sollen.
	 */
	private $_targetStream = null;

	/**
	 * Gibt den Datenstrom mit den zu minimierenden Daten zurück.
	 *
	 * @return Curly_Stream
	 */
	public function getSourceStream() {
		return($this->_sourceStream);
	}

	/**
	 * Setzt den Datenstrom mit den zu minimierenden Daten.
	 *
	 * @return void
	 * @param Curly_Stream
	 */
	public function setSourceStream(Curly_Stream $stream) {
		$this->_sourceStream=$stream;
	}

	/**
	 * Gibt den Datenstrom, in den die minimierten Daten geschrieben werden
	 * sollen zurück.
	 *
	 * @return Curly_Stream
	 */
	public function getTargetStream() {
		return($this->_targetStream);
	}

	/**
	 * Setzt den Datenstrom, in den die minimierten Daten geschrieben werden
	 * sollen.
	 *
	 * @return void
	 * @param Curly_Stream
	 */
	public function setTargetStream(Curly_Stream $stream) {
		$this->_targetStream=$stream;
	}

	/**
	 * Diese Methode ließt die zu minimierenden Daten aus dem Datenstrom und
	 * verkleinert diese nach dem Schema dieser Klasse.
	 *
	 * Diese Basisimplementierung wirft jeweils eine Exception, wenn der
	 * SourceStream oder TargetStream nicht gesetzt ist.
	 *
	 * @throws Exception
	 * @return boolean
	 */
	public function minimize() {
		if( is_null( $this->getSourceStream() ) ) {
			require_once "Curly/Sourcecode/Minimizer/Exception.php";
			throw new Curly_Sourcecode_Minimizer_Exception('The SourceStream property is not set');
		}
		if( is_null( $this->getTargetStream() ) ) {
			require_once "Curly/Sourcecode/Minimizer/Exception.php";
			throw new Curly_Sourcecode_Minimizer_Exception('The TargetStream property is not set');
		}
	}
	
}