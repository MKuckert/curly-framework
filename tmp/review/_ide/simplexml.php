<?php
	
	/**
	 * Die SimpleXML-Extension bietet ein besonders einfaches und leicht zu
	 * handhabendes Toolset, um XML in ein Objekt zu konvertieren, das mit
	 * normalen Eigenschafts-Selektoren und Array-Iteratoren bearbeitet werden
	 * kann.
	 */
	class SimpleXMLElement implements Traversable {
		
		/**
		 * Fügt einem SimpleXML-Element ein Attribut hinzu
		 * 
		 * @return void
		 * @param string Der Name des hinzuzufügenden Attributs.
		 * @param string Der Wert des Attributs.
		 * @param string Wenn angegeben, der Namespace, zu dem das Attribut
		 * gehört.
		 */
		public function addAttribute( $name, $value, $namespace='' ) {}
		
		/**
		 * Fügt dem XML-Knoten ein Kind-Element hinzu
		 * 
		 * @return SimpleXMLElement
		 * @param string Der Name des hinzuzufügenden Kind-Elements.
		 * @param string Wenn angegeben, der Wert des Kind-Elements.
		 * @param string Wenn angegeben, der Namespace, zu dem das Kind-Element
		 * gehört.
		 */
		public function addChild( $name, $value='', $namespace='' ) {}
		
		/**
		 * Gibt einen wohlgeformten XML String zurück, der auf einem
		 * SimpleXML-Element basiert
		 * 
		 * @return mixed
		 * @param string Sofern angegeben, schreibt die Funktion die Daten in
		 * das File anstatt sie direkt zurück zu geben.
		 */
		public function asXML( $filename='' ) {}
		
		/**
		 * Identifiziert die Attribute eines Elements.
		 * Die Funktion liefert die Attribute und Werte, die innerhalb eines
		 * XML-Tags definiert wurden.
		 * 
		 * @return SimpleXMLElement
		 * @param string Ein optionaler Namespace für das abgefragte Attribut.
		 * @param bool Standardmäßig auf FALSE gesetzt.
		 */
		public function attributes( $ns='', $is_prefix=false ) {}
		
		/**
		 * Findet Kindelemente des gegebenen Knotens.
		 * Die Methode findet Kinder des Elements, dem es angehört. Das
		 * Ergebnis entspricht den normalen Iterierungsregeln.
		 * 
		 * @return SimpleXMLElement
		 * @param string Ein optionaler Namespace für das abgefragte Attribut.
		 * @param bool Standardmäßig auf FALSE gesetzt.
		 */
		public function children( $ns='', $is_prefix=false ) {}
		
		/**
		 * Erstellt ein neues SimpleXMLElement-Objekt.
		 * Erzeugt eine E_WARNING-Fehlermeldung für jeden gefundenen Fehler in
		 * den XML-Daten und wirft eine Exception, sobald ein solcher Fehler
		 * festgestellt wurde.
		 * 
		 * @param string Ein wohlgeformter XML-String, oder der Pfad oder URL
		 *  zu einem XML-Dokument, wenn data_is_url TRUE ist.
		 * @param int Optional zu verwenden, um weitere Libxml Parameter anzugeben.
		 * @param bool data ist eine URL zu einem Dokument.
		 * @param string Ein optionaler Namespace für das abgefragte Attribut.
		 * @param bool Standardmäßig auf FALSE gesetzt.
		 * @see http://php.net/manual/ref.libxml.php#libxml.constants
		 */
		final public function __construct( $data, $options=0, $data_is_url=false, $ns='', $is_prefix=false ) {}
		
		/**
		 * Gibt die im Dokument vorhandenen Namespaces zurück
		 * 
		 * @return array
		 * @param bool Sofern angegeben, werden alle Namespaces zurückgegeben,
		 * die in Eltern- und Kindknoten deklariert wurden. Andernfalls werden
		 * nur die im Wurzelknoten deklarierten Namespaces zurückgegeben.
		 */
		public function getDocNamespaces( $recursive=false ) {}
		
		/**
		 * Ermittelt den Namen eines XML-Elements
		 * 
		 * @return string Die getName-Methode gibt den Namen des durch das
		 * SimpleXMLElement-Objekt referenzierte XML-Tag als String zurück.
		 */
		public function getName() {}
		
		/**
		 * Gibt die im Dokument verwendeten Namespaces zurück
		 * 
		 * @return array
		 * @param bool Sofern angegeben, werden alle Namespaces zurückgegeben,
		 * die in Eltern- und Kindknoten deklariert wurden. Andernfalls werden
		 * nur die im Wurzelknoten deklarierten Namespaces zurückgegeben.
		 */
		public function getNamespaces( $recursive=false ) {}
		
		/**
		 * Erzeugt einen Prefix/Namespace-Kontext für den nächsten XPath-Query.
		 * Dies ist besonders hilfreich, wenn der Anbieter des übergebenen
		 * XML-Dokuments die Namespace-Prefixe verändert hat.
		 * registerXPathNamespace erzeugt einen Prefix für die dazugehörigen
		 * Namespaces, die das Abfragen der Knoten in diesem Namespace
		 * erlauben, ohne dass der Code verändert werden muss, um die vom
		 * Anbieter diktierten neuen Prefixes zu erlauben.
		 * 
		 * @return bool Gibt bei Erfolg TRUE zurück, im Fehlerfall FALSE.
		 * @param string Der Namespace-Prefix der im XPath-Query für den
		 *  angegebenen Namespace verwendet werden soll.
		 * @param string Der Namespace, der für den XPath-Query verwendet wird.
		 * Dies muss einem Namespace entsprechen, der im XML-Dokument oder im
		 * XPath-Query verwendet wird.
		 */
		public function registerXPathNamespace( $prefix, $ns ) {}
		
		/**
		 * Führt ein XPath Query auf XML-Daten aus.
		 * Die xpath-Methode durchsucht den SimpleXML-Knoten nach
		 * Kind-Elementen, die der XPath path-Anfrage entsprechen.
		 * 
		 * @return array Die Funktion gibt ein Array von
		 * SimpleXMLElement-Objekten oder im Fehlerfall FALSE zurück.
		 * @param string Eine XPath Pfad-Anfrage
		 */
		public function xpath( $path ) {}
		
	}
	
	/**
	 * Erzeugt ein SimpleXMLElement-Objekt aus einem DOM-Knoten.
	 * Die Funktion wandelt einen Knoten eines DOM-Dokuments in einen 
	 * SimpleXML-Knoten. Das neue Objekt kann wie ein normales
	 * SimpleXML-Element verwendet werden.
	 * 
	 * @return SimpleXMLElement Gibt ein SimpleXMLElement oder im Fehlerfall
	 * FALSE zurück.
	 * @param DOMNode Ein DOM-Element-Knoten.
	 * @param string Sie können diesen optionalen Parameter verwenden, damit
	 * simplexml_load_string() ein Objekt der angegebenen Klasse zurückgibt.
	 * Diese Klasse sollte die SimpleXMLElement-Klasse erweitern.
	 */
	function simplexml_import_dom( $node, $class_name='' ) {}
	
	/**
	 * Übersetzt ein XML-File in ein Objekt.
	 * Die Funktion wandelt das übergebene wohlgeformte (well-formed)
	 * XML-Dokument in ein Objekt um.
	 * 
	 * @return object Gibt ein object der Klasse SimpleXMLElement zurück,
	 * dessen Eigenschaften die Daten des XML-Dokuments enthalten. Im
	 * Fehlerfall wird FALSE zurückgegeben.
	 * @param string Pfad zur XML-Datei.
	 * Hinweis: Libxml 2 demaskiert den URI, wollen Sie also zum Beispiel b&c
	 * als Wert für den URI Parameter a verwenden, müssen Sie die Funktion wie
	 * folgt aufrufen:
	 * simplexml_load_file(rawurlencode('http://example.com/?a=' . urlencode('b&c'))).
	 * Seit PHP 5.1.0 wird Ihnen dieser Schritt von PHP abgenommen. 
	 * @param string Sie können den optionalen Parameter class_name verwenden,
	 * wenn simple_load_file() ein Objekt der spezifischen Klasse zurückgeben
	 * soll. Die gewählte Klasse sollte von der Klasse SimpleXMLElement
	 * abgeleitet sein.
	 * @param int Seit PHP 5.1.0 und Libxml 2.6.0 können Sie zusätzlich den
	 * Parameter options  verwenden, um weitere Libxml-Parameter anzugeben.
	 * @param string Ein optionaler Namespace für das abgefragte Attribut.
	 * @param bool Standardmäßig auf FALSE gesetzt.
	 * @see http://de2.php.net/manual/de/ref.libxml.php#libxml.constants
	 */
	function simplexml_load_file( $filename, $class_name='', $options=0, $ns='', $is_prefix=false ) {}
	
	/**
	 * Übersetzt einen XML-String in ein Objekt
	 * 
	 * @return object Gibt ein object der Klasse SimpleXMLElement zurück,
	 * dessen Eigenschaften die Daten des XML-Dokuments enthalten. Im
	 * Fehlerfall wird FALSE zurückgegeben.
	 * @param string Ein wohlgeformter XML-String.
	 * @param string Sie können den optionalen Parameter class_name verwenden,
	 * wenn simple_load_file() ein Objekt der spezifischen Klasse zurückgeben
	 * soll. Die gewählte Klasse sollte von der Klasse SimpleXMLElement
	 * abgeleitet sein.
	 * @param int Seit PHP 5.1.0 und Libxml 2.6.0 können Sie zusätzlich den
	 * Parameter options  verwenden, um weitere Libxml-Parameter anzugeben.
	 * @param string Ein optionaler Namespace für das abgefragte Attribut.
	 * @param bool Standardmäßig auf FALSE gesetzt.
	 * @see http://de2.php.net/manual/de/ref.libxml.php#libxml.constants
	 */
	function simplexml_load_string( $filename, $class_name='', $options=0, $ns='', $is_prefix=false ) {}
	
?>