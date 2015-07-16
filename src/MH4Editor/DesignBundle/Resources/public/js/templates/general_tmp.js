(function(){
	
	window.mh4app = window.mh4app || {};
	window.mh4app.templates = window.mh4app.templates || {};

	window.mh4app.templates.general = {

		'shop-item':   '<div data-id="{id}" class="item" data-role="hint" data-hint-mode="2" data-hint="{name}|{description}" data-hint-position="top">'+
							'<div class="image-container rounded">'+
								'<div class="frame">'+
									'<img src="{img}"/>'+
								'</div><br>'+
							'</div>'+
								
							'<p class="item-info">'+
								'<span id="sItemName">{name}</span><br>'+
								'Precio: <span id="sItemPrice">{buyIngame}</span> z<br>'+
								'Puntos cazador: <span id="sItemCp">{caravanPoints}</span> pts.<br>'+
								'Max. Capacidad: <strong>{carryMax}</strong> uds.<br/>'+
								'<button data-buy class="button mini-button">Comprar</button>'+
								'{?admin}'+
									'<button data-edit class="button mini-button">Editar</button>'+
								'{/admin}'+
							'</p>'+

						'</div>',
		'item-dialog':  '<div data-role="dialog" data-id="{iId}" id="{id}" data-overlay="true" data-overlay-color="op-dark" class="padding20">'+
							'<h1>Comprar {itemName}</h1>'+
							'<p>'+
								'Cuantas unidades quieres comprar?'+
								'<div class="input-control select">'+
									'<select id="{selectId}">'+
										'{#options}'+
											'<option value="{.}">{.}</option>'+
										'{/options}'+
									'</select>'+
								'</div>'+
							'</p>'+
							'<p>'+
								'<button data-action="buy" class="button success block-shadow-success text-shadow">Comprar</button>'+
								'<button data-action="cancel" class="button danger block-shadow-danger text-shadow">Cancelar</button>'+
							'</p>'+
						'</div>',
   'item-edit-dialog': '<div data-role="dialog" id="{id}" data-id="{iId}" data-overlay="true" data-overlay-color="op-dark" class="padding20">'+
							'<h1>Editar {itemName}</h1>'+
							'<p>'+
								'<div class="grid">'+
									'<div class="row cells2">'+
										'<div class="cell">'+
											'<label>Nombre</label>'+
											'<div class="input-control text">'+
												'<input type="text" name="item-name" id="item-name" value="{itemName}">'+
											'</div>'+
										'</div>'+
									'</div>'+
									'<div class="row cells2">'+
										'<div class="cell">'+
											'<label>Precio</label>'+
											'<div class="input-control text">'+
												'<input type="text" name="item-buy" id="item-buy" value="{buyIngame}" >'+
											'</div>'+
										'</div>'+
									'</div>'+
									'<div class="row cells2">'+
										'<div class="cell">'+
											'<label>Puntos cazador</label>'+
											'<div class="input-control text">'+
												'<input type="text" name="item-buy-cp" id="item-buy-cp" value="{caravanPoints}" >'+
											'</div>'+
										'</div>'+
									'</div>'+
								'</div>'+
							'</p>'+
							'<p>'+
								'<button data-action="edit" class="button success block-shadow-success text-shadow">Guardar</button>'+
								'<button data-action="cancel" class="button danger block-shadow-danger text-shadow">Cancelar</button>'+
							'</p>'+
						'</div>',
		'pagination': 	'<div class="pagination" id="{controlId}">'+
							'<div class="item"><</div>'+
							'{#pages}'+
								'<div class="item {itemType}">{.}</div>'+
							'{/pages}'+
							'<div class="item">></div>'+
						'</div>'
	};

})();