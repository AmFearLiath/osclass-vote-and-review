<?php
/*
 *      OSCLass – software for creating and publishing online classified
 *                           advertising platforms
 *
 *                        Copyright (C) 2010 OSCLASS
 *
 *       This program is free software: you can redistribute it and/or
 *     modify it under the terms of the GNU Affero General Public License
 *     as published by the Free Software Foundation, either version 3 of
 *            the License, or (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful, but
 *         WITHOUT ANY WARRANTY; without even the implied warranty of
 *        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *             GNU Affero General Public License for more details.
 *
 *      You should have received a copy of the GNU Affero General Public
 * License along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
  
if (!defined('OC_ADMIN') || OC_ADMIN!==true) exit('Access is not allowed.');
require_once(osc_plugin_path(dirname(dirname(__FILE__))) . '/classes/var.class.php');
$var = new v_a_r;
$import = $var->_var_checkImport();

$action = Params::getParam('plugin_action');

if (Params::getParam('plugin_action') == 'saveSettings') {        
    $pref = $var->_var_sect();
    if(Params::getParam('type_vote') == 'user') { $open = '0'; $user = '1'; } 
    elseif(Params::getParam('type_vote') == 'open') { $open = '1'; $user = '0'; }
                    
    $opts = array(
        'load_FA'                   => array(Params::getParam('load_FA'), $pref, 'BOOLEAN'),
        'load_bxSlider'             => array(Params::getParam('load_bxSlider'), $pref, 'BOOLEAN'),
        'load_sellerVerification'   => array(Params::getParam('load_sellerVerification'), $pref, 'BOOLEAN'),
        'user_voting'               => array(Params::getParam('user_voting'), $pref, 'BOOLEAN'),
        'item_voting'               => array(Params::getParam('item_voting'), $pref, 'BOOLEAN'),
        'open'                      => array($open, $pref, 'BOOLEAN'),
        'user'                      => array($user, $pref, 'BOOLEAN')
    );
    
    if ($var->_var_install($opts)) {        
        if(osc_version() < 300) {            
            echo '<div style="text-align:center; font-size:20px; background-color:#B0EFC0;"><p>'.__('<strong>All Settings saved.</strong> Your plugin is now configured', 'vote-and-review').'.</p></div>' ;
            osc_reset_preferences();            
        } else {            
            ob_get_clean();
            osc_add_flash_ok_message(__('<strong>All Settings saved.</strong> Your plugin is now configured', 'vote-and-review'), 'admin');
            osc_admin_render_plugin( osc_plugin_folder(__FILE__) . 'admin.php');            
        }        
    } else {        
        if(osc_version() < 300) {            
            echo '<div style="text-align:center; font-size:20px; background-color:#EFB0B0;"><p>'.__('<strong>Error.</strong> Your settings can not be saved, please try again', 'vote-and-review').'.</p></div>' ;
            osc_reset_preferences();            
        } else {            
            ob_get_clean();
            osc_add_flash_error_message(__('<strong>Error.</strong> Your settings can not be saved, please try again', 'vote-and-review'), 'admin');
            osc_admin_render_plugin( osc_plugin_folder(__FILE__).'admin.php');            
        }        
    }
}
    
elseif (Params::getParam('plugin_action') == 'import') {
    if ($var->_var_import()) {
        if(osc_version() < 300) {            
            echo '<div style="text-align:center; font-size:20px; background-color:#B0EFC0;"><p>'.__('<strong>Import done.</strong> All ratings from old voting plugin are transfered. Now you can uninstall old voting plugin.', 'vote-and-review').'.</p></div>' ;
            osc_reset_preferences();            
        } else {            
            ob_get_clean();
            osc_add_flash_ok_message(__('<strong>Import done.</strong> All ratings from old voting plugin are transfered. Now you can uninstall old voting plugin.', 'vote-and-review'), 'admin');
            osc_admin_render_plugin( osc_plugin_folder(__FILE__).'admin.php');            
        }        
    } else {
        if(osc_version() < 300) {            
            echo '<div style="text-align:center; font-size:20px; background-color:#EFB0B0;"><p>'.__('<strong>ERROR</strong> There was an Error while transfering the old votings, please try again', 'vote-and-review').'.</p></div>' ;
            osc_reset_preferences();            
        } else {            
            ob_get_clean();
            osc_add_flash_error_message(__('<strong>ERROR</strong> There was an Error while transfering the old votings, please try again', 'vote-and-review'), 'admin');
            osc_admin_render_plugin( osc_plugin_folder(__FILE__).'admin.php');            
        }        
    }
}

?>

<div id="settings_form" style="padding-left: 15px; padding-right: 15px;">
    <div style="padding: 20px;">
        <div style="width: 100%;">
            
            <b style="font-size: 2em;"><?php _e('Votes and Reviews', 'vote-and-review');?></b>
            <p><?php _e('Remake by Liath based on Voting by OSClass', 'vote-and-review');?></p>
            <br /><br /><br />
            
            <?php if (is_array($import)) { ?>
            <b style="font-size: 1.5em;"><?php _e('Import', 'vote-and-review');?></b>
            <p><?php _e('Click here to import all ratings from old voting plugin to this plugin. Please don\'t refresh page while import is running', 'vote-and-review');?></p>
            <p><?php echo sprintf(__('There are %d item and %d user votings to import', 'vote-and-review'), $import['items'], $import['users']); ?></p>
            <div class="formGroup">
                <div id="importStart" class="buttonSubmit">
                    <form id="startImport" action="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=<?php echo osc_plugin_folder(__FILE__).'admin.php'; ?>" method="POST">
                        <input type="hidden" name="plugin_action" value="import" />
                        <input class="btn btn-submit dark" id="buttonImport" type="submit" value="<?php _e('Import', 'vote-and-review'); ?>"/>
                        <label for="buttonImport"><?php _e('Import', 'vote-and-review'); ?></label>
                    </form>
                </div>
                <div id="importRunning" style="display: none;">
                    <i class="fa fa-spinner fa-spin"></i> <?php _e('Import is working', 'vote-and-review'); ?>
                </div>
            </div>
            <div style="clear: both;"></div>
            <script>
            $(document).ready(function(){
                $("form#startImport").on("submit", function(){
                    $("#importRunning").fadeToggle("fast");    
                });
            });
            </script>
            
            <br /><hr /><br />
            <?php } ?>
                
            <form id="saveSettings" action="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=<?php echo osc_plugin_folder(__FILE__).'admin.php'; ?>" method="POST">
                <input type="hidden" name="plugin_action" value="saveSettings" />
                
                <b style="font-size: 1.5em;"><?php _e('Main settings', 'vote-and-review');?></b>
                <p><?php _e('Here you can determine whether the necessary libraries to be loaded as well. If they are already loaded by other scripts, disable it. If plugin <em>seller verification</em> is installed, you can activate it, to show a banner on best rated items.', 'vote-and-review');?></p>
                
                <div class="formGroup">                        
                    <div class="checkSlide">                        
                        <input type="checkbox" value="1" id="load_FA" name="load_FA" <?php if(osc_get_preference('load_FA', 'vote_and_review') == '1') echo 'checked="checked"';?>/>
                        <label for="load_FA"></label> 
                    </div>
                    <div><?php _e('Load Font Awesome (only deactivate, if loaded by other scripts)', 'vote-and-review');?></div>
                </div>
                
                <div class="formGroup">                        
                    <div class="checkSlide">                        
                        <input type="checkbox" value="1" id="load_bxSlider" name="load_bxSlider" <?php if(osc_get_preference('load_bxSlider', 'vote_and_review') == '1') echo 'checked="checked"';?>/>
                        <label for="load_bxSlider"></label> 
                    </div>
                    <div><?php _e('Load jQuery.bxSlider.js (only deactivate, if loaded by other scripts)', 'vote-and-review');?></div>
                </div>
                <?php if (function_exists('sellerver_user_detail')) { ?>
                <div class="formGroup">                        
                    <div class="checkSlide">                        
                        <input type="checkbox" value="1" id="load_sellerVerification" name="load_sellerVerification" <?php if(osc_get_preference('load_sellerVerification', 'vote_and_review') == '1') echo 'checked="checked"';?>/>
                        <label for="load_sellerVerification"></label> 
                    </div>
                    <div><?php _e('Do you want to show verificated seller on best rated items and user?', 'vote-and-review');?></div>
                </div>
                <?php } ?>
                
                <br /><hr /><br />
                <b style="font-size: 1.5em;"><?php _e('Item settings', 'vote-and-review');?></b>
                <p><?php _e('Here you can activate the votes and reviews for items and determine who can vote', 'vote-and-review');?></p>
                
                <div class="formGroup">                        
                    <div class="checkSlide">                        
                        <input type="checkbox" value="1" id="item_voting" name="item_voting" <?php if(osc_get_preference('item_voting', 'vote_and_review') == '1') echo 'checked="checked"';?>/>
                        <label for="item_voting"></label> 
                    </div>
                    <div><?php _e('Enable for items', 'vote-and-review') ; ?></div>
                </div>
                <div class="formGroup">                        
                    <div class="roundRadio">
                        <input type="radio" value="user" id="type_vote_user" name="type_vote" <?php if(!osc_get_preference('item_voting', 'vote_and_review')){ echo 'disabled=""'; }?> <?php if(osc_get_preference('user', 'vote_and_review') == '1') echo 'checked="checked"'?> />
                        <label for="type_vote_user"></label>
                    </div>
                    <div><?php _e("Only registered users can vote", 'vote-and-review'); ?></div>
                </div>
                <div class="formGroup">                        
                    <div class="roundRadio">
                        <input type="radio" value="open" id="type_vote_open" name="type_vote" <?php if(!osc_get_preference('item_voting', 'vote_and_review')){ echo 'disabled=""'; }?> <?php if(osc_get_preference('open', 'vote_and_review') == '1') echo 'checked="checked"'?> />
                        <label for="type_vote_open"></label>
                    </div>
                    <div><?php _e('All can vote the items', 'vote-and-review'); ?></div>
                </div>
                
                <br /><hr /><br />
                <b style="font-size: 1.5em;"><?php _e('User settings', 'vote-and-review');?></b>
                <p><?php _e('Here you can activate the votes and reviews for users. Only registered user can vote other user.', 'vote-and-review');?></p>
                
                <div class="formGroup">                        
                    <div class="checkSlide">                        
                        <input type="checkbox" value="1" id="user_voting" name="user_voting" <?php if(osc_get_preference('user_voting', 'vote_and_review') == '1') echo 'checked="checked"';?>/>
                        <label for="user_voting"></label> 
                    </div>
                    <div><?php _e('Enable for users', 'vote-and-review') ; ?></div>
                </div>

                <br /><hr /><br />
                
                <div class="buttonSubmit">
                    <input class="btn btn-submit dark" id="buttonSubmit" type="submit" value="<?php _e('Save', 'vote-and-review'); ?>"/>
                    <label for="buttonSubmit"><?php _e('Save', 'vote-and-review'); ?></label>
                </div>
            </form>        
        </div>
    </div>
</div>