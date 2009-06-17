<?php

/**
 * Dokumentation der DOM-Extension vom Stand am 04.07.2008.
 * 
 * @author Martin Kuckert
 * @since 08.07.2008
 * @see http://www.php.net/manual/de/book.dom.php
 */

/**
 * Represents nodes with character data. No nodes directly correspond to this
 * class, but other nodes do inherit from it.
 */
class DOMCharacterData extends DOMNode {
	
	/**
	 * @var string The contents of the node.
	 */
	public $data;
	
	/**
	 * @var int The length of the contents.
	 * @readonly
	 */
	public $length;
	
	/**
	 * Append the string data to the end of the character data of the node.
	 * 
	 * @return void
	 * @param string The string to append.
	 */
	public function appendData( $data ) {}
	
	/**
	 * Remove a range of characters from the node
	 * 
	 * @return void
	 * @param int The offset from which to start removing.
	 * @param int The number of characters to delete. If the sum of offset and
	 * count exceeds the length, then all characters to the end of the data are
	 * deleted.
	 */
	public function deleteData( $offset, $count ) {}
	
	/**
	 * Insert a string at the specified 16-bit unit offset
	 * 
	 * @return void
	 * @param int The character offset at which to insert.
	 * @param string The string to insert.
	 */
	public function insertData( $offset, $data ) {}
	
	/**
	 * Replace a substring within the DOMCharacterData node
	 * 
	 * @return void
	 * @param int The offset from which to start replacing.
	 * @param int The number of characters to replace. If the sum of offset and
	 * count exceeds the length, then all characters to the end of the data are
	 * replaced.
	 * @param string The string to insert.
	 */
	public function replaceData( $offset, $count, $data ) {}
	
	/**
	 * Extracts a range of data from the node
	 * 
	 * @return string The specified substring. If the sum of offset and count
	 * exceeds the length, then all 16-bit units to the end of the data are
	 * returned.
	 * @param int Start offset of substring to extract.
	 * @param int The number of characters to extract.
	 */
	public function substringData( $offset, $count ) {}
	
}

/**
 * Represents comment nodes, characters delimited by <!-- and -->.
 */
class DOMComment extends DOMCharacterData {
	
	/**
	 * Creates a new DOMComment object. This object is read only. It may be
	 * appended to a document, but additional nodes may not be appended to this
	 * node until the node is associated with a document. To create a writeable
	 * node, use DOMDocument::createComment.
	 * 
	 * @param string The value of the comment.
	 */
	public function __construct( $value ) {}
	
}

/**
 * Represents an entire HTML or XML document; serves as the root of the
 * document tree.
 */
class DOMDocument extends DOMNode {
	
	/**
	 * @var string Deprecated. Actual encoding of the document, is a readonly
	 * equivalent to encoding.
	 * @readonly
	 */
	public $actualEncoding;
	
	/**
	 * @var DOMConfiguration Deprecated. Configuration used when
	 * DOMDocument::normalizeDocument() is invoked.
	 * @readonly
	 */
	public $config;
	
	/**
	 * @var DOMDocumentType The Document Type Declaration associated with this
	 * document.
	 * @readonly
	 */
	public $doctype;
	
	/**
	 * @var DOMElement This is a convenience attribute that allows direct
	 * access to the child node that is the document element of the document.
	 * @readonly
	 */
	public $documentElement;
	
	/**
	 * @var string The location of the document or NULL if undefined.
	 */
	public $documentURI;
	
	/**
	 * @var string Encoding of the document, as specified by the XML
	 * declaration. This attribute is not present in the final DOM Level 3
	 * specification, but is the only way of manipulating XML document encoding
	 * in this implementation.
	 */
	public $encoding;
	
	/**
	 * @var boolean Nicely formats output with indentation and extra space.
	 */
	public $formatOutput;
	
	/**
	 * @var DOMImplementation The DOMImplementation object that handles this
	 * document.
	 * @readonly
	 */
	public $implementation;
	
	/**
	 * @var boolean Do not remove redundant white space. Default to TRUE.
	 */
	public $preserveWhiteSpace = true;
	
	/**
	 * @var boolean Proprietary. Enables recovery mode, i.e. trying to parse
	 * non-well formed documents. This attribute is not part of the DOM
	 * specification and is specific to libxml.
	 */
	public $recover;
	
	/**
	 * @var boolean Set it to TRUE to load external entities from a doctype
	 * declaration. This is useful for including character entities in your XML
	 * document.
	 */
	public $resolveExternals;
	
	/**
	 * @var boolean Deprecated. Whether or not the document is standalone, as
	 * specified by the XML declaration, corresponds to xmlStandalone.
	 */
	public $standalone;
	
	/**
	 * @var boolean Throws DOMException on errors. Default to TRUE.
	 */
	public $strictErrorChecking = true;
	
	/**
	 * @var boolean Proprietary. Whether or not to substitute entities. This
	 * attribute is not part of the DOM specification and is specific to
	 * libxml.
	 */
	public $substituteEntities;
	
	/**
	 * @var boolean Loads and validates against the DTD. Default to FALSE.
	 */
	public $validateOnParse = false;
	
	/**
	 * @var string Deprecated. Version of XML, corresponds to xmlVersion
	 */
	public $version;
	
	/**
	 * @var string An attribute specifying, as part of the XML declaration, the
	 * encoding of this document. This is NULL when unspecified or when it is
	 * not known, such as when the Document was created in memory.
	 * @readonly
	 */
	public $xmlEncoding;
	
	/**
	 * @var boolean An attribute specifying, as part of the XML declaration,
	 * whether this document is standalone. This is FALSE when unspecified.
	 */
	public $xmlStandalone;
	
