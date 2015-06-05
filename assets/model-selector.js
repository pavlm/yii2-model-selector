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
        		/*
        		query:function(q){
        			self.debug(self, q);
        			if (!q.term || !q.term.length) {
    					self.loadItems(null, function(data){
    						q.callback({results: data, more:false});
    					});
        			} else {
        				if (self.items) {
        					var queryFn = Select2.query.local(self.items);
        					queryFn(q);
        				} else {
        					self.loadItems(q.term, function(data){
        							q.callback({results: data, more:false});
        					});
        				}
        			}
        		}
        		*/
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
        		$('.ms-value', self.$el).val(v);
        		/*
        		var item = self.getItemById(v);
        		if (item && item.link) {
        			$('.ms-link', self.$el).attr('href', item.link);
        		}
        		*/
        	});  
        	
        	if (this.options.value && this.options.model) {
        		try {
	        		s2.select2('data', this.options.model);
	        		s2.select2('val',this.options.value);
        		} catch(e) {}
        	}
        },
        
        getItemById : function(id) {
        	var found = false;
        	$.each(this.items, function(i, item){
        		if (item.id == id) {
        			found = item;
        		}
        	});
        	return found;
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