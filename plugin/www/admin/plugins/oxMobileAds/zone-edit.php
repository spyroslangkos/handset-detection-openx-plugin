<?php

/*
+---------------------------------------------------------------------------+
| OpenX v2.8                                             |
| ==========                            |
|                                                                           |
| Copyright (c) 2003-2009 OpenX Limited                                     |
| For contact details, see: http://www.openx.org/                           |
|                                                                           |
| This program is free software; you can redistribute it and/or modify      |
| it under the terms of the GNU General Public License as published by      |
| the Free Software Foundation; either version 2 of the License, or         |
| (at your option) any later version.                                       |
|                                                                           |
| This program is distributed in the hope that it will be useful,           |
| but WITHOUT ANY WARRANTY; without even the implied warranty of            |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
| GNU General Public License for more details.                              |
|                                                                           |
| You should have received a copy of the GNU General Public License         |
| along with this program; if not, write to the Free Software               |
| Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA |
+---------------------------------------------------------------------------+
$Id: zone-edit.php 34688 2009-04-01 16:18:28Z andrew.hill $
*/

// Require the initialisation file
require_once '../../../../init.php';

// Required files
require_once MAX_PATH . '/lib/OA/Dal.php';
require_once MAX_PATH . '/www/admin/config.php';
require_once MAX_PATH . '/www/admin/lib-statistics.inc.php';
require_once MAX_PATH . '/www/admin/lib-zones.inc.php';
require_once MAX_PATH . '/www/admin/lib-size.inc.php';
require_once MAX_PATH . '/lib/max/Admin_DA.php';
require_once MAX_PATH . '/lib/max/other/html.php';
require_once MAX_PATH .'/lib/OA/Admin/UI/component/Form.php';
require_once MAX_PATH . '/lib/OA/Central/AdNetworks.php';
require_once MAX_PATH . '/lib/OA/Admin/NumberFormat.php';
require_once 'handsetdetection.config.php';
require_once 'handsetdetection.mobileconfig.php';

// Added Files
require_once MAX_PATH . '/lib/OA/Admin/TemplatePlugin.php';

// Register input variables
phpAds_registerGlobalUnslashed(
    'zonename',
    'description',
    'delivery',
    'sizetype',
    'size',
    'width',
    'height',
    'submit',
    'comments'
);

/*-------------------------------------------------------*/
/* Affiliate interface security                          */
/*-------------------------------------------------------*/

OA_Permission::enforceAccount(OA_ACCOUNT_MANAGER, OA_ACCOUNT_TRAFFICKER);
OA_Permission::enforceAccessToObject('affiliates', $affiliateid);
OA_Permission::enforceAccessToObject('zones', $zoneid, true);

if (OA_Permission::isAccount(OA_ACCOUNT_TRAFFICKER)) {
    if (!empty($zoneid)) {
        OA_Permission::enforceAllowed(OA_PERM_ZONE_EDIT);
    } else {
        OA_Permission::enforceAllowed(OA_PERM_ZONE_ADD);
    }
}


/*-------------------------------------------------------*/
/* Initialise data                                       */
/*-------------------------------------------------------*/
if (!empty($zoneid)) {
    $doZones = OA_Dal::factoryDO('zones');
    $doZones->zoneid = $zoneid;
    if ($doZones->find() && $doZones->fetch()) {
        $zone = $doZones->toArray();
    }

    if ($zone['width'] == -1) $zone['width'] = '*';
    if ($zone['height'] == -1) $zone['height'] = '*';
}
else {
    $doAffiliates = OA_Dal::factoryDO('affiliates');
    $doAffiliates->affiliateid = $affiliateid;

    if ($doAffiliates->find() && $doAffiliates->fetch() && $affiliate = $doAffiliates->toArray())
        $zone["zonename"] = $affiliate['name'].' - ';
    else {
        $zone["zonename"] = '';
    }

    $zone['zonename']        .= $GLOBALS['strDefault'];
    $zone['description']     = '';
    $zone['width']           = '468';
    $zone['height']          = '60';
    $zone['delivery']        = phpAds_ZoneBanner;
    $zone['comments'] = null;
}
$zone['affiliateid']     = $affiliateid;

/*-------------------------------------------------------*/
/* MAIN REQUEST PROCESSING                               */
/*-------------------------------------------------------*/
//build form
 $zoneForm = buildZoneForm($zone);
// 
if ($zoneForm->validate()) {
	//     process submitted values
	$errors = processForm($zoneForm);
// 	$errors = processForm($zoneForm);
// 	if(!empty($errors)) {
// 	}
}
//display the page - show any validation errors that may have occured
displayPage($zone, $zoneForm, $errors);