	/**
	 * @var string An attribute specifying, as part of the XML declaration, the
	 * version number of this document. If there is no declaration and if this
	 * document supports the "XML" feature, the value is "1.0".
	 */
	public $xmlVersion;
	
	/**
	 * Creates a new DOMDocument object.
	 * 
	 * @param string The version number of the document as part of the XML
	 * declaration.
	 * @param string The encoding of the document as part of the XML
	 * declaration.
	 */
	public function __construct( $version='', $encoding='' ) {}
	
	/**
	 * This function creates a new instance of class DOMAttr. Dieser Knoten
	 * wird in Ihrem Dokument nicht sichtbar sein, bis dieser zum Beispiel mit
	 * der Funktion DOMNode->appendChild() eingefügt wird.
	 * 
	 * @return DOMAttr The new DOMAttr or FALSE if an error occured.
	 * @param string The name of the attribute.
	 */
	public function createAttribute( $name ) {}
	
	/**
	 * This function creates a new instance of class DOMAttr. Dieser Knoten
	 * wird in Ihrem Dokument nicht sichtbar sein, bis dieser zum Beispiel mit
	 * der Funktion DOMNode->appendChild() eingefügt wird.
	 * 
	 * @return DOMAttr The new DOMAttr or FALSE if an error occured.
	 * @param string The URI of the namespace.
	 * @param string The tag name and prefix of the attribute, as prefix:tagname.
	 */
	public function createAttributeNS( $namespaceURI, $qualifiedName ) {}
	
	/**
	 * This function creates a new instance of class DOMCDATASection. Dieser
	 * Knoten wird in Ihrem Dokument nicht sichtbar sein, bis dieser zum
	 * Beispiel mit der Funktion DOMNode->appendChild() eingefügt wird.
	 * 
	 * @return DOMCDATASection The new DOMCDATASection or FALSE if an error
	 * occured.
	 * @param string The content of the cdata.
	 */
	public function createCDATASection( $data ) {}
	
	/**
	 * This function creates a new instance of class DOMComment. Dieser Knoten
	 * wird in Ihrem Dokument nicht sichtbar sein, bis dieser zum Beispiel mit
	 * der Funktion DOMNode->appendChild() eingefügt wird.
	 * 
	 * @return DOMComment The new DOMComment or FALSE if an error occured.
	 * @param string The content of the comment.
	 */
	public function createComment( $data ) {}
	
	/**
	 * This function creates a new instance of class DOMDocumentFragment.
	 * Dieser Knoten wird in Ihrem Dokument nicht sichtbar sein, bis dieser zum
	 * Beispiel mit der Funktion DOMNode->appendChild() eingefügt wird.
	 * 
	 * @return DOMDocumentFragment The new DOMDocumentFragment or FALSE if an error occured.
	 */
	public function createDocumentFragment() {}
	
	/**
	 * This function creates a new instance of class DOMElement.
	 * Dieser Knoten wird in Ihrem Dokument nicht sichtbar sein, bis dieser zum
	 * Beispiel mit der Funktion DOMNode->appendChild() eingefügt wird.
	 * 
	 * @return DOMElement Returns a new instance of class DOMElement or FALSE
	 * if an error occured.
	 * @param string The tag name of the element.
	 * @param string The value of the element. By default, an empty element
	 * will be created. You can also set the value later with
	 * DOMElement->nodeValue.
	 */
	public function createElement( $name, $value='' ) {}
	
	/**
	 * This function creates a new element node with an associated namespace.
	 * Dieser Knoten wird in Ihrem Dokument nicht sichtbar sein, bis dieser zum
	 * Beispiel mit der Funktion DOMNode->appendChild() eingefügt wird.
	 * 
	 * @return DOMElement Returns a new instance of class DOMElement or FALSE
	 * if an error occured.
	 * @param string The URI of the namespace.
	 * @param string The qualified name of the element, as prefix:tagname.
	 * @param string The value of the element. By default, an empty element
	 * will be created. You can also set the value later with
	 * DOMElement->nodeValue.
	 */
	public function createElementNS( $namespaceURI , $qualifiedName, $value='' ) {}
	
	/**
	 * This function creates a new instance of class DOMEntityReference.
	 * Dieser Knoten wird in Ihrem Dokument nicht sichtbar sein, bis dieser zum
	 * Beispiel mit der Funktion DOMNode->appendChild() eingefügt wird.
	 * 
	 * @return DOMEntityReference The new DOMEntityReference or FALSE if an
	 * error occured.
	 * @param string The content of the entity reference, e.g. the entity
	 * reference minus the leading & and the trailing; characters.
	 */
	public function createEntityReference( $name ) {}
	
	/**
	 * This function creates a new instance of class DOMProcessingInstruction.
	 * Dieser Knoten wird in Ihrem Dokument nicht sichtbar sein, bis dieser zum
	 * Beispiel mit der Funktion DOMNode->appendChild() eingefügt wird.
	 * 
	 * @return DOMProcessingInstruction The new DOMProcessingInstruction or
	 * FALSE if an error occured.
	 * @param string The target of the processing instruction.
	 * @param string The content of the processing instruction.
	 */
	public function createProcessingInstruction( $target, $data ) {}
	
	/**
	 * This function creates a new instance of class DOMText.
	 * Dieser Knoten wird in Ihrem Dokument nicht sichtbar sein, bis dieser zum
	 * Beispiel mit der Funktion DOMNode->appendChild() eingefügt wird.
	 * 
	 * @return DOMText The new DOMText or FALSE if an error occured.
	 * @param string The content of the text.
	 */
	public function createTextNode( $content ) {}
	
