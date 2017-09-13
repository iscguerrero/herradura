$(document).ready(function(){
	window['lastId'] = 0;
// Controlamos el envio del formulario principal
	$('#formReport').submit(function(e){
		e.preventDefault();
		if( $('#checkOpen').prop('checked') ){
			$.ajax({
				async: true,
				type: 'POST',
				cache: false,
				data: {fi: $('#fi').val(), ff: $('#ff').val()},
				url: 'reporte/completeTree',
				dataType: 'json',
				beforeSend: function(){
					$('#msjAlert').html('PROCESANDO SOLICITUD, ESPERA UN MOMENTO POR FAVOR...');
					$('#modalAlert').modal('show');
				},
				success: function(json) {
					$('#msjAlert').html(json.msj);
					if(json.flag == true){
						$('#bodyTablaUno').empty();
						insertAllDOM(json.data);
						window['lastId'] = json.lastId;
						$('#modalAlert').modal('hide');
					}
				}
			});
		} else{
			window['lastId'] = 0;
			$.ajax({
				async: true,
				type: 'POST',
				cache: false,
				data: {fi: $('#fi').val(), ff: $('#ff').val()},
				url: 'reporte/categoriasGeneral',
				dataType: 'json',
				beforeSend: function(){
					$('#msjAlert').html('PROCESANDO SOLICITUD, ESPERA UN MOMENTO POR FAVOR...');
					$('#modalAlert').modal('show');
				},
				success: function(json){
					if(json.flag == true){
						$('#bodyTablaUno').empty();
						insertInitialData(json.data);
						window['lastId'] = json.lastId;
						$('#modalAlert').modal('hide');
					} else{
						$('#msjAlert').html(json.msj);
					}
				}
			});






		}
	});
// Cargamos el data de la tabla segun se va clickeando
	$('#tablaUno tbody').on('click', 'td.details-control', function () {
		var tr = $(this).closest('tr');
		// Insertamos o removemos los rows de ta tabla
		if(tr.attr('data-open') == 'false'){
			var childData = getData(tr.attr('id'), tr.attr('data-tipo'), tr.attr('data-cat-gral'), tr.attr('data-tienda'), tr.attr('data-division'), tr.attr('data-categoria'), tr.attr('data-product-code'), tr.attr('data-valor'));
			if(childData.length > 0){
				insertDOMElements(tr.attr('id'), childData, 'after');
			}
			tr.addClass('shown');
			tr.attr('data-open', 'true');
		} else {
			tr.removeClass('shown');
			tr.attr('data-open', 'false');
			removeDOMElements(tr);
		}
	});
});
// Funcion para obtener las tiendas
var getData = function(idTr, tipo, cat_gral, tienda, division, categoria, product_code, valor){
	if(tipo == 'cat_gral'){
		var url = 'reporte/tiendas';
	} else if(tipo == 'tienda'){
		var url = 'reporte/divisionCode';
	} else if(tipo == 'division'){
		var url = 'reporte/itemCategory';
	} else if(tipo == 'categoria'){
		var url = 'reporte/itemGroupCode';
	} else if(tipo == 'product_code'){
		var url = 'reporte/productSale';
	}
	var data = [];
	$.ajax({
		async: false,
		type: 'POST',
		cache: false,
		data: {fi: $('#fi').val(), ff: $('#ff').val(), idTr: idTr, cat_gral: cat_gral, tienda: tienda, division: division, categoria: categoria, product_code: product_code, valor: valor, lastId: window['lastId']},
		url: url,
		dataType: 'json',
		beforeSend: function(){
			$('#msjAlert').html('ACTUALIZANDO REPORTE, ESPERA POR FAVOR...');
			$('#modalAlert').modal('show');
		},
		success: function(json){
			$('#msjAlert').html(json.msj);
			if(json.flag == true){
				data = json.data;
				window['lastId'] = json.lastId;
				$('#modalAlert').modal('hide');
			}
		}
	});
	return data
}
// Funcion para pintar el reporte inicial
var insertInitialData = function(data){
	var nextIndex = 0;
	var body = document.getElementById('bodyTablaUno');
	$.each(data, function(index, rowData){
		if(rowData['cantidad'] > 0){
			var newRow = body.insertRow(nextIndex);
			// Seteamos los atributos de la nueva fila
			newRow.style.backgroundColor = '#d1dcfe';
			newRow.setAttribute('id', rowData['id']);
			newRow.setAttribute('data-tipo', rowData['tipo']);
			newRow.setAttribute('data-cat-gral', rowData['valor']);
			newRow.setAttribute('data-tienda', rowData['tienda']);
			newRow.setAttribute('data-division', rowData['division']);
			newRow.setAttribute('data-categoria', rowData['categoria']);
			newRow.setAttribute('data-product-code', rowData['product_code']);
			newRow.setAttribute('data-valor', rowData['valor']);
			newRow.setAttribute('data-open', rowData['open']);
			newRow.setAttribute('data-parent', rowData['parent_id']);
			// Estos son los campos visibles de la tabla
			var cellDetalles = newRow.insertCell(0);
			rowData['tipo'] == 'no_identificado' ? cellDetalles.className = '' : cellDetalles.className = 'details-control';
			var cellValor = newRow.insertCell(1);
			cellValor.innerHTML = rowData['descripcion'];
			var cellCantidad = newRow.insertCell(2);
			cellCantidad.innerHTML = formato_numero(rowData['cantidad'], 0, '.', ',');
			cellCantidad.className = 'text-right';
			var cellImporte = newRow.insertCell(3);
			cellImporte.innerHTML = formato_numero(rowData['importe'], 2, '.', ',');
			cellImporte.className = 'text-right';
			// Incrementamos el valor del index de la tabla
			nextIndex = nextIndex + 1;
		}
	});
}
// Funcion para setear en el cuerpo de la tabla del reporte su contenido inicial
var insertDOMElements = function(element, data, typeAdd){
	$element = document.getElementById(element);
	$body = document.getElementById('bodyTablaUno');
	var nextIndex = $element.rowIndex;
	$.each(data, function(index, rowData){
		if(rowData['cantidad'] > 0){
			var newRow = $body.insertRow(nextIndex);
			if( rowData['tipo'] == 'tienda' ){
				var tab = '&nbsp;&nbsp;&nbsp;&nbsp;';
				newRow.style.backgroundColor = '#dae3fe';
			} else if( rowData['tipo'] == 'division' ){
				var tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				newRow.style.backgroundColor = '#e3eafe';
			} else if( rowData['tipo'] == 'categoria' ){
				var tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				newRow.style.backgroundColor = '#ecf1fe';
			} else if( rowData['tipo'] == 'product_code' ){
				var tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				newRow.style.backgroundColor = '#f5f8fe';
			} else if( rowData['tipo'] == 'product' ){
				var tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				newRow.style.backgroundColor = '#ffffff';
			}
			// Seteamos los atributos de la nueva fila
			newRow.setAttribute('id', rowData['id']);
			newRow.setAttribute('data-tipo', rowData['tipo']);
			newRow.setAttribute('data-cat-gral', $element.getAttribute('data-cat-gral'));
			newRow.setAttribute('data-tienda', rowData['tienda']);
			newRow.setAttribute('data-division', rowData['division']);
			newRow.setAttribute('data-categoria', rowData['categoria']);
			newRow.setAttribute('data-product-code', rowData['product_code']);
			newRow.setAttribute('data-valor', rowData['valor']);
			newRow.setAttribute('data-open', rowData['open']);
			newRow.setAttribute('data-parent', rowData['parent_id']);
			// Estos son los campos visibles de la tabla
			var cellDetalles = newRow.insertCell(0);
			rowData['tipo'] != 'product' ? cellDetalles.className = 'details-control' : cellDetalles.className = '';
			var cellValor = newRow.insertCell(1);
			cellValor.innerHTML = tab + rowData['valor'] + ' - ' + rowData['descripcion'];
			var cellCantidad = newRow.insertCell(2);
			cellCantidad.innerHTML = formato_numero(rowData['cantidad'], 0, '.', ',');
			cellCantidad.className = 'text-right';
			var cellImporte = newRow.insertCell(3);
			cellImporte.innerHTML = formato_numero(rowData['importe'], 2, '.', ',');
			cellImporte.className = 'text-right';
			// Incrementamos el valor del index de la tabla
			nextIndex = nextIndex + 1;
		}
	});
}
// Funcion para setear en el cuerpo de la tabla del reporte su contenido inicial
var insertAllDOM = function(data){
	$body = document.getElementById('bodyTablaUno');
	var nextIndex = 0;
	$.each(data, function(index, rowData){
		if(rowData['cantidad'] > 0){
			var newRow = $body.insertRow(nextIndex);
			if( rowData['tipo'] == 'cat_gral' ){
				var tab = '';
				newRow.style.backgroundColor = '#d1dcfe';
			} else if( rowData['tipo'] == 'tienda' ){
				var tab = '&nbsp;&nbsp;&nbsp;&nbsp;';
				newRow.style.backgroundColor = '#dae3fe';
			} else if( rowData['tipo'] == 'division' ){
				var tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				newRow.style.backgroundColor = '#e3eafe';
			} else if( rowData['tipo'] == 'categoria' ){
				var tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				newRow.style.backgroundColor = '#ecf1fe';
			} else if( rowData['tipo'] == 'product_code' ){
				var tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				newRow.style.backgroundColor = '#f5f8fe';
			} else if( rowData['tipo'] == 'product' ){
				var tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				newRow.style.backgroundColor = '#ffffff';
			} else if( rowData['tipo'] == 'no_identificado' ){
				var tab = '';
				newRow.style.backgroundColor = '#ffffff';
			}
			// Seteamos los atributos de la nueva fila
			newRow.classList.add('shown');
			newRow.setAttribute('id', rowData['id']);
			newRow.setAttribute('data-tipo', rowData['tipo']);
			newRow.setAttribute('data-cat-gral', rowData['cat-gral']);
			newRow.setAttribute('data-tienda', rowData['tienda']);
			newRow.setAttribute('data-division', rowData['division']);
			newRow.setAttribute('data-categoria', rowData['categoria']);
			newRow.setAttribute('data-product-code', rowData['product_code']);
			newRow.setAttribute('data-valor', rowData['valor']);
			newRow.setAttribute('data-open', rowData['open']);
			newRow.setAttribute('data-parent', rowData['parent_id']);
			// Estos son los campos visibles de la tabla
			var cellDetalles = newRow.insertCell(0);
			if(rowData['tipo'] != 'product' && rowData['tipo'] != 'no_identificado' ){
				cellDetalles.className = 'details-control';
			} else{
				cellDetalles.className = '';
			}
			var cellValor = newRow.insertCell(1);
			if(rowData['tipo'] == 'cat_gral'){
				cellValor.innerHTML = tab + rowData['descripcion'];
			} else{
				cellValor.innerHTML = tab + rowData['valor'] + ' - ' + rowData['descripcion'];
			}
			var cellCantidad = newRow.insertCell(2);
			cellCantidad.innerHTML = formato_numero(rowData['cantidad'], 0, '.', ',');
			cellCantidad.className = 'text-right';
			var cellImporte = newRow.insertCell(3);
			cellImporte.innerHTML = formato_numero(rowData['importe'], 2, '.', ',');
			cellImporte.className = 'text-right';
			// Incrementamos el valor del index de la tabla
			nextIndex = nextIndex + 1;
		}
	});
}
// Funcion para remover los child rows de un tr
var removeDOMElements = function(parent){
	var parentId = parent.attr('id');
	var cat_gral = parent.attr('data-cat-gral');
	var tienda = parent.attr('data-tienda');
	var division = parent.attr('data-division');
	var categoria = parent.attr('data-categoria');
	var product_code = parent.attr('data-product-code');

	if(parent.attr('data-tipo') == 'cat_gral'){
		$('#tablaUno tbody tr').each(function(index){
			if($(this).attr('id') != parentId && $(this).attr('data-cat-gral') == cat_gral){
				$(this).remove();
			}
		});
	} else if(parent.attr('data-tipo') == 'tienda'){
		$('#tablaUno tbody tr').each(function(index){
			if($(this).attr('id') != parentId && $(this).attr('data-cat-gral') == cat_gral && $(this).attr('data-tienda') == tienda){
				$(this).remove();
			}
		});
	} else if(parent.attr('data-tipo') == 'division'){
		$('#tablaUno tbody tr').each(function(index){
			if($(this).attr('id') != parentId && $(this).attr('data-cat-gral') == cat_gral && $(this).attr('data-tienda') == tienda && $(this).attr('data-division') == division){
				$(this).remove();
			}
		});
	} else if(parent.attr('data-tipo') == 'categoria'){
		$('#tablaUno tbody tr').each(function(index){
			if($(this).attr('id') != parentId && $(this).attr('data-cat-gral') == cat_gral && $(this).attr('data-tienda') == tienda && $(this).attr('data-division') == division && $(this).attr('data-categoria') == categoria){
				$(this).remove();
			}
		});
	} else if(parent.attr('data-tipo') == 'product_code'){
		$('#tablaUno tbody tr').each(function(index){
			if($(this).attr('id') != parentId && $(this).attr('data-cat-gral') == cat_gral && $(this).attr('data-tienda') == tienda && $(this).attr('data-division') == division && $(this).attr('data-categoria') == categoria && $(this).attr('data-product-code') == product_code){
				$(this).remove();
			}
		});
	}
}