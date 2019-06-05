<?php
defined('APP') or die('Invalid request!');


class AJNBot{
	public $to;
	public $reply_to;
	
	private $chat = [];
	private $user = [];
	private $text;
	private $db = false;
	private $debug = [];
	private $options = [];
	private $isDebug = false;
	private $platform;
	private $username;
	private $isOnline = false;
	private $telegram;
	private $line;
	private $messenger;
	private $bot = [];
	private $rules = [];
	private $engine;
	private $mode;
	private $verify_token = 'test';
	
	function __construct($platform='', $username=''){
		if($platform) $this->platform = $platform;
		if($username) $this->username = $username;
		if($_GET['platform']) $this->platform = $_GET['platform'];
		if($_GET['username']) $this->username = $_GET['username'];
		$this->isOnline = ($_SERVER['REMOTE_ADDR']=='::' || substr($_SERVER['REMOTE_ADDR'],0,6)=='127.0.' || substr($_SERVER['REMOTE_ADDR'],0,8)=='192.168.') ? false : true;
	}
	
	function load($config){
		// load manual config
		$this->config = $config;
		if(count($config['bot'])==1){
			$bot_data = $config['bot'];
			$platform = array_keys($bot_data)[0];
			$username = array_keys($bot_data[$platform])[0];
			$this->bot = $config[$platform][$username];
			$this->platform = $platform;
			$this->username = $username;
			$this->$platform = $config['bot'][$platform][$username];
		}elseif(count($config['bot'])>1){
			// bot config
			$this->bot =$config[$this->platform][$this->username];
			$platform = $this->platform;
			$this->$platform = $config['bot'][$this->platform][$this->username];
		}else{
			die('Invalid config. Tidak ada bot yang diatur!');
		}
		$this->isDebug = isset($config['debug']) ? $config['debug'] : true;
		
		// bot mode
		if($config['mode']=='rule'){
			$this->mode = 'rule';
			if($config['source']=='db'){
				$db = $config['db'];
				$this->db = new mysqli($db['host'], $db['user'], $db['password'], $db['name']);
				$this->rules = $this->db_rule($config['source_name']);
			}else{
				$this->rules = json_decode(file_get_contents(BASE_PATH.$config['source_name']), true);
			}
			include APP_PATH.base64_decode('ZW5naW5lLnBocA==');
			include BASE_PATH.base64_decode('Ym90RW5naW5lLnBocA==');
			$this->engine = new botEngine;
		}else{
			$this->mode = 'manual';
		}
		
		if($isOnline==false && $this->platform){
			$file = BASE_PATH.'data/'.$this->platform.'_debug.json';
			$this->debug = json_decode(file_get_contents($file), true);
		}
	}

	function listen(){
		if($this->isOnline){
		    $phpinput = str_replace("\n",'',file_get_contents('php://input'));
    		$this->chat = json_decode($phpinput, true);
		}else{
			$this->chat = $this->debug;
			if($_GET['msg']){
				if($this->platform=='telegram')
					$this->chat['message']['text'] = $_GET['msg'];
				elseif($this->platform=='line')
					$this->chat['events'][0]['message']['text'] = $_GET['msg'];
				elseif($this->platform=='messenger')
					$this->chat['entry'][0]['messaging'][0]['message']['text'] = $_GET['msg'];
			}else{
				return false;
			}
		}
			

		if($this->platform=='telegram'){
			$this->user = ['id'=>$this->chat['message']['from']['id'], 'name'=>trim($this->chat['message']['from']['first_name'].' '.$this->chat['message']['from']['last_name'])];
			if($this->chat['message']['from']['username']) $this->user['username'] = $this->chat['message']['from']['username'];
			$this->text = $this->chat['message']['text'];
			$this->to = $this->chat['message']['chat']['id'];
		}elseif($this->platform=='line'){
			$this->user = ['id'=>$this->chat['events'][0]['source']['userId'], 'name'=>$this->userLine($this->chat['events'][0]['source']['userId'])];
			$this->text = $this->chat['events'][0]['message']['text'];
			$this->to = $this->chat['events'][0]['replyToken'];
		}elseif($this->platform=='messenger'){
			/** Begin Challenge Verify **/
			if(isset($_REQUEST['hub_challange'])){
    			$challenge = $_REQUEST['hub_challenge'];
    			$verify_token = $_REQUEST['hub_verify_token'];
    			if ($verify_token === $this->verify_token) {
    				echo $challenge;exit;
    			}
			}
			/** End Challenge Verify **/
			$this->user = ['id'=>$this->chat['entry'][0]['messaging'][0]['sender']['id'], 'name'=>$this->userMessenger($this->chat['entry'][0]['messaging'][0]['sender']['id'])];
			$this->text = $this->chat['entry'][0]['messaging'][0]['message']['text'];
			$this->to = $this->chat['entry'][0]['messaging'][0]['sender']['id'];
		}
		$this->text = str_replace('@'.$this->username, '', $this->text);
	}