	/**
	 * This function is similar to DOMDocument::getElementsByTagName but
	 * searches for an element with a given id.
	 * 
	 * For this function to work, you will need either to set some ID
	 * attributes with DOMElement::setIdAttribute or a DTD which defines an
	 * attribute to be of type ID. In the later case, you will need to validate
	 * your document with DOMDocument::validate or DOMDocument->validateOnParse
	 * before using this function. 
	 * 
	 * @return DOMElement Returns the DOMElement or NULL if the element is not
	 * found.
	 * @param string The unique id value for an element.
	 */
	public function getElementById( $elementId ) {}
	
	/**
	 * This function returns a new instance of class DOMNodeList containing the
	 * elements with a given tag name.
	 * 
	 * @return DOMNodeList A new DOMNodeList object containing all the matched
	 * elements.
	 * @param string The name of the tag to match on. The special value *
	 * matches all tags.
	 */
	public function getElementsByTagName( $name ) {}
	
	/**
	 * Returns a DOMNodeList of all elements with a given local name and a
	 * namespace URI.
	 * 
	 * @return DOMNodeList A new DOMNodeList object containing all the matched
	 * elements.
	 * @param string The namespace URI of the elements to match on. The special
	 * value * matches all namespaces.
	 * @param string The local name of the elements to match on. The special
	 * value * matches all local names.
	 */
	public function getElementsByTagNameNS( $namespaceURI, $localName ) {}
	
	/**
	 * This function returns a copy of the node to import and associates it
	 * with the current document.
	 * 
	 * @return DOMNode The copied node or FALSE, if it cannot be copied.
	 * @param DOMNode The node to import.
	 * @param boolean If set to TRUE, this method will recursively import the
	 * subtree under the importedNode .
	 */
	public function importNode( $importedNode, $deep=false ) {}
	
	/**
	 * Loads an XML document from a file.
	 * 
	 * Warnung: Unix style paths with forward slashes can cause significant
	 * performance degradation on Windows systems; be sure to call realpath()
	 * in such a case.
	 * 
	 * @return mixed Gibt bei Erfolg TRUE zurück, im Fehlerfall FALSE. If
	 * called statically, returns a DOMDocument but also causes an E_STRICT
	 * warning.
	 * @param string The path to the XML document.
	 * @param int Bitwise OR of the libxml option constants.
	 */
	public function load( $filename, $options ) {}
	
	/**
	 * The function parses the HTML contained in the string source . Unlike
	 * loading XML, HTML does not have to be well-formed to load. This function
	 * may also be called statically to load and create a DOMDocument object.
	 * The static invocation may be used when no DOMDocument properties need to
	 * be set prior to loading.
	 * 
	 * @return mixed Gibt bei Erfolg TRUE zurück, im Fehlerfall FALSE.
	 * @param string The HTML string.
	 */
	public function loadHTML( $source ) {}
	
	/**
	 * The function parses the HTML document in the file named filename.
	 * Unlike loading XML, HTML does not have to be well-formed to load.
	 * 
	 * This function may also be called statically to load and create a
	 * DOMDocument object. The static invocation may be used when no
	 * DOMDocument properties need to be set prior to loading. 
	 * 
	 * @return boolean Gibt bei Erfolg TRUE zurück, im Fehlerfall FALSE.
	 * @param string The path to the HTML file.
	 */
	public function loadHTMLFile( $filename ) {}
	
	/**
	 * Loads an XML document from a string.
	 * 
	 * This method may also be called statically to load and create a
	 * DOMDocument object. The static invocation may be used when no
	 * DOMDocument properties need to be set prior to loading. 
	 * 
	 * @return mixed Gibt bei Erfolg TRUE zurück, im Fehlerfall FALSE. If
	 * called statically, returns a DOMDocument.
	 * @param string The string containing the XML.
	 * @param int Bitwise OR  of the libxml option constants.
	 */
	public function loadXML( $source, $options ) {}
	
	/**
	 * This method acts as if you saved and then loaded the document, putting
	 * the document in a "normal" form.
	 * 
	 * @return void
	 */
	public function normalizeDocument() {}
	
	/**
	 * This method allows you to register your own extended DOM class to be
	 * used afterward by the PHP DOM extension.
	 * 
	 * This method is not part of the DOM standard.
	 * 
	 * @return boolean Gibt bei Erfolg TRUE zurück, im Fehlerfall FALSE.
	 * @param string The DOM class that you want to extend. You can find a list
	 * of these classes in the chapter introduction.
	 * 
	 * Of course, you won't be able to register a class extending DOMDocument
	 * but you can always start your document by instanciating your own
	 * extending class.
	 * @param string Your extended class name. If NULL is provided, any
	 * previously registered class extending baseclass  will be removed.
	 */
	public function registerNodeClass( $baseclass, $extendedclass ) {}
	
	/**
	 * Performs relaxNG validation on the document based on the given RNG
	 * schema.
	 * 
	 * @return boolean Gibt bei Erfolg TRUE zurück, im Fehlerfall FALSE.
	 * @param string The RNG file.
	 */
	public function relaxNGValidate( $filename ) {}
	
	/**
	 * Performs relaxNG validation on the document based on the given RNG
	 * schema.
	 * 
	 * @return boolean Gibt bei Erfolg TRUE zurück, im Fehlerfall FALSE.
	 * @param string A string containing the RNG schema.
	 */
	public function relaxNGValidateSource( $source ) {}
	
