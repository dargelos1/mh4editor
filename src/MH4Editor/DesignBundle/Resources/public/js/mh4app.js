(function(){

	window.mh4app = {

		loadTemplate : function(tmpGroup){

			var templates = this.templates[tmpGroup], count=0;
            
            for (var key in templates) {
                var tmpString = templates[key];

                var tmp = dust.compile(tmpString, key);
                dust.loadSource(tmp);
                count++;
            }
            console.info('Loaded '+count+' templates from "'+tmpGroup+'".');

            return this;
		},

		getData : function(dataUrl,data,callback){

			$.ajax({
				type: 'POST',
				url: dataUrl,
				dataType: 'json',
				data: data,
				success: function(data){

					callback(data);
				},
				error: function(){
					return "There was an error contacting with the server...";
				}
			});

			return this;

		},

		bindPagination : function(element){
		}
	};

})();
