<?php

require_once "Curly/Types/Ensure.php";

/**
* @TODO:
function getDocComments($source)
{
    $doc_comments = array();
    
    foreach(token_get_all($source) as $token)
    {
        if(is_array($token) && $token[0] == T_DOC_COMMENT)
        {
            $doc_comments[] = $token[1];
        }
    }
    
    return $doc_comments;
}
*/

/**
 * Curly_Sourcecode_DocComment_Parser
 * 
 * Einfacher DocComment-Parser
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Sourcecode.DocComment
 * @since 1.0 - 30.11.2008
 * @version 1.0
 */
class Curly_Sourcecode_DocComment_Parser {
	
	const TAG_DESC='desc';
	
	/**
	 * @var array
	 */
	private $lines=array();
	
	/**
	 * @var array Liste der bereits gefundenen Tags
	 */
	private $tags=array();
	
	/**
	 * @var array Liste der Tags, die einmalig vorkommen können, d.h.
	 * zusammengefasst werden.
	 */
	private $tagsToCombine=array('desc');
	
	/**
	 * Gibt die Liste der Tags, die einmalig vorkommen können, zurück.
	 * 
	 * @return array
	 */
	public function getTagsToCombine() {
		return($this->tagsToCombine);
	}
	
	/**
	 * Setzt die Liste der Tags, die einmalig vorkommen können.
	 * 
	 * @return void
	 * @param array
	 */
	public function setTagsToCombine(array $tags) {
		$this->tagsToCombine=$tags;
	}
	
	/**
	 * Parst den übergebenen Kommentar und extrahiert alle vorhandenen Tags
	 * und gibt diese als Array zurück.
	 * 
	 * @return array
	 * @param string
	 */
	public function parse($code) {
		$this->tags=array();
		
		$code=str_replace(array("\r\n", "\n", "\r"), "\n", $code);
		$this->lines=explode("\n", $code);
		
		$this->extractText();
		
		$this->parseTags();
		
		$this->combineTags();
		
		return $this->tags;
	}
	
	/**
	 * Extrahiert alle DocComment-Blöcke aus dem übergebenen Array von Texten
	 * und parst diese jeweils. Die Rückgabe ist ein Array aller
	 * Parse-Ergebnisse.
	 * 
	 * @return array
	 * @param array
	 */
	public function parseAll($code) {
		$code=Curly_Types_Ensure::arr($code);
		$retval=array();
		foreach($code as $item) {
			$retval[]=$this->parse($item);
		}
		return $retval;
	}
	
	/**
	 * @return void
	 */
	private function extractText() {
		$tmp=$this->lines;
		$this->lines=array();
		foreach($tmp as $line) {
			$line=trim($line, " \t\n\r\0\x0B*");
			// Kommentarbeginn/Ende übergehen
			if($line=='/') {
				continue;
			}
			$this->lines[]=$line;
		}
	}
	
	/**
	 * @return array
	 */
	private function parseTags() {
		$currentTagName=self::TAG_DESC;
		$currentTag=NULL;
		
		foreach($this->lines as $line) {
			list($tag, $content)=$this->parseLine($line);
			
			if($tag==NULL) {
				if($content=='') {
					continue;
				}
				if($currentTag==NULL) {
					$currentTag=$content;
				}
				else {
					$currentTag.=' '.$content;
				}
			}
			else {
				if(!isset($this->tags[$currentTagName])) {
					$this->tags[$currentTagName]=array();
				}
				$this->tags[$currentTagName][]=$currentTag;
				$currentTagName=$tag;
				$currentTag=$content;
			}
		}
		
		if(!isset($this->tags[$currentTagName])) {
			$this->tags[$currentTagName]=array($currentTag);
		}
		else {
			$this->tags[$currentTagName][]=$currentTag;
		}
	}
	
	/**
	 * @return array
	 * @param string
	 */
	private function parseLine($line) {
		// Tag gefunden
		if(isset($line[0]) and $line[0]=='@') {
			$pos=strpos($line, ' ');
			if($pos===false) {
				$tag=substr($line, 1);
				$content='';
			}
			else {
				$tag=substr($line, 1, $pos-1);
				$content=ltrim(substr($line, $pos));
			}
			return(array($tag, $content));
		}
		// Kein Tag
		else {
			return(array(NULL, $line));
		}
	}
	
	/**
	 * Kombiniert die zu kombinierenden Tags.
	 * 
	 * @return void
	 */
	private function combineTags() {
		foreach($this->tagsToCombine as $tag) {
			if(isset($this->tags[$tag])) {
				$this->tags[$tag]=implode(" ", $this->tags[$tag]);
			}
		}
	}
	
}