	/**
	 * Creates an XML document from the DOM representation. This function is
	 * usually called after building a new dom document from scratch as in the
	 * example below.
	 * 
	 * @return int Returns the number of bytes written or FALSE if an error
	 * occurred.
	 * @param string The path to the saved XML document.
	 * @param int Additional Options. Currently only LIBXML_NOEMPTYTAG is
	 * supported.
	 */
	public function save( $filename, $options=0 ) {}
	
	/**
	 * Creates an HTML document from the DOM representation. This function is
	 * usually called after building a new dom document from scratch as in the
	 * example below.
	 * 
	 * @return string Returns the HTML, or FALSE if an error occurred.
	 */
	public function saveHTML() {}
	
	/**
	 * Creates an HTML document from the DOM representation. This function is
	 * usually called after building a new dom document from scratch as in the
	 * example below.
	 * 
	 * @return int Returns the number of bytes written or FALSE if an error
	 * occurred.
	 * @param string The path to the saved HTML document.
	 */
	public function saveHTMLFile( $filename ) {}
	
	/**
	 * Creates an XML document from the DOM representation. This function is
	 * usually called after building a new dom document from scratch as in the
	 * example below.
	 * 
	 * @return string
	 * @param DOMNode Use this parameter to output only a specific node without
	 * XML declaration rather than the entire document.
	 * @param int Additional Options. Currently only LIBXML_NOEMPTYTAG is
	 * supported.
	 */
	public function saveXML( $node=null, $options=0 ) {}
	
	/**
	 * Validates a document based on the given schema file.
	 * 
	 * @return boolean Gibt bei Erfolg TRUE zurück, im Fehlerfall FALSE.
	 * @param string The path to the schema.
	 */
	public function schemaValidate( $filename ) {}
	
	/**
	 * Validates a document based on a schema defined in the given string.
	 * 
	 * @return boolean Gibt bei Erfolg TRUE zurück, im Fehlerfall FALSE.
	 * @param string A string containing the schema.
	 */
	public function schemaValidateSource( $source ) {}
	
	/**
	 * Validates the document based on its DTD.
	 * 
	 * You can also use the validateOnParse property of DOMDocument to make a
	 * DTD validation. 
	 * 
	 * @return boolean Gibt bei Erfolg TRUE zurück, im Fehlerfall FALSE. If the
	 * document have no DTD attached, this method will return FALSE.
	 */
	public function validate() {}
	
	/**
	 * This method substitutes » XIncludes in a DOMDocument object.
	 * 
	 * Hinweis: Due to libxml2 automatically resolving entities, this method
	 * will produce unexpected results if the included XML file have an
	 * attached DTD.
	 * 
	 * @return int Returns the number of XIncludes in the document.
	 * @param int libxml parameters. Available since PHP 5.1.0 and
	 * Libxml 2.6.7.
	 */
	public function xinclude( $options=0 ) {}
	
}

/**
 * DOMAttr represents an attribute in the DOMElement object.
 */
class DOMAttr extends DOMNode {
	
	/**
	 * @var string The name of the attribute
	 * @readonly
	 */
	public $name;
	
	/**
	 * @var DOMElement The element which contains the attribute
	 * @readonly
	 */
	public $ownerElement;
	
	/**
	 * @var bool Not implemented yet, always is NULL
	 * @readonly
	 */
	public $schemaTypeInfo;
	
	/**
	 * @var bool Not implemented yet, always is NULL
	 * @readonly
	 */
	public $specified;
	
	/**
	 * @var string The value of the attribute
	 */
	public $value;
	
	/**
	 * Creates a new DOMAttr object. This object is read only. It may be
	 * appended to a document, but additional nodes may not be appended to this
	 * node until the node is associated with a document. To create a writeable
	 * node, use DOMDocument::createAttribute.
	 * 
	 * @param string
	 * @param string
	 */
	public function __construct( $name, $value ) {}
	
	/**
	 * This function checks if the attribute is a defined ID.
	 * 
	 * According to the DOM standard this requires a DTD which defines the
	 * attribute ID to be of type ID. You need to validate your document with
	 * DOMDocument::validate or DOMDocument::validateOnParse before using this
	 * function.
	 * 
	 * @return bool
	 */
	public function isId() {}
	
}

class DOMDocumentFragment extends DOMNode {
	
	/**
	 * Appends raw XML data to a DOMDocumentFragment.
	 * 
	 * This method is not part of the DOM standard. It was created as a
	 * simplier approach for appending an XML DocumentFragment in a
	 * DOMDocument.
	 * 
	 * If you want to stick to the standards, you will have to create a
	 * temporary DOMDocument with a dummy root and then loop through the child
	 * nodes of the root of your XML data to append them.
	 * 
	 * @return boolean Gibt bei Erfolg TRUE zurück, im Fehlerfall FALSE.
	 * @param string XML to append.
	 */
	public function appendXML( $data ) {}
	
}

/**
 * Each DOMDocument has a doctype  attribute whose value is either NULL or a
 * DOMDocumentType object.
 */
class DOMDocumentType extends DOMNode {
	
	/**
	 * @var string The public identifier of the external subset.
	 * @readonly
	 */
	public $publicId;
	
	/**
	 * @var string The system identifier of the external subset. This may be an
	 * absolute URI or not.
	 * @readonly
	 */
	public $systemId;
	
	/**
	 * @var string The name of DTD; i.e., the name immediately following the
	 * DOCTYPE keyword.
	 * @readonly
	 */
	public $name;
	
	/**
	 * @var DOMNamedNodeMap A DOMNamedNodeMap containing the general entities,
	 * both external and internal, declared in the DTD.
	 * @readonly
	 */
	public $entities;
	
