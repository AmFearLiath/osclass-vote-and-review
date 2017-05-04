<?php 
    if (!defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');
    $path = osc_base_url().'oc-content/plugins/'. dirname(osc_plugin_folder(__FILE__)); 
?>
<div id="wrapper_voting_plugin">
    <style>
        .user_vote_results i {
          font-size: <?php echo (isset($item_size) ? $item_size : '45px'); ?>;
        }
    </style>
    <div class="sidebar-box">
        <div id="user_voting_loading" style="display:none; text-align: center; font-size: 14px;">
            <img src="<?php echo $path; ?>/assets/images/spinner.gif" style="margin-left:20px;"/> <?php _e('Loading', 'voting');?>
        </div>
        <div id="user_voting_plugin">
            <?php include('voteUsers.php');?>
        </div>
    </div>
    <div style="clear:both;"></div>
</div>