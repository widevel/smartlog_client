<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$object = unserialize(base64_decode($argv[1]));

if(!is_object($object)) die('Unable to unserialize');

$fields = [
	'command' => $object->command,
	'serialized_data' => $object->serialized_data,
];

$ch = curl_init($object->server_url);

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

if(!$object->ssl_verify) {

	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

}

$response = curl_exec($ch);
curl_close($ch);

if($response === false && $object->unsended_logs_path !== null && $object->unsended_logs_path !== false) {
	file_put_contents($object->unsended_logs_path . hash('sha256', $argv[1]), $argv[1]);
} else if($response === false && $object->unsended_logs_path === false) {
	echo "Unsended logs path not exist";
}