	/**
	 * @var DOMNamedNodeMap A DOMNamedNodeMap containing the notations declared
	 * in the DTD.
	 * @readonly
	 */
	public $notations;
	
	/**
	 * @var string The internal subset as a string, or null if there is none.
	 * This is does not contain the delimiting square brackets.
	 * @readonly
	 */
	public $internalSubset;
}

class DOMElement extends DOMNode {
	
	/**
	 * @var boolean Not implemented yet, always return NULL
	 * @readonly
	 */
	public $schemaTypeInfo;
	
	/**
	 * @var string The element name
	 * @readonly
	 */
	public $tagName;
	
	/**
	 * Creates a new DOMElement object. This object is read only. It may be
	 * appended to a document, but additional nodes may not be appended to this
	 * node until the node is associated with a document. To create a writeable
	 * node, use DOMDocument::createElement or DOMDocument::createElementNS.
	 * 
	 * @param string The tag name of the element. When also passing in
	 * namespaceURI, the element name may take a prefix to be associated with
	 * the URI.
	 * @param string The value of the element.
	 * @param string A namespace URI to create the element within a specific
	 * namespace.
	 */
	public function __construct( $name, $value='', $namespaceURI='' ) {}
	
	/**
	 * Gets the value of the attribute with name name  for the current node.
	 * 
	 * @return string The value of the attribute, or an empty string if no
	 * attribute with the given name is found.
	 * @param string The name of the attribute.
	 */
	public function getAttribute( $name ) {}
	
	/**
	 * Returns the attribute node with name name for the current element.
	 * 
	 * @return DOMAttr The attribute node.
	 * @param string The name of the attribute.
	 */
	public function getAttributeNode( $name ) {}
	
	/**
	 * Returns the attribute node in namespace namespaceURI with local name
	 * localName for the current node.
	 * 
	 * @return DOMAttr The attribute node.
	 * @param string The namespace URI.
	 * @param string The local name.
	 */
	public function getAttributeNodeNS( $namespaceURI , $localName ) {}
	
	/**
	 * Gets the value of the attribute in namespace namespaceURI with local
	 * name localName for the current node.
	 * 
	 * @return string The value of the attribute, or an empty string if no
	 * attribute with the given localName and namespaceURI is found.
	 * @param string The namespace URI.
	 * @param string The local name.
	 */
	public function getAttributeNS( $namespaceURI, $localName ) {}
	
	/**
	 * Gets the value of the attribute in namespace namespaceURI with local
	 * name localName for the current node.
	 * 
	 * @return DOMNodeList This function returns a new instance of the class
	 * DOMNodeList of all descendant elements with a given tag name, in the
	 * order in which they are encountered in a preorder traversal of this
	 * element tree.
	 * @param string The tag name. Use * to return all elements within the
	 * element tree.
	 */
	public function getElementsByTagName( $name ) {}
	
	/**
	 * This function fetch all the descendant elements with a given localName
	 * and namespaceURI.
	 * 
	 * @return DOMNodeList This function returns a new instance of the class
	 * DOMNodeList of all descendant elements with a given tag name, in the
	 * order in which they are encountered in a preorder traversal of this
	 * element tree.
	 * @param string The namespace URI.
	 * @param string The local name. Use * to return all elements within the
	 * element tree.
	 */
	public function getElementsByTagNameNS( $namespaceURI, $localName ) {}
	
	/**
	 * Indicates whether attribute named name exists as a member of the
	 * element.
	 * 
	 * @return boolean Gibt bei Erfolg TRUE zurück, im Fehlerfall FALSE.
	 * @param string The attribute name.
	 */
	public function hasAttribute( $name ) {}
	
	/**
	 * Indicates whether attribute in namespace namespaceURI named localName
	 * exists as a member of the element.
	 * 
	 * @return boolean Gibt bei Erfolg TRUE zurück, im Fehlerfall FALSE.
	 * @param string The namespace URI.
	 * @param string The local name.
	 */
	public function hasAttributeNS( $namespaceURI, $localName ) {}
	
	/**
	 * Removes attribute named name from the element.
	 * 
	 * @return boolean Gibt bei Erfolg TRUE zurück, im Fehlerfall FALSE.
	 * @param string The name of the attribute.
	 */
	public function removeAttribute( $name ) {}
	
	/**
	 * Removes attribute oldnode from the element.
	 * 
	 * @return boolean Gibt bei Erfolg TRUE zurück, im Fehlerfall FALSE.
	 * @param DOMAttr The attribute node.
	 */
	public function removeAttributeNode( $oldnode ) {}
	
	/**
	 * Removes attribute is namespace namespaceURI named localName from the
	 * element.
	 * 
	 * @return boolean Gibt bei Erfolg TRUE zurück, im Fehlerfall FALSE.
	 * @param string The namespace URI.
	 * @param string The local name.
	 */
	public function removeAttributeNS( $namespaceURI, $localName ) {}
	
	/**
	 * Sets an attribute with name name to the given value. If the attribute
	 * does not exist, it will be created.
	 * 
	 * @return DOMAttr The new DOMAttr or FALSE if an error occured.
	 * @param string The name of the attribute.
	 * @param string The value of the attribute.
	 */
	public function setAttribute( $name, $value ) {}
	
	/**
	 * Adds new attribute node attr to element.
	 * 
	 * @return DOMAttr Returns old node if the attribute has been replaced or
	 * NULL.
	 * @param DOMAttr The attribute node.
	 */
	public function setAttributeNode( $attr ) {}
	
