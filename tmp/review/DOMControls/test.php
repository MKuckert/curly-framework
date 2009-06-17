<?php
error_reporting( E_ALL );
session_start();

include "Html.php";

// Init
$implementation = new HtmlImplementation();
$doc = $implementation->createDocument();
$doc->formatOutput = true;

$html = $doc->documentElement;
$doc->head = $html->appendChild( new DOMElement( 'head' ) );
$doc->body = $html->appendChild( new DOMElement( 'body' ) );
$div = $doc->body->appendChild( new DIVTag() );
$a = $div->appendChild( new ATag() );
$a->appendChild( new DOMText( 'Innerhalb des Divs' ) );
$a->setHref( 'http://www.google.de' );

$form = $div->appendChild( new DOMElement( 'form' ) );
$form->setAttribute( 'method', 'post' );

$btn1 = $form->appendChild( new INPUTTag() );
$btn1->setType( 'submit' );
$btn1->setValue( 'Klick me' );

$btn2 = $form->appendChild( new INPUTTag() );
$btn2->setType( 'submit' );
$btn2->setValue( 'Klick me 2' );

$doc->finishInit();

// PreRender
$inputs = $doc->getElementsByTagName( 'input' );
for( $i=0; $i<$inputs->length; $i++ ) {
	$input = $inputs->item( $i );
	$name = $input->getAttribute( 'name' );
	if( $name!='' and isset( $_POST[$name] ) ) {
		var_dump( 'Callback for '.$_POST[$name] );
	}
}

// Render
echo $doc->saveXML();
?>