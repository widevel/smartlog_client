<?php

namespace Widevel\SmartlogClient;

class Log extends Sender {
	const LEVEL_INFO 		= 4;
	const LEVEL_DEBUG 		= 3;
	const LEVEL_WARNING 	= 2;
	const LEVEL_ERROR 		= 1;
	
	protected $instance_token;
	protected $session_token;
	protected $server_url;
	protected $sender_method = 'cmd';
	protected $server_cmd_path;
	protected $instance_data;
	protected $unsended_logs_path;
	protected $verbose = false;
	protected $ssl_verify = true;
	
	private static $class_instance;
	
	public static $pending_logs = [];
		
	public function __construct() {
		$this->instance_token = self::generateRandomHash();
		
		self::$class_instance = $this;
		
	}
	
	public function sendPendingLogs() :Log {
		foreach(self::$pending_logs as $log_object) $this->send('log', $log_object);
		self::$pending_logs = [];
		return $this;
	}
	
	public function setVerbose(bool $verbose) :Log { $this->verbose = $verbose; return $this; }
	public function setSslVerify(bool $ssl_verify) :Log { $this->ssl_verify = $ssl_verify; return $this; }
	
	public function setServerUrl(string $server_url) :Log { $this->server_url = $server_url; return $this; }
	public function getServerUrl() :?string { return $this->server_url; }
	
	public function setServerCmdPath(string $server_cmd_path) :Log { $this->server_cmd_path = $server_cmd_path; return $this; }
	public function getServerCmdPath() :?string { return $this->server_cmd_path; }
	
	public function setSenderMethod(string $sender_method) :Log { $this->sender_method = $sender_method; return $this; }
	public function getSenderMethod() :?string { return $this->sender_method; }
	
	public function setUnsendedLogsPath(string $unsended_logs_path) :Log { $this->unsended_logs_path = realpath($unsended_logs_path) . DIRECTORY_SEPARATOR; return $this; }
	public function getUnsendedLogsPath() :?string { return $this->unsended_logs_path; }
	
	public function setInstanceToken(string $instance_token) :Log {
		$this->setNewInstanceToken($this->instance_token, $instance_token);
		$this->instance_token = $instance_token;
		return $this;
	}
	public function getInstanceToken() :?string { return $this->instance_token; }
	
	public function setSessionToken(string $session_token) :Log {
		$this->session_token = $session_token;
		$this->setNewSessionToken();
		return $this;
	}
	public function getSessionToken() :?string { return $this->session_token; }
	
	public function setInstanceData($instance_data) :Log {
		$this->instance_data = $instance_data;
		$this->updateInstanceData();
		return $this;
	}
	public function getInstanceData() :?\stdclass { return $this->instance_data; }
	
	public static function error() {
		$arguments = func_get_args();
		array_unshift($arguments , self::LEVEL_ERROR);
		return call_user_func_array('self::write', $arguments);
	}
	
	public static function debug() {
		$arguments = func_get_args();
		array_unshift($arguments , self::LEVEL_DEBUG);
		return call_user_func_array('self::write', $arguments);
	}
	
	public static function warning() {
		$arguments = func_get_args();
		array_unshift($arguments , self::LEVEL_WARNING);
		return call_user_func_array('self::write', $arguments);
	}
	
	public static function info() {
		$arguments = func_get_args();
		array_unshift($arguments , self::LEVEL_INFO);
		return call_user_func_array('self::write', $arguments);
	}
		
	private static function write(int $level, $message = null, string $name = null, array $tags = [], $data = null) {
		
		$log_object = new \stdclass;
		$log_object->date = new \DateTime('now');
		$log_object->level = $level;
		$log_object->message = $message;
		$log_object->name = $name;
		$log_object->tags = $tags;
		$log_object->data = $data;
		$log_object->uniq_id = self::generateRandomHash(serialize($log_object));

		if(self::$class_instance !== null) {
			self::$class_instance->send('log', $log_object);
		} else {
			self::$pending_logs[] = $log_object;
		}
		
	}
	
	private static function generateRandomHash($seed = null) {
		return hash('sha512', random_bytes(512) . $seed);
	}
}