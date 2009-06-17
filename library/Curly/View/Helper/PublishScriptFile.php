<?php

/**
 * Curly_View_Helper_PublishScriptFile
 * 
 * Diese ViewHelper-Klasse erweitert den PublishAsset-ViewHelper um die
 * automatische Skriptnamens-Auflösung. D.h. dieser ViewHelper kann direkt eine
 * Datei aus dem views/scripts-Verzeichnis veröffentlichen, ohne dass der
 * komplette Pfad angegeben werden muss.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.View.Helper
 * @since 1.0 - 26.11.2008
 * @version 1.0
 */
class Curly_View_Helper_PublishScriptFile extends Curly_View_Helper_PublishAsset {
	
	/**
	 * Führt die Aktion dieser ViewHelper-Klasse aus. Dabei wird die
	 * {@link publishFile}-Methode der {@link getManager AssetManager}-Instanz
	 * ausgeführt.
	 * 
	 * @return string
	 * @param string
	 */
	public function publishScriptFile($file) {
		$path=$this->view->getScriptPath($file);
		if($path===false) {
			throw new Curly_Assets_Exception('Unable to find script file '.$file);
		}
		
		return($this->publishAsset($path));
	}
	
}