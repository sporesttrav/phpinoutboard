/*
Yetii - Yet (E)Another Tab Interface Implementation
version 1.2
http://www.kminek.pl/lab/yetii/
Copyright (c) 2007-2008 Grzegorz Wojcik
Code licensed under the BSD License:
http://www.kminek.pl/bsdlicense.txt
*/

function Yetii() {

	this.defaults = {
		
		id: null,
		active: 1,
		interval: null,
		wait: null,
		tabclass: 'tab',
		activeclass: 'active',
		callback: null
	
	};
	
	for (var n in arguments[0]) { this.defaults[n]=arguments[0][n]; };	
	
	this.getTabs = function() {
        	
        var retnode = [];
        var elem = document.getElementById(this.defaults.id).getElementsByTagName('*');
		
		var regexp = new RegExp("(^|\\s)" + this.defaults.tabclass.replace(/\-/g, "\\-") + "(\\s|$)");
	
        for (var i = 0; i < elem.length; i++) {
        if (regexp.test(elem[i].className)) retnode.push(elem[i]);
        }
    
        return retnode;
    
    };
	
	this.links = document.getElementById(this.defaults.id + '-nav').getElementsByTagName('a');
	
	this.show = function(number){
        
        for (var i = 0; i < this.tabs.length; i++) {
        this.tabs[i].style.display = ((i+1)==number) ? 'block' : 'none';
        this.links[i].className = ((i+1)==number) ? this.defaults.activeclass : '';
		}
		
		this.defaults.active = number;
		if (this.defaults.callback) this.defaults.callback(number);
    
    };
	
	this.rotate = function(interval){
    
        this.show(this.defaults.active);
        this.defaults.active++;
    
        if(this.defaults.active > this.tabs.length) this.defaults.active = 1;
    
	
        var self = this;
		
		if (this.defaults.wait) clearTimeout(this.timer2);
		 
        this.timer1 = setTimeout(function(){self.rotate(interval);}, interval*1000);
    
    };
	
	this.next = function() {
		
		this.defaults.active++;
    	if(this.defaults.active > this.tabs.length) this.defaults.active = 1;
		this.show(this.defaults.active);
	
	};
	
	this.previous = function() {
		
		this.defaults.active--;
    	if(!this.defaults.active) this.defaults.active = this.tabs.length;
		this.show(this.defaults.active);
	
	};
	
	this.tabs = this.getTabs();
	this.show(this.defaults.active);
	
	var self = this;
	for (var i = 0; i < this.links.length; i++) {
	this.links[i].customindex = i+1;
	this.links[i].onclick = function(){ 
		
		if (self.timer1) clearTimeout(self.timer1);
		if (self.timer2) clearTimeout(self.timer2); 
		
		self.show(this.customindex);
		
		if (self.defaults.wait) self.timer2 = setTimeout(function(){self.rotate(self.defaults.interval);}, self.defaults.wait*1000);
		
		return false;
	};
    }
	
	if (this.defaults.interval) this.rotate(this.defaults.interval);
	
};