<?php 

//namespace Models;

require_once '../shared/helpers.php';
/**
 * @Database class
 */
class Database
{

	// specify your own database credentials
    private $host = "localhost";
    private $db_name = "your_db_name";
    private $username = "your_db_username";
    private $password = "your_db_password";
    public $bdd;

	function __construct()
	{
		
	}

	public function open(){
		$this->bdd = null;
 
        try{
            $this->bdd = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->bdd->exec("set names utf8");	
            $this->bdd->query("SET lc_time_names = 'fr_FR'");

        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }		
		$this->bdd->query("SET lc_time_names = 'fr_FR'");
		return $this->bdd;
	}

	public function select($table, $selectOptions = array(), $orderOrLimit=""){
		
		$select_opt = selectOptions($selectOptions);
		$query = "SELECT ".$select_opt."\nFROM ".$table;
		$query .= ($orderOrLimit != "")? "\n".$orderOrLimit : "";

		$stmt = $this->bdd->query($query);

		return $stmt;
	}

	public function selectWhere($table, $whereOptions, $selectOptions = array(), $orderOrLimit=""){
		
		$select_opt = selectOptions($selectOptions);
		if (is_array($table)) {
			$table = tableList($table);
		}
		$where_opt = whereOptionsNew($whereOptions);

		$query = "SELECT ".$select_opt."\nFROM ".$table."\nWHERE ".$where_opt;
		$query .= ($orderOrLimit != "")? "\n".$orderOrLimit : "";

		$stmt = $this->bdd->query($query);
		
		return $stmt;
	}

	public function selectJoin($table, $joinOptions, $selectOptions = array(), $orderOrLimit = ""){

		$select_opt = selectOptions($selectOptions);
		$join_opt = joinOptions($joinOptions);

		$query = "SELECT ".$select_opt."\nFROM ".$table.$join_opt;
		$query .= ($orderOrLimit != "")? "\n".$orderOrLimit : "";

		$stmt = $this->bdd->query($query);
		
		return $stmt;
	}

	public function selectJoinWhere($table, $joinOptions, $whereOptions, $selectOptions = array(), $orderOrLimit = ""){

		$select_opt = selectOptions($selectOptions);
		$join_opt = joinOptions($joinOptions);
		$where_opt = whereOptionsNew($whereOptions);

		$query = "SELECT ".$select_opt."\nFROM ".$table.
				$join_opt."\nWHERE ".$where_opt;
		$query .= ($orderOrLimit != "")? "\n".$orderOrLimit : "";

		$stmt = $this->bdd->query($query);
		
		return $stmt;
	}

	public function insert($table, $data){
		$data_list = insertOptions($data);

		$query = "INSERT INTO ".$table." SET ".$data_list;

		try {
			$stmt = $this->bdd->query($query);
		} catch (Exception $e) {
			return $stmt;
		}
		return $stmt;
	}

	public function update($table, $id, $data){
		$data_list = insertOptions($data);
		if (is_array($id)){
			$where_opt = whereOptions($id,'=');
			$query = "UPDATE ".$table."\n SET ".$data_list."\nWHERE ".$where_opt;
		}else{
			$query = "UPDATE ".$table."\n SET ".$data_list."\nWHERE id_".$table." = '".$id."'";
		}

		try {
			$query = $this->bdd->query($query);
		} catch (Exception $e) {
			return $query;
		}
		return $query;
	}

	public function existTable($table){
		
		$query = "SHOW TABLES LIKE '%".$table."%'";

		try {
			$stmt = $this->bdd->query($query);
		} catch (Exception $e) {
			die("Error: ".$e->getMessage());
			return false;
		}
		if ($stmt->rowCount()) return true;
		else return false;
	}

	public function delete($table, $id){
		if (is_array($id)){
			$where_opt = whereOptions($id,'=');
			$query = "DELETE FROM ".$table."\nWHERE ".$where_opt;
		}else{
			$query = "DELETE FROM ".$table."\nWHERE id_".$table." = '".$id."'";
		}

		try {
			$query = $this->bdd->query($query);
		} catch (Exception $e) {
			return false;
		}
		return true;
	}

	/**
	 * @Params val: int (O or 1)
	 * return void
	 */
	public function setAutoCommit($val){ 
		$this->bdd->query("SET autocommit = ".$val);
	}

	public function startTransaction(){
		$this->bdd->query("START transaction");
	}

	public function commit(){
		$this->bdd->query("commit");
	}

	public function rollback(){
		$this->bdd->query("rollback");
	}

	public function close(){
		$this->bdd = null;
	}
}
