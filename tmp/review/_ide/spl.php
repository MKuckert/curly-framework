<?php

function spl_autoload(string $class_name, string $file_extensions = NULL) {};

function spl_autoload_call(string $class_name) {};

function spl_autoload_extensions($file_extensions) {};

function spl_autoload_functions() {};

function spl_autoload_register(string $autoload_function = "spl_autoload", $throw = true) {};

function spl_autoload_unregister(string $autoload_function = "spl_autoload") {};

function spl_classes() {};

function iterator_count(Traversable $it) {};

function iterator_to_array(Traversable $it, $use_keys = true) {};

class Exception
{
   protected $message;
   
   private $string;
   
   protected $code;
   
   protected $file;
   
   protected $line;
   
   private $trace;

   final private function __clone() {}

   function __construct($message = NULL, $code = 0) {
       if (func_num_args()) {
           $this->message = $message;
       }
       $this->code = $code;
       $this->file = __FILE__; // of throw clause
       $this->line = __LINE__; // of throw clause
       $this->trace = debug_backtrace();
       $this->string = StringFormat($this);
   } 

   final public function getMessage()
   {
       return $this->message;
   }

   final public function getCode()
   {
       return $this->code;
   }

   final public function getFile()
   {
       return $this->file;
   }

   final public function getLine()
   {
       return $this->line;
   }

   final public function getTrace()
   {
       return $this->trace;
   }

   final public function getTraceAsString()
   {
   }

   public function __toString()
   {
       return $this->string;
   }
}

class LogicException extends Exception
{
}

class BadFunctionCallException extends LogicException
{
}

class BadMethodCallException extends BadFunctionCallException
{
}

class DomainException extends LogicException
{
}

class InvalidArgumentException extends LogicException
{
}

class LengthException extends LogicException
{
}

class OutOfRangeException extends LogicException
{
}

class RuntimeException extends Exception
{
}

class OutOfBoundsException extends RuntimeException
{
}

class OverflowException extends RuntimeException
{
}

class RangeException extends RuntimeException
{
}

class UnderflowException extends RuntimeException
{
}

class UnexpectedValueException extends RuntimeException
{
}

interface ArrayAccess
{
   function offsetSet($offset, $value);

   function offsetGet($offset);

   function offsetUnset($offset);

   function offsetExists($offset);
}

interface Traversable
{
}

interface IteratorAggregate extends Traversable
{
   function getIterator();
}

interface Iterator extends Traversable
{
   function rewind();

   function current();

   function key();

   function next();

   function valid();
}

interface Countable
{
   function count();
}

interface Serializable
{
   function serialize();
   
   function unserialize($serialized);
}

class ArrayObject implements IteratorAggregate, ArrayAccess, Countable
{
   const STD_PROP_LIST     = 0x1;
   const ARRAY_AS_PROPS    = 0x2;

   function __construct($array, $flags = 0, $iterator_class = "ArrayIterator") {}

   function setFlags($flags) {}
                       
   function getFlags() {}

   function asort() {}
   
   function ksort() {}
   
   function uasort(mixed $cmp_function) {}
   
   function uksort(mixed $cmp_function) {}
   
   function natsort() {}
   
   function natcasesort() {}
   
   function exchangeArray($array) {}

   function getIterator() {}

   function offsetExists($index) {}

   function offsetGet($index) {}

   function offsetSet($index, $newval) {}

   function offsetUnset($index) {}

   function append($value) {}

   function getArrayCopy() {}

   function count() {}
   
   /* @param $iterator_class new class used in getIterator()
    */
   function setIteratorClass($itertor_class) {}
   
   /* @return class used in getIterator()
    */
   function getIteratorClass() {}
}

class ArrayIterator implements SeekableIterator, ArrayAccess, Countable
{
   const STD_PROP_LIST  = 0x1;
   const ARRAY_AS_PROPS = 0x2;

   function __construct($array, $flags = 0) {}

   function setFlags($flags) {}

   function getFlags() {}

   function asort() {}
   
   function ksort() {}
   
   function uasort(mixed $cmp_function) {}
   
   function uksort(mixed $cmp_function) {}
   
   function natsort() {}
   
   function natcasesort() {}
   
   function offsetExists($index) {}

   function offsetGet($index) {}

   function offsetSet($index, $newval) {}

   function offsetUnset($index) {}

   function append($value) {}

   function getArrayCopy() {}

   function seek($position) {}

   function count() {}

   function rewind() {}
   
   function valid() {}
   
   function current() {}
   
   function key() {}
   
   function next() {}
}

class SplFileInfo 
{
   function __construct($file_name) {}

   function getPath() {}   

   function getFilename() {}   

   function getFileInfo($class_name = NULL) {}

   function getPathname() {}   

   function getPathInfo($class_name = NULL) {}

   function getPerms() {}

   function getInode() {}

   function getSize() {}

   function getOwner() {}

   function getGroup() {}

   function getATime() {}

   function getMTime() {}

   function getCTime() {}

   function getType() {}

   function isWritable() {}

   function isReadable() {}

   function isExecutable() {}

   function isFile() {}

   function isDir() {} 

   function isLink() {}

   function getLinkTarget() {}

   function getRealPath() {}
   
   function __toString() {}

   function openFile($mode = 'r', $use_include_path = false, $context = NULL) {}

   function setFileClass($class_name = "SplFileObject") {}

   function setInfoClass($class_name = "SplFileInfo") {}
}

class DirectoryIterator extends SplFileInfo implements Iterator
{
   function __construct($path) {}

   function rewind() {}
   
   function valid() {}
   
   function key() {}

   function current() {}

   function next() {}

   function isDot() {} 

   function isLink() {}

   function __toString() {}
}

class RecursiveDirectoryIterator extends DirectoryIterator implements RecursiveIterator
{
   const CURRENT_AS_FILEINFO  = 0x0; /* make RecursiveDirectoryTree::current() return SplFileInfo */
   const KEY_AS_FILENAME      = 0x0; /* make RecursiveDirectoryTree::key() return getFilename() */
   const NEW_CURRENT_AND_KEY  = 0x0; /* CURRENT_AS_FILEINFO + KEY_AS_FILENAME */

   function __construct($path, $flags = 0) {}

   function key() {}

   function current() {}

   function hasChildren() {}   

   function getChildren() {}   

   function getSubPath() {}

   function getSubPathname() {}
}

class SimpleXMLIterator extends SimpleXMLElement implements RecursiveIterator, Countable
{
   function hasChildren() {}   

   function getChildren() {}   

   function count() {}

   function rewind() {}
   
   function valid() {}
   
   function current() {}
   
   function key() {}
   
   function next() {}
}

interface SplObserver
{
   function update(SplSubject $subject);
}

interface SplSubject
{
   function attach(SplObserver $observer);

   function detach(SplObserver $observer);

   function notify();
}

?>