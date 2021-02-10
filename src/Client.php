<?php

namespace Widevel\SmartlogClient;

class Client extends Sender {
	const LEVEL_INFO 		= 4;
	const LEVEL_DEBUG 		= 3;
	const LEVEL_WARNING 	= 2;
	const LEVEL_ERROR 		= 1;
	
	protected $instance_token;
	protected $session_token;
	protected $server_url;
	protected $sender_method = 'cmd';
	protected $sender_cmd_path;
	protected $instance_data;
	
	private $class_has_initialized = false;
	
	public static $prelogs = [];
	
	protected $queue = [];
	
	public function __construct() {
		$this->instance_token = hash('sha256', random_bytes(128) . microtime());
		
		$this->class_has_initialized = true;
		
		$this->queue = self::$prelogs;
		
		foreach($this->queue as $log_object) $this->send('log', $log_object);
	}
	
	public function setServerUrl(string $server_url) { $this->server_url = $server_url; }
	public function getServerUrl() :?string { return $this->server_url; }
	
	public function setSenderMethod(string $sender_method) { $this->sender_method = $sender_method; }
	public function getSenderMethod() :?string { return $this->sender_method; }
	
	public function setInstanceToken(string $instance_token) {
		$this->setNewInstanceToken($this->instance_token, $instance_token);
		$this->instance_token = $instance_token;
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
	
	public static function error() {
		$arguments = func_get_args();
		array_unshift($arguments , self::LEVEL_ERROR);
		return call_user_func_array(['self::write'], $arguments);
	}
	
	public static function debug() {
		$arguments = func_get_args();
		array_unshift($arguments , self::LEVEL_DEBUG);
		return call_user_func_array(['self::write'], $arguments);
	}
	
	public static function warning() {
		$arguments = func_get_args();
		array_unshift($arguments , self::LEVEL_WARNING);
		return call_user_func_array(['self::write'], $arguments);
	}
	
	public static function info() {
		$arguments = func_get_args();
		array_unshift($arguments , self::LEVEL_INFO);
		return call_user_func_array(['self::write'], $arguments);
	}
	
	private static function write(int $level, $message = null, array $tags = [], $data = null) {
		$log_object = new \stdclass;
		$log_object->date = new \DateTime('now');
		$log_object->level = $level;
		$log_object->message = $message;
		$log_object->tags = $tags;
		$log_object->data = $data;
		
		$this->send('log', $log_object);
	}
}