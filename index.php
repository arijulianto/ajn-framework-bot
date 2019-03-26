<?php
date_default_timezone_set('id_ID');
setlocale(LC_ALL, 'Asia/Jakarta');
ini_set('date.timezone', 'Asia/Jakarta');


class Bot{
    private $bot_token = 'BOT_TOKEN_KAMU_DISINI';
    private $user;
    private $chat;
    private $to;
    private $text;
    private $sText;

    function __construct(){
        $this->chat = json_decode(file_get_contents('php://input'), true);
        $this->text = $this->chat['message']['text'];
        $this->to = $this->chat['message']['chat']['id'];
        $this->user = ['id'=>$this->chat['message']['from']['id'], 'name'=>trim($this->chat['message']['from']['first_name'].' '.$this->chat['message']['from']['last_name'])];
    }

    private function jalankanBot(){
        $text = trim($this->text, '/');
        $text = strtolower($text);
        $text = trim($this->text);
        $this->sText = $text;
        $kata = explode(' ', $text);
        $this->eksekusiBot($kata[0]);
    }
    
    function eksekusiBot($perintah){
        // default output jawaban        
        $output = 'Maaf, kami belum mengenali pesan Anda!';

        /** METODE 1 **/
        // eksekusi berdasarkan perintah;
        if($perintah=='start'){
            $output = 'Halo '.$this->user['nama'];
        }elseif($perintah=='help'){
            $output = 'Daftar perintah yang didukung:'."\nhelp: daftar perintah yang didukung\njam: menampilkan jam saat ini\ntanggal: menampilkan tanggal hari ini";
        }
        
        /** METODE 2 **/
        // eksekusi berdasarkan frase kata
        if($this->cariKata($this->sText, 'tanggal') && $this->cariKata($this->sText, 'berapa')){
            $output = 'Sekarang tanggal '.date('j F Y');
        }elseif($this->cariKata($this->sText, 'tgl') && $this->cariKata($this->sText, 'berapa')){
            $output = 'Sekarang tanggal '.date('j F Y');
        }elseif($this->cariKata($this->sText, 'hari') && $this->cariKata($this->sText, 'apa')){
            $output = 'Sekarang hari '.date('l');
        }
        
        /** METODE 3 **/
        // eksekusi berdasarkan frase kata (by konfig)
        $aData = json_decode(file_get_contents('tanya-jawab.json'), true);
		$num = 0;
		foreach($aData as $row){
			$ek = explode('&&', $row['tanya']);
			foreach($ek as $tanya){
				$num += cariKata($msg, $tanya)?1:0;
			}
			if(count($ek)==$num){
				$output = $row['jawab'];
				break;
			}
		}


        // kirim ke telegram
        $this->send($this->to, $output);
    }

    private function cURL($url, $params=array(), $type='get', $headers=array()){
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
        if($headers['referer']){
            curl_setopt($ch, CURLOPT_REFERER, $headers['referer']);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch); 
        curl_close($ch); 
        return $response;
    }
    
    private function cariKata($string, $array) {
        $count = 0;
        if(is_array($array)){
            foreach($array as $value) {
                if (false !== stripos($string,$value)) {
                    ++$count;
                }
            }
        }else{
            if (false !== stripos($string,$array)) {
                ++$count;
                $array = array($array);
            }
        }
        return $count == count($array);
    }

    private function send($to, $text, $options=[]){
        $url = "https://api.telegram.org/bot" . $this->bot_token . "/sendMessage";
        $postData = [
            'chat_id' => $to,
            'text' => $text
        ];
        if($options){
            foreach($options as $k=>$v){
                $postData[$k] = $v;
            }
        }
        $this->cURL($url, $postData, 'post');
    }
}

$bot = new Bot;
$bot->jalankanBot();