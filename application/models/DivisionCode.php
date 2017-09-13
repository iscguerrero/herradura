<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class DivisionCode extends CI_Model{
	public function __construct(){
		parent::__construct();
	}
	public function getCodes($fi, $ff){
		$query = $this->db->query("SELECT Code, Description FROM [REPORTES].[dbo].[LA HERRADURA\$Division]");
		return $query->result();
	}
}