(function(){

	window.mh4app = window.mh4app || {};
	window.mh4app.paginationManager = window.mh4app.paginationManager || {};

	window.mh4app.paginationManager = {

		paginations: {},

		getPagination: function(pagId){

			return this.paginations[pagId];
		},

		registerEvents: function(pageId){

		},

		addPagination: function(options){

			var template_default = options.template ? options.template : 'pagination';
			this.paginations[options.id] = {
				pages: [], /* array of pages*/
				showAll: false, /* display all pages in control*/
				buttons: '', /*html with the controls*/
				pageElements: 0, /* Num of elements per page*/
				template: template_default,
				templateOptions: options.templateOptions,
				currentPage: 0, /* Number of active page*/
				element: options.element, /* html element wich will binded the controls*/


				addPage: function(setCurrent){

					var len = this.getNumPages();
					this.pages.push(parseInt(len+1));
					len = this.getNumPages();
					if(setCurrent){
						this.setCurrentPage(len-2);
					}
					return this;
				},

				setCurrentPage: function(page){
						
					//Si hago $(tthis.buttons) solo lo estoy cambiando en memoria no en el DOM!
					$(this.buttons).find('.item').each(function(i,e){
						if($(this).hasClass('current')){
							$(this.removeClass('current'));
							return;
						}
					});

					this.currentPage = page; /* equal to len-2 because (len-1) == ">" */
					$(this.buttons).find(".item:eq("+(page)+")").addClass('current');
					return this;
				},
				removePage: function(pageNumber){
					var index = this.pages.indexOf(pageNumber);
					if(index > -1){
						this.pages.slice(index,1);
					}
					return this;
				},
				removeLastPage: function(){

					this.pages.pop();
					return this;
				},
				getNumPages: function(){

					return this.pages.length;
				},
				getAllPages: function(){

					return this.pages;
				},
				refreshPages: function(){

					var pages = this.getPages();
					for(var i=0;i<pages;i++){

					}
				},
				changeToPage: function(numPage){

					var that = this;
					$(this).trigger('pageChanged',{
						changedFrom: that.currentPage,
						changedTo: numPage
					});

					that.currentPage = numPage;
				},
				loadTemplate: function(params){

					params = params || { pages: this.getAllPages(),controlId: options.id};
					console.log(params);
					var that = this;
					dust.render(this.template,params,function(err,out){

						that.buttons = out;
					});

					return this;
				},
				bindControlToElement: function(element){

					$(element).append(this.buttons);
					console.info("Controls binded to =>",element);
					return this;
				},
				init: function(){

					this.addPage(false);
					this.loadTemplate(this.templateOptions);
					this.bindControlToElement(this.element);
					this.setCurrentPage(1);
					this.registerButtons();
				},
				registerButtons: function(){

					var that = this;
					$("#"+options.id+".pagination .item").each(function(i,e){

						$(this).on('click', function(event) {
							event.preventDefault();
							/* Act on the event */
							//alert(i);
							that.changeToPage(parseInt(i+1));
						});
					});
				}
			};
		}

	};

})();