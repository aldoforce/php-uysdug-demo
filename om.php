<?php



function processSObject($pSObject) {
	$id 			= $pSObject->Id;
  
  //do something
  $s = print_r($pSObject, true);

  debug($s);

}

function ack($value) {
	return array('Ack' => $value);
}

function notifications($data) {	
  debug("notification: ".$data->Notification);

  //multiple notifications
	if (is_array($data->Notification)) {
	    $result = array();
	    for ($i = 0; $i < count($data->Notification); $i++) {
      		processSObject($data->Notification[$i]->sObject);
      		array_push($result, ack(true));
    	}
    	return $result;    
  	} 
  	//single notification
  	else {
    	processSObject($data->Notification->sObject);
    	return ack(true);
  	}
}

function debug($s) {
  $stdout = fopen('php://stdout', 'w');
  fwrite($stdout, $s);
  fclose($stdout);
}

// MAIN LOADER /////////////////////////////////////////
//load specific wsdl for outbound message handler
$server = new SoapServer("./wsdl/om.wsdl.xml");		 
$server->addFunction("notifications");
$server->handle();  

debug('huzzah!');

?>
