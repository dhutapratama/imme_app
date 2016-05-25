<?php

while (true) {
	$result = file_get_contents("http://rufi.hol.es/v1_server/import_sms");
	$result = json_decode($result);

	if ($result->error) {
		echo $result->error_message."\n";
	} else {
		echo "SMS Count : ".$result->data->sms_recorded."\n";
	}
	sleep(3);
}

?>