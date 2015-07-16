(function(){

	window.mh4app = window.mh4app || {};
	window.mh4app.dialogManager = window.mh4app.dialogManager || {};

	window.mh4app.dialogManager = {

		dialogs: {},

		getDialog : function(dialogId){

			return this.dialogs[dialogId];
		},
		registerButtons : function(dialogId,oButtons){

			//console.log($(this.dialog).find("button[data-action=cancel]"));
			var that = this;

			/* Register cancel button */
			$("#"+dialogId).find("button[data-action=cancel]")
			.on('click',function(){
				that.getDialog(dialogId).closeDialog();
			});

			/*Register the other buttons*/
			if(typeof oButtons !== "undefined" && typeof oButtons === "object"){
				console.info(oButtons);
				$(oButtons).each(function(i,e){

					var elements;
					if(e.find == "inputs"){
						elements = $("#"+dialogId).find('input');
					}

					$("#"+dialogId).find('button[data-action='+e.action+']')
					.on('click',function(){
						
						e.callback(elements,this);
					})
				});	
			}

			

			return this;
		},

		addDialog : function(options){

			var template_default = options.template ? options.template : 'item-dialog';
			this.dialogs[options.id] = {
				html: "",
				dialog: '',
				buttons: '',
				dialogId: options.id,
				template: template_default,
				templateOptions: options.templateOptions,
				fromTemplate: true,
				openDialog: function(){
					//console.log(this.dialog);
					var d =  $('#'+this.dialogId).data("dialog");
					d.open();
					
					return this;
				},
				closeDialog: function(){
					var d =  $('#'+this.dialogId).data("dialog");
					d.close();
					return this;
				},
				setTemplate : function(template){
					this.template = template;
					return this;
				},
				getTemplate : function(){
					return this.template;
				},
				getDialog : function(){
					return this.dialog;
				},
				setDialog: function(dialog){
					this.dialog = $(dialog);
					//console.log($(dialog));
					return this;
				},
				isLoadedFromTemplate : function(){
					return this.fromTemplate;
				},
				setLoadFromTemplate : function(load){

					this.fromTemplate = load;
					return this;
				},
				loadFromTemplate: function(params){

					var that = this;
					var selectOptions;
					if(typeof params === "undefined"){
						params = {};
						params.options = [0];
						params.selectId = 'select_noitem';
						params.itemName = 'NoItem';
					}
					else if(typeof params.options === "undefined" || ! params.options instanceof Array){
						params.options = [0];
					}
					else{
						params.options = params.options;
					}

					/*
					{
						id: options.id,
						options: selectOptions,
						selectId: params.selectId,
						itemName: params.itemName
					}
					*/
					if(typeof params.id === "undefined")
						params.id = options.id;
					console.log("PARAMS",params);
					dust.render(this.template,params,function(err,out){

						that.html = out;
						that.setDialog(that.html);
						console.log(that.html);
						
					});

					return this;
				},
				loadFromHTML: function(html){
					this.html = html;
					this.setDialog(html);
					return this;
				},
				init: function(){

					if (this.isLoadedFromTemplate()) {
						this.loadFromTemplate(this.templateOptions);
					}else{
						this.loadFromHTML(this.html);
					}
					$('body').append(this.html);

					return this;
				}



			};

			return this;
		}

	};

})();