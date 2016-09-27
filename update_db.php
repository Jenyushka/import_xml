
<html>
    <head>
    <meta http-equiv="refresh" content="<?php echo $sec?>;URL='<?php echo $page?>'">
    </head>
    <body>
	
<?php
$actual_file = file_get_contents($map_url);
$result_string_previous = "...";
$result_string_new = "";
if (($response_xml_data = $actual_file)===false){
    echo "Error fetching XML\n";

} else {
   libxml_use_internal_errors(true);
   $data = simplexml_load_string($response_xml_data);	

   if (!$data) {
       echo "Error loading XML\n";
       foreach(libxml_get_errors() as $error) {
           echo "\t", $error->message;
       }
   } else {	
	
    	$xml=simplexml_load_file($map_url) or die("Error: Cannot create object");
    	
    	foreach($xml->children() as $nodes) {
    	// ATTRIBUTE:  values
     	   $result_string_new .= "INSERT IGNORE INTO `rate_anomaly` (`property_id`,`property_identifier`,`anomaly_id`,`node_values`) VALUES ('". $nodes["property_id"] ."','". $nodes["property_identifier"] ."','". $nodes["anomaly_id"] ."', '". $nodes->start .", ". $nodes->end .", ". $nodes->percentage ."');";
    	}
    
    
        if(0!=strcmp($result_string_new,$result_string_previous)){
            $result_string_previous = $result_string_new;
        
            $servername = "localhost";
            $username = "username";
            $password = "password";
            $dbname = "myDB";
            
            // Create connection
            $conn = mysqli_connect($servername, $username, $password, $dbname);
            // Check connection
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }
            
            $sql = $result_string_new;
            
            if (mysqli_query($conn, $sql)) {
                echo "New records created successfully";
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
            
            mysqli_close($conn);
        
        } else{
            echo "There are nothing to update. Page reload itself in 5 minutes.";
        }
   }
}
?>
    </body>
</html>
