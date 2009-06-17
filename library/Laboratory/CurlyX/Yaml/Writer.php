<?php

/**
 * @since 28.10.2008
 */
class CurlyX_Yaml_Writer {
	
	const DOC_BEGIN="---\n";
	
	/**
	 * @return string
	 * @param string
	 */
	public function write($value) {
		return(self::DOC_BEGIN.$this->_write($value, 0));
	}
	
	/**
	 * @return string
	 * @param string
	 * @param integer
	 */
	private function _write($value, $indention) {
		
	}
	
	/**
	 * @return string
	 * @param array
	 * @param integer
	 */
	private function _writeArray($value, $indention) {
		
	}
	# boolean"
# "integer"
# "double" (aus historischen Gründen wird "double" im Fall eines float zurückgegeben, und nicht einfach float.
# "string"
# "array"
# "object"
# "resource"
# "NULL"
# "unknown type" 
	
	/**
	 * @return string
	 * @param string
	 */
	private function indent($input, $indention) {
		$output='';
		foreach(explode("\n", $input) as $line) {
			$output.=str_repeat("    ", $indention).$line;
		}
		return(implode("\n", $output));
	}
	
}

?>