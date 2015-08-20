(function(){

	window.mh4app = {

		lang: 'en',
		cache: {},

		setLanguage: function(lang){

			this.lang = lang;
			return this;
		},
		getLanguage: function(){
			return this.lang;
		},

		addCache: function(cacheName,value){

			this.cache[cacheName] = value;
			return this;
		},

		getCache: function(cacheName){

			return this.cache[cacheName];
		},

		loadTemplate : function(tmpGroup){

			var templates = this.templates[this.getLanguage()][tmpGroup], count=0;
            
            for (var key in templates) {
                var tmpString = templates[key];

                var tmp = dust.compile(tmpString, key);
                dust.loadSource(tmp);
                count++;
            }
            console.info('Loaded '+count+' templates['+this.getLanguage()+'] from "'+tmpGroup+'".');

            return this;
		},

		getData : function(dataUrl,data,callback,type){

			$.ajax({
				type: type ? type : 'POST',
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
