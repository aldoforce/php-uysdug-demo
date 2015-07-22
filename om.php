<?php

function processSObject($pSObject) {
	$id 			= $pSObject->Id;
	echo "Inbound Message from SFDC: ID: $id";
  
  //do something
  print_r($pSObject);

}

function ack($value) {
	return array('Ack' => $value);
}

function notifications($data) {	
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

// MAIN LOADER /////////////////////////////////////////
//load specific wsdl for outbound message handler
$server = new SoapServer("./wsdl/om.wsdl.xml");		 
$server->addFunction("notifications");
$server->handle();  
?>
