<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class ItemEscala extends CI_Model{
	public function __construct(){
		parent::__construct();
	}
	public function getRows(){
		$query = $this->db->query("SELECT [Item No_] AS item_no, Menudeo AS menudeo, MedioMayoreoInferior AS mmi, MedioMayoreoSuperior AS mms, Mayoreo AS mayoreo FROM [REPORTES].[dbo].[Item_Escala]");
		return $query->result();
	}
	public function getLatestNoEscala(){
		$query = $this->db->query("SELECT TOP(1) [Version] AS version FROM [REPORTES].[dbo].[Item_Escala_Version] ORDER BY [Version] DESC");
		return $query->result();
	}
	public function noEscalasByRange($fi, $ff){
		$query = $this->db->query("SELECT DISTINCT [Version] AS version, CONVERT(VARCHAR, [FechaActualizacion], 106) AS fecha FROM [REPORTES].[dbo].[Item_Escala_Version] WHERE FechaActualizacion BETWEEN '$fi' AND '$ff' ORDER BY [Version] DESC");
		return $query->result();
	}
	public function getVersionRows($version){
		$query = $this->db->query("SELECT [Item No_] AS item_no, item.Description, Menudeo AS menudeo, MedioMayoreoInferior AS mmi, MedioMayoreoSuperior AS mms, Mayoreo AS mayoreo FROM [REPORTES].[dbo].[Item_Escala_Version] escala INNER JOIN [REPORTES].[dbo].[LA HERRADURA\$Item] item ON escala.[Item No_] = item.[No_] WHERE [Version] = $version");
		return $query->result();
	}
	public function updateItemEscala($item, $version){
		$item_no = $item['item_no'];
		$menudeo = $item['menudeo'];
		$mmi = $item['menudeo'] + 1;
		$mms = $item['mayoreo'] - 1;
		$mayoreo = $item['mayoreo'];
		try {
			$query = $this->db->query("UPDATE TOP(1) [REPORTES].[dbo].[Item_Escala] SET Menudeo = '$menudeo', MedioMayoreoInferior = '$mmi', MedioMayoreoSuperior = '$mms', Mayoreo = '$mayoreo', FechaActualizacion = GETDATE(), HoraActualizacion = (CONVERT(time, GETDATE())) WHERE [Item No_] = '$item_no'");
			$query = $this->db->query("INSERT INTO [REPORTES].[dbo].[Item_Escala_Version] VALUES('$item_no', '$menudeo', '$mmi', '$mms', '$mayoreo', $version, GETDATE(), (CONVERT(time, GETDATE())), null)");
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	public function createItemEscala($item, $version){
		$item_no = $item['item_no'];
		$menudeo = $item['menudeo'];
		$mmi = $item['menudeo'] + 1;
		$mms = $item['mayoreo'] - 1;
		$mayoreo = $item['mayoreo'];
		try {
			$query = $this->db->query("INSERT INTO [REPORTES].[dbo].[Item_Escala] VALUES( '$item_no', '$menudeo', '$mmi', '$mms', '$mayoreo', GETDATE(), (CONVERT(time, GETDATE())) )");
			$query = $this->db->query("INSERT INTO [REPORTES].[dbo].[Item_Escala_Version] VALUES('$item_no', '$menudeo', '$mmi', '$mms', '$mayoreo', $version, GETDATE(), (CONVERT(time, GETDATE())), null)");
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	public function getItemEscala($item_no){
		$query = $this->db->query("SELECT [Item No_] AS item_no, Menudeo AS menudeo, MedioMayoreoInferior AS mmi, MedioMayoreoSuperior AS mms, Mayoreo AS mayoreo FROM [REPORTES].[dbo].[Item_Escala] WHERE [Item No_] = '$item_no'");
		return $query->result();
	}
	public function dropItemEscala($version){
		$query = $this->db->query("DELETE FROM [REPORTES].[dbo].[Item_Escala_Version] WHERE [Version] = $version");
		return true;
	}
}