	function parse(){
		$text = trim(strtolower($this->text));
		$text = trim($text, '/');
		$text = trim($text, '?');
		$text = trim($text, '!');
		$text = trim($text, '.');
		$text = trim($text);
		$output = '';
		
		if($_GET['debug']=='0'){$this->isDebug = false;}
		
		if($this->mode=='manual'){
			$chat = $this->chat;
			$this->user = (object)$this->user;
			$message = $this->text;
			$app = $this;
			include BASE_PATH.base64_decode('Ym90RW5naW5lLnBocA==');
			return false;
		}else{
			foreach($this->rules as $role){
				if(preg_match($role['pattern'], $text)){
					if($role['callback']){
						$func = $role['callback'];
						$this->reply_to = $this->chat['message']['message_id'];
					}
					if($role['jenis']=='ask'){$this->reply_to = $this->chat['message']['message_id'];}
					if($role['prepare_text'] && $this->platform=='telegram'){
						$funcHelper = $func.'Prepare';
						if($funcHelper && method_exists($this->engine, $funcHelper)){
							$cek = call_user_func([$this->engine, $funcHelper], $text);
							if($cek){
								$this->send($role['prepare_text'], ['reply_to'=>$this->reply_to]);$this->reply_to = null;
							}
						}else{
							$this->send($role['prepare_text'], ['reply_to'=>$this->reply_to]);$this->reply_to = null;
						}
					}
					if($role['jawaban']) $output = $role['jawaban'];
					break;
				}
			}
		
			if($func){
				$exec = $this->engine->$func($text);
				$output = $exec;
				if(is_array($output)){
					$this->options = $this->options ? $this->options+$output['options'] : $output['options'];
					$output = $output['text'];
				}
			}
		}
		return $this->parseOut($output);
	}
	
