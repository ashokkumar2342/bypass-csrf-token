<?php
	$url = "http://domain/admin/login";
	$username = "admin@gmail.com";
	$password = "admin123";
	
	$csrf_token_field_name = "_token";
	$params = array(
					"email" => $username,
					"password" => $password,
					"another_mendatory_field" => "value"
					);
					
	$token_cookie= realpath("test.txt");

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/32.0.1700.107 Chrome/32.0.1700.107 Safari/537.36');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $token_cookie);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $token_cookie);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	$response = curl_exec($ch);
	 
	/* print_r($response); */
	
	if (curl_errno($ch)) die(curl_error($ch));
	libxml_use_internal_errors(true);
	$dom = new DomDocument();
	$dom->loadHTML($response);
	libxml_use_internal_errors(false);
	$tokens = $dom->getElementsByTagName("input");
	$t='';
	for ($i = 0; $i < $tokens->length; $i++) 
	{
		$meta = $tokens->item($i);
		if($meta->getAttribute('name') == '_token')
			$t = $meta->getAttribute('value');
	}  
	if($t) {
		$csrf_token = file_get_contents(realpath("another-cookie.txt"));
		$postinfo = "";
		foreach($params as $param_key => $param_value) 
		{
			$postinfo .= $param_key ."=". $param_value . "&";	
		}
		 $postinfo .= $csrf_token_field_name ."=". $t;
		
		$headers = array();
		
		$header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
		$header[] = "Cache-Control: max-age=0";
		$header[] = "Connection: keep-alive";
		$header[] = "Keep-Alive: 300";
		$header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
		$header[] = "Accept-Language: en-us,en;q=0.5";
		$header[] = "Pragma: ";
		$headers[] = "X-CSRF-Token: $t";
		$headers[] = "Cookie: $token_cookie";

		 curl_setopt($ch, CURLOPT_URL, $url);
		 curl_setopt($ch, CURLOPT_POST, true);
		 curl_setopt($ch, CURLOPT_POSTFIELDS, $postinfo);
		 curl_setopt($ch, CURLOPT_COOKIEJAR, $token_cookie);
		 curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		 curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		 $response = curl_exec($ch);
		 //echo $response; 
		 curl_setopt($ch, CURLOPT_URL, 'http://eageskool.com/admin/dashboard');
		 curl_setopt($ch, CURLOPT_HTTPGET, true);
		 curl_setopt($ch, CURLOPT_COOKIEJAR, $token_cookie);
		 $response =curl_exec($ch);
		 echo $response;

		 
	}
	
?>	
