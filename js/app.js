jQuery(document).ready(function($) {
	var timer, totalPins;
	var goodBoard = false;
	$("div.result").hide();
	$("div.file").hide();
	$("div.error").hide();

	$('input#submit').prop('disabled', false);

	$('input#submit').live('click', function() {
		console.log('click');

		$("form").hide();

		console.log($(this));
		url = $('form input#url').attr('value');
		console.log('url ' + url);
		$(this).prop('disabled', true);
		
		timer =  setInterval( function() {
	  		console.log('Timer called');
			$.ajax({  
				url: 'ptscrap.php?url=' + url + '&action=check',  
				dataType: 'json',  
				async: true,  
				success: function(json){  
					if(json.files != null) {
						console.log('timer results :');
						console.log(json);
						var load = (json.files / totalPins) * 100;
						$('div.progress div.bar').css('width', load +'%');
					}
				}
			});
		}, 2000);
		getTotalPins(url);
		if(goodBoard) {
			scrap(url);	
		}
		return false;
	});

	function getTotalPins(url) {
		console.log('getTotalPins() called');
		$("div.error").hide();
		$("div.result").show();

	    // call   
	    $.ajax({  
			url: 'ptscrap.php?url=' + url + '&action=getTotalPins',  
			dataType: 'json',  
			async: false,  
			success: function(json){  
				console.log(json);
				if(!(json.error)) {
				  	console.log(json);
				  	totalPins = json.pins;
				  	$('p#status').text('Retrieving ' + json.pins + ' pins (' + json.pages + ' pages to load)…');
				  	goodBoard = true;
		  		}
		  		else {
					clearInterval(timer);
		  			console.log('error ! Message : ' + json.error);
		  			goodBoard = false;
		  			$("div.result").hide();
		  			$("div.error").show();
		  			$("p.message").html("<strong>Error :</strong> " + json.error + "!<br/>Should be <strong>/username/board/</strong>");
		  			$("form").show();
		  			$('input#submit').prop('disabled', false);
		  		}
			}  
	    });  	    
		return true;
	}

	function scrap(url) {
		console.log('scrap() called');
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
				  	console.log("delivered");
				  	$("div.result").fadeOut(400);
				  	$('div.file').prepend('<p>Your file (' + json.pins + ' pins) is ready :)</p>');
				  	$('div.file a#download').attr('href', "files/" + json.file);
				  	$('div.file a#download').text(json.file);
				  	$("div.file").fadeIn(800);
		  		}
		  		else {
		  			console.log('error, not delivered ! Message : ' + json.error);
		  			$("div.result").hide();
		  			$("div.error").show();
		  			$("p.message").html("<strong>Error :</strong> " + json.error + "!<br/>Should be <strong>/username/board/</strong>");
		  			$("form").show();
		  			$('input#submit').prop('disabled', false);

		  		}
				clearInterval(timer);
			}  
	    });  	    
		return true;
	}
});
