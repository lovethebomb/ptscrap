/* PtScrap Prototype */
function PtScrap() { 
	this.pins = this.totalPins = this.pages = this.timerTries = this.timerLoad = 0;
	this.goodBoard = false;

	console.log("[ptscrap] Ready.");
}

/**
 * set and returns the loading bar
 * @param {String}   url      URL to retrieve data from
 * @param {Function} callback 
 */
PtScrap.prototype.setLoadingBar = function(url, callback) {
	var self = this;

	// Set a Timer to manage the loading bar
	this.timer =  setInterval( function() {
  		
  		if( self.timerTries == 10 && self.timerLoad == 0 ) {
  			clearInterval(self.timer);
  			
  			// Callback
			typeof callback === 'function' && callback({"error":"noconnection"});
  		}
  		else {
			$.ajax({  
				url: 'ptscrap.php?url=' + url + '&action=check',  
				dataType: 'json',  
				async: true,  
				success: function(json){  
					if(json.files != null) {
						this.timerLoad = (json.files / self.totalPins) * 100;
						$('div.progress div.bar').css('width', self.timerLoad +'%');
						console.log('[ptscrap] Loading... '+self.timerLoad+'%', self.timerTries);
					}
				}
			});
			self.timerTries++;
		}
	}, 2000);
};

/**
 * Returns the number of total pins of a board and its pages to scape
 * @param  {String}   url      URL of the board
 * @param  {Function} callback 

 */
PtScrap.prototype.getTotalPins = function(url, callback) {
	console.log('[ptscrap] Retrieveing total Pins to load for ', url);

	var self = this;

	$("div.error").hide();
	$("div.result").show();

	// let's do our ajax call  
    $.ajax({  
		url: 'ptscrap.php?url=' + url + '&action=getTotalPins',  
		dataType: 'json',  
		async: true,  
		success: function(json){  
			
			if(!(json.error)) {
			  	self.totalPins = json.pins;
			  	self.pages = json.pages;
			  	self.goodBoard = true;
			  	
				typeof callback === 'function' && callback({"totalPins":self.totalPins, "pages": self.pages, "goodBoard":self.goodBoard});
	  		}
	  		else {
				clearInterval(self.timer);
	  			self.goodBoard = false;
				typeof callback === 'function' && callback({"error": json.error ,"goodBoard":self.goodBoard});
	  		}
		}
    });  	   
};

/**
 * Scapre the URL and parse data to deliver file link
 * @param  {String}   url      URL of the Board
 * @param  {Function} callback 
 */
PtScrap.prototype.scrapeUrl = function(url, callback) {
	console.log('[ptscrap] Scraping ', url);

	var self = this;

	$("div.error").hide();
	$("div.result").show();

    // call   
    $.ajax({  
		url: 'ptscrap.php?url=' + url + '&action=create',  
		dataType: 'json',  
		async: true,  
		success: function(json){  
			console.log(json);
			if(json.delivered == 'true') {
			  	self.file = json.file
				typeof callback === 'function' && callback({"delivered": true ,"totalPins":self.totalPins, "file": self.file});
	  		}
	  		else {
	  			typeof callback === 'function' && callback({"delivered": false ,"error":json.error});
	  		}

			clearInterval(self.timer);
		}  
    });  	  
};

/* Main application */
jQuery(document).ready(function($) {
	var ptscrap = new PtScrap();

	var timer, totalPins;
	var goodBoard = false;

	// We hide them for now
	$("div.result").hide();
	$("div.file").hide();
	$("div.error").hide();

	// Force submit button state
	$('input#submit').prop('disabled', false);

	// Manage click event
	$('input#submit').live('click', function() {
		$("form").hide();
		url = $('form input#url').attr('value');
		console.log('[ptscrap] Checking board ' + url);
		$(this).prop('disabled', true);
		
		// Manages loading bar
		ptscrap.setLoadingBar(url, function(data) {
			if(data.error != null) {
				console.log("[ptscrap] Error : it seems I could not get a connection :(");
			} 
		});

		// Grab total Pins and start iteration
		ptscrap.getTotalPins(url, function(data) {
			if(data.goodBoard == true) {
				$('p#status').text('Retrieving ' + data.totalPins + ' pins (' + data.pages + ' pages to load)…');
			  	console.log('[ptscrap] There are ' + data.totalPins + ' pins to load, on ' + data.pages + ' page(s)');

			  	// We can scrape the URL
				ptscrap.scrapeUrl(url, function(data) {
					if( data.delivered == true) {
						$("div.result").fadeOut(400);
					  	$('div.file').prepend('<p>Your file (' + data.totalPins + ' pins) is ready :)</p>');
					  	$('div.file a#download').attr('href', "files/" + data.file);
					  	$('div.file a#download').text(data.file);
					  	$("div.file").fadeIn(800);
					} 
					else {
						console.log('[ptscrap] Error, not delivered ! Message : ' + data.error);

			  			$("div.result").hide();
			  			$("div.error").show();
			  			$("p.message").html("<strong>Error :</strong> " + data.error + "!<br/>Should be <strong>/username/board/</strong>");
			  			$("form").show();
			  			$('input#submit').prop('disabled', false);
					}
				});
			} 
			else {
	  			console.log('[ptscrap] error ! Message : ' + data.error);

	  			$("div.result").hide();
	  			$("div.error").show();
	  			$("p.message").html("<strong>Error :</strong> " + data.error + "!<br/>Should be <strong>/username/board/</strong>");
	  			$("form").show();
	  			$('input#submit').prop('disabled', false);
			}
		});

		// Do not reload the page
		return false;
	});

});
