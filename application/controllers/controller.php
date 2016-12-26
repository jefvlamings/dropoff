<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Controller extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->library('session');
		$this->load->model('Model');

		$this->prepare_database();
	}

	// Make a database if it doesn't already exist
	public function prepare_database(){
		$this->Model->create_tables();
	}

	// submit snippet to database
	public function submit(){
		$post = htmlspecialchars($_POST["message"]);
		$zoneid = $_POST["zoneid"];

		// get file extension
		$extension = $this->file_extension($post);

		// store url if message is an image
		if($this->is_an_image($extension)){
			$image_url = $this->store_file_from_url($post,$extension);
			$message = $image_url;
			$type = 'image';
		}
		else{
			// it's a message
			$message = $post;
			$type = 'message';
		}

		// put snippet in database
		$this->Model->write_snippet($message,$extension,$type,$zoneid);

	}

	public function submit_comment(){
		$comment_id = htmlspecialchars($_POST["id"]);
		$comment = htmlspecialchars($_POST["comment"]);

		// put comment in database
		$this->Model->write_comment($comment,$comment_id);
	}

	// delete snippet from database
	public function delete(){
		$id = htmlspecialchars($_POST["id"]);

		// delete possible file attached to snippet # $id
		$this->delete_file($id);

		// delete actual snippet
		$this->Model->delete_snippet($id);

		// delete comments
		$this->Model->delete_comments($id);

	}

	// delete file attached to snippet
	public function delete_file($id){
		$snippet = $this->Model->read_snippet_by_id($id);

		$type = $snippet[0]['type'];
		
		if($type == 'image'){
			$absolute_path = $snippet[0]['snippet'];
			$relative_path = end(explode('dropoff/', $absolute_path)); //use relative path to delete file
			unlink($relative_path);
		}
	}

	// read snippets from database
	public function read(){

		$zoneid = $_GET["zoneid"];

		$snippet_array = $this->Model->read_snippet($zoneid);
		
		// add comments array to each snippet array
		for($i=0;$i<sizeof($snippet_array);$i++){
			$snippetid = $snippet_array[$i]['id'];
			$comments = $this->Model->read_comments_by_id($snippetid);

			if(sizeof($comments) == 0){
				$comments[0] = array('id'=>'x','snippetid'=>'','comment'=>'');	
			}
			$newarray = array_push($snippet_array[$i], $comments);

		}

		$output = json_encode($snippet_array);
		echo $output;
	}

	// return the extension of possible files
	public function file_extension($message){
		return pathinfo($message, PATHINFO_EXTENSION);
	}

	// return true if extension stands for image
	public function is_an_image($extension){
		if($extension == "jpg" or $extension =="gif" or $extension == "png" or $extension == "jpeg"){
			return TRUE;
		}
		else{
			return FALSE;
		}
	}

	// download image from url
	public function store_file_from_url($url,$extension){
		$ch = curl_init();
		$timeout = 5;
		
		$relative_path = $this->build_filename($url,$extension);

		// open file (but first make a new one)
		$fp = fopen($relative_path, 'w') or die('Cannot open file:  '. $relative_path);

		// go to specified url
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);

		// store contents of url-file in local file
		curl_setopt($ch, CURLOPT_FILE, $fp);

		//execute CURL command
		curl_exec($ch);
		curl_close($ch);

		// close file
		fclose($fp);

		// return local url of stored file
		$absolute_path = base_url() . $relative_path;  //store absolute url instead of relative
		return $absolute_path;
	}

	// build new filename from original 
	public function build_filename($url,$extension){

		$filename = end(explode('/', $url));
		$filename = reset(explode('.',$filename));
		$relative_path = 'files/' . $filename . "." . $extension;

	    $counter = 1;
	    while (file_exists($relative_path)) {
	           $relative_path = 'files/' . $filename .'_'. $counter . '.' . $extension;
	           $counter++;
	     }

	     return $relative_path;
	 }

	// create new zone
	public function create_new_zone(){

		$this->Model->write_zone();

		$all_zones = $this->Model->read_zones();
		$new_id = $all_zones[0]["id"];

		$new_url = site_url('controller/zone/') . $new_id;
		$url="$new_url"; 

		header ("Location: $url"); 
	}

	// create new zone
	public function update_zone_name(){

		$zone_id = $_POST["zoneid"];
		$zone_name = $_POST["zonename"];

		$this->Model->update_zone_name($zone_id,$zone_name);

	}

	// Send data to view 
	public function index(){

		$data["title"] = "Dropoff, easy sharing";
		$data["allid"] = $this->Model->read_zones();

		$this->load->view('header',$data);
		$this->load->view('index',$data);	
		$this->load->view('footer');	
	
	}

	// Send data to zone 
	public function zone(){

		$zone_id = $this->uri->segment(3,0);
		$zone_content = $this->Model->read_zone_by_id($zone_id);

		if($this->Model->zone_exists($zone_id) === TRUE){
			$data["title"] = "Dropoff -  #$zone_id";
			$data["zoneid"] = $zone_content[0]["id"];
			$data["zonename"] = $zone_content[0]["name"];

			$this->load->view('header',$data);
			$this->load->view('zone',$data);	
			$this->load->view('footer');	

		}
		else{
			$new_url = site_url();
			$url="$new_url"; 
			header ("Location: $url"); 
		}	

	}

}


