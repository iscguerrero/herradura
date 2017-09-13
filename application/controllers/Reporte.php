<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Reporte extends Base_Controller{
	function __construct(){
		parent::__construct();
		$this->open = 'false';
	}
	function index(){
		$this->load->view('index');
	}
	function categoriasGeneral(){
		if(!$this->input->is_ajax_request()) show_404();
		# Obtenemos los parametros de la peticion
			$fi = $this->str_to_date($this->input->post('fi'));
			$ff = $this->str_to_date($this->input->post('ff')) ;
		# Cargamos el modelo de escalas
			$this->load->model('ItemEscala');
			$result = $this->ItemEscala->getLatestNoEscala();
			$lastVersion = $result[0]->version;
			$escalas = $this->ItemEscala->getVersionRows($lastVersion);
		# Formamos el arreglo base que se enviara a la vista
			$data = array();
			array_push($data, $this->item(0, 'cat_gral', 'menudeo', null, null, null, null, 'menudeo', 'MENUDEO', null));
			array_push($data, $this->item(1, 'cat_gral', 'medio_mayoreo', null, null, null, null, 'medio_mayoreo', 'MEDIO MAYOREO', null));
			array_push($data, $this->item(2, 'cat_gral', 'mayoreo', null, null, null, null, 'mayoreo', 'MAYOREO', null));
			array_push($data, $this->item(3, 'cat_gral', 'no_identificado', null, null, null, null, 'no_identificado', 'VENTA DE ESCALA NO DEFINIDA', null));
		# Cargamos el modelo de transacciones de venta
			$this->load->model('TransSalesEntry');
			$ventas = $this->TransSalesEntry->getAllSales($fi, $ff);
		# Llenamos el arreglo acorde a las escalas por producto
			foreach ($ventas as $venta) {
				$es_escalado = 0;
				foreach ($escalas as $escala) {
					if($venta->item_no == $escala->item_no) {
						$es_escalado++;
						if($venta->quantity <= $escala->menudeo) {
							$data[0]['cantidad'] += $venta->quantity;
							$data[0]['importe'] += $venta->amound;
						} else if($venta->quantity >= $escala->mmi && $venta->quantity <= $escala->mms) {
							$data[1]['cantidad'] += $venta->quantity;
							$data[1]['importe'] += $venta->amound;
						} else if($venta->quantity >= $escala->mayoreo) {
							$data[2]['cantidad'] += $venta->quantity;
							$data[2]['importe'] += $venta->amound;
						}
					}
				}
				if($es_escalado == 0) {
					$data[3]['cantidad'] += $venta->quantity;
					$data[3]['importe'] += $venta->amound;
				}
			}
		# Obtenemos la venta del producto no escalado agrupado por producto
			if($data[3]['cantidad'] > 0){
				$id = 4;
				$this->load->model('Item');
				$ventas = $this->TransSalesEntry->getUndefinedSales($fi, $ff, $lastVersion);
				foreach ($ventas as $venta) {
					$item = $this->Item->getItem($venta->item_no);
					array_push($data, $this->item($id, 'no_identificado', 'no_identificado', null, null, null, null, $venta->item_no, "&nbsp;&nbsp;&nbsp;&nbsp;".$item[0]->Description, 3));
					$data[$id]['cantidad'] += $venta->quantity;
					$data[$id]['importe'] += $venta->amound;
					$id++;
				}
			}


		exit(json_encode(array('flag'=>true, 'msj'=>'CARGA EXITOSA', 'data'=>$data, 'lastId'=>count($data)-1)));
	}
	function tiendas(){
		# Comprobamos que sea una peticion ajax la que hizo la peticin
			if(!$this->input->is_ajax_request()) show_404();
		# Obtenemos los datos del encabezado de la peticion
			$fi = $this->str_to_date($this->input->post('fi'));
			$ff = $this->str_to_date($this->input->post('ff'));
			$idParent = $this->input->post('idTr');
			$cat_gral = $this->input->post('cat_gral');
			$tienda = $this->input->post('tienda');
			$division = $this->input->post('division');
			$categoria = $this->input->post('categoria');
			$valor = $this->input->post('valor');
			$lastId = $this->input->post('lastId');
		# Creamos el arreglo de tiendas
			$this->load->model('TransSalesEntry');
			$stores = $this->TransSalesEntry->getStores($fi, $ff);
			$tiendas = array();
			foreach ($stores as $store) {
				$lastId++;
				array_push($tiendas, $this->item($lastId, 'tienda', $cat_gral, $store->tienda, null, null, null, $store->tienda, $store->Description, $idParent));
			}
		# Cargamos las escalas
			$this->load->model('ItemEscala');
			$result = $this->ItemEscala->getLatestNoEscala();
			$lastVersion = $result[0]->version;
			$escalas = $this->ItemEscala->getVersionRows($lastVersion);
		# Recorremos la venta de cada tienda
			foreach ($tiendas as $key => $tienda) {
				$store = $tienda['valor'];
				$ventas = $this->TransSalesEntry->getSalesStore($fi, $ff, $store);
				foreach ($ventas as $venta) {
					foreach ($escalas as $escala) {
						if ($venta->item_no == $escala->item_no){
							if($cat_gral == 'menudeo' && $venta->quantity <= $escala->menudeo){
								$tiendas[$key]['cantidad'] += $venta->quantity;
								$tiendas[$key]['importe'] += $venta->amount;
							} else if($cat_gral == 'medio_mayoreo' && $venta->quantity >= $escala->mmi && $venta->quantity <= $escala->mms){
								$tiendas[$key]['cantidad'] += $venta->quantity;
								$tiendas[$key]['importe'] += $venta->amount;
							} else if($cat_gral == 'mayoreo' && $venta->quantity >= $escala->mayoreo){
								$tiendas[$key]['cantidad'] += $venta->quantity;
								$tiendas[$key]['importe'] +=$venta->amount;
							}
						}
					}
				}
			}
		exit(json_encode(array('flag'=>true, 'msj'=>'CARGA EXITOSA', 'data'=>$tiendas, 'lastId'=>$lastId)));
	}
	function divisionCode(){
		# Comprobamos que sea una peticion ajax la que hizo la peticin
			if(!$this->input->is_ajax_request()) show_404();
		# Obtenemos los datos del encabezado de la peticion
			$fi = $this->str_to_date($this->input->post('fi'));
			$ff = $this->str_to_date($this->input->post('ff'));
			$idParent = $this->input->post('idTr');
			$cat_gral = $this->input->post('cat_gral');
			$tienda = $this->input->post('tienda');
			$division = $this->input->post('division');
			$categoria = $this->input->post('categoria');
			$valor = $this->input->post('valor');
			$lastId = $this->input->post('lastId');
		# Obtenemos los codigos de division
			$this->load->model('divisionCode');
			$divisionCodes = $this->divisionCode->getCodes($fi, $ff);
		# Cargamos las escalas
			$this->load->model('ItemEscala');
			$result = $this->ItemEscala->getLatestNoEscala();
			$lastVersion = $result[0]->version;
			$escalas = $this->ItemEscala->getVersionRows($lastVersion);
		# Formamos el arreglo que se enviara a la vista
			$divisiones = array();
			foreach ($divisionCodes as $divisionCode) {
				$lastId++;
				array_push($divisiones, $this->item($lastId, 'division', $cat_gral, $tienda, $divisionCode->Code, null, null, $divisionCode->Code, $divisionCode->Description, $idParent));
			}
			$this->load->model('TransSalesEntry');
		# Obtenemos las ventas por division
			foreach ($divisiones as $key => $division) {
				$divisionCode = $division['valor'];
				$ventas = $this->TransSalesEntry->getSalesDivision($fi, $ff, $tienda, $divisionCode);
				foreach ($ventas as $venta) {
					foreach ($escalas as $escala) {
						if ($venta->item_no == $escala->item_no){
							if($cat_gral == 'menudeo' && $venta->quantity <= $escala->menudeo){
								$divisiones[$key]['cantidad'] += $venta->quantity;
								$divisiones[$key]['importe'] += $venta->amount;
							} else if($cat_gral == 'medio_mayoreo' && $venta->quantity >= $escala->mmi && $venta->quantity <= $escala->mms){
								$divisiones[$key]['cantidad'] += $venta->quantity;
								$divisiones[$key]['importe'] += $venta->amount;
							} else if($cat_gral == 'mayoreo' && $venta->quantity >= $escala->mayoreo){
								$divisiones[$key]['cantidad'] += $venta->quantity;
								$divisiones[$key]['importe'] += $venta->amount;
							}
						}
					}
				}
			}
		exit(json_encode(array('flag'=>true, 'msj'=>'CARGA EXITOSA', 'data'=>$divisiones, 'lastId'=>$lastId)));
	}
	function itemCategory(){
		# Comprobamos que sea una peticion ajax la que hizo la peticin
			if(!$this->input->is_ajax_request()) show_404();
		# Obtenemos los datos del encabezado de la peticion
			$fi = $this->str_to_date($this->input->post('fi'));
			$ff = $this->str_to_date($this->input->post('ff'));
			$idParent = $this->input->post('idTr');
			$cat_gral = $this->input->post('cat_gral');
			$tienda = $this->input->post('tienda');
			$division = $this->input->post('division');
			$categoria = $this->input->post('categoria');
			$valor = $this->input->post('valor');
			$lastId = $this->input->post('lastId');
		# Obtenemos los codigos de categorias
			$this->load->model('ItemCategory');
			$itemCats = $this->ItemCategory->getCodes($division);
		# Cargamos las escalas
			$this->load->model('ItemEscala');
			$result = $this->ItemEscala->getLatestNoEscala();
			$lastVersion = $result[0]->version;
			$escalas = $this->ItemEscala->getVersionRows($lastVersion);
		# Formamos el arreglo que se enviara a la vista
			$itemCategorias = array();
			foreach ($itemCats as $itemCat) {
				$lastId++;
				array_push($itemCategorias, $this->item($lastId, 'categoria', $cat_gral, $tienda, $division, $itemCat->Code, null, $itemCat->Code, $itemCat->Description, $idParent));
			}
			$this->load->model('TransSalesEntry');
		# Obtenemos las ventas por division
			foreach ($itemCategorias as $key => $itemCategoria) {
				$categoria = $itemCategoria['categoria'];
				$ventas = $this->TransSalesEntry->getSalesCategory($fi, $ff, $tienda, $division, $categoria);
				foreach ($ventas as $venta) {
					foreach ($escalas as $escala) {
						if ($venta->item_no == $escala->item_no){
							if($cat_gral == 'menudeo' && $venta->quantity <= $escala->menudeo){
								$itemCategorias[$key]['cantidad'] += $venta->quantity;
								$itemCategorias[$key]['importe'] += $venta->amount;
							} else if($cat_gral == 'medio_mayoreo' && $venta->quantity >= $escala->mmi && $venta->quantity <= $escala->mms){
								$itemCategorias[$key]['cantidad'] += $venta->quantity;
								$itemCategorias[$key]['importe'] += $venta->amount;
							} else if($cat_gral == 'mayoreo' && $venta->quantity >= $escala->mayoreo){
								$itemCategorias[$key]['cantidad'] += $venta->quantity;
								$itemCategorias[$key]['importe'] += $venta->amount;
							}
						}
					}
				}
			}
		exit(json_encode(array('flag'=>true, 'msj'=>'CARGA EXITOSA', 'data'=>$itemCategorias, 'lastId'=>$lastId)));
	}
	function itemGroupCode(){
		# Comprobamos que sea una peticion ajax la que hizo la peticin
			if(!$this->input->is_ajax_request()) show_404();
		# Obtenemos los datos del encabezado de la peticion
			$fi = $this->str_to_date($this->input->post('fi'));
			$ff = $this->str_to_date($this->input->post('ff'));
			$idParent = $this->input->post('idTr');
			$cat_gral = $this->input->post('cat_gral');
			$tienda = $this->input->post('tienda');
			$division = $this->input->post('division');
			$categoria = $this->input->post('categoria');
			$valor = $this->input->post('valor');
			$lastId = $this->input->post('lastId');
		# Obtenemos los product group codes
			$this->load->model('ProductGroupCode');
			$groupCodes = $this->ProductGroupCode->getCodes($fi, $ff, $tienda, $division, $categoria);
		# Cargamos las escalas
			$this->load->model('ItemEscala');
			$result = $this->ItemEscala->getLatestNoEscala();
			$lastVersion = $result[0]->version;
			$escalas = $this->ItemEscala->getVersionRows($lastVersion);
		# Formamos el arreglo que se enviara a la vista
			$productos = array();
			foreach ($groupCodes as $groupCode) {
				$lastId++;
				array_push($productos, $this->item($lastId, 'product_code', $cat_gral, $tienda, $division, $categoria, $groupCode->Code, $groupCode->Code, $groupCode->Code, $idParent));
			}
			$this->load->model('TransSalesEntry');
		# Obtenemos las ventas por division
			foreach ($productos as $key => $producto) {
				$productCode = $producto['valor'];
				$ventas = $this->TransSalesEntry->getSalesProductCode($fi, $ff, $tienda, $division, $categoria, $productCode);
				foreach ($ventas as $venta) {
					foreach ($escalas as $escala) {
						if ($venta->item_no == $escala->item_no){
							if($cat_gral == 'menudeo' && $venta->quantity <= $escala->menudeo){
								$productos[$key]['cantidad'] += $venta->quantity;
								$productos[$key]['importe'] += $venta->amount;
							} else if($cat_gral == 'medio_mayoreo' && $venta->quantity >= $escala->mmi && $venta->quantity <= $escala->mms){
								$productos[$key]['cantidad'] += $venta->quantity;
								$productos[$key]['importe'] += $venta->amount;
							} else if($cat_gral == 'mayoreo' && $venta->quantity >= $escala->mayoreo){
								$productos[$key]['cantidad'] += $venta->quantity;
								$productos[$key]['importe'] += $venta->amount;
							}
						}
					}
				}
			}
		exit(json_encode(array('flag'=>true, 'msj'=>'CARGA EXITOSA', 'data'=>$productos, 'lastId'=>$lastId)));
	}
	function productSale(){
		# Comprobamos que sea una peticion ajax la que hizo la peticin
			if(!$this->input->is_ajax_request()) show_404();
		# Obtenemos los datos del encabezado de la peticion
			$fi = $this->str_to_date($this->input->post('fi'));
			$ff = $this->str_to_date($this->input->post('ff'));
			$idParent = $this->input->post('idTr');
			$cat_gral = $this->input->post('cat_gral');
			$tienda = $this->input->post('tienda');
			$division = $this->input->post('division');
			$categoria = $this->input->post('categoria');
			$product_code = $this->input->post('product_code');
			$valor = $this->input->post('valor');
			$lastId = $this->input->post('lastId');
		# Obtenemos los product group codes
			$this->load->model('Product');
			$products = $this->Product->getCodes($fi, $ff, $tienda, $division, $categoria, $product_code);
		# Cargamos las escalas
			$this->load->model('ItemEscala');
			$result = $this->ItemEscala->getLatestNoEscala();
			$lastVersion = $result[0]->version;
			$escalas = $this->ItemEscala->getVersionRows($lastVersion);
		# Formamos el arreglo que se enviara a la vista
			$productos = array();
			foreach ($products as $product) {
				$lastId++;
				array_push($productos, $this->item($lastId, 'product', $cat_gral, $tienda, $division, $categoria, $product_code, $product->item_no, $product->Description, $idParent));
			}
			$this->load->model('TransSalesEntry');
		# Obtenemos las ventas por division
			foreach ($productos as $key => $producto) {
				$product = $producto['valor'];
				$ventas = $this->TransSalesEntry->getSalesProduct($fi, $ff, $tienda, $division, $categoria, $product_code, $product);
				foreach ($ventas as $venta) {
					foreach ($escalas as $escala) {
						if ($venta->item_no == $escala->item_no){
							if($cat_gral == 'menudeo' && $venta->quantity <= $escala->menudeo){
								$productos[$key]['cantidad'] += $venta->quantity;
								$productos[$key]['importe'] += $venta->amount;
							} else if($cat_gral == 'medio_mayoreo' && $venta->quantity >= $escala->mmi && $venta->quantity <= $escala->mms) {
								$productos[$key]['cantidad'] += $venta->quantity;
								$productos[$key]['importe'] += $venta->amount;
							} else if($cat_gral == 'mayoreo' && $venta->quantity >= $escala->mayoreo){
								$productos[$key]['cantidad'] += $venta->quantity;
								$productos[$key]['importe'] += $venta->amount;
							}
						}
					}
				}
			}
		exit(json_encode(array('flag'=>true, 'msj'=>'CARGA EXITOSA', 'data'=>$productos, 'lastId'=>$lastId)));
	}
	function completeTree(){
		$this->open = 'true';
		# Comprobamos que sea una peticion ajax la que hizo la peticin
			if(!$this->input->is_ajax_request()) show_404();
		# Obtenemos los datos del encabezado de la peticion
			$fi = $this->str_to_date($this->input->post('fi'));
			$ff = $this->str_to_date($this->input->post('ff'));

		# Cargamos los modelos necesarios
			$this->load->model('ItemEscala');
			$this->load->model('divisionCode');
			$this->load->model('ItemCategory');
			$this->load->model('ProductGroupCode');
			$this->load->model('Product');
			$this->load->model('TransSalesEntry');
			$this->load->model('Item');

		# Arreglo con el ultimo escalado registrado
			$resultEscalas = $this->ItemEscala->getLatestNoEscala();
			$lastVersion = $resultEscalas[0]->version;
			$escalas = $this->ItemEscala->getVersionRows($lastVersion);

		# Arreglo con las categorias generales del periodo de venta
			$cat_grals = array(
				array('tipo'=>'cat_gral', 'cat_gral'=>'menudeo', 'valor'=>'menudeo', 'descripcion'=>'MENUDEO'),
				array('tipo'=>'cat_gral', 'cat_gral'=>'medio_mayoreo', 'valor'=>'medio_mayoreo', 'descripcion'=>'MEDIO MAYOREO'),
				array('tipo'=>'cat_gral', 'cat_gral'=>'mayoreo', 'valor'=>'mayoreo', 'descripcion'=>'MAYOREO')
			);

		# Arreglo con las tiendas que tuvieron venta en el periodo consultado
			$tiendas = $this->TransSalesEntry->getStores($fi, $ff);

		# Arreglo con las divisiones de los productos que tuvieron venta en el periodo consultado
			$divisiones = $this->divisionCode->getCodes($fi, $ff);

		# FORMAMOS EL ARBOL
			$id = $id_cat_gral = $id_tienda = $id_division = $id_categoria = $id_codigo = $id_producto = 0;
			$data = array();
			foreach ($cat_grals as $key => $cat_gral) {
				$id_cat_gral = $id;
				array_push($data, $this->item($id_cat_gral, 'cat_gral', $cat_gral['cat_gral'], null, null, null, null, $cat_gral['valor'], $cat_gral['descripcion'], null));
				$id++;
				$ventas = $this->TransSalesEntry->getAllSales($fi, $ff);
				$cantidadNoEscalado = $importeNoEscalado = 0;
				foreach ($ventas as $venta) {
					$es_escalado = 0;
					foreach ($escalas as $escala) {
						if($venta->item_no == $escala->item_no) {
							$es_escalado++;
							if($data[$id_cat_gral]['cat-gral'] == 'menudeo' && $venta->quantity <= $escala->menudeo) {
								$data[$id_cat_gral]['cantidad'] += $venta->quantity;
								$data[$id_cat_gral]['importe'] += $venta->amound;
							} else if($data[$id_cat_gral]['cat-gral'] == 'medio_mayoreo' && $venta->quantity >= $escala->mmi && $venta->quantity <= $escala->mms) {
								$data[$id_cat_gral]['cantidad'] += $venta->quantity;
								$data[$id_cat_gral]['importe'] += $venta->amound;
							} else if($data[$id_cat_gral]['cat-gral'] == 'mayoreo' && $venta->quantity >= $escala->mayoreo) {
								$data[$id_cat_gral]['cantidad'] += $venta->quantity;
								$data[$id_cat_gral]['importe'] += $venta->amound;
							}
						}
					}
					if($es_escalado == 0) {
						$cantidadNoEscalado += $venta->quantity;
						$importeNoEscalado += $venta->amound;
					}
				}

				foreach ($tiendas as $tienda) {
					$id_tienda = $id;
					array_push($data, $this->item($id_tienda, 'tienda', $cat_gral['cat_gral'], $tienda->tienda, null, null, null, $tienda->tienda, $tienda->Description, $id_cat_gral));
					$id++;
					$ventas = $this->TransSalesEntry->getSalesStore($fi, $ff, $tienda->tienda);
					foreach ($ventas as $venta) {
						foreach ($escalas as $escala) {
							if ($venta->item_no == $escala->item_no){
								if($data[$id_tienda]['cat-gral'] == 'menudeo' && $venta->quantity <= $escala->menudeo){
									$data[$id_tienda]['cantidad'] += $venta->quantity;
									$data[$id_tienda]['importe'] += $venta->amount;
								} else if($data[$id_tienda]['cat-gral'] == 'medio_mayoreo' && $venta->quantity >= $escala->mmi && $venta->quantity <= $escala->mms){
									$data[$id_tienda]['cantidad'] += $venta->quantity;
									$data[$id_tienda]['importe'] += $venta->amount;
								} else if($data[$id_tienda]['cat-gral'] == 'mayoreo' && $venta->quantity >= $escala->mayoreo){
									$data[$id_tienda]['cantidad'] += $venta->quantity;
									$data[$id_tienda]['importe'] +=$venta->amount;
								}
							}
						}
					}

					foreach ($divisiones as $division) {
						$id_division = $id;
						array_push($data, $this->item($id_division, 'division', $cat_gral['cat_gral'], $tienda->tienda, $division->Code, null, null, $division->Code, $division->Description, $id_tienda));
						$id++;
						$ventas = $this->TransSalesEntry->getSalesDivision($fi, $ff, $tienda->tienda, $division->Code);
						foreach ($ventas as $venta) {
							foreach ($escalas as $escala) {
								if ($venta->item_no == $escala->item_no){
									if($data[$id_division]['cat-gral'] == 'menudeo' && $venta->quantity <= $escala->menudeo){
										$data[$id_division]['cantidad'] += $venta->quantity;
										$data[$id_division]['importe'] += $venta->amount;
									} else if($data[$id_division]['cat-gral'] == 'medio_mayoreo' && $venta->quantity >= $escala->mmi && $venta->quantity <= $escala->mms){
										$data[$id_division]['cantidad'] += $venta->quantity;
										$data[$id_division]['importe'] += $venta->amount;
									} else if($data[$id_division]['cat-gral'] == 'mayoreo' && $venta->quantity >= $escala->mayoreo){
										$data[$id_division]['cantidad'] += $venta->quantity;
										$data[$id_division]['importe'] += $venta->amount;
									}
								}
							}
						}

						# Arreglo con las categorias
						$categorias = $this->ItemCategory->getCodes($division->Code);
						foreach ($categorias as $categoria) {
							$id_categoria = $id;
							array_push($data, $this->item($id_categoria, 'categoria', $cat_gral['cat_gral'], $tienda->tienda, $division->Code, $categoria->Code, null, $categoria->Code, $categoria->Description, $id_division));
							$id++;
							$ventas = $this->TransSalesEntry->getSalesCategory($fi, $ff, $tienda->tienda, $division->Code, $categoria->Code);
							foreach ($ventas as $venta) {
								foreach ($escalas as $escala) {
									if ($venta->item_no == $escala->item_no){
										if($data[$id_categoria]['cat-gral'] == 'menudeo' && $venta->quantity <= $escala->menudeo){
											$data[$id_categoria]['cantidad'] += $venta->quantity;
											$data[$id_categoria]['importe'] += $venta->amount;
										} else if($data[$id_categoria]['cat-gral'] == 'medio_mayoreo' && $venta->quantity >= $escala->mmi && $venta->quantity <= $escala->mms){
											$data[$id_categoria]['cantidad'] += $venta->quantity;
											$data[$id_categoria]['importe'] += $venta->amount;
										} else if($data[$id_categoria]['cat-gral'] == 'mayoreo' && $venta->quantity >= $escala->mayoreo){
											$data[$id_categoria]['cantidad'] += $venta->quantity;
											$data[$id_categoria]['importe'] += $venta->amount;
										}
									}
								}
							}

							# Arreglo con los codigos de productos
							$codigos = $this->ProductGroupCode->getCodes($fi, $ff, $tienda->tienda, $division->Code, $categoria->Code);
							foreach ($codigos as $codigo) {
								$id_codigo = $id;
								array_push($data, $this->item($id_codigo, 'product_code', $cat_gral['cat_gral'], $tienda->tienda, $division->Code, $categoria->Code, $codigo->Code, $codigo->Code, $codigo->Code, $id_categoria));
								$id++;
								$ventas = $this->TransSalesEntry->getSalesProductCode($fi, $ff, $tienda->tienda, $division->Code, $categoria->Code, $codigo->Code);
								foreach ($ventas as $venta) {
									foreach ($escalas as $escala) {
										if ($venta->item_no == $escala->item_no){
											if($data[$id_codigo]['cat-gral'] == 'menudeo' && $venta->quantity <= $escala->menudeo){
												$data[$id_codigo]['cantidad'] += $venta->quantity;
												$data[$id_codigo]['importe'] += $venta->amount;
											} else if($data[$id_codigo]['cat-gral'] == 'medio_mayoreo' && $venta->quantity >= $escala->mmi && $venta->quantity <= $escala->mms){
												$data[$id_codigo]['cantidad'] += $venta->quantity;
												$data[$id_codigo]['importe'] += $venta->amount;
											} else if($data[$id_codigo]['cat-gral'] == 'mayoreo' && $venta->quantity >= $escala->mayoreo){
												$data[$id_codigo]['cantidad'] += $venta->quantity;
												$data[$id_codigo]['importe'] += $venta->amount;
											}
										}
									}
								}

								# Arreglo con los productos que tuvieron venta en el periodo seleccionado
								$productos = $this->Product->getCodes($fi, $ff, $tienda->tienda, $division->Code, $categoria->Code, $codigo->Code);
								foreach ($productos as $producto) {
									$id_producto = $id;
									array_push($data, $this->item($id_producto, 'product', $cat_gral['cat_gral'], $tienda->tienda, $division->Code, $categoria->Code, $codigo->Code, $producto->item_no, $producto->Description, $id_codigo));
									$id++;
									$ventas = $this->TransSalesEntry->getSalesProduct($fi, $ff, $tienda->tienda, $division->Code, $categoria->Code, $codigo->Code, $producto->item_no);
									foreach ($ventas as $venta) {
										foreach ($escalas as $escala) {
											if ($venta->item_no == $escala->item_no){
												if($data[$id_producto]['cat-gral'] == 'menudeo' && $venta->quantity <= $escala->menudeo){
													$data[$id_producto]['cantidad'] += $venta->quantity;
													$data[$id_producto]['importe'] += $venta->amount;
												} else if($data[$id_producto]['cat-gral'] == 'medio_mayoreo' && $venta->quantity >= $escala->mmi && $venta->quantity <= $escala->mms) {
													$data[$id_producto]['cantidad'] += $venta->quantity;
													$data[$id_producto]['importe'] += $venta->amount;
												} else if($data[$id_producto]['cat-gral'] == 'mayoreo' && $venta->quantity >= $escala->mayoreo){
													$data[$id_producto]['cantidad'] += $venta->quantity;
													$data[$id_producto]['importe'] += $venta->amount;
												}
											}
										}
									}
								} # Producto
							} # Codigo de Producto
						} # Categorias
					} # Divisiones
				} # Tiendas
			} # Categorias generales

			# Obtenemos la venta del producto no escalado agrupado por producto
			if($cantidadNoEscalado > 0){
				$id_cat_gral = count($data);
				array_push($data, $this->item($id_cat_gral, 'cat_gral', 'no_identificado', null, null, null, null, 'no_identificado', 'VENTA DE ESCALA NO DEFINIDA', null));
				$data[$id_cat_gral]['cantidad'] = $cantidadNoEscalado;
				$data[$id_cat_gral]['importe'] = $importeNoEscalado;

				$id = $id_cat_gral + 1;
				$ventas = $this->TransSalesEntry->getUndefinedSales($fi, $ff, $lastVersion);
				foreach ($ventas as $venta) {
					$item = $this->Item->getItem($venta->item_no);
					array_push($data, $this->item($id, 'no_identificado', 'no_identificado', null, null, null, null, $venta->item_no, "&nbsp;&nbsp;&nbsp;&nbsp;".$item[0]->Description, $id_cat_gral));
					$data[$id]['cantidad'] += $venta->quantity;
					$data[$id]['importe'] += $venta->amound;
					$id++;
				}
			}

		exit(json_encode(array('flag'=>true, 'msj'=>'CARGA EXITOSA', 'data'=>$data, 'lastId'=>count($data)-1)));
	}

	function item($id, $tipo, $cat_gral, $tienda, $division, $categoria, $product_code, $valor, $descripcion, $parentId = null){
		global $open;
		return array(
			'id' => $id,
			'tipo' => $tipo,
			'cat-gral' => $cat_gral,
			'tienda' => $tienda,
			'division' => $division,
			'categoria' => $categoria,
			'product_code' => $product_code,
			'valor' => $valor,
			'descripcion' => $descripcion,
			'cantidad' => 0,
			'importe' => 0,
			'open' => $this->open,
			'parent_id' => $parentId
		);
	}
}