	/**
	 * Adds new attribute node attr to element.
	 * 
	 * @return DOMAttr Returns the old node if the attribute has been replaced.
	 * @param DOMAttr The attribute node.
	 */
	public function setAttributeNodeNS( $attr ) {}
	
	/**
	 * Sets an attribute with namespace namespaceURI and name name to the given
	 * value. If the attribute does not exist, it will be created.
	 * 
	 * @return void
	 * @param string The namespace URI.
	 * @param string The qualified name of the attribute, as prefix:tagname.
	 * @param string The value of the attribute.
	 */
	public function setAttributeNS( $namespaceURI, $qualifiedName, $value ) {}
	
	/**
	 * Declares the attribute name to be of type ID.
	 * 
	 * @return void
	 * @param string The name of the attribute.
	 * @param boolean The name of the attribute.
	 */
	public function setIdAttribute( $name, $isId ) {}
	
	/**
	 * Declares the attribute name to be of type ID.
	 * 
	 * @return void
	 * @param DOMAttr The attribute node.
	 * @param boolean Set it to TRUE if you want name  to be of type ID, FALSE otherwise.
	 */
	public function setIdAttributeNode( $attr, $isId ) {}
	
	/**
	 * Declares the attribute specified by localName and namespaceURI to be of
	 * type ID.
	 * 
	 * @return void
	 * @param string The namespace URI of the attribute.
	 * @param string The local name of the attribute, as prefix:tagname.
	 * @param boolean Set it to TRUE if you want name  to be of type ID, FALSE
	 * otherwise.
	 */
	public function setIdAttributeNS( $namespaceURI, $localName, $isId ) {}
	
}

/**
 * This interface represents a known entity, either parsed or unparsed, in an
 * XML document.
 */
class DOMEntity extends DOMNode {
	
	/**
	 * @var string The public identifier associated with the entity if
	 * specified, and NULL otherwise.
	 * @readonly
	 */
	public $publicId;
	
	/**
	 * @var string The system identifier associated with the entity if
	 * specified, and NULL otherwise. This may be an absolute URI or not.
	 * @readonly
	 */
	public $systemId;
	
	/**
	 * @var string For unparsed entities, the name of the notation for the
	 * entity. For parsed entities, this is NULL.
	 * @readonly
	 */
	public $notationName;
	
	/**
	 * @var string An attribute specifying the encoding used for this entity at
	 * the time of parsing, when it is an external parsed entity. This is NULL
	 * if it an entity from the internal subset or if it is not known.
	 */
	public $actualEncoding;
	
	/**
	 * @var string An attribute specifying, as part of the text declaration,
	 * the encoding of this entity, when it is an external parsed entity. This
	 * is NULL otherwise.
	 * @readonly
	 */
	public $encoding;
	
	/**
	 * @var string An attribute specifying, as part of the text declaration,
	 * the version number of this entity, when it is an external parsed entity.
	 * This is NULL otherwise.
	 * @readonly
	 */
	public $version;
	
}

class DOMEntityReference extends DOMNode {
	
	/**
	 * Creates a new DOMEntityReference object.
	 * 
	 * @param string The name of the entity reference.
	 */
	public function __construct( $name ) {}
	
}

/**
 * DOM operations raise exceptions under particular circumstances, i.e., when
 * an operation is impossible to perform for logical reasons.
 */
class DOMException extends Exception {
	
	/**
	 * @var int An integer indicating the type of error generated
	 * @readonly
	 */
	public $code;
	
}

/**
 * The DOMImplementation interface provides a number of methods for performing
 * operations that are independent of any particular instance of the document
 * object model.
 */
class DOMImplementation {
	/**
	 * Creates a new DOMImplementation object.
	 */
	public function __construct() {}
	
	/**
	 * Creates a DOMDocument object of the specified type with its document
	 * element.
	 * 
	 * @return DOMDocument A new DOMDocument object. If namespaceURI,
	 * qualifiedName, and doctype are null, the returned DOMDocument is empty
	 * with no document element
	 * @param string The namespace URI of the document element to create.
	 * @param string The qualified name of the document element to create.
	 * @param DOMDocumentType The type of document to create or NULL.
	 */
	public function createDocument( $namespaceURI='', $qualifiedName='', $doctype=null ) {}
	
	/**
	 * Creates an empty DOMDocumentType object. Entity declarations and
	 * notations are not made available. Entity reference expansions and 
	 * default attribute additions do not occur.
	 * 
	 * @return DOMDocumentType A new DOMDocumentType node with its
	 * ownerDocument set to NULL.
	 * @param string The qualified name of the document type to create.
	 * @param string The external subset public identifier.
	 * @param string The external subset system identifier.
	 */
	public function createDocumentType( $qualifiedName='', $publicId='', $systemId='' ) {}
	
	/**
	 * Test if the DOM implementation implements a specific feature.
	 * 
	 * You can find a list of all features in the » Conformance section of the DOM specification. 
	 * 
	 * @return boolean Gibt bei Erfolg TRUE zurück, im Fehlerfall FALSE.
	 * @return string The feature to test.
	 * @return string The version number of the feature  to test. In level 2,
	 * this can be either 2.0 or 1.0.
	 */
	public function hasFeature( $feature, $version ) {}
	
}

class DOMNamedNodeMap {
	
	/**
	 * Retrieves a node specified by its nodeName
	 * 
	 * @return DOMNode A node (of any type) with the specified nodeName, or
	 * NULL if no node is found.
	 * @param string The nodeName of the node to retrieve.
	 */
	public function getNamedItem( $name ) {}
	
