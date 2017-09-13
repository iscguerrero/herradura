<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Escalas extends Base_Controller{
	function __construct(){
		parent::__construct();
	}
	function index(){
		$this->load->view('escalas');
	}

	function latestNoEscala(){
		if(!$this->input->is_ajax_request()) show_404();
		$this->load->model('ItemEscala');
		$result = $this->ItemEscala->getLatestNoEscala();
		exit(json_encode(array('flag'=>true, 'data'=>$result)));
	}
	function noEscalasByRange(){
		if(!$this->input->is_ajax_request()) show_404();
		# Obtenemos los parametros de la peticion
			$fi = $this->str_to_date($this->input->post('fi'));
			$ff = $this->str_to_date($this->input->post('ff')) ;
		$this->load->model('ItemEscala');
		$result = $this->ItemEscala->noEscalasByRange($fi, $ff);
		exit(json_encode(array('flag'=>true, 'data'=>$result)));
	}
	function itemEscala(){
		if(!$this->input->is_ajax_request()) show_404();
		$version = $this->input->post('version');
		if($version==''){
			$this->load->model('ItemEscala');
			$result = $this->ItemEscala->getRows();
			exit(json_encode(array('flag'=>true, 'data'=>$result)));
		} else{
			$this->load->model('ItemEscala');
			$result = $this->ItemEscala->getVersionRows($version);
			exit(json_encode(array('flag'=>true, 'data'=>$result)));
		}
	}
	function createNewEscala(){
		if(!$this->input->is_ajax_request()) show_404();
		$data = $this->input->post('data');

		$this->load->model('Item');

		$this->load->model('ItemEscala');
		$result = $this->ItemEscala->getLatestNoEscala();
		$newVersion = ($result[0]->version) + 1;

		$this->db->trans_start();
		foreach ($data as $key => $item) {
			if($item['item_no'] != null && $item['item_no'] != ''){
				$result = $this->ItemEscala->getItemEscala($item['item_no']);
				if(count($result)==0){
					$itemExit = $this->Item->getItem($item['item_no']);
					if(count($itemExit) == 0) exit(json_encode(array('flag'=>false, 'msj'=>$item['item_no'] . " NO VALIDO")));
					else
						$estatusTrans = $this->ItemEscala->createItemEscala($item, $newVersion);
				} else{
					$estatusTrans = $this->ItemEscala->updateItemEscala($item, $newVersion);
				}
				if($estatusTrans == false) break;
			}
		}
		$this->db->trans_complete();

		if ($this->db->trans_status() === false || $estatusTrans = false) {
			exit(json_encode(array('flag'=>false, 'msj'=>'SE PRESENTO UN ERROR AL GUARDAR LOS CAMBIOS')));
		} else{
			exit(json_encode(array('flag'=>true, 'msj'=>'LOS CAMBIOS SE GUARDARON CON EXITO, GENERA EL REPORTE PARA VER LA NUEVA VERSION')));
		}
	}

	function dropVersion(){
		if(!$this->input->is_ajax_request()) show_404();
		$version = $this->input->post('version');

		$this->load->model('ItemEscala');
		$this->ItemEscala->dropItemEscala($version);

		exit(json_encode(array('flag'=>true, 'msj'=>'LA VERSION FUE BORRADA CON EXITO')));
	}

	function items(){
		$this->load->model('ItemEscala');
		$result = $this->ItemEscala->getLatestNoEscala();
		$LastVersion = $result[0]->version;

		$this->load->model('Item');
		$result = $this->Item->getItems($LastVersion);

		$this->load->library('excel');
		$objPHPExcel = new PHPExcel();

		$objPHPExcel->
				getProperties()
						->setCreator("La Herradura")
						->setLastModifiedBy("La Herradura User")
						->setTitle("Item_List")
						->setSubject("Reporte")
						->setDescription('Lista de items La Herradura')
						->setKeywords("herradura")
						->setCategory("reportes");

		$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue('A1', 'ITEM NO')
						->setCellValue('B1', 'DESCRIPCION')
						->setCellValue('C1', 'MENUDEO')
						->setCellValue('D1', 'MAYOREO');

		$x = 2;
		foreach ($result as $item) {
			$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue('A'.$x, $item->item_no)
							->setCellValue('B'.$x, $item->Description)
							->setCellValue('C'.$x, $item->menudeo)
							->setCellValue('D'.$x, $item->mayoreo);
			$x++;
		}

		$objPHPExcel->getActiveSheet()->setTitle('Items');
		$objPHPExcel->setActiveSheetIndex(0);

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="items.xls"');
		header('Cache-Control: max-age=0');

		$objWriter=PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}
}