	function parseOut($perintah){
		if($perintah==''){
			if($this->text && ($this->chat['message']['chat']['type']=='private' || $this->chat['events'][0]['message']['type']=='text'))
			    return 'Maaf kami belum mengenali pesan Anda!';
			else
			    return '';
		}
		$perintah = trim(($perintah),'/');
		$task = explode(' ', $this->text);
		$task2 = $task[0].' '.$task[1];
		$task = trim($task[0],'/');
		$txt_ori = $perintah;
		$days = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');
		$months_en  = array('January','February','March','April','May','June','July','August','September','October','November','December');
		$months = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
		$time = array('bulan'=>$months[date('n')-1], 'tahun'=>date('Y'));
		$jam = date('H');
		$jams = date('H:i');
		$day = $days[date('w')];
		$day_next = $days[date('w', strtotime('+1day'))];
		$day_prev = $days[date('w', strtotime('-1day'))];
		$day_next2 = $days[date('w', strtotime('+2day'))];
		$day_prev2 = $days[date('w', strtotime('-2day'))];
		$date = date('j F Y');
		$date_next = date('j F Y', strtotime('+1day'));
		$date_prev = date('j F Y', strtotime('-1day'));
		$date_next2 = date('j F Y', strtotime('+2day'));
		$date_prev2 = date('j F Y', strtotime('-2day'));
		$date_nextw = date('j F Y', strtotime('+1week'));
		$date_prevw = date('j F Y', strtotime('-1week'));
		$date = str_replace($months_en, $months, $date);
		$date_next = str_replace($months_en, $months, $date_next);
		$date_prev = str_replace($months_en, $months, $date_prev);
		// sapaan
		if($jam>=1 && $jam<12)
			$perintah = str_replace('{{WAKTU}}', 'pagi', $perintah);
		elseif($jam>=12 && $jam<15)
			$perintah = str_replace('{{WAKTU}}', 'siang', $perintah);
		elseif($jam>=15 && $jam<19)
			$perintah = str_replace('{{WAKTU}}', 'sore', $perintah);
		else
			$perintah = str_replace('{{WAKTU}}', 'malam', $perintah);
		// waktu
		$perintah = str_replace('{{JAM}}', $jam, $perintah);
		$perintah = str_replace('{{BULAN}}', $time['bulan'], $perintah);
		$perintah = str_replace('{{TAHUN}}', $time['tahun'], $perintah);
		$perintah = str_replace('{{DATE}}', $date, $perintah);
		$perintah = str_replace('{{HOUR}}', $jams, $perintah);
		$perintah = str_replace('{{DATE_NEXT}}', $date_next, $perintah);
		$perintah = str_replace('{{DATE_PREV}}', $date_prev, $perintah);
		$perintah = str_replace('{{DATE_NEXT2}}', $date_next2, $perintah);
		$perintah = str_replace('{{DATE_PREV2}}', $date_prev2, $perintah);
		$perintah = str_replace('{{DATE_NWEEK}}', $date_nextw, $perintah);
		$perintah = str_replace('{{DATE_LWEEK}}', $date_prevw, $perintah);
		$perintah = str_replace('{{DAY}}', $day, $perintah);
		$perintah = str_replace('{{DAY_NEXT}}', $day_next, $perintah);
		$perintah = str_replace('{{DAY_PREV}}', $day_prev, $perintah);
		$perintah = str_replace('{{DAY_NEXT2}}', $day_next2, $perintah);
		$perintah = str_replace('{{DAY_PREV2}}', $day_prev2, $perintah);

		$platform = $this->platform;
		$bot = $this->$platform;

		// user info
		if($platform=='telegram'){
			$perintah = str_replace('{{NAMA}}', $this->user['name'], $perintah);
		}elseif($platform=='line'){
			$perintah = str_replace('{{NAMA}}', $this->user['name'], $perintah);
		}elseif($platform=='messenger'){
			$perintah = str_replace('{{NAMA}}', $this->user['name'], $perintah);
		}

		$perintah = str_replace('{{ID}}', $this->user['id'], $perintah);
		$perintah = str_replace('{{USERNAME}}', $this->chat['message']['from']['username'], $perintah);
		$perintah = str_replace('{{NAMA_BOT}}', $bot['name'], $perintah);

		// other
		$perintah = str_replace('{{PLATFORM}}', ucfirst($this->platform), $perintah);
		$perintah = str_replace('{{PERINTAH}}', $task, $perintah);
		
		// emoji
		$perintah = str_replace(':lol:', ':rofl:', $perintah);
		$emoji = json_decode(file_get_contents(BASE_PATH.'data/emoji.json'), true);
		$perintah = str_replace(array_keys($emoji), array_values($emoji), $perintah);

		return $perintah;
	}	
	
	function send($text, $options=[]){
		if(!empty($text) || $text!=false){
			if($this->reply_to) $options['reply_to'] = $this->reply_to;
			$send = 'send'.ucfirst($this->platform);
			$this->$send($this->to, $text, $options);
		}
	}

