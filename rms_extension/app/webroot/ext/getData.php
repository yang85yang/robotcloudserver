<?php
/*
Author: Matouš Jezerský

Dispatcher - PHP interface.
*/

function getJSON() {
	$ip = "localhost";
	$port = 2107;

	$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	if ($sock == false) {
		echo "socket_create error\n";
	}

	socket_connect($sock, $ip, $port);

	$msg = socket_read($sock, 128);

	$msg = "APP_CLIENT";
	socket_write($sock, $msg, strlen($msg));
	$msg = socket_read($sock, 128);

	$msg_bindings = "GET_ALL_DATA";
	$msg = (strlen($msg_bindings)).'#'.$msg_bindings;
	socket_write($sock, $msg, strlen($msg));

	$lenStr = "";
	$lastChar = "";

	while (true) {
		$lastChar = socket_read($sock, 1);
		if ($lastChar == "") {
			socket_close($sock);
			return "ERROR";
		}
		if ($lastChar == '#') {
			break;
		}
		$lenStr = $lenStr.$lastChar;
	}

	$msg = socket_read($sock, (int) $lenStr);
	
	socket_shutdown($sock);
	socket_close($sock);
	
	$retStr = '{ "ip" : "'.$_SERVER['REMOTE_ADDR'].'", "payload" : '.$msg.' }';
	
	return $retStr;
}

echo getJSON();
?>
