/**
 * Curly.Typewriter
 * 
 * @author Martin Kuckert
 * @package Curly.Typewriter
 * @category Curly.Typewriter
 * @since 19.11.2008
 * @copyright Copyright (c) 2008 Martin Kuckert
 * @license New BSD License
 * 
 * Copyright (c) 2008, Martin Kuckert
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 * 
 *  - Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 * 
 *  - Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 * 
 *  - The names of its contributors may not be used to endorse or promote
 *    products derived from this software without specific prior written
 *    permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
/**
 * Curly.Typewriter
 * 
 * Stellt einen Schreibmaschinen-Effekt dar.
 * 
 * @author Martin Kuckert
 * @package Curly
 * @since 19.11.2008
 * 
 * @param string ID des zu referenzierten Elements
 * @param Object Optionen
 */
Curly.Typewriter=function(id) {
	var options=arguments.length>1 ? arguments[1] : {};
	for(var i in options) {
		this.options[i]=options[i];
	}
	
	this.targetElement=typeof id=="string"?document.getElementById(id):id;
	
	this.loadHtml();
	this.clearTargetElement();
	
	var prot='?';
};
Curly.Typewriter.prototype={
	/**
	 * @var Object Optionen für diese Instanz
	 */
	options: {
		interval:		200,
		chars:			1,
		useCursor:		true,
		cursor:			'<span style="color:#AAA;">\u2588</span>'
	},
	/**
	 * @var Element Zu verwendendes DOMElement
	 */
	targetElement: null,
	/**
	 * @var string Aktueller HTML-Text dieser Instanz
	 */
	sourceHtml: '',
	/**
	 * @var integer Der im aktuellen Prozess erreichte Index
	 */
	currentIndex: 0,
	/**
	 * @var IntervalID Timer-Interval für diese Instanz
	 */
	timer: null,
	/**
	 * @var integer Aktueller Zustand
	 */
	state: Curly.Typewriter.STATE_STOPPED,
	/**
	 * Extrahiert den HTML-Text aus dem zu verwendenden DOMElement.
	 * 
	 * @return Curly.Typewriter
	 */
	loadHtml: function() {
		this.sourceHtml=this.targetElement.innerHTML.replace(/^\s*/, '').replace(/\s*$/, '');
		return(this);
	},
	/**
	 * Gibt den Optionswert zu dem übergebenen Namen zurück.
	 * 
	 * @return mixed
	 * @param string
	 */
	getOption: function(name) {
		return(this.options[name]==undefined ? null : this.options[name]);
	},
	/**
	 * Setzt einen Optionswert.
	 * 
	 * @return Curly.Typewriter
	 * @param string
	 * @param mixed
	 */
	setOption: function(name, value) {
		this.options[name]=value;
		return(this);
	},
	/**
	 * Gibt den aktuellen HTML-Text zurück.
	 * 
	 * @return string
	 */
	getHtml: function() {
		return(this.sourceHtml);
	},
	/**
	 * Setzt den aktuell zu verwendenden HTML-Text.
	 * 
	 * @return Curly.Typewriter
	 * @param string
	 */
	setHtml: function(html) {
		this.sourceHtml=html;
		return(this);
	},
	/**
	 * Leert das aktuelle DOMElement.
	 * 
	 * @return Curly.Typewriter
	 */
	clearTargetElement: function() {
		this.targetElement.innerHTML='';
		return(this);
	},
	/**
	 * Event-Handler für das TimerEvent.
	 * 
	 * @return void
	 */
	onTimerEvent: function() {
		if(this.state==Curly.Typewriter.STATE_WRITE) {
			this.write();
		}
		else if(this.state==Curly.Typewriter.STATE_ERASE) {
			this.erase();
		}
	},
	beginWrite: function() {
		this.currentIndex=0;
		this.setState(Curly.Typewriter.STATE_WRITE);
		return(this);
	},
	beginErase: function() {
		this.currentIndex=this.sourceHtml.length-1;
		this.setState(Curly.Typewriter.STATE_ERASE);
		return(this);
	},
	stop: function() {
		this.setState(Curly.Typewriter.STATE_STOPPED);
		return(this);
	},
	getState: function() {
		return(this.state);
	},
	setState: function(state) {
		this.state=state;
		if(state==Curly.Typewriter.STATE_WRITE || state==Curly.Typewriter.STATE_ERASE) {
			this.startTimer();
		}
		else {
			this.stopTimer();
		}
		return(this);
	},
	startTimer: function() {
		this.stopTimer();
		this.timer=window.setInterval(Curly.bind(this.onTimerEvent, this), this.options.interval);
	},
	stopTimer: function() {
		if(this.timer!=null) {
			window.clearInterval(this.timer);
			this.timer=null;
		}
	},
	write: function() {
		for(var i=0; i<this.options.chars; i++) {
			this.currentIndex++;
			while(true) {
				if(this.sourceHtml[this.currentIndex]=='<') {
					this.currentIndex=this.sourceHtml.indexOf('>', this.currentIndex)+1;
				}
				else if(this.sourceHtml[this.currentIndex]=='&') {
					this.currentIndex=this.sourceHtml.indexOf(';', this.currentIndex)+1;
				}
				else {
					break;
				}
			}
		}
		
		this.log(this.currentIndex+': '+this.sourceHtml.substring(0, this.currentIndex).replace(/</g, '&lt;').replace(/>/g, '&gt;'));
		
		var out=this.sourceHtml.substring(0, this.currentIndex);
		
		if(this.currentIndex>=this.sourceHtml.length) {
			this.stop();
		}
		else if(this.options.useCursor){
			out+=this.options.cursor;
		}
		
		this.targetElement.innerHTML=out;
	},
	erase: function() {
		
	},
	log: function(msg) {
		//document.getElementById('LOG').innerHTML+=msg+"\n";
	}
};

/**#@+
 * Zustandskonstanten
 */
/**
 * @desc Gestoppt-Zustand
 */
Curly.Typewriter.STATE_STOPPED=0;
/**
 * @desc Schreibend-Zustand
 */
Curly.Typewriter.STATE_WRITE=1;
/**
 * @desc Löschend-Zustand
 */
Curly.Typewriter.STATE_ERASE=2;
/**#@-*/