<?php
/*
+---------------------------------------------------------------------------+
| OpenX v2.8                                                                |
| ==========                                                                |
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
$Id: account-user-email.php 34688 2009-04-01 16:18:28Z andrew.hill $
*/

// Require the initialisation file
require_once '../../../../init.php';

// Required files
require_once MAX_PATH . '/lib/OA/Dal.php';
require_once MAX_PATH . '/www/admin/config.php';
require_once MAX_PATH . '/lib/max/other/html.php';
require_once MAX_PATH . '/lib/OA/Auth.php';
require_once MAX_PATH . '/lib/OA/Admin/UI/UserAccess.php';
require_once MAX_PATH . '/lib/OA/Admin/TemplatePlugin.php';
require_once MAX_PATH . '/lib/max/Admin/Languages.php';
require_once MAX_PATH . '/lib/max/Plugin/Translation.php';
require_once MAX_PATH .'/lib/OA/Admin/UI/component/Form.php';
require_once MAX_PATH .'/lib/OA/Admin/Template.php';
require_once 'handsetdetection.config.php';
require_once 'handsetdetection.mobileconfig.php';
require_once 'enc.functions.php';

// Security check
OA_Permission::enforceAccount(OA_ACCOUNT_ADMIN, OA_ACCOUNT_MANAGER, OA_ACCOUNT_ADVERTISER, OA_ACCOUNT_TRAFFICKER);

// Prepare an array for storing error messages
$aErrormessage = array();

# Initialize Handsetdetection.com Record is not exist in the database for the Login User.
$norecord 	= true; # set User don't have record in database for Handsetdetection.com
$insert 	= true; # Record to be insert
$update 	= false; # Record to be upadte
$id 		= null; # int(11); Primary Key; NOT NULL
$user_id 	= null; # int(11); Foreign Key; NOT NULL
$husername 	= "";   # varchar(50); NULL
$hsecret 	= "";   # varchar(50); NULL
$hapikey 	= "";   # varchar(50); NULL

# Create a connection to the Handsetdetection database.
$doHandsetdetection = OA_Dal::factoryDO('handsetdetection');

# Find Handsetdetection record of Login User if exist.
$doHandsetdetection->user_id = OA_Permission::getUserId();;

# Find all matching campaigns
$doHandsetdetection->find();

# count no. of rows, if row=1, mean user is already set Handsetdetection record.
$rec = $doHandsetdetection->getRowCount();

# Fetch the record of the User if exist.
$doHandsetdetection->fetch();

# If record exist, then variables to display recrods on the form.
if($rec){
	# collect records
	$id		= $doHandsetdetection->id ;
	$husername	= $doHandsetdetection->husername ;
	$hsecret	= $doHandsetdetection->hsecret 	;
	$hapikey	= $doHandsetdetection->hapikey 	;
	$hsecret 	=  decrypt("$hsecret", "$keycode");
	$hapikey 	=  decrypt("$hapikey", "$keycode");
	$update = true;
	$insert = false;
	$norecord = false;
}

# If the settings page is a submission, deal with the form data
if (isset($_POST['submitok']) && $_POST['submitok'] == 'Save') {
	$submitform 	= true;
	$husername 	= $_POST['husername'];
	$hsecret 	= $_POST['hsecret'];
	$hapikey 	= $_POST['hapikey'];
	
	$doHandsetdetection->user_id 	= OA_Permission::getUserId();
	$hsecret =  encrypt("$hsecret", "$keycode");
	$hapikey =  encrypt("$hapikey", "$keycode");
	$doHandsetdetection->husername 	= "$husername";
	$doHandsetdetection->hsecret 	= "$hsecret";
	$doHandsetdetection->hapikey 	= "$hapikey";
	

	if($update){
		$doHandsetdetection->id = $id;
		$doHandsetdetection->update();
		$insert=false;
	}
	if($insert){
		$doHandsetdetection->insert();
	}
	
	
}




/*-------------------------------------------------------*/
/* HTML framework                                        */
/*-------------------------------------------------------*/
# Display the settings page's header and sections
$title = 'Handsetdetection.com Mobile Ads Configuration Page'; # Title of head and Pages.
$oHeaderModel = new OA_Admin_UI_Model_PageHeaderModel($title);
phpAds_PageHeader('handsetdetection', $oHeaderModel);

# Create and display Form.
$oForm = new OA_Admin_UI_Component_Form("handsetdetection", "POST", $_SERVER['PHP_SELF']);
if(isset($submitform) &&  ($submitform==true) ){
	$msg = '';
	if($update) $msg = 'Update Record';
	if($insert) $msg = 'Insert Record';
	$oForm->addElement('html', 'msg', "$msg");
}

$oForm->forceClientValidation(false);
$oForm->addElement('header', 'Header', 'Handsetdetection.com Settings');

$oForm->addElement('text', 	'husername', 	'Email',  	array('class' => 'medium', 'value'=>"$husername"));
$oForm->addElement('password', 	'hsecret', 	'Secret', 	array('class' => 'medium', 'value'=>"$hsecret"));
$oForm->addElement('password', 	'hapikey', 	'API KEY',	array('class' => 'medium', 'value'=>"$hapikey"));

$oForm->addElement('submit', 'submitok', 'Save');

//get template and display form
$oTpl = new OA_Plugin_Template('handsetdetection.html');
$oTpl->assign('form', $oForm->serialize());

$oTpl->assign('norecord', $norecord);
$oTpl->assign('url', $url);
$oTpl->assign('displayname', $displayname);
$oTpl->assign('link_text', "Please signup at $displayname to get your Username & Secret to activate Mobile banners display on your website.");

$oTpl->display();



// Display the page footer
phpAds_PageFooter();

?>