	/**
	 * Retrieves a node specified by localName and namespaceURI.
	 * 
	 * @return DOMNode A node (of any type) with the specified local name and
	 * namespace URI, or NULL if no node is found.
	 * @param string The namespace URI of the node to retrieve.
	 * @param string The local name of the node to retrieve.
	 */
	public function getNamedItemNS( $namespaceURI, $localName ) {}
	
	/**
	 * Retrieves a node specified by index within the DOMNamedNodeMap object.
	 * 
	 * @return DOMNode The node at the index th position in the map, or NULL if
	 * that is not a valid index (greater than or equal to the number of nodes
	 * in this map).
	 * @param int Index into this map.
	 */
	public function item( $index ) {}
	
}

class DOMNode {
	
	/**
	 * @var string Returns the most accurate name for the current node type
	 * @readonly
	 */
	public $nodeName;
	
	/**
	 * @var string The value of this node, depending on its type
	 */
	public $nodeValue;
	
	/**
	 * @var int Gets the type of the node. One of the predefined XML_xxx_NODE
	 * constants
	 * @readonly
	 */
	public $nodeType;
	
	/**
	 * @var DOMNode The parent of this node
	 * @readonly
	 */
	public $parentNode;
	
	/**
	 * @var DOMNodeList A DOMNodeList that contains all children of this node.
	 * If there are no children, this is an empty DOMNodeList.
	 * @readonly
	 */
	public $childNodes;
	
	/**
	 * @var DOMNode The first child of this node. If there is no such node,
	 * this returns NULL.
	 * @readonly
	 */
	public $firstChild;
	
	/**
	 * @var DOMNode The last child of this node. If there is no such node,
	 * this returns NULL.
	 * @readonly
	 */
	public $lastChild;
	
	/**
	 * @var DOMNode The node immediately preceding this node. If there is no
	 * such node, this returns NULL.
	 * @readonly
	 */
	public $previousSibling;
	
	/**
	 * @var DOMNode The node immediately following this node. If there is no
	 * such node, this returns NULL.
	 * @readonly
	 */
	public $nextSibling;
	
	/**
	 * @var DOMNamedNodeMap A DOMNamedNodeMap containing the attributes of this
	 * node (if it is a DOMElement) or NULL otherwise.
	 * @readonly
	 */
	public $attributes;
	
	/**
	 * @var DOMDocument The DOMDocument object associated with this node.
	 * @readonly
	 */
	public $ownerDocument;
	
	/**
	 * @var string The namespace URI of this node, or NULL if it is
	 * unspecified.
	 * @readonly
	 */
	public $namespaceURI;
	
	/**
	 * @var string The namespace prefix of this node, or NULL if it is
	 * unspecified.
	 */
	public $prefix;
	
	/**
	 * @var string Returns the local part of the qualified name of this node.
	 * @readonly
	 */
	public $localName;
	
	/**
	 * @var string The absolute base URI of this node or NULL if the
	 * implementation wasn't able to obtain an absolute URI.
	 * @readonly
	 */
	public $baseURI;
	
	/**
	 * @var string This attribute returns the text content of this node and its
	 * descendants.
	 */
	public $textContent;
	
	/**
	 * This functions appends a child to an existing list of children or
	 * creates a new list of children. The child can be created with e.g.
	 * DOMDocument::createElement, DOMDocument::createTextNode etc. or simply
	 * by using any other node.
	 * 
	 * @return DOMNode The node added.
	 * @param DOMNode The appended child.
	 */
	public function appendChild( $newnode ) {}
	
	/**
	 * Creates a copy of the node.
	 * 
	 * @return DOMNode The cloned node.
	 * @param boolean Indicates whether to copy all descendant nodes. This
	 * parameter is defaulted to FALSE.
	 */
	public function cloneNode( $deep=false ) {}
	
	/**
	 * This method checks if the node has attributes. The tested node have to
	 * be an XML_ELEMENT_NODE.
	 * 
	 * @return boolean Gibt bei Erfolg TRUE zurück, im Fehlerfall FALSE.
	 */
	public function hasAttributes() {}
	
	/**
	 * This function checks if the node has children.
	 * 
	 * @return boolean Gibt bei Erfolg TRUE zurück, im Fehlerfall FALSE.
	 */
	public function hasChildNodes() {}
	
	/**
	 * This function inserts a new node right before the reference node. If you
	 * plan to do further modifications on the appended child you must use the
	 * returned node.
	 * 
	 * @return DOMNode The inserted node.
	 * @param DOMNode The new node.
	 * @param DOMNode The reference node. If not supplied, newnode is appended
	 * to the children.
	 */
	public function insertBefore( $newnode, $refnode=null ) {}
	
	/**
	 * Tells whether namespaceURI  is the default namespace.
	 * 
	 * @return boolean Return TRUE if namespaceURI  is the default namespace,
	 * FALSE otherwise.
	 * @param string The namespace URI to look for.
	 */
	public function isDefaultNamespace( $namespaceURI ) {}
	
	/**
	 * This function indicates if two nodes are the same node. The comparison
	 * is not based on content
	 * 
	 * @return boolean Gibt bei Erfolg TRUE zurück, im Fehlerfall FALSE.
	 * @param DOMNode The compared node.
	 */
	public function isSameNode( $node ) {}
	
	/**
	 * Checks if the asked feature is supported for the specified version.
	 * 
	 * @return boolean Gibt bei Erfolg TRUE zurück, im Fehlerfall FALSE.
	 * @param string The feature to test. See the example of
	 * DOMImplementation::hasFeature for a list of features.
	 * @param string The version number of the feature to test.
	 */
	public function isSupported( $feature, $version ) {}
	