	function sendTelegram($to, $text, $options=[]){
		// detect options
		if($this->options){
			if($options){$options = $options+$this->options;}else{$options = $this->options;}
		}
				
	    if($this->isOnline==false){
			echo '<pre><strong>send via Telegram ('.$this->username.') to '.$to.':</strong>'."\n".$text.'';
			if($options) echo "\n".'<em>Options: '.json_encode($options).'</em>';
			echo '</pre>';
			if($this->isDebug) return false;
		}
		$platform = $this->platform;
		$bot = $this->$platform;
		$postData = ['chat_id' => $to,'text' => $text];
			
		// detect message type
		if($options['photo']){
			$postData['caption'] = $text;
			$method = 'sendPhoto';
		}elseif($options['audio']){
			$postData['caption'] = $text;
			$method = 'sendAudio';
		}elseif($options['video']){
			$postData['caption'] = $text;
			$method = 'sendVideo';
		}elseif($options['document']){
			$postData['caption'] = $text;
			$method = 'sendDocument';
		}else{
			$method = 'sendMessage';
		}
		$url = "https://api.telegram.org/bot" . $bot['token'] . "/$method";
		
		if($options){
			foreach($options as $key=>$val){
				if($key=='reply_to') $key = 'reply_to_message_id';
				if($key=='format') $key = 'parse_mode';
				if($key=='reply_markup') $val = json_encode($val);
				$postData[$key] = $val;
			}
		}

		$header = [
			"X-Requested-With: XMLHttpRequest",
			"User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.84 Safari/537.36" 
		];

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);   
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    $response = curl_exec($ch);
	    $err = curl_error($ch);
	    curl_close($ch);
	    if($err){
	        $response = $err;
	    }
	    return $response;
	}

	function sendLine($to, $text, $options=[]){
		if($this->isOnline==false){
			echo '<p>send via Line ('.$this->username.') to '.$to.': '.$text;
			if($options) echo '<br />Options: '.json_encode($options);
			return false;
		}
		$platform = $this->platform;
		$bot = $this->$platform;
		$url = "https://api.line.me/v2/bot/message/reply";
		$postData = array('replyToken' => $to,'messages' => [array('type'=>'text', 'text'=>$text)]);
		$header = ['Content-Type: application/json','Authorization: Bearer  '.$bot['token']];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));   
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$response = curl_exec($ch);
		$err = curl_error($ch);
		curl_close($ch);
		if($err){$response=$err;}
		return $response;
	}

	function sendMessenger($to, $text, $options=[]){
		if($this->isOnline==false){
			echo '<p>send via Messenger ('.$this->username.') to '.$to.': '.$text;
			if($options) echo '<br />Options: '.json_encode($options);
			return false;
		}
		$platform = $this->platform;
		$bot = $this->$platform;
		$url = 'https://graph.facebook.com/v2.6/me/messages?access_token='.$bot['token'];
		$postData = array('recipient'=>array('id'=>$to),'message'=>array('text'=>$text));
		$header = ["Content-Type: application/json"];
	    $ch = curl_init($url);
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	    $response = curl_exec($ch);
	    $err = curl_error($ch);
	    curl_close($ch);
		if($err){$response=$err;}
	    return $response;
	}
	
	private function userLine($userId){
		$url = "https://api.line.me/v2/bot/profile/".$userId;
		$platform = $this->platform;
		$bot = $this->$platform;
		$header = ['Content-Type: application/json','Authorization: Bearer  '.$bot['token']];
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$response = curl_exec($ch);
		$err = curl_error($ch);
		curl_close($ch);
		if($err){$response=$err;}
		return json_decode($response, true);
	}
	
	private function userMessenger($userId){
		$platform = $this->platform;
		$bot = $this->$platform;
		$url = 'https://graph.facebook.com/v2.6/'.$userId.'?fields=first_name,last_name,profile_pic&access_token='.$bot['token'];
		$header = ['Content-Type: application/json',"User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.84 Safari/537.36"];
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSLVERSION, 6);
		$response = curl_exec($ch);
		$err = curl_error($ch);
		curl_close($ch);
		if($err){$response=$err;}
		return json_decode($response, true);
	}
	
	
	
	private function db_rule($tbl, $fields = []){
		if($fields){
			if(is_array($fields)) $fields = implode(',', $fields);
		}else{
			$fields = '*';
		}
		$rules = [];
		$sql = $this->db->query("SELECT $fields from {$tbl} WHERE aktif='1' order by prio");
		while($row = $sql->fetch_assoc()){
			$rules[] = $row;
		}
		return $rules;
	}
}