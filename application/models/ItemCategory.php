<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class ItemCategory extends CI_Model{
	public function __construct(){
		parent::__construct();
	}
	public function getCodes($divisionCode){
		$query = $this->db->query("SELECT Code, Description FROM [REPORTES].[dbo].[LA HERRADURA\$Item Category] WHERE [Division Code] = '$divisionCode'");
		return $query->result();
	}
}