	/**
	 * Gets the namespace URI of the node based on the prefix.
	 * 
	 * @return string The namespace URI of the node.
	 * @param string The prefix of the namespace.
	 */
	public function lookupNamespaceURI( $prefix ) {}
	
	/**
	 * Gets the namespace prefix of the node based on the namespace URI.
	 * 
	 * @return string The prefix of the namespace.
	 * @param string The namespace URI.
	 */
	public function lookupPrefix( $namespaceURI ) {}
	
	/**
	 * Normalizes the node.
	 * 
	 * @return void
	 */
	public function normalize() {}
	
	/**
	 * This functions removes a child from a list of children.
	 * 
	 * @return DOMNode If the child could be removed the functions returns the
	 * old child.
	 * @param DOMNode The removed child.
	 */
	public function removeChild( $oldnode ) {}
	
	/**
	 * This function replaces the child oldnode  with the passed new node. If
	 * the new node is already a child it will not be added a second time. If
	 * the replacement succeeds the old node is returned.
	 * 
	 * @return DOMNode The old node or FALSE if an error occur.
	 * @param DOMNode The new node. It must be a member of the target document,
	 * i.e. created by one of the DOMDocument->create*() methods or imported
	 * in the document by DOMDocument::importNode.
	 * @param DOMNode The old node.
	 */
	public function replaceChild( $newnode, $oldnode ) {}
	
}

class DOMNodeList {
	
	/**
	 * @var int The number of nodes in the list. The range of valid child node
	 * indices is 0 to length - 1 inclusive.
	 * @readonly
	 */
	public $length;
	
	/**
	 * Retrieves a node specified by index within the DOMNodeList object.
	 * 
	 * @return DOMNode The node at the index th position in the DOMNodeList,
	 * or NULL if that is not a valid index.
	 * @param int Index of the node into the collection.
	 */
	public function item( $index ) {}
	
}

class DOMNotation extends DOMNode {
	
	/**
	 * @param string
	 * @readonly
	 */
	public $publicId;
	
	/**
	 * @param string
	 * @readonly
	 */
	public $systemId;
	
}

class DOMProcessingInstruction extends DOMNode {
	
	/**
	 * @var string 
	 * @readonly
	 */
	public $target;
	
	/**
	 * @var string 
	 */
	public $data;
	
	/**
	 * Creates a new DOMProcessingInstruction object. This object is read only.
	 * It may be appended to a document, but additional nodes may not be
	 * appended to this node until the node is associated with a document. To
	 * create a writeable node, use DOMDocument::createProcessingInstruction.
	 * 
	 * @param string
	 * @param string
	 */
	public function __construct( $name, $value='' ) {}
	
}

class DOMText extends DOMCharacterData {
	
	/**
	 * @var string
	 * @readonly
	 */
	public $wholeText;
	
	/**
	 * Creates a new DOMText object.
	 * 
	 * @param string The value of the text node. If not supplied an empty text
	 * node is created.
	 */
	public function __construct( $value='' ) {}
	
	/**
	 * Indicates whether this text node contains whitespace. The text node is
	 * determined to contain whitespace in element content during the load of
	 * the document.
	 * 
	 * @return boolean Gibt bei Erfolg TRUE zurück, im Fehlerfall FALSE.
	 */
	public function isWhitespaceInElementContent() {}
	
	/**
	 *  Breaks this node into two nodes at the specified offset, keeping both
	 * in the tree as siblings.
	 * 
	 * After being split, this node will contain all the content up to the
	 * offset. If the original node had a parent node, the new node is inserted
	 * as the next sibling of the original node. When the offset is equal to
	 * the length of this node, the new node has no data. 
	 * 
	 * @return DOMText The new node of the same type, which contains all the
	 * content at and after the offset.
	 * @param int The offset at which to split, starting from 0.
	 */
	public function splitText( $offset ) {}
	
}

class DOMXPath {
	
	/**
	 * @var DOMDocument
	 */
	public $document;
	
	/**
	 * Creates a new DOMXPath object.
	 * 
	 * @param DOMDocument The DOMDocument associated with the DOMXPath.
	 */
	public function __construct( $doc ) {}
	
	/**
	 * Executes the given XPath expression and returns a typed result if
	 * possible.
	 * 
	 * @return mixed Returns a typed result if possible or a DOMNodeList
	 * containing all nodes matching the given XPath expression.
	 * @param string The XPath expression to execute.
	 * @param DOMNode The optional contextnode can be specified for doing
	 * relative XPath queries. By default, the queries are relative to the root
	 * element.
	 */
	public function evaluate( $expression, $contextnode=null ) {}
	
	/**
	 * Executes the given XPath expression.
	 * 
	 * @return DOMNodeList
	 * @param string The XPath expression to execute.
	 * @param DOMNode The optional contextnode can be specified for doing
	 * relative XPath queries. By default, the queries are relative to the root
	 * element.
	 */
	public function query( $expression, $contextnode=null ) {}
	
	/**
	 * Registers the namespaceURI and prefix with the DOMXPath object.
	 * 
	 * @return boolean Gibt bei Erfolg TRUE zurück, im Fehlerfall FALSE.
	 * @param string The prefix.
	 * @param string The URI of the namespace.
	 */
	public function registerNamespace( $prefix, $namespaceURI ) {}
	
}

/**
 * This function takes the node node of class SimpleXML and makes it into a
 * DOMElement node. This new object can then be used as a native DOMElement
 * node.
 * 
 * @return DOMElement The DOMElement node added or FALSE if any errors occur.
 * @param SimpleXMLElement The SimpleXMLElement node.
 */
function dom_import_simplexml( $node ) {}

?>