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
				lastPage: 0, /*Number of last page active*/
				element: options.element, /* html element wich will binded the controls*/
				maxPagesToShow: 17,


				addPage: function(setCurrent,toHtml){

					var len = this.getNumPages();
					this.pages.push({
						itemType: '',
						val: parseInt(len+1)
					});
					len = this.getNumPages();
					if(setCurrent){
						this.setCurrentPage(len);
					}
					if(toHtml){
						this.refreshPages();
						if(setCurrent){
							this.setCurrentPage(len);
						}else{
							this.setCurrentPage(1);
						}
						
					}
					return this;
				},

				setLastPage: function(page){
					this.lastPage = page;
					return this;
				},
				getLastPage: function(){

					return this.lastPage;
				},

				setCurrentPage: function(page){
						
					//Si hago $(tthis.buttons) solo lo estoy cambiando en memoria no en el DOM!
					$(this.element).find(".item.pageButton").each(function(i,e){
						if($(this).hasClass('current')){
							$(this).removeClass('current');
							return;
						}
					});

					this.currentPage = page; /* equal to len-2 because (len-1) == ">" */
					console.log("Caller::",arguments.callee.caller);
					console.log("setCurrentPage::page=>",page);
					var total = this.getNumPages();
					var maxToShow = this.getMaxPagesToShow();
					var pagOffset;
					var mod = $(".item.pageButton[data-offset="+page+"]").index();
					//alert(mod);

					if(maxToShow < total){
						pagOffset = mod%(this.getMaxPagesToShow()-1);
					}else{
						pagOffset = page;
					}
					//alert(pagOffset);
					$(this.element).find(".item.pageButton:eq("+(pagOffset-1)+")").addClass('current');
					return this;
				},
				getCurrentPage: function(){

					return this.currentPage;
				},
				removePage: function(pageNumber){
					var index = this.pages.indexOf(pageNumber);
					if(index > -1){
						this.pages.slice(index,1);
					}
					return this;
				},
				removeAllPages: function(){

					this.pages = [];
					return this;
				},
				addMultiplePages: function(numPages){

					for(i=0;i<numPages;i++){
						this.addPage(false,false);
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
				getMaxPagesToShow: function(){

					return this.maxPagesToShow;
				},
				refreshPages: function(pageToStart){

					var that = this;
					var pages = [];
					var total = this.getNumPages();
					var currentPage = this.getCurrentPage();
					var maxToShow = this.getMaxPagesToShow();
					

					if(maxToShow < total){
						maxToShow-= 2;
						
						pageToStart = pageToStart ? pageToStart : 1;
						if(pageToStart<1)
							pageToStart = 1;
						maxToShow += pageToStart-1;
						if(maxToShow > total){
							maxToShow = maxToShow - (maxToShow-total);
						}
						for(i=pageToStart;i<=maxToShow;i++){
							pages.push({
								itemType: '',
								val: i
							});
						}

						if(maxToShow < total){
							pages.push({
								itemType: 'spaces',
								val: '...'
							});

							pages.push({
								itemType: '',
								val: total
							});
						}
					}else{
						pages = this.getAllPages();
					}
					var params = { pages: pages, controlId: options.id}
					dust.render(this.template,params,function(err,out){

						that.buttons = out;
					});

					var $p = $("#"+options.id);
					if($p.length > 0){
						$p.html($(that.buttons).html());

						/*if((this.getLastPage()+1) == this.getCurrentPage() ){
								console.log("RefreshPages::pageToStart|+1|",(pageToStart+1));
								this.setCurrentPage(pageToStart+1);
							}else if((this.getLastPage()-1) == this.getCurrentPage() ){
								console.log("RefreshPages::pageToStart|-1|",( this.getCurrentPage()));
								this.setCurrentPage(this.getCurrentPage());
							}else if(this.getLastPage() > this.getCurrentPage()){
								console.log("RefreshPages::pageToStart=>lastPage",this.getLastPage());
								this.setCurrentPage(this.getLastPage());
							}else{
								console.log("RefreshPages::pageToStart",(pageToStart));
								this.setCurrentPage(pageToStart);
							}*/

							var total = this.getNumPages();
							var maxToShow = this.getMaxPagesToShow();
							var pagOffset;
							var mod = $(".item.pageButton[data-offset="+this.getCurrentPage()+"]").index();
							//alert(mod);

							if(maxToShow < total){
								pagOffset = mod%(this.getMaxPagesToShow()-1);
							}else{
								pagOffset = pageToStart;
							}
							//alert(pagOffset);
							$(this.element).find(".item.pageButton:eq("+(pagOffset-1)+")").addClass('current');
						
					}else{
						this.bindControlToElement(this.element);
					}
					this.registerButtons();
				},
				changeToPage: function(numPage){

					var that = this;
					var current = that.currentPage;
					var newPage = numPage;
					var maxPages = that.getNumPages()
					if(numPage >= 1 && numPage <= maxPages && that.currentPage != numPage){
						$(this).trigger('pageChanged',{
							changedFrom: current,
							changedTo: newPage,
							pagId: options.id
						});
						this.setLastPage(current);
						that.setCurrentPage(numPage);
						//Refresh on change (if the total pages is over maxToShowPages)
						var maxToShow = this.getMaxPagesToShow();
						var index = $(".item.pageButton[data-offset="+numPage+"]").index();
						var mod = index%(maxToShow-2);
						if(mod == 0 && index == (maxToShow-2)){

							var pageToStart = this.getCurrentPage();
							this.refreshPages(pageToStart);
						}else if(index == -1 && newPage == current-1){
							var pageToStart = this.getCurrentPage()-(maxToShow-3);
							this.refreshPages(pageToStart);
						}else if(index == -1 && newPage == current+1){
							var pageToStart = this.getLastPage();
							this.refreshPages(pageToStart);
						}else if(index == maxToShow && newPage == maxPages){
							var pageToStart = maxPages-(maxToShow-4);
							this.refreshPages(pageToStart);
						}
						
					}
					
					return this;
				},
				loadTemplate: function(params){

					var that = this;
					var pages = [];
					var total = this.getNumPages();
					var currentPage = this.getCurrentPage();
					var maxToShow = this.getMaxPagesToShow();

					if(maxToShow < total){

						maxToShow-=2;
						for(i=1;i<=maxToShow;i++){
							pages.push({
								itemType: '',
								val: i
							});
						}

						pages.push({
							itemType: 'spaces',
							val: '...'
						});

						pages.push({
							itemType: '',
							val: total
						});
					}else{
						pages = this.getAllPages();
					}
					var oParams = { pages: pages, controlId: options.id}
					params = params || oParams;
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
				init: function(numPages){

					this.addMultiplePages(numPages);
					
					$(this.element).wrapInner(function(index){

						var html = $(this).html();
						$(this).html('');
						return "<div id='pag_content_"+options.id+"' class='pagination-content'>"+html+"</div>";
					});
					this.loadTemplate(this.templateOptions);
					this.bindControlToElement(this.element);
					this.setCurrentPage(1);
					this.registerButtons();
				},
				registerButtons: function(){

					var that = this;
					$("#"+options.id+".pagination .item").each(function(i,e){

						if(!$(this).data('pagination-action') && !$(this).hasClass('spaces')){
							$(this).on('click', function(event) {
								event.preventDefault();
								/* Act on the event */
								that.changeToPage(parseInt($(this).data("offset")));
							});
						}else{

							var dir = $(this).data('pagination-action');
							if(dir === "backward"){
								$(this).on('click', function(event) {
									event.preventDefault();
									/* Act on the event */
									that.changeToPage(parseInt(that.getCurrentPage()-1));
								});
							}else if(dir === "forward"){
								$(this).on('click', function(event) {
									event.preventDefault();
									/* Act on the event */
									that.changeToPage(parseInt(that.getCurrentPage()+1));
								});
							}
						}
					});
				}
			};
		}

	};

})();