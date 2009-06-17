<?php
	
	/**
	 * SeekOrigin
	 * Stellt die Felder zur Verfügung, die Bezugspunkte in Streams für die
	 * Suche darstellen.
	 * 
	 * @author Martin Kuckert
	 * @version 1.0
	 */
	class SeekOrigin {
		
		/**
		 * Gibt den Anfang eines Streams an.
		 */
		const Begin = 0;
		
		/**
		 * Gibt die aktuelle Position innerhalb eines Streams an.
		 */
		const Current = 1;
		
		/**
		 * Gibt das Ende eines Streams an.
		 */
		const End = 2;
		
	}
	
?>