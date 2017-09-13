$(document).ready(function () {
	// Otenemos la resolucion de la pantalla y configuramos en ancho inicial de la tabla
	var winAnchor = $(window).width();
	var itemNoAnchor = winAnchor * 0.08;
	var descriptionAnchor = winAnchor * 0.25;
	var menudeoAnchor = winAnchor * 0.07;
	var mayoreoAnchor = winAnchor * 0.07;
	// Configuracion de la tabla de partidas de la poliza
	var container = $('#tablaEscalas');
	container.handsontable({
		data: loadEscalas($('#version').val()),
		rowHeaders: false,
		colHeaders: true,
		contextMenu: true,
		colWidths: [itemNoAnchor, descriptionAnchor, menudeoAnchor, mayoreoAnchor],
		manualColumnResize: true,
		manualRowResize: true,
		colHeaders: ['ITEM NO', 'DESCRIPCION', 'MENUDEO', 'MAYOREO'],
		startRows: 10,
		startCols: 4,
		minSpareRows: 1,
		contextMenuCopyPaste: {
			swfPath: '~/../../Js/sources/ZeroClipboard.swf'
		},
		columns: [
			{
				data: 'item_no',
				className: 'htCenter htMiddle',
			},
			{
				data: 'Description'
			},
			{
				data: 'menudeo',
				type: 'numeric',
				format: '0'
			},
			{
				data: 'mayoreo',
				type: 'numeric',
				format: '0'
			}
		],
		contextMenu: ['copy', 'paste', 'row_above', 'row_below', 'remove_row']
	});
	// Configuracion del comportamiento de los datepicker
	$('#fi, #ff').blur(function(){
		var versiones = getVersiones($('#fi').val(), $('#ff').val());
		$('#version').empty().append('<option value="">Selecciona una versión</option>');
		$.each(versiones, function(index, version){
			$('#version').append('<option value="'+version['version']+'">['+version['version']+'] '+version['fecha']+'</option>');
		});
	});
	// Recargar el contenido de la tabla
	$('#btnGenerarReporte').click(function (e) {
		e.preventDefault();
		$('#btnGenerarReporte').attr('disabled', 'disabled');
		$('#btnGuardarVersion').attr('disabled', 'disabled');
		$('#tablaEscalas').handsontable('loadData', loadEscalas($('#version').val()));
	});
	// Guardamos el contenido de la tabla y los cambios
	$('#btnGuardarVersion').click(function (e) {
		e.preventDefault();
		$('#btnGenerarReporte').attr('disabled', 'disabled');
		$('#btnGuardarVersion').attr('disabled', 'disabled');
		saveVersion(container);
	});
	// Funcion para descargar el excel con los items de la tabla
	$('#btnExcel').click(function(e){
		e.preventDefault();
		window.open('escalas/items');
	});
	$('#btnBorrarVersion').click(function (e) {
		e.preventDefault();
		$('#btnBorrarVersion').attr('disabled', 'disabled');
		dropVersion($('#version').val());
	});
});
// Funcion para obtener el conjunto de escalas de tipo de venta
var loadEscalas = function (version) {
	var escalas = [];
	if(version != ''){
		$.ajax({
			type: 'POST',
			url: 'escalas/itemEscala',
			data: { version: version },
			dataType: 'json',
			async: false,
			success: function (response) {
				escalas = response.data;
				$('#btnGenerarReporte').removeAttr('disabled');
				$('#btnGuardarVersion').removeAttr('disabled');
			}
		});
	}
	return escalas;
}
// Funcion para cargar las versiones creadas en el rango de fechas seleccionadas
var getVersiones = function (fi, ff) {
	var versiones = [];
	if(fi != '' && ff != ''){
		$.ajax({
			type: 'POST',
			url: 'escalas/noEscalasByRange',
			data: { fi: fi, ff: ff },
			dataType: 'json',
			async: false,
			success: function (response) {
				versiones = response.data;
			}
		});
	}
	return versiones;
}
// Funcion para guardar la nueva version de escalas
var saveVersion = function (container) {
	var handsontable = container.data('handsontable');
	$.ajax({
		cache: false,
		async: true,
		type: 'POST',
		dataType: 'json',
		url: 'escalas/createNewEscala',
		data: {'data': handsontable.getData()},
		beforeSend: function(){
			$('#msjAlert').html('PROCESANDO SOLICITUD, ESPERA UN MOMENTO POR FAVOR');
			$('#modalAlert').modal('show');
		},
		success: function (response) {
			$('#msjAlert').html(response.msj);
			if (response.flag == true) {
				$('#tablaEscalas').handsontable('loadData', loadEscalas($('#version').val()));
				toastr.success(response.msj, 'MENSAJE DEL SISTEMA', {timeOut: 8000});
				$('#modalAlert').modal('hide');
			}
		},
		error: function () {
			$('#msjAlert').html('SE PRESENTO UN ERROR INESPERADO AL INTENTAR GUARDAR LOS CAMBIOS');
			$('#modalAlert').modal('hide');
		},
		complete: function() {
			$('#btnGenerarReporte').removeAttr('disabled');
			$('#btnGuardarVersion').removeAttr('disabled');
		}
	});
}
// Funcion para borrar la version selecionada 
var dropVersion = function (version) {
	$.ajax({
		cache: false,
		type: 'POST',
		dataType: 'json',
		url: 'escalas/dropVersion',
		async: true,
		data: {version: version},
		beforeSend: function(){
			$('#msjAlert').html('PROCESANDO SOLICITUD, ESPERA UN MOMENTO POR FAVOR');
			$('#modalAlert').modal('show');
		},
		success: function (response) {
			if (response.flag == true) {
				$('#modalAlert').modal('hide');
				versiones = getVersiones($('#fi').val(), $('#ff').val());
				$('#version').empty().append('<option value="">Selecciona una versión</option>');
				$.each(versiones, function(index, version){
					$('#version').append('<option value="'+version['version']+'">['+version['version']+'] '+version['fecha']+'</option>');
				});
				toastr.success(response.msj, 'MENSAJE DEL SISTEMA', {timeOut: 8000});
			}
		},
		error: function () {
			$('#msjAlert').html('SE PRESENTO UN ERROR INESPERADO AL INTENTAR GUARDAR LOS CAMBIOS');
			$('#modalAlert').modal('hide');
		},
		complete: function() {
			$('#btnBorrarVersion').removeAttr('disabled');
		}
	});
}