<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Item extends CI_Model{
	public function __construct(){
		parent::__construct();
	}
	public function getItem($item_no){
		$query = $this->db->query("SELECT TOP(1) [No_], Description FROM [REPORTES].[dbo].[LA HERRADURA\$Item] WHERE [No_] = '$item_no' ");
		return $query->result();
	}
	public function getItems($version){
		$query = $this->db->query("SELECT [No_] AS item_no, item.Description, Menudeo AS menudeo, MedioMayoreoInferior AS mmi, MedioMayoreoSuperior AS mms, Mayoreo AS mayoreo FROM [REPORTES].[dbo].[LA HERRADURA\$Item] item LEFT JOIN [REPORTES].[dbo].[Item_Escala_Version] escala ON item.[No_] = escala.[Item No_] AND [Version] = $version ORDER BY [No_] ASC");
		return $query->result();
	}
}