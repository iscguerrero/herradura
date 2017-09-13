<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class TransSalesEntry extends CI_Model{
	public function __construct(){
		parent::__construct();
	}
	public function getAllSales($fi, $ff){
		$query = $this->db->query("SELECT [Item No_] AS item_no, -[Total Rounded Amt_] AS amound, -[Quantity] AS quantity FROM [REPORTES].[dbo].[LA HERRADURA\$Trans_ Sales Entry] WHERE [Date] BETWEEN '$fi 00:00:00:000' AND '$ff 23:59:59:999'");
		return $query->result();
	}
	public function getUndefinedSales($fi, $ff, $version){
		$query = $this->db->query("SELECT [Item No_] AS item_no, SUM(-[Total Rounded Amt_]) AS amound, SUM(-[Quantity]) AS quantity FROM [REPORTES].[dbo].[LA HERRADURA\$Trans_ Sales Entry] WHERE [Date] BETWEEN '$fi 00:00:00:000' AND '$ff 23:59:59:999' AND [Item No_] NOT IN ( SELECT [Item No_] FROM [REPORTES].[dbo].[Item_Escala_Version] WHERE Version = $version )  GROUP BY [Item No_]");
		return $query->result();
	}
	public function getStores($fi, $ff){
		$query = $this->db->query("SELECT [No_] AS tienda, Name AS Description FROM [REPORTES].[dbo].[LA HERRADURA\$Store]");
		return $query->result();
	}
	public function getSalesStore($fi, $ff, $store){
		$query = $this->db->query("SELECT [Item No_] AS item_no, -[Total Rounded Amt_] AS amount, -[Quantity] AS quantity FROM [REPORTES].[dbo].[LA HERRADURA\$Trans_ Sales Entry] WHERE [Date] BETWEEN '$fi 00:00:00:000' AND '$ff 23:59:59:999' AND [Store No_] = '$store'");
		return $query->result();
	}
	public function getSalesDivision($fi, $ff, $store, $divisionCode){
		$query = $this->db->query("SELECT [Item No_] AS item_no, -[Total Rounded Amt_] AS amount, -[Quantity] AS quantity FROM [REPORTES].[dbo].[LA HERRADURA\$Trans_ Sales Entry] AS sales INNER JOIN [REPORTES].[dbo].[LA HERRADURA\$Item] item ON sales.[Item No_] = item.[No_] WHERE [Date] BETWEEN '$fi 00:00:00:000' AND '$ff 23:59:59:999' AND [Store No_] = '$store' AND item.[Division Code] = '$divisionCode'");
		return $query->result();
	}
	public function getSalesCategory($fi, $ff, $store, $divisionCode, $itemCategory){
		$query = $this->db->query("SELECT [Item No_] AS item_no, -[Total Rounded Amt_] AS amount, -[Quantity] AS quantity FROM [REPORTES].[dbo].[LA HERRADURA\$Trans_ Sales Entry] AS sales INNER JOIN [REPORTES].[dbo].[LA HERRADURA\$Item] item ON sales.[Item No_] = item.[No_] WHERE [Date] BETWEEN '$fi 00:00:00:000' AND '$ff 23:59:59:999' AND [Store No_] = '$store' AND item.[Division Code] = '$divisionCode' AND item.[Item Category Code] = '$itemCategory'");
		return $query->result();
	}
	public function getSalesProductCode($fi, $ff, $store, $divisionCode, $itemCategory, $productCode){
		$query = $this->db->query("SELECT [Item No_] AS item_no, -[Total Rounded Amt_] AS amount, -[Quantity] AS quantity FROM [REPORTES].[dbo].[LA HERRADURA\$Trans_ Sales Entry] AS sales INNER JOIN [REPORTES].[dbo].[LA HERRADURA\$Item] item ON sales.[Item No_] = item.[No_] WHERE [Date] BETWEEN '$fi 00:00:00:000' AND '$ff 23:59:59:999' AND [Store No_] = '$store' AND item.[Division Code] = '$divisionCode' AND item.[Item Category Code] = '$itemCategory' AND sales.[Product Group Code] = '$productCode'");
		return $query->result();
	}
	public function getSalesProduct($fi, $ff, $store, $divisionCode, $itemCategory, $productCode, $item_no){
		$query = $this->db->query("SELECT [Item No_] AS item_no, -[Total Rounded Amt_] AS amount, -[Quantity] AS quantity FROM [REPORTES].[dbo].[LA HERRADURA\$Trans_ Sales Entry] AS sales INNER JOIN [REPORTES].[dbo].[LA HERRADURA\$Item] item ON sales.[Item No_] = item.[No_] WHERE [Date] BETWEEN '$fi 00:00:00:000' AND '$ff 23:59:59:999' AND [Store No_] = '$store' AND item.[Division Code] = '$divisionCode' AND item.[Item Category Code] = '$itemCategory' AND sales.[Product Group Code] = '$productCode' AND sales.[Item No_] = '$item_no'");
		return $query->result();
	}
}