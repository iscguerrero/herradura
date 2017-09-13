<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class ProductGroupCode extends CI_Model{
	public function __construct(){
		parent::__construct();
	}
	public function getCodes($fi, $ff, $store, $division, $category){
		$query = $this->db->query("SELECT sales.[Product Group Code] AS Code FROM [REPORTES].[dbo].[LA HERRADURA\$Trans_ Sales Entry] AS sales INNER JOIN [REPORTES].[dbo].[LA HERRADURA\$Item] item ON sales.[Item No_] = item.[No_] WHERE [Date] BETWEEN '$fi 00:00:00:000' AND '$ff 23:59:59:999' AND [Store No_] = '$store' AND item.[Division Code] = '$division' AND sales.[Item Category Code] = '$category' GROUP BY sales.[Product Group Code]");
		return $query->result();
	}
}