<?php 
require_once 'model.php';
/**
 * @Continent Class
 */
class Continent extends Model
{	
	public $nom_continent = "";

	function __construct($db)
	{
		$this->db = $db;
		$this->table = "continent";
	}

	public function search($keywords,$from_record_num=0, $records_per_page=4)
	{
		$where_opt = [
			'nom_continent LIKE' => "%".$keywords."%"
		];
		$order_and_limit = "ORDER BY id_continent DESC LIMIT ".$from_record_num.", ".$records_per_page;

		$this->db->open();
		$stmt = $this->db->selectWhere($this->table, $where_opt, ['*'], $order_and_limit);
		$this->db->close();
       
		return $stmt;
	}
	
	public function setData(){     
		$this->data['nom_continent'] = $this->nom_continent;
	}
}