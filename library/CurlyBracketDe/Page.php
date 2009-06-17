<?php

/**
 * CurlyBracketDe_Page
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyBracketDe
 * @since 1.0 - 29.11.2008
 * @version 1.0
 */
class CurlyBracketDe_Page {
	
	/**
	 * @return string
	 * @param string
	 * @param array
	 */
	static public function replaceContent($filepath, $content) {
		$titleCentered=$content['title'];
		$multiplier=(CurlyBracketDe_Page_Renderer::CONTENT_TITLESTARS-strlen($content['title']))/2;
		$titleCentered=str_repeat(' ', $multiplier).$titleCentered;
		
		$contents=$content['contents'];
		$contents=str_replace(array("\r\n", "\r", "\n"), "\n", $contents);
		$out=array();
		foreach(explode("\n", $contents) as $line) {
			$out[]=wordwrap("    ".$line, 80, "\n    ");
		}
		$contents=implode("\n", $out);
		
		$file=file_get_contents($filepath);
		$file=strtr($file, array(
			CurlyBracketDe_Page_Renderer::CONTENT_TITLE_PLACEHOLDER		=> $titleCentered,
			CurlyBracketDe_Page_Renderer::CONTENT_TITLE_UC_PLACEHOLDER	=> 'CONTENT',
			CurlyBracketDe_Page_Renderer::CONTENT_CONTENTS_PLACEHOLDER	=> $contents
		));
		return($file);
	}
	
}