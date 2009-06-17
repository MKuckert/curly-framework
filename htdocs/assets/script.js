function checkAllCBs(state, dontCheck) {
	if(!(dontCheck instanceof Array)) {dontCheck=[dontCheck];}
    var cbs=document.getElementsByTagName('input');
    for(var i=0; i<cbs.length; i++) {
        if(cbs[i].getAttribute('type').toLowerCase()!='checkbox') {
            continue;
        }
        var check=true;
        for(var j=0; j<dontCheck.length; j++) {
	        if(cbs[i]==dontCheck[j]) {
	            check=false;
	        }
        }
        if(check) {
        	cbs[i].checked=state;
        	if(cbs[i].CustomCheckBoxHandle) {
        		cbs[i].CustomCheckBoxHandle.updateState();
        	}
        }
    }
}

/**
 * @return boolean True on success
 */
function onFormSubmit() {
	
}

var Curly = {
	/**
	 * @return function
	 * @param function
	 * @param object
	 */
	bind: function(func, instance) {
		var f=function() {
			return(func.apply(instance, arguments));
		};
		return(f);
	},
	classNameAdd: function(allNames, name) {
		if(allNames.match(name)==null) {
			if(allNames=='') {
				return(name);
			}
			else {
				return(allNames+" "+name);
			}
		}
		return(allNames);
	},
	classNameRemove: function(allNames, name) {
		if(allNames.match(name)!=null) {
			if(allNames==name) {
				return('');
			}
			else {
				var split=allNames.split(" ");
				for(var i=0; i<split.length; i++) {
					if(split[i]==name) {
						split.splice(i, 1);
					}
				}
				return(split.join(" "));
			}
		}
		return(allNames);
	}
};

/**
 * @param Element
 */
Curly.CustomCheckBox = function(element) {
	this.CheckBox=element;
	this.Label;
	
	this.stateChange=Curly.bind(this.stateChange, this);
	
	var sibl=element.nextSibling;
	while(sibl!=null) {
		if(sibl.nodeName.toLowerCase()=='label') {
			this.Label=sibl;
			break;
		}
		sibl=sibl.nextSibling;
	}
	
	this.CheckBox.className=Curly.CustomCheckBox.CLASS_CHECKBOX;
	if(this.CheckBox.addEventListener) {
		this.CheckBox.addEventListener('change', this.stateChange, false);
	}
	else {
		this.CheckBox.attachEvent('onchange', this.stateChange);
	}
	
	if(this.Label) {
		this.Label.className=Curly.CustomCheckBox.CLASS_LABEL;
	}
	
	this.CheckBox.CustomCheckBoxHandle=this;
	
	this.updateState();
};

Curly.CustomCheckBox.prototype = {
	stateChange: function(event) {
		event=event||window.event;
		
		this.updateState();
	},
	updateState: function() {
		if(this.CheckBox.checked) {
			this.CheckBox.className = Curly.classNameAdd(this.CheckBox.className, Curly.CustomCheckBox.CLASS_CHECKED);
			if(this.Label) {
				this.Label.className = Curly.classNameAdd(this.Label.className, Curly.CustomCheckBox.CLASS_CHECKED);
			}
		}
		else {
			this.CheckBox.className = Curly.classNameRemove(this.CheckBox.className, Curly.CustomCheckBox.CLASS_CHECKED);
			if(this.Label) {
				this.Label.className = Curly.classNameRemove(this.Label.className, Curly.CustomCheckBox.CLASS_CHECKED);
			}
		}
	}	// end of updateState
};	// end of Curly.CustomCheckBox.prototype
Curly.CustomCheckBox.initAll = function() {
    var cbs=document.getElementsByTagName('input');
    for(var i=0; i<cbs.length; i++) {
        if(cbs[i].getAttribute('type').toLowerCase()!='checkbox') {
            continue;
        }
        new Curly.CustomCheckBox(cbs[i]);
    }
};

Curly.CustomCheckBox.CLASS_CHECKBOX='CustomCheckBox';
Curly.CustomCheckBox.CLASS_LABEL='CustomLabel';
Curly.CustomCheckBox.CLASS_CHECKED='Checked';
