<?php

namespace Widevel\SmartlogClient;

class Sender {
	
	protected function send(string $command, \stdclass $object) {
		
		$object->instance_token = $this->instance_token;
		$object->session_token = $this->session_token;
		
		$serialized_data = base64_encode(gzdeflate(serialize($object), 9));
		
		if($this->sender_method == 'cmd') {
			$cmd = 'php ' . escapeshellarg($this->server_cmd_path) . ' ' . escapeshellarg($command) . ' ' . escapeshellarg($serialized_data) . (!$this->verbose ? self::cmd_get_background_str() : null);
		}
		
		if($this->sender_method == 'http') {
			$object = new \stdclass;
			$object->server_url = $this->server_url;
			$object->ssl_verify = $this->ssl_verify;
			$object->command = $command;
			$object->serialized_data = $serialized_data;
			$object->unsended_logs_path = $this->unsended_logs_path;
			
			$script_path = realpath(__DIR__ . '/../scripts/http_sender.php');
			$cmd = 'php ' . escapeshellarg($script_path) . ' ' . escapeshellarg(base64_encode(serialize($object))) . (!$this->verbose ? self::cmd_get_background_str() : null);
		}
		
		if(isset($cmd)) {
			$response = shell_exec($cmd);
			if($this->verbose) echo $response;
		}
	}
	
	protected function setNewInstanceToken(string $old_instance_token, string $new_instance_token) {
		$object = new \stdclass;
		$object->old_instance_token = $old_instance_token;
		$object->new_instance_token = $new_instance_token;
		$this->send('set_new_instance_token', $object);
	}
	
	protected function setNewSessionToken() {
		$object = new \stdclass;
		$object->instance_token = $this->instance_token;
		$object->new_session_token = $this->session_token;
		$object->date = new \DateTime('now');
		$this->send('set_new_session_token', $object);
	}
	
	protected function updateInstanceData() {
		$object = new \stdclass;
		$object->instance_token = $this->instance_token;
		$object->session_token = $this->session_token;
		$object->data = $this->instance_data;
		$object->date = new \DateTime('now');
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