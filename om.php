<?php 
//om.php

//debug method
function debug($s) {
  $stdout = fopen('php://stdout', 'w');
  fwrite($stdout, $s);
  fclose($stdout);
}

//process a notification
function process_notification($pNotification) {
	$result = true;

  //get notification id
  $notification_id = $pNotification->Id;

  //verify idempotence with dramatic exit
  if (is_already_processed($notification_id)) return true; //do nothing

  try {
    $sobject = $pNotification->sObject;
    //  - do something with the sobject (ie serialize in a queue)
    //  QUEUE.save($sobject)

    //  - save the notification id as already processed
    //  DB.save($notification_id);
  } catch (Exception $e) {
    $result = false;
  }

  return $result;
}

//create ACK response
function ack($value) {
	return array('Ack' => $value);
}

//idempotence
function is_already_processed($n) {
  //this is bad, you should verify if it's a duplicate notification!!!
  return false; 
}

//process notifications
function notifications($data) {
  //debug
  $n = (array) $data;
  debug("payload: ". print_r($n, true) );

  //define response
  $response = null;

  //multiple notifications
	if (is_array($data->Notification)) {
    $response = array();
    for ($i = 0; $i < count($data->Notification); $i++) {
    		array_push(
          $response, 
          ack(
            process_notification($data->Notification[$i])
          )
        );    		
  	}  	 
	} 
	//single notification
	else {  	
  	$response = ack(
                  process_notification($data->Notification)
                );
	}

  return $response;   
}

// MAIN LOADER 
//load specific wsdl for outbound message handler
$server = new SoapServer("./wsdl/opp.wsdl.xml");		 
$server->addFunction("notifications");
$server->handle();  

debug('huzzah!');

?>
