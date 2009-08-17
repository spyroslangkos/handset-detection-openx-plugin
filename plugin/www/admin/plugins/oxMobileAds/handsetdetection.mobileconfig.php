<?php
# Use prefix to display input field text on Banner upload page, e.g. Prefix X-Large ( n X n)
$prefixMobile = '';
$prerixMapping= '';

# Store name of the Mobile types as Names
$mobileTypes = array();
$mobileTypes[0] = 'extralarge';
$mobileTypes[1] = 'large';
$mobileTypes[2] = 'medium';
$mobileTypes[3] = 'small';

$mobileSize = array();
$mobileSize['extralarge']['default'] 	= 0; # either default image if no image found; 0=NO, 1=YES
$mobileSize['extralarge']['zone'] 	= 1; # No. of zones in sequence
$mobileSize['extralarge']['mz'] 	= 'mz1'; # field name as defined in database
$mobileSize['extralarge']['label'] 	= 'X-Large';
$mobileSize['extralarge']['width'] 	= 300;
$mobileSize['extralarge']['height']	= 50;

$mobileSize['large']['default'] 	= 0; 
$mobileSize['large']['zone'] 		= 3; 
$mobileSize['large']['mz']	 	= 'mz2';
$mobileSize['large']['label'] 		= 'Extra';
$mobileSize['large']['width'] 		= 216;
$mobileSize['large']['height'] 		= 36;

$mobileSize['medium']['default'] 	= 1;
$mobileSize['medium']['zone'] 		= 2;
$mobileSize['medium']['mz'] 		= 'mz3'; 
$mobileSize['medium']['label'] 		= 'Meduim';
$mobileSize['medium']['width'] 		= 168;
$mobileSize['medium']['height'] 	= 28;

$mobileSize['small']['default'] 	= 0; 
$mobileSize['small']['zone'] 		= 4; 
$mobileSize['small']['mz'] 		= 'mz4';
$mobileSize['small']['label'] 		= 'Small';
$mobileSize['small']['width'] 		= 120;
$mobileSize['small']['height'] 		= 20;

#################################
# Set text for  Multiple Banners
#################################
$bannerLabels=array();
# Set input field text for uploading banner
# e.g. "X-Large (n X n)";
foreach($mobileSize as $key=>$val){
	$bannerLabels["$key"]['strNewBannerFile'] = $prefixMobile. ' '. $mobileSize["$key"]['label']. ' ( '. $mobileSize["$key"]['width'] . ' X '. $mobileSize["$key"]['height']. ' )';
}

$mobileBannerTypes=array(); //Input field names of uploaded banners
$mobileBannerTypes['normal']	= 'normal'; # values is the name of the input field use on the form to upload file.
$mobileBannerTypes['extralarge']= 'extralarge'; 
$mobileBannerTypes['large']	= 'large'; 
$mobileBannerTypes['medium']	= 'medium'; 
$mobileBannerTypes['small']	= 'small';

$mobileBannerWidthHeight=array(); # Mobile Banner's Width and Height.
$mobileBannerWidthHeight["{$mobileBannerTypes['normal']}"]["width"]	= $aFile['width'];;
$mobileBannerWidthHeight["{$mobileBannerTypes['normal']}"]["height"]	= $aFile['height'];;
$mobileBannerWidthHeight["{$mobileBannerTypes['extralarge']}"]["width"]	= $mobileSize['extralarge']['width'];
$mobileBannerWidthHeight["{$mobileBannerTypes['extralarge']}"]["height"]= $mobileSize['extralarge']['height'];
$mobileBannerWidthHeight["{$mobileBannerTypes['large']}"]["width"]	= $mobileSize['large']['width'];
$mobileBannerWidthHeight["{$mobileBannerTypes['large']}"]["height"]	= $mobileSize['large']['height'];
$mobileBannerWidthHeight["{$mobileBannerTypes['medium']}"]["width"]	= $mobileSize['medium']['width'];
$mobileBannerWidthHeight["{$mobileBannerTypes['medium']}"]["height"]	= $mobileSize['medium']['height'];
$mobileBannerWidthHeight["{$mobileBannerTypes['small']}"]["width"]	= $mobileSize['small']['width'];
$mobileBannerWidthHeight["{$mobileBannerTypes['small']}"]["height"]	= $mobileSize['small']['height'];

#################################
# Set text for  Mapping Zone
#################################
$mobileZoneSize = array();
$i=0;
foreach($mobileSize as $key=>$val){
	$i++;
	$mobileZoneSize[$i] = $prefixMobile. ' '. $mobileSize["$key"]['label']. ' ( '. $mobileSize["$key"]['width'] . ' X '. $mobileSize["$key"]['height']. ' )';
}


?> 
