<?php
/*
Plugin Name: Vote and Review
Plugin URI: http://amfearliath.tk/osclass-vote-and-review
Description: User can vote and review items or users (based on voting plugin by OSClass)
Version: 1.1.0
Author: Liath
Author URI: http://amfearliath.tk
Short Name: vote-and-review
Plugin update URI: vote-and-review
*/

require_once('classes/var.class.php');
include_once('functions.php');

function var_install() {
    $var = new v_a_r;
    $var->_var_install();
}

function var_uninstall() {
    $var = new v_a_r;
    $var->_var_uninstall();
}

function var_admin_style() {
    $params = Params::getParamsAsArray();
    if (isset($params['file'])) {
        $plugin = explode("/", $params['file']);
        if ($plugin[0] == 'vote-and-review') {
            osc_enqueue_style('var-styles-admin', osc_plugin_url('vote-and-review/assets/css/admin.css').'admin.css');
        }
    }
}
function var_style() {
    osc_enqueue_style('var-styles', osc_plugin_url('vote-and-review/assets/css/var.css').'var.css');
    if (osc_get_preference('load_FA', 'vote_and_review') == '1') {
        osc_enqueue_style('var-styles-fa', osc_plugin_url('vote-and-review/assets/css/font-awesome.min.css').'font-awesome.min.css');    
    } if (osc_get_preference('load_bxSlider', 'vote_and_review') == '1') {
        osc_enqueue_style('var-styles-bxSlider', osc_plugin_url('vote-and-review/assets/css/jquery.bxslider.css').'jquery.bxslider.css');    
    }
}

function var_script() {
    echo '<script type="text/javascript" src="'.osc_plugin_url('vote-and-review/assets/js/var.js').'var.js"></script>';
    if (osc_get_preference('load_bxSlider', 'vote_and_review') == '1') {
        echo '<script type="text/javascript" src="'.osc_plugin_url('vote-and-review/assets/js/jquery.bxslider.min.js').'jquery.bxslider.min.js"></script>';    
    }
}

function var_configuration() {
    osc_plugin_configure_view(osc_plugin_path(__FILE__));
}

function var_admin_menu() {    
    if (osc_version() < 311) {
        echo '<h3><a href="#">' . __('Vote and Review', 'vote-and-review') . '</a></h3>
        <ul>
            <li><a href="' . osc_admin_render_plugin_url(osc_plugin_folder(__FILE__).'admin/admin.php') . '">&raquo; ' . __('Settings', 'vote-and-review') . '</a></li>
            <li><a href="' . osc_admin_configure_plugin_url(osc_plugin_folder(__FILE__).'index.php') . '">&raquo; ' . __('Categories', 'vote-and-review') . '</a></li>
            <li><a href="' . osc_admin_render_plugin_url(osc_plugin_folder(__FILE__).'admin/help.php') . '">&raquo; ' . __('Help', 'vote-and-review') . '</a></li>
        </ul>';
    } else {
        osc_add_admin_menu_page( __('Voting', 'vote-and-review'), osc_admin_render_plugin_url(osc_plugin_folder(__FILE__).'admin/admin.php'), 'vote_and_review', 'administrator' );
        osc_add_admin_submenu_page('vote_and_review', __('Settings', 'vote-and-review'), osc_admin_render_plugin_url(osc_plugin_folder(__FILE__).'admin/admin.php'), 'vote_and_review_settings', 'administrator');
        osc_add_admin_submenu_page('vote_and_review', __('Categories', 'vote-and-review'), osc_admin_configure_plugin_url(osc_plugin_folder(__FILE__).'index.php'), 'vote_and_review_categories', 'administrator');
        osc_add_admin_submenu_page('vote_and_review', __('Help', 'vote-and-review'), osc_admin_render_plugin_url(osc_plugin_folder(__FILE__).'admin/help.php'), 'vote_and_review_help', 'administrator');
    }
}
    
osc_register_plugin(osc_plugin_path(__FILE__), 'var_install') ;
osc_add_hook(osc_plugin_path(__FILE__).'_uninstall', 'var_uninstall') ;

osc_add_hook('header', 'var_style');
osc_add_hook('admin_header', 'var_admin_style');
osc_add_hook(osc_plugin_path(__FILE__).'_configure', 'var_configuration');

osc_add_hook('delete_item', 'var_delItem');
osc_add_hook('delete_user', 'var_delUser');

if (osc_version() < 311) {
    osc_add_hook('footer', 'var_script');
    osc_add_hook('admin_menu', 'var_admin_menu');
} else {
    osc_register_script('var-script', osc_plugin_url('vote-and-review/assets/js/var.js') . 'var.js', array('jquery'));
    osc_enqueue_script('var-script');
    
    if (osc_get_preference('load_bxSlider', 'vote_and_review')) {
        osc_register_script('var-bxSlider', osc_plugin_url('vote-and-review/assets/js/jquery.bxslider.min.js') . 'jquery.bxslider.min.js', array('jquery'));
        osc_enqueue_script('var-bxSlider');    
    }
    
    osc_add_hook('admin_menu_init', 'var_admin_menu');
}

$file = '-';
if (Params::getParam('file')!='') {
    $file = Params::getParam('file');
}

if (Params::getParam('page') == 'plugins' && strpos('vote-and-review/admin/help.php',$file) === 0) {
    osc_add_filter('custom_plugin_title', 'var_title_help');
}
function var_title_help($title) {
    return __('Help', 'voting');
}

if (Params::getParam('page') == 'plugins' && strpos('vote-and-review/admin/admin.php',$file) === 0) {
    osc_add_filter('custom_plugin_title', 'var_title_config');
}
function var_title_config($title) {
    return __('Configuration', 'voting');
}                        
?>