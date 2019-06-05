<?php
defined('APP') or die('Invalid request!');

class AJN{
	private $data = [];

	function cURL($url, $params=[], $type='get', $headers=[]){
		$pu = parse_url($url);
		$header = [
			'referer: '.$pu['scheme'] . '://' . $pu['host'],
			"User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.84 Safari/537.36" 
		];
		if($headers){
			if($headers['timeout']){
				$timeout = $headers['timeout'];
				unset($headers['timeout']);
			}
			foreach($headers as $key=>$val){
				$header[] = $key.': '.$val;
			}
		}
		$ch = curl_init(); 
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	    if(strtolower($type)=='post'){
	    	curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	    }
	    if($timeout){
			curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	    }
	    if($params){
	    	curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	    }
	    curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	    $response = curl_exec($ch); 
	    curl_close($ch); 
	    return $response;
	}

	function load($type){
		$type = strtolower($type);
		return json_decode(file_get_contents(APP_DATA.$type.'.json'), true);
	}
}