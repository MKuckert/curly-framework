<?php

/**
 * CurlyX_Archive_Compiler_Interface
 *
 * Basis-Interface für einen Archiv-Compiler.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Archive.Compiler
 * @since 1.0 - 11.12.2008
 * @version 1.0
 */
interface CurlyX_Archive_Compiler_Interface {
	
	/**
	 * Fügt diesem Archiv einen Datensatz hinzu.
	 *
	 * @return void
	 * @param CurlyX_Archive_Dataset_Interface
	 */
	public function addDataset(CurlyX_Archive_Dataset_Interface $dataset);
	
	/**
	 * Kompiliert dieses Archiv.
	 *
	 * @return boolean
	 * @param string Dateiname, unter dem das Archiv erstellt werden soll.
	 * @param integer Flags für den Kompiliervorgang.
	 */
	public function compile($file, $flags=0);
	
}