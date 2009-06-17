<?php

/**
 * Curly_View_Helper_PublishAsset
 * 
 * Diese ViewHelper-Klasse kann die
 * {@link Curly_Assets_Manager::publishFile publishFile}-Methode an einer
 * {@link Curly_Assets_Manager}-Instanz aufrufen und somit auf einfache Art
 * und Weise eine Datei veröffentlichen
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.View.Helper
 * @since 1.0 - 26.11.2008
 * @version 1.0
 */
class Curly_View_Helper_PublishAsset extends Zend_View_Helper_Abstract {
	
	/**
	 * @var Curly_Assets_Manager Zu verwendende Manager-Instanz
	 */
	private $_manager=NULL;
	
	/**
	 * Gibt die zu verwendende Manager-Instanz zurück.
	 * 
	 * Wurde noch keine Manager-Instanz gesetzt, so wird die
	 * {@link Curly_Assets::getManager}-Methode aufgerufen und deren
	 * Rückgabewert verwendet.
	 * 
	 * @return Curly_Assets_Manager
	 */
	public function getManager() {
		if($this->_manager===NULL) {
			$this->_manager=Curly_Assets::getManager();
		}
		return($this->_manager);
	}
	
	/**
	 * Setzt die zu verwendende Manager-Instanz.
	 * 
	 * @return void
	 * @param Curly_Assets_Manager
	 */
	public function setManager(Curly_Assets_Manager $manager) {
		$this->_manager=$manager;
	}
	
	/**
	 * Führt die Aktion dieser ViewHelper-Klasse aus. Dabei wird die
	 * {@link publishFile}-Methode der {@link getManager AssetManager}-Instanz
	 * ausgeführt.
	 * 
	 * @return string
	 * @param string
	 */
	public function publishAsset($file) {
		return($this->getManager()->publishFile($file));
	}
	
}