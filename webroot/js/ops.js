/* 
 * Global variables
 * 
 */
	// @DBOPS Settings
	// This must be set to the event date in ISO format YYYY-MM-DD 00:00:00 
	var DB_EVENT_DATE = "2016-02-06 00:00:00";
	




/**
 * Bootstrap Multiselect
 */

/**
 * Gets whether all the options are selected
 * @param {jQuery} $el
 * @returns {bool}
 */
function multiselect_selected($el) {
	var ret = true;
	$('option', $el).each(function(element) {
		if (!!!$(this).prop('selected')) {
			ret = false;
		}
	});
	return ret;
}

/**
 * Selects all the options
 * @param {jQuery} $el
 * @returns {undefined}
 */
function multiselect_selectAll($el) {
	$('option', $el).each(function(element) {
		$el.multiselect('select', $(this).val());
	});
}

/**
 * Deselects all the options
 * @param {jQuery} $el
 * @returns {undefined}
 */
function multiselect_deselectAll($el) {
	$('option', $el).each(function(element) {
		$el.multiselect('deselect', $(this).val());
	});
}

/**
 * Clears all the selected options
 * @param {jQuery} $el
 * @returns {undefined}
 */
function multiselect_toggle($el, $btn) {
	if (multiselect_selected($el)) {
		multiselect_deselectAll($el);
		$btn.text('All');
	} else {
		multiselect_selectAll($el);
		$btn.text('None');
	}
}


	$('.boot-popover').hover(function () {
	    $(this).popover({
            html: true
        }).popover('show');
            }, function () {
                $(this).popover('hide');
            });
            


/*
 * jQuery Highlight plugin
 * Based on highlight v3 by Johann Burkard
 * http://johannburkard.de/blog/programming/javascript/highlight-javascript-text-higlighting-jquery-plugin.html
 * Copyright (c) 2009 Bartek Szopka
 * Licensed under MIT license.
 * URL: http://bartaz.github.io/sandbox.js/jquery.highlight.html
 */
	jQuery.extend({
	    highlight: function (node, re, nodeName, className) {
	        if (node.nodeType === 3) {
	            var match = node.data.match(re);
	            if (match) {
	                var highlight = document.createElement(nodeName || 'span');
	                highlight.className = className || 'highlight';
	                var wordNode = node.splitText(match.index);
	                wordNode.splitText(match[0].length);
	                var wordClone = wordNode.cloneNode(true);
	                highlight.appendChild(wordClone);
	                wordNode.parentNode.replaceChild(highlight, wordNode);
	                return 1; //skip added node in parent
	            }
	        } else if ((node.nodeType === 1 && node.childNodes) && // only element nodes that have children
	                !/(script|style)/i.test(node.tagName) && // ignore script and style nodes
	                !(node.tagName === nodeName.toUpperCase() && node.className === className)) { // skip if already highlighted
	            for (var i = 0; i < node.childNodes.length; i++) {
	                i += jQuery.highlight(node.childNodes[i], re, nodeName, className);
	            }
	        }
	        return 0;
    	}
	});

	jQuery.fn.unhighlight = function (options) {
	    var settings = { className: 'highlight', element: 'span' };
	    jQuery.extend(settings, options);
	
	    return this.find(settings.element + "." + settings.className).each(function () {
	        var parent = this.parentNode;
	        parent.replaceChild(this.firstChild, this);
	        parent.normalize();
	    }).end();
	};

	jQuery.fn.highlight = function (words, options) {
	    var settings = { className: 'highlight', element: 'span', caseSensitive: false, wordsOnly: false };
	    jQuery.extend(settings, options);
	    
	    if (words.constructor === String) {
	        words = [words];
	    }
	    words = jQuery.grep(words, function(word, i){
	      return word != '';
	    });
	    words = jQuery.map(words, function(word, i) {
	      return word.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&");
	    });
	    if (words.length == 0) { return this; };
	
	    var flag = settings.caseSensitive ? "" : "i";
	    var pattern = "(" + words.join("|") + ")";
	    if (settings.wordsOnly) {
	        pattern = "\\b" + pattern + "\\b";
	    }
	    var re = new RegExp(pattern, flag);
	    
	    return this.each(function () {
	        jQuery.highlight(this, re, settings.element, settings.className);
	    });
	};
	
	$( document ).ajaxError(function( event, request, settings ) {
		if(request.status == 403){
			alert('It looks like you\'ve been logged out.  Redirecting you to the log in page.  Please log in and try again.');
	  		$('body').append( '<script> window.location = "/users/login/" </script>' );
		}
		else if(request.status !== 200){
				$('#ajax-content-load').append('<div class="well" style="background-color: #d9edf7;">If you\'re seeing this, an error was reported on your last connection to the server. This error message will help track it down.<br/><ul><li>Error: '+request.status+' (' +request.statusText+ ')</li><li>URL: '+settings.url+'</li></ul></div>');	
		}
	});
	


    //$('div.flash-success').delay(3000).fadeOut();


            
    // Accordion toggle for menu   
    function toggleCaChevron(e) {
        $(e.target)
            .prev('.panel-ctab')
            .find('i.cAindicator')
            .toggleClass('fa-chevron-down fa-chevron-up ');
    }
 


    
    
        
    
   