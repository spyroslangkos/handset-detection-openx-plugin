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
$Id: demoUI-page.php 30820 2009-01-13 19:02:17Z andrew.hill $
*/

/**
 * Table Definition for handset
 */
require_once MAX_PATH.'/lib/max/Dal/DataObjects/DB_DataObjectCommon.php';

class DataObjects_Mobilezones extends DB_DataObjectCommon
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'mobilezones';    // table name
    public $id;                        	// Int(11) 
    public $masterzoneid;               // Int(11) 
    public $mz1;                   		// Int(11); MobileZone1 ID
    public $mz2;                   		// Int(11); MobileZone2 ID
    public $mz3;                   		// Int(11); MobileZone3 ID
    public $mz4;                   		// Int(11); MobileZone4 ID
    public $mz5;                   		// Int(11); MobileZone5 ID

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Mobilezones',$k,$v); }

    var $defaultValues = array();

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

}
?>