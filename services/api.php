<?php
 	require_once("Rest.inc.php");
	
	class API extends REST {
	
		public $data = "";
		
		const DB_SERVER = "127.0.0.1";
		const DB_USER = "root";
		const DB_PASSWORD = "admin";
		const DB = "mawaqit_db";

		private $db = NULL;
		private $mysqli = NULL;
		public function __construct(){
			parent::__construct();				// Init parent contructor
			$this->dbConnect();					// Initiate Database connection
		}
		
		/*
		 *  Connect to Database
		*/
		private function dbConnect(){
			$this->mysqli = new mysqli(self::DB_SERVER, self::DB_USER, self::DB_PASSWORD, self::DB);
		}
		
		/*
		 * Dynmically call the method based on the query string
		 */
		public function processApi(){
			$func = strtolower(trim(str_replace("/","",$_REQUEST['x'])));
			if((int)method_exists($this,$func) > 0)
				$this->$func();
			else
				$this->response('',404); // If the method not exist with in this class "Page not found".
		}
				
		private function login(){
                        
                    if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			$username = $this->_request['username'];		
			$password = $this->_request['pass'];
			
			if(!empty($username) and !empty($password)){
				
				
					$query="SELECT username, password, active FROM masjid_admin WHERE username = '$username' AND password = '$password' AND active = 1 LIMIT 1";
					$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

					if($r->num_rows > 0) {
						$result = $r->fetch_assoc();	
						// If success everythig is good send header as "OK" and user details
						$this->response($this->json(array('msg' => 'success', 'result' => $result)), 200);
					}
					else{
					$this->response($this->json(array('msg' => 'Invalid Credentials Entered or Your Account is not activated.')), 200);	// If no records "No Content" status
					}
					$this->response('Error',204);
			}
			
			$error = array('status' => "Failed", "msg" => "Invalid Email address or Password");
			$this->response($this->json($error), 400);
		}
		
		private function getMasjidInfo(){
		    if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			$username = $this->_request['username'];
			
			if(!empty($username)){
			    $query="SELECT masajids.name, masajids.country, masajids.state, masajids.city, masajids.street, masajids.zipcode, masajids.website FROM masajids JOIN masjid_admin ON (masajids.id = masjid_admin.mid) WHERE masjid_admin.username = '$username' LIMIT 1";
					$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

					if($r->num_rows > 0) {
						$result = $r->fetch_assoc();	
						// If success everythig is good send header as "OK" and user details
						$this->response($this->json(array('msg' => 'success', 'result' => $result)), 200);
					}
			}
		}
		    
		
		
		private function masjid_lookup(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			$country = $_POST['country'];
            $state = $_POST['state'];
            $city = $_POST['city'];
           
            $zipcode = $_POST['zipcode'];
			
		
				
				
					$query="SELECT name, id FROM masajids WHERE (country = '$country' AND zipcode = $zipcode AND zipcode <> 0) OR (country = '$country' AND state = '$state' AND city = '$city')";
					$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
                        
                    $result = array();
					if($r->num_rows > 0) {
						while($row = $r->fetch_assoc()){
					$result[] = $row;
				}	
						// If success everythig is good send header as "OK" and user details
						$this->response($this->json(array('msg' => 'success', 'result' => $result)), 200);
					}
					else{
					$this->response($this->json(array('msg' => 'No Masjids Found.')), 200);	// If no records "No Content" status
					}
					$this->response('Error',204);
			
			
			$error = array('status' => "Failed", "msg" => "Invalid Email address or Password");
			$this->response($this->json($error), 400);
		}
		
		private function getTransactions(){	
			if($this->get_request_method() != "GET"){
				$this->response('');
			}
			$mtd = $this->_request['mtd'];
			$to = $this->_request['to'];
			$query="SELECT sum(amount) category_total,category FROM transactions where tranc_date >= '$mtd' AND tranc_date <= '$to' Group By category";
			$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
            $result = array();
			if($r->num_rows > 0){
				
				while($row = $r->fetch_assoc()){
					$result[] = $row;
				}
				 //print_r($result);
				$this->response($this->json($result)); // send 
			}
			$this->response($this->json($result));	// If no records "No Content" status
		}
		private function getTransaction(){	
			if($this->get_request_method() != "GET"){
				$this->response('');
			}
			$mtd = $this->_request['mtd'];
			$to = $this->_request['to'];
			$query="SELECT * FROM transactions where tranc_date >= '$mtd' AND tranc_date <= '$to' ORDER BY id";
			$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
            $result = array();
			if($r->num_rows > 0){
				
				while($row = $r->fetch_assoc()){
					$result[] = $row;
				}
				 //print_r($result);
				$this->response($this->json($result)); // send 
			}
			$this->response($this->json($result));	// If no records "No Content" status
		}
		
		private function getItems(){	
			if($this->get_request_method() != "GET"){
				$this->response('');
			}
			$mtd = $this->_request['mtd'];
			$query="SELECT * FROM items where tranc_date >= '$mtd' ORDER BY id";
			$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
            $result = array();
			if($r->num_rows > 0){
				
				while($row = $r->fetch_assoc()){
					$result[] = $row;
				}
				 //print_r($result);
				$this->response($this->json($result)); // send 
			}
			$this->response($this->json($result));	// If no records "No Content" status
		}
		
		private function getTasks(){	
			if($this->get_request_method() != "GET"){
				$this->response('');
			}
			$mtd = $this->_request['mtd'];
			$query="SELECT * FROM tasks where date_add >= '$mtd' ORDER BY id";
			$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
            $result = array();
			if($r->num_rows > 0){
				
				while($row = $r->fetch_assoc()){
					$result[] = $row;
				}
				 //print_r($result);
				$this->response($this->json($result)); // send 
			}
			$this->response($this->json($result));	// If no records "No Content" status
		}
		
		private function customer(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$id = (int)$this->_request['id'];
			if($id > 0){	
				$query="SELECT distinct c.customerNumber, c.customerName, c.email, c.address, c.city, c.state, c.postalCode, c.country FROM angularcode_customers c where c.customerNumber=$id";
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				if($r->num_rows > 0) {
					$result = $r->fetch_assoc();	
					$this->response($this->json($result), 200); // send user details
				}
			}
			$this->response('',204);	// If no records "No Content" status
		}
		
		private function signup(){
			if($this->get_request_method() != "POST"){
				$this->response('');
			}
            $username = $_POST['username'];
            $password = $_POST['pass'];
            $name = $_POST['name'];
            $phone_no = $_POST['phone_no'];
            $masjid_no = $_POST['masjid_no'];
            $masjid_name = $_POST['masjid_name'];
            $country = $_POST['country'];
            $state = $_POST['state'];
            $city = $_POST['city'];
            $street = $_POST['street'];
            $zipcode = $_POST['zipcode'];   
            //echo $masjid_name;
            // 'username' : username, 'pass' : password, 'name' : name, 'phone_no' : phone_no, 'masjid_no' : masjid_no, 'masjid_name' : masjid_name, 'country' : country, 'state' : state, 'city' : city, 'street' : street, 'zipcode' : zipcode};
            if($masjid_no == '')
            $masjid_no = 0;
            
           $exist = "SELECT username FROM masjid_admin WHERE username = '$username' LIMIT 1";
					$re = $this->mysqli->query($exist) or die($this->mysqli->error.__LINE__);

					if($re->num_rows > 0) {
						//$result = $r->fetch_assoc();	
						// If success everythig is good send header as "OK" and user details
						$this->response($this->json(array('msg' => 'Username Already Taken')), 200);
					}
					else{
           $query = "INSERT INTO masjid_admin (username,password,name,phone_no,masjid_no,masjid_name,masjid_country,masjid_state,masjid_city,masjid_street,masjid_zip) VALUES('$username','$password','$name',$phone_no,$masjid_no,'$masjid_name','$country','$state','$city','$street',$zipcode)";
			
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				$success = "Account Created Successfully.";//array('status' => "Success", "msg" => "Transaction Created Successfully.", "data" => $req_values);
				 $this->response($this->json(array('msg' => 'success', 'result' => $success)), 200);
			
					}
					$this->response('Error', 204);
					//"No Content" status
		}
		
		private function insertItem(){
			if($this->get_request_method() != "POST"){
				$this->response('');
			}
			$useradd = 'Ateef';
            $item = $_POST['item'];
            $desc = $_POST['desc'];
            $qty = $_POST['qty'];
            $tranc_date = $_POST['tranc_date'];
            $category = $_POST['category'];
           
           $query = "INSERT INTO items (item,qty,tranc_date,status,category,user_add,description) VALUES('$item',$qty,'$tranc_date','Not Yet','$category','$useradd','$desc')";
			if(!empty($item)){
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				$success = "Item added successfully.";//array('status' => "Success", "msg" => "Transaction Created Successfully.", "data" => $req_values);
				 $this->response($success);
			}else
				$this->response('No valid item entered.');	//"No Content" status
		}
		
		private function insertTask(){
			if($this->get_request_method() != "POST"){
				$this->response('');
			}
			$useradd = 'Ateef';
            $task = $_POST['task'];
            $prior = $_POST['prior'];
            $date_comp = $_POST['date_comp'];
            $date_add = $_POST['date_add'];
            
           
           $query = "INSERT INTO tasks (task,user,date_add,date_comp,priority,status) VALUES('$task','$useradd','$date_add','$date_comp','$prior','Not Yet')";
			if(!empty($task)){
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				$success = "Task added successfully.";//array('status' => "Success", "msg" => "Transaction Created Successfully.", "data" => $req_values);
				 $this->response($success);
			}else
				$this->response('No valid task entered.');	//"No Content" status
		}
		
		private function updateCustomer(){
			if($this->get_request_method() != "POST"){
				$this->response('Error');
			}
			$customer = json_decode(file_get_contents("php://input"),true);
			$id = (int)$customer['id'];
			$column_names = array('customerName', 'email', 'city', 'address', 'country');
			$keys = array_keys($customer['customer']);
			$columns = '';
			$values = '';
			foreach($column_names as $desired_key){ // Check the customer received. If key does not exist, insert blank into the array.
			   if(!in_array($desired_key, $keys)) {
			   		$$desired_key = '';
				}else{
					$$desired_key = $customer['customer'][$desired_key];
				}
				$columns = $columns.$desired_key."='".$$desired_key."',";
			}
			$query = "UPDATE angularcode_customers SET ".trim($columns,',')." WHERE customerNumber=$id";
			if(!empty($customer)){
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				$success = array('status' => "Success", "msg" => "Customer ".$id." Updated Successfully.", "data" => $customer);
				$this->response($this->json($success),200);
			}else
				$this->response('',204);	// "No Content" status
		}
		
		private function updateTransaction(){
			if($this->get_request_method() != "POST"){
				$this->response('Error');
			}
			$price = str_replace('$','',$_POST['price']);
            $desc = $_POST['desc'];
            $place = $_POST['place'];
            $tranc_date = $_POST['tranc_date'];
            $category = $_POST['category'];
            $id = $_POST['id'];
			$user = $_POST['user'];
			
			$query = "UPDATE transactions SET amount=".$price.",user='".$user."',place='".$place."',tranc_date='".$tranc_date."',category='".$category."',description='".$desc."' WHERE id=$id";
			if(!empty($price)){
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				$success = "Transaction Updated Successfully.";//array('status' => "Success", "msg" => "Transaction Created Successfully.", "data" => $req_values);
				 $this->response($success);
			}else
				$this->response('No valid amount entered.');	// "No Content" status
		}
		
		private function updateItem(){
			if($this->get_request_method() != "POST"){
				$this->response('Error');
			}
			$check = $_POST['check'];
            $id = $_POST['id'];
			$user_tick = $_POST['user_tick'];
			if($check == 1){
			    $status = 'Done';
			}
			else{
			    $status = 'Not Yet';
			}
			$query = "UPDATE items SET user_tick='".$user_tick."',status='".$status."' WHERE id=$id";
			if($check==1 || $check == 0){
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				$success = "Item checked successfully.";//array('status' => "Success", "msg" => "Transaction Created Successfully.", "data" => $req_values);
				 $this->response($success);
			}else
				$this->response('Not updated.');	// "No Content" status
		}
		
		private function updateTask(){
			if($this->get_request_method() != "POST"){
				$this->response('Error');
			}
			$check = $_POST['check'];
            $id = $_POST['id'];
			$date_tick = $_POST['date_tick'];
			if($check == 1){
			    $status = 'Done';
			}
			else{
			    $status = 'Not Yet';
			}
			$query = "UPDATE tasks SET status='".$status."',date_tick='".$date_tick."' WHERE id=$id";
			if($check==1 || $check == 0){
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				$success = "Task checked successfully.";//array('status' => "Success", "msg" => "Transaction Created Successfully.", "data" => $req_values);
				 $this->response($success);
			}else
				$this->response('Not updated.');	// "No Content" status
		}
		
		private function deleteCustomer(){
			if($this->get_request_method() != "DELETE"){
				$this->response('',406);
			}
			$id = (int)$this->_request['id'];
			if($id > 0){				
				$query="DELETE FROM angularcode_customers WHERE customerNumber = $id";
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				$success = array('status' => "Success", "msg" => "Successfully deleted one record.");
				$this->response($this->json($success),200);
			}else
				$this->response('',204);	// If no records "No Content" status
		}
		
		/*
		 *	Encode array into JSON
		*/
		private function json($data){
			if(is_array($data)){
				return json_encode($data);
			}
		}
	}
	
	// Initiiate Library
	
	$api = new API;
	$api->processApi();
?>