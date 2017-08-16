<?php
 	echo "hereee";

	
		//public $data = "";
		
		$DB_SERVER = "ateefweb.com.mysql";
		$DB_USER = "ateefweb_com";
		$DB_PASSWORD = "Atif786";
		$DB = "ateefweb_com";

		//private $db = NULL;
		//private $mysqli = NULL;
		
							// Init parent contructor
		//	$this->dbConnect();	
			//$this->insertmasjids();// Initiate Database connection
		
		
		/*
		 *  Connect to Database
		*/
		
		    print "here";
			$mysqli = mysqli_connect($DB_SERVER, $DB_USER, $DB_PASSWORD, $DB);
			
	
		if (!$mysqli) {
    die("Connection failed: " . mysqli_connect_error());
}
	
		$jsondata = file_get_contents('masjiddetails.json');
		$data = json_decode($jsondata, true);
		echo "<pre>";
		//print_r($data);
		//die;
           foreach ($data as $masjids) {
  //echo "$masjids <br>";
            $street = explode(",", $masjids['Remarks']);
            //print_r($street);
            $zip = explode(" ", $street[2]);
            //print_r($zip);
            $zip = (isset($zip[2]))?$zip[2]:0;
          $query = "INSERT INTO masajids (name,country,state,city,street,zipcode) VALUES('".addslashes($masjids['Name'])."','USA','".addslashes($masjids['State'])."','".addslashes($masjids['City'])."','".addslashes($street[2])."','".addslashes($zip)."')";
			if (mysqli_query($mysqli, $query)) {
    echo "New record created successfully";
} else {
    echo "Error: " . $query . "<br>" . mysqli_error($mysqli);
}
}

mysqli_close($mysqli);
	
		
		
		
		
	
	?>
	