<?php
function sendjson($data, $url) {
	$tmp = json_encode($data);
	$wsse = new WSSE(USERNAME, SECRET);
	$hdr = $wsse->get_header(true);

	$req =& new HTTP_Request($url);
	$req->addHeader("Content-Type", "application/json");
	$req->addHeader("ApiKey", APIKEY);
	foreach ($hdr as $key => $value) {
		$req->addHeader($key, $value);
	}
	$req->setMethod(HTTP_REQUEST_METHOD_POST);
	$req->addRawPostData($tmp);
	$req->sendRequest();
	$reply = $req->getResponseBody();
	$tmp = json_decode($reply, true);
 	return $tmp;
}
function sendxml($data, $url) {
	$str = "<?xml version=\"1.0\"?><request>";
	foreach($data as $key => $value) {
	        $str .= "<$key>$value</$key>";
	}
	$str .= "</request>";
	
	$req =& new HTTP_Request($url);
	$req->addHeader("Content-Type", "text/xml");
	$req->setMethod(HTTP_REQUEST_METHOD_POST);
	$req->addRawPostData($str);
	$req->sendRequest();
	$reply = $req->getResponseBody();
	
	return simplexml_load_string($reply);
}

// Fetch a list of vendors
function doVendor() {
	$data = array();
	$data['apikey'] = APIKEY;
	
	$result = sendjson($data, HD_SERVER."/devices/vendors.json");
	//$result = sendxml($data, HD_SERVER."/devices/vendors.xml");
}

// Fetch a list of all models for a given vendor
function doModel() {
	$data = array();
	$data['apikey'] = APIKEY;
	
	// Fetch model information about all nokia devices
	$data['vendor'] = "Nokia";
	
	$result = sendjson($data, HD_SERVER."/devices/models.json");
	//$result = sendxml($data, HD_SERVER."/devices/models.xml");
}

function doDetect() {
	$data = array();
	$data['apikey'] = APIKEY;
 	$data['User-Agent'] = $_SERVER['HTTP_USER_AGENT'];
//   	$data['ipaddress'] = $_SERVER['REMOTE_ADDR'];
// 	$data['options'] = "geoip, product_info, display";

	// Passing $_SERVER options in is optional.
// 	$data = array_merge ($data, $_SERVER);
	
	$result = sendjson($data, HD_SERVER."/devices/detect.json");
	//$result = sendxml($data, HD_SERVER."/devices/detect.xml");
	return $result;
}
?> 
