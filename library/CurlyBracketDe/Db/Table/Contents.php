<?php
	
/**
 * CurlyBracketDe_Controller_Base
 * 
 * Basis-Controller
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyBracketDe.Db.Table
 * @since 1.0 - 27.11.2008
 * @version 1.0
 */
class CurlyBracketDe_Db_Table_Contents extends Zend_Db_Table {
	
    /**
     * @var string Der Tabellenname
     */
    protected $_name='contents';
    
    /**
     * @var string Der Primärschlüssel der Tabelle
     */
    protected $_primary='name';
    
    /**
     * @var boolean Flag, ob ein AutoIncrement-Key verwendet wird.
     */
    protected $_sequence=false;
    
}