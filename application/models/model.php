<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Model extends CI_Model {

	public function __construct(){
		$this->load->database();		
	}

	public function zone_exists($zone_id){
		$zone_id = mysql_real_escape_string($zone_id);
		$sql = 
			"SELECT *
			 FROM zones
			 WHERE id = $zone_id
			";
		
		$query = $this->db->query($sql);

		if ($query->num_rows() === 1){
		  	return TRUE;
		}
		else{
			return FALSE;
		}
	}

	public function read_zones(){
		
		$sql = 
			"SELECT *
			 FROM zones
			 ORDER BY id DESC
			";
		
		$query = $this->db->query($sql);
		return $query->result_array();

	}

	public function read_zone_by_id($zone_id){
		$zone_id = mysql_real_escape_string($zone_id);
		$sql = 
			"SELECT *
			 FROM zones
			 WHERE id = $zone_id
			";
		
		$query = $this->db->query($sql);
		return $query->result_array();

	}

	public function update_zone_name($zone_id, $zone_name){
		$zone_id = mysql_real_escape_string($zone_id);
		$zone_name = mysql_real_escape_string($zone_name);

		$sql = 
			"UPDATE zones
			 SET name = '$zone_name'
			 WHERE id = $zone_id
			";
		
		$query = $this->db->query($sql);

	}

	public function write_zone(){
		
		$sql = 
			"INSERT INTO zones (name)
			 VALUES ('')
			";

		$this->db->query($sql);
		
	}

	public function create_tables(){

		$sql1 = 
			"CREATE TABLE IF NOT EXISTS zones(
				id int NOT NULL AUTO_INCREMENT,
			 	name text,
			 	PRIMARY KEY (id)
			 )
			";

		$sql2 = 
			"CREATE TABLE IF NOT EXISTS snippets(
				id int NOT NULL AUTO_INCREMENT,
			 	snippet text,
			 	extension varchar(4),
			 	type varchar(7),
			 	zoneid int,
			 	PRIMARY KEY (id)
			 )
			";

		$sql3 = 
			"CREATE TABLE IF NOT EXISTS comments(
				id int NOT NULL AUTO_INCREMENT,
				snippetid int,
			 	comment text,
			 	PRIMARY KEY (id)
			 )
			";

		$this->db->query($sql1);	
		$this->db->query($sql2);
		$this->db->query($sql3);
	}

	public function write_snippet($snippet,$extension,$type,$zoneid){
		$snippet = mysql_real_escape_string($snippet);
		$extension = mysql_real_escape_string($extension);

		$sql = 
			"INSERT INTO snippets (snippet,extension,type,zoneid)
			 VALUES ('$snippet','$extension','$type',$zoneid)
			";

		$this->db->query($sql);
	}

	public function write_comment($comment,$id){
		$comment = mysql_real_escape_string($comment);

		$sql = 
			"INSERT INTO comments (snippetid,comment)
			 VALUES ($id,'$comment')
			";

		$this->db->query($sql);
	}

	public function delete_snippet($value){
		$value = mysql_real_escape_string($value);

		$sql = 
			"DELETE FROM snippets
			 WHERE id = $value
			";

		$this->db->query($sql);
	}

	public function delete_comments($value){
		$value = mysql_real_escape_string($value);

		$sql = 
			"DELETE FROM comments
			 WHERE snippetid = $value
			";

		$this->db->query($sql);
	}

	public function read_snippet($zoneid){
		$sql = 
			"SELECT *
			 FROM snippets
			 WHERE zoneid = $zoneid
			 ORDER BY id DESC
			";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function read_snippet_by_id($id){
		$sql = 
			"SELECT *
			 FROM snippets
			 WHERE id = $id
			";
		$query = $this->db->query($sql);
		return $query->result_array();
	}	

	public function read_comments_by_id($id){
		$sql = 
			"SELECT *
			 FROM comments
			 WHERE snippetid = $id
			";
		$query = $this->db->query($sql);
		return $query->result_array();
	}	

}
