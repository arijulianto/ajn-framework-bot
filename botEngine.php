<?php

// :: Manual Mode ::
if($text=='start'){
	$app->send('Selamat Datang');
}else{
	// definisikan kondisi perintah lainnya 
	$app->send('ada apa test');
}



/*
// :: Rule Mode ::
class botEngine extends AJN{
	function sapa_waktu($text){
		// definisikan kondisi perintah
		$output = "output chat disini"; // output chat yang akan dikirim ke user
		return $output;
	}	
}
*/