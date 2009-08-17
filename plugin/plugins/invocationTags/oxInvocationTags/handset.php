<?php
require_once '../../../init.php';
require_once LIB_PATH . '/Extension/invocationTags/InvocationTags.php';
require_once MAX_PATH . '/lib/max/Plugin/Translation.php';
require_once MAX_PATH . '/lib/OA/Dal.php';
require_once MAX_PATH. '/lib/pear/HTTP/Request.php';
require_once MAX_PATH . '/www/admin/plugins/oxMobileAds/enc.functions.php';
require_once MAX_PATH . '/www/admin/plugins/oxMobileAds/handsetdetection.config.php';
require_once MAX_PATH . '/www/admin/plugins/oxMobileAds/handsetdetection.mobileconfig.php';
require_once MAX_PATH . '/www/admin/plugins/oxMobileAds/handsetdetection.api.php';
require_once MAX_PATH . '/www/admin/plugins/oxMobileAds/class.wsse.php';
define('HD_SERVER','http://api-us1.handsetdetection.com');
$host = $_SERVER['HTTP_HOST'];
$errorM = '';
# Check if the file is being access by any website and not access directly;
// if(empty($_SERVER['HTTP_REFERER']))
// return false;

# if phpfile is set from the requesting file, then proceed
# phpfile is used for calling the correct tags generating php file.
$e = $n = substr(md5(rand()), 0, 8);

if(isset($_REQUEST['phpfile'])){

	if( empty($_REQUEST['zoneid']) || empty($_REQUEST['indicator']) )
	return false;

	# Change zoneid if the user visit Ads/Banners from Mobile
	$zoneid = changeZone($_REQUEST['indicator'], $_REQUEST['zoneid']); # userid, zoneid;

	# if any way zoneid could not set, then set it to the default value which passes through referer file.
// 	if(false == $zoneid) $zoneid = $_REQUEST['zoneid'];

	$file = ''; # .php file use to display images
	$file = $_REQUEST['phpfile'];
	$path = "http://$host/www/delivery/$file";
	
	$queryString = explode('&', $_SERVER['QUERY_STRING']);
	# Generate New Query String
	$newQueryString = "zoneid=$zoneid&error=$errorM"; 
	foreach($queryString as $k=>$v){
		if( (false === stripos($v, 'zoneid')) && (false === stripos($v, 'indicator')) && (false === stripos($v, 'phpfile')) )
			$newQueryString .= "&amp;$v";
	}
	header("Location: $path?$newQueryString");
	exit;
}# end phpfile



##########################################################################################################################################
# 							FUNCTIONS
##########################################################################################################################################

#######################
# Get all Mobile Zones
#######################
# Pass UserID and ZoneID;
# $userid: used to detect Api information for specific user;
# $zoneid: used to get the zone id to display Mobile Ads;
function changeZone($userid=0, $zoneid=0){
	if( (false == $userid) || (false == $zoneid) )
	return false;
	global $keycode, $mobileSize, $errorM;

	# Check if Handset Account is exsist in OpenX

	# if account, then test for Mobile Request, else return $zoneid as passed.
		# Get  Handset Detection's information username, apikey, secret;
		$doHandsetdetection = OA_Dal::factoryDO('handsetdetection');
		$doHandsetdetection->user_id = $userid;
		$doHandsetdetection->find();
		$countH = $doHandsetdetection->getRowCount();
		$doHandsetdetection->fetch();
		$husername	= $doHandsetdetection->husername;
		$hsecret	= $doHandsetdetection->hsecret;
		$hapikey	= $doHandsetdetection->hapikey;
		$hsecret 	=  decrypt($hsecret, "$keycode");
		$hapikey 	=  decrypt($hapikey, "$keycode");
		if(false != $countH){ #if handset detection's username, apikey and secret find in the database.

			# Define Constant used in handsetdetection.api.php functions;
			if(!empty($husername))	define('USERNAME',	$husername);
			if(!empty($hsecret))	define('SECRET',	$hsecret);
			if(!empty($hapikey))	define('APIKEY', 	$hapikey);
		
			# Send Api Request to Handset Detection and Get required information about Request Type; Regular/Mobile request.
			$mobileInfo = array();
			$mobileInfo = doDetect();
		

			################
			# Mobile Request
			################
			# If Mobile Request
			# change ZoneID according to the size of Mobile Screen.
			if(!empty($mobileInfo) && $mobileInfo['message']=='OK') # if $message=='OK', then the application is mobile device.
			{
				# Change zone id to dislay banner on Mobile by checking Mobile Screen Size;
				# Get Mobile Zone's Informations
				$doMobilezones = OA_Dal::factoryDO('mobilezones');
				$doMobilezones->masterzoneid = $zoneid;
				$doMobilezones->find();
				$countM = $doMobilezones->getRowCount();
				$doMobilezones->fetch();
				if(false != $countM){ # If zone' is mapped in the database.
			
					# Make Array and store Mobile Screen display information
					# MR = MobileResolution
					$MR = array();
					$MR['width']  = $mobileInfo['display']['max_image_width'];
					$MR['height'] = $mobileInfo['display']['max_image_height'];
					$mz=0;
		
// 	 				if( ($MR['width']>=$mobileSize['extralarge']['width']) && ($MR['height'] >= $mobileSize['extralarge']['height']) )
					if( $MR['width']>=$mobileSize['extralarge']['width'] )
					{
					$mz=1;
					}else{
						if( $MR['width']>=$mobileSize['large']['width'] )
						{
						$mz=2;
						}else{
							if( $MR['width']>=$mobileSize['medium']['width'] )
							{
							$mz=3;
							}else{
								if( $MR['width']>=$mobileSize['small']['width'] )
								{
								$mz=4;
								}
							}
						}
					}
					# Check Which MobileZone is detect
					# Get related Mobile Zone ID
					switch($mz){
						case 1:
							$zoneid = $doMobilezones->mz1;
						break;
						case 2:
							$zoneid = $doMobilezones->mz2;
						break;
						case 3:
							$zoneid = $doMobilezones->mz3;
						break;
						case 4:
							$zoneid = $doMobilezones->mz4;
						break;
						default:
							$zoneid = $doMobilezones->mz4;
						break;
					}
				}else{
				 $errorM .= 'Error:Mobile Zone';
				}
			
			}# End Mobile Request
			else{
			 ################
			 # Window Request
			 ################
			 # If Normal Request
			 $errorM .= 'Normal Request';
			 return $zoneid; # if request is from Window, not from Mobile
			 
			}
			

			#################
			# Regular Request
			#################
			# If regular request, then don't change ZoneID
			# Remaing Zone id as it is.
			# End Regular Request
		}else{
		 $errorM .= 'Handset error';
		}
	return $zoneid; #return zoneid as passed if Handset Detection account is not present in OpenX for the User
}
?>