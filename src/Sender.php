<?php

namespace Widevel\SmartlogClient;

class Sender {
	
	protected function send(string $command, \stdclass $object) {
		
		$serialized_data = base64_encode(gzdeflate($object, 9));
		
		if($this->sender_method == 'cmd') {
			$cmd = 'php ' . escapeshellarg($this->sender_cmd_path) . ' ' . escapeshellarg($command) . ' ' . escapeshellarg($serialized_data) . self:.cmd_get_background_str();
			shell_exec($cmd);
		}
		
		if($this->sender_method == 'http') {
			$object = new \stdclass;
			$object->server_url = $this->server_url;
			$object->command = $command;
			$object->serialized_data = $serialized_data;
			
			$script_path = realpath(__DIR__ . '/../scripts/http_sender.php');
			$cmd = 'php ' . escapeshellarg($script_path) . ' ' . escapeshellarg(base64_encode(serialize($object)));
		}
	}
	
	protected function setNewInstanceToken(string $old_instance_token, string $new_instance_token) {
		$object->old_instance_token = $old_instance_token;
		$object->new_instance_token = $new_instance_token;
		$this->send('set_new_instance_token', $object);
	}
	
	protected function setNewSessionToken() {
		$object->instance_token = $this->instance_token;
		$object->new_session_token = $this->session_token;
		$this->send('set_new_session_token', $object);
	}
	
	protected function updateInstanceData() {
		$object->instance_token = $this->instance_token;
		$object->data = $this->instance_data;
		$this->send('update_instance_data', $object);
	}
	
	private static function cmd_get_background_str() {
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
		return ' > NUL 2> NUL';
	} else {
		return ' > /dev/null 2>/dev/null &';
	}
	}
}