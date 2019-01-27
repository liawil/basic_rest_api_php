<?php 
/**
 * @Model Class
 */
class Model
{	
    protected $db;
    protected $table = "";
    public $id= "";

	//Tableau pour les données à enregistrer
	protected $data = array();
	//Tableau pour les données à modifier
	public $update_values = array();

	function __construct($db)
	{
		$this->db = $db;
	}

	/**
	 * Function i.e au getAll(). 
	 * Elle affiche toute la liste de tous les enregistement de la table 
	 */
	public function read()
	{
		$this->db->open();
		$order = "ORDER BY id_".$this->table;
		$stmt = $this->db->select($this->table,['*'],$order);
		$this->db->close();
       
		return $stmt;
	}

	/**
	 * Function i.e au read(). 
	 * Elle affiche toute la liste de données avec un système de pagination
	 */
	public function readPerPage($from_record_num=0, $records_per_page=4)
	{
		$this->db->open();
		$order_and_limit = "ORDER BY id_".$this->table." DESC LIMIT ".$from_record_num.", ".$records_per_page;
		$stmt = $this->db->select($this->table,['*'],$order_and_limit);
		$this->db->close();
       
		return $stmt;
	}

	/**
	 * Elle affiche un enregistrement donnée
	 */
	public function show()
	{	
		$where_opt = [
			'id_'.$this->table.' = '=>$this->id
		];
		$this->db->open();
		$stmt = $this->db->selectWhere($this->table, $where_opt);
		$this->db->close();
       
		return $stmt;
	}

	/**
	 * Elle crée un nouvel enregistrement 
	 */
	public function create()
	{
		$this->db->open();
		$stmt = $this->db->insert($this->table, $this->data);
		$this->db->close();
       
		return $stmt;
	}

	/**
	 * Elle met à jour un enregistrment
	 */
	public function update()
	{
		$this->db->open();
		$stmt = $this->db->update($this->table, $this->id, $this->update_values);
		$this->db->close();
       
		return $stmt;
	}

	/**
	 *  Elle supprime à jour un enregistrment
	 */
	public function delete()
	{
		$this->db->open();
		$stmt = $this->db->delete($this->table, $this->id);
		$this->db->close();
       
		return $stmt;
	}

	/*public function search($keywords,$from_record_num=0, $records_per_page=20)
	{
		$where_opt = [
			'nom_continent LIKE' => "%".$keywords."%"
		];
		$order_and_limit = "ORDER BY id_".$this->table." DESC LIMIT ".$from_record_num.", ".$records_per_page;

		$this->db->open();
		$stmt = $this->db->selectWhere($this->table, $where_opt, ['*'], $order_and_limit);
		$this->db->close();
       
		return $stmt;
	}*/

	// utilisé pour la pagination
    public function count(){
     
        $this->db->open();
        $stmt = $this->db->select($this->table,['COUNT(*) as total_rows']);
     	$this->db->close();
     	$data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data['total_rows'];
    }
}