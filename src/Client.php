<?php

namespace Widevel\SmartlogClient;

class Client extends Sender {
	const LEVEL_DEBUG 		= 3;
	const LEVEL_WARNING 	= 2;
	const LEVEL_ERROR 		= 1;
	
	protected $instance_token;
	protected $session_token;
	protected $server_url;
	protected $sender_method = 'cmd';
	protected $instance_data;
	
	public static $prelogs = [];
	
	protected $queue = [];
	
	public function __construct() {
		$this->instance_token = hash('sha256', random_bytes(128) . microtime());
		
		$this->queue = self::$prelogs;
		
		foreach($this->queue as $queue_row) $this->sendQueue($queue_row);
	}
	
	public function setServerUrl(string $server_url) { $this->server_url = $server_url; }
	public function getServerUrl() :?string { return $this->server_url; }
	
	public function setSenderMethod(string $sender_method) { $this->sender_method = $sender_method; }
	public function getSenderMethod() :?string { return $this->sender_method; }
	
	public function setInstanceToken(string $instance_token) {
		$this->instance_token = $instance_token;
		$this->setNewInstanceToken();
	}
	public function getInstanceToken() :?string { return $this->instance_token; }
	
	public function setSessionToken(string $session_token) {
		$this->session_token = $session_token;
		$this->setNewSessionToken();
	}
	public function getSessionToken() :?string { return $this->session_token; }
	
	public function setInstanceData(\stdclass $instance_data) {
		$this->instance_data = $instance_data;
		$this->updateInstanceData();
	}
	public function getInstanceData() :?\stdclass { return $this->instance_data; }
}