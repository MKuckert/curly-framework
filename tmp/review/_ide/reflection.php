<?php
class Reflection { }
interface Reflector { }
class ReflectionException extends Exception { }
class ReflectionFunction extends ReflectionFunctionAbstract implements Reflector
{
	final private function __clone() {}
    public function __construct( $name) {}
    public function __toString() {}
    public static function export($name, $return) {}
    public function getName() {}
    public function isInternal() {}
    public function isUserDefined() {}
    public function getFileName() {}
    public function getStartLine() {}
    public function getEndLine() {}
    public function getDocComment() {}
    public function getStaticVariables() {}
    public function invoke($args) {}
    public function invokeArgs($args) {}
    public function returnsReference() {}
    public function getParameters() {}
    public function getNumberOfParameters() {}
    public function getNumberOfRequiredParameters() {}
}
class ReflectionParameter implements Reflector
{
    final private function __clone() {}
    public function __construct($function, $parameter) {}
    public function __toString() {}
    public static function export($function, $parameter, $return) {}
    public function getName() {}
    public function isPassedByReference() {}
    public function getDeclaringClass() {}
    public function getClass() {}
    public function isArray() {}
    public function allowsNull() {}
    public function isPassedByReference() {}
    public function isOptional() {}
    public function isDefaultValueAvailable() {}
    public function getDefaultValue() {}
}
class ReflectionClass implements Reflector
{
    final private function __clone() {}
    public function __construct($name) {}
    public function __toString() {}
    public static function export($class, $return) {}
    public function getName() {}
    public function isInternal() {}
    public function isUserDefined() {}
    public function isInstantiable() {}
    public function hasConstant($name) {}
    public function hasMethod($name) {}
    public function hasProperty($name) {}
    public function getFileName() {}
    public function getStartLine() {}
    public function getEndLine() {}
    public function getDocComment() {}
    public function getConstructor() {}
    public function getMethod($name) {}
    public function getMethods() {}
    public function getProperty($name) {}
    public function getProperties() {}
    public function getConstants() {}
    public function getConstant($name) {}
    public function getInterfaces() {}
    public function isInterface() {}
    public function isAbstract() {}
    public function isFinal() {}
    public function getModifiers() {}
    public function isInstance($object) {}
    public function newInstance($args) {}
    public function newInstanceArgs($args) {}
    public function getParentClass() {}
    public function isSubclassOf(ReflectionClass $class) {}
    public function getStaticProperties() {}
    public function getStaticPropertyValue($name, $default) {}
    public function setStaticPropertyValue($name, $value) {}
    public function getDefaultProperties() {}
    public function isIterateable() {}
    public function implementsInterface($name) {}
    public function getExtension() {}
    public function getExtensionName() {}
}
class ReflectionMethod extends ReflectionFunctionAbstract implements Reflector
{
    public function __construct($class, $name) {}
    public function __toString() {}
    public static function export($class, $name, $return) {}
    public function invoke($object, $args) {}
    public function invokeArgs($object, $args) {}
    public function isFinal() {}
    public function isAbstract() {}
    public function isPublic() {}
    public function isPrivate() {}
    public function isProtected() {}
    public function isStatic() {}
    public function isConstructor() {}
    public function isDestructor() {}
    public function getModifiers() {}
    public function getDeclaringClass() {}

    // Ererbt von ReflectionFunction
    final private function __clone() {}
    public function getName() {}
    public function isInternal() {}
    public function isUserDefined() {}
    public function getFileName() {}
    public function getStartLine() {}
    public function getEndLine() {}
    public function getDocComment() {}
    public function getStaticVariables() {}
    public function returnsReference() {}
    public function getParameters() {}
    public function getNumberOfParameters() {}
    public function getNumberOfRequiredParameters() {}
}
class ReflectionObject extends ReflectionClass
{
    final private function __clone() {}
    public function __construct($object) {}
    public function __toString() {}
    public static function export($object, $return) {}
}
class ReflectionProperty implements Reflector
{
    final private function __clone() {}
    public function __construct($class, $name) {}
    public function __toString() {}
    public static function export($class, $name, $return) {}
    public function getName() {}
    public function isPublic() {}
    public function isPrivate() {}
    public function isProtected() {}
    public function isStatic() {}
    public function isDefault() {}
    public function getModifiers() {}
    public function getValue($object) {}
    public function setValue($object, $value) {}
    public function getDeclaringClass() {}
    public function getDocComment() {}
}
class ReflectionExtension implements Reflector {
    final private function __clone() {}
    public function __construct($name) {}
    public function __toString() {}
    public static function export($name, $return) {}
    public function getName() {}
    public function getVersion() {}
    public function getFunctions() {}
    public function getConstants() {}
    public function getINIEntries() {}
    public function getClasses() {}
    public function getClassNames() {}
}
?>