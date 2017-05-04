<?php
/*
Plugin Name: Toggle Item Status
Plugin URI: http://amfearliath.tk/osclass-toggle-item-status
Description: User can mark items as sold or make them available again
Version: 1.0.0
Author: Liath
Author URI: http://amfearliath.tk
Short Name: toggle_item_status
Plugin update URI: toggle-item-status
*/
 
require_once('classes/tis.class.php');

if (Params::getParam('ti_status')) {
    t_i_s::tis_change_status();        
} 
?>