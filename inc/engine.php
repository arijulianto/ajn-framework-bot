<?php
class Bot{
	public $data;
	
	public function __get($varName){
      if (!array_key_exists($varName,$this->data)){
          throw new Exception('.....');
      }else{
		return $this->data[$varName];
	}
   }

   public function __set($varName,$value){
      $this->data[$varName] = $value;
   }

	function cURL($url, $params=[], $type='get', $headers=[]){
		$pu = parse_url($url);
		$header = [
			'referer: '.$pu['scheme'] . '://' . $pu['host'],
			"User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.84 Safari/537.36" 
		];
		if($headers){
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
	    if($params){
	    	curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	    }
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    $response = curl_exec($ch); 
	    curl_close($ch); 
	    return $response;
	}
}