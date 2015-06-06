;(function ( $, window, document, undefined ) {

    // plugin constructor
    function ModelSelector( element, options ) {
        this.element = element;
        this.$el = $(element);
        this.options = $.extend(true, {}, $.ModelSelector.defaults, 
        		(typeof options == 'undefined') ? JSON.parse(this.$el.attr('data-options')) : options);
        this._name = "modelSelector";
        this.init();
    }

    ModelSelector.prototype = {

        init: function() {
        	var self = this;
       	
        	var s2 = $(".ms-field", this.$el).select2($.extend({
        	    ajax: {
        	        url: this.options.ajaxUrl || "",
        	        type:'post', // TODO customize
        	        dataType: 'json',
        	        data: function (term, page) {
        	        	//self.debug('data', arguments);
        	            var postData = {
        	                query: term, //search term
        	                page: page-1, // page number
        	                ajaxId:self.options.ajaxId, ajaxView:self.options.ajaxView
        	            };
        	            if (self.options.postDataPrepare)
        	            	self.options.postDataPrepare(postData);
        	            return postData;
        	        },
        	        results: function (data, page) {
        	        	//self.debug('results', arguments);
        	        	var more = self.options.listPageSize && data.length == self.options.listPageSize;
        	            return {results: data, more: more};
        	        }
        	    },
        		
        	}, this.options.select2Options))
        	.on('change', function(){
        		var v = $(this).select2('val');
        		var data = $(this).select2('data');
       			var link = $('.ms-link', self.$el);
        		if (data && data.link) {
        			link.attr('href', data.link);
        		}
        		link.attr('disabled', !data || !data.link);
        	});  
        	
        	if (this.options.value && this.options.model) {
        		try {
	        		s2.select2('data', this.options.model);
	        		s2.select2('val',this.options.value);
        		} catch(e) {}
        	}
        },
        
        debug : function() {
        	if (window.console)
        		console.log.apply(console, arguments);
        }
        
    };
    
    $.ModelSelector = ModelSelector;

	$.ModelSelector.defaults = {
		'select2Options' : {
			placeholder : '-',
			allowClear : true,
			minimumInputLength : 0,
			width : 'resolve'
		}
	};

    $.fn['modelSelector'] = function ( options ) {
    	var callArgs = Array.prototype.slice.call(arguments);
        return this.each(function () {
            if (!$.data(this, "modelSelector")) {
                $.data(this, "modelSelector",
                		new ModelSelector( this, options ));
            }
        	if (typeof options == 'string') {
        		var te = $.data(this, "modelSelector");
        		te[options].apply(te, callArgs.slice(1));
        	}
        });
    };

})( jQuery, window, document );