/*-------------------------------------------------------*/
/* Build form                                            */
/*-------------------------------------------------------*/
function buildZoneForm($zone)
{
    global $conf;
    global $mobileZoneSize, $mobileSize;

    $img = "<img src='".OX::assetPath()."/images/icon-zone.gif' align='absmiddle'>&nbsp;";
    // Initialise Ad  Networks
    $oAdNetworks = new OA_Central_AdNetworks();

    $form = new OA_Admin_UI_Component_Form("zoneform", "POST", $_SERVER['PHP_SELF']);
    $form->forceClientValidation(false);
    $form->addElement('hidden', 'zoneid', $zone['zoneid']);
    $form->addElement('hidden', 'affiliateid', $zone['affiliateid']);

    $form->addElement('header', 'mobile_zone_info', "Mapping Zone for [ {$zone['zonename']} ]");

	###### START
	# Get all zones id if present
	$isMobileZone = false;
	$dMobileZones  = array();
	$dd = OA_Dal::factoryDO('mobilezones');
	$dd->masterzoneid = $zone['zoneid'];
	$dd->find();
	$countM = $dd->getRowCount();
	$dd->fetch();
	if(false != $countM){ # If zone' is mapped in the database.
		$isMobileZone = true;
		$dMobileZones[1] = $dd->mz1;
		$dMobileZones[2] = $dd->mz2;
		$dMobileZones[3] = $dd->mz3;
		$dMobileZones[4] = $dd->mz4;
	}
	###### END 

	$allzones = Admin_DA::getZones(array('publisher_id' => $zone['affiliateid']));
	$relatedZones = array();
	$relatedZones[0] = '---Select Zone---';
	$mobileSizes = array();
	foreach($allzones as $allzone)
	{
		if($allzone['zone_id'] != $zone['zoneid'])
			$relatedZones["{$allzone['zone_id']}"] = "{$allzone['name']}";
	}
	# $mobileSize contain total types of Banners to upload e.g. xlarge, large, ... etc
	for($inc=1; $inc <= count($mobileSize); $inc++){
		if(true === $isMobileZone){
		 $form->addElement('static', "mz{$inc}", "", "$inc- <b>Currently Selected Zone</b>: [ {$relatedZones[$dMobileZones[$inc]]} ]");
		}
		$form->addElement('select', "mobilezone[$inc]", "$img {$mobileZoneSize[$inc]}", $relatedZones);
	}

    $form->addElement('submit', 'submit', 'Save');


    return $form;
}


/*-------------------------------------------------------*/
/* Process submitted form                                */
/*-------------------------------------------------------*/
/**
 * Processes submit values of zone form
 *
 * @param OA_Admin_UI_Component_Form $form form to process
 * @return An array of Pear::Error objects if any
 */
function processForm($form)
{
	$norecord 	= true; # set User don't have record in database for Handsetdetection.com
	$insert 	= true; # Record to be insert
	$update 	= false; # Record to be upadte
	$aFields = $form->exportValues();

	$doMobilezones = OA_Dal::factoryDO('mobilezones');
	$doMobilezones->masterzoneid = $aFields['zoneid'];

	$doMobilezones->find();
	$rec = $doMobilezones->getRowCount();
	$doMobilezones->fetch();
	# If record exist, then variables to display recrods on the form.
	if($rec){
		# collect records
		$id	= $doMobilezones->id ;
		$mz1 	= $doMobilezones->mz1 ;
		$mz2 	= $doMobilezones->mz2 ;
		$mz3 	= $doMobilezones->mz3 ;
		$mz4 	= $doMobilezones->mz4 ;
		$mz5 	= $doMobilezones->mz5 ;

		$update = true;
		$insert = false;
		$norecord = false;
	}
	$doMobilezones->mz1 = ($aFields['mobilezone'][1] != false) ? $aFields['mobilezone'][1] : 0;
	$doMobilezones->mz2 = ($aFields['mobilezone'][2] != false) ? $aFields['mobilezone'][2] : 0;
	$doMobilezones->mz3 = ($aFields['mobilezone'][3] != false) ? $aFields['mobilezone'][3] : 0;
	$doMobilezones->mz4 = ($aFields['mobilezone'][4] != false) ? $aFields['mobilezone'][4] : 0;
	$doMobilezones->mz5 = ($aFields['mobilezone'][5] != false) ? $aFields['mobilezone'][5] : 0;
	if($update){
		$doMobilezones->update();
		$insert=false;
	}
	if($insert){
		$doMobilezones->insert();
	}
        $page = '/plugins/oxMobileAds/affiliate-zones.php';
        OX_Admin_Redirect::redirect($page);


}


/*-------------------------------------------------------*/
/* Display page                                          */
/*-------------------------------------------------------*/
function displayPage($zone, $form, $zoneErrors = null)
{
    //header and breadcrumbs
    $pageName = basename($_SERVER['PHP_SELF']);
    $agencyId = OA_Permission::getAgencyId();
    $aEntities = array('affiliateid' => $zone['affiliateid'], 'zoneid' => $zone['zoneid']);

    $aOtherPublishers = Admin_DA::getPublishers(array('agency_id' => $agencyId));
    $aOtherZones = Admin_DA::getZones(array('publisher_id' => $zone['affiliateid']));


	# Display the settings page's header and sections
	$title = 'Mobile Mapping Zones'; # Title of head and Pages.
	$oHeaderModel = new OA_Admin_UI_Model_PageHeaderModel($title);
	phpAds_PageHeader('affiliates-zones', $oHeaderModel);

//    MAX_displayNavigationZone($pageName, $aOtherPublishers, $aOtherZones, $aEntities);

    //get template and display form
    require_once MAX_PATH . '/lib/OA/Admin/Template.php';
    $oTpl = new OA_Plugin_Template('zones-edit.html');
//     $oTpl = new OA_Admin_Template('zone-edit.html');
    $oTpl->assign('ADMINPATH', '/www/admin/');
    $oTpl->assign('zoneid', $zone['zoneid']);
    $oTpl->assign('zoneHeight', $zone["height"]);
    $oTpl->assign('zoneWidth', $zone["width"]);

    $oTpl->assign('zoneErrors', $zoneErrors);
    $oTpl->assign('form', $form->serialize());

    $oTpl->display();

    //footer
    phpAds_PageFooter();
}

?>
