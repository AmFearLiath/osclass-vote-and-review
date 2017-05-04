<?php 
    if (!defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');
    $path = osc_base_url().'oc-content/plugins/'. dirname(osc_plugin_folder(__FILE__)); 
?>
<div class="inner-box" style="padding: 15px;">
    <div class="widget-title">
        <h4><?php _e('Vote', 'voting');?></h4>
    </div>
    <div id="wrapper_voting_plugin" class="sidebar-box widget-box form-container form-vertical" style="position: relative;">
        <style>
            .vote_results i {
              font-size: <?php echo (isset($item_size) ? $item_size : '45px'); ?>;
            }
        </style>
        <div class="sidebar-box inner_wrap">
            <div id="voting_loading" style="display:none; text-align: center; font-size: 14px;">
                <img src="<?php echo $path; ?>/assets/images/spinner.gif" style="margin-left:20px;"/> <?php _e('Loading', 'voting');?>
            </div>
            <div id="voting_plugin">
                <?php include('voteItems.php');?>
            </div>
        </div>
        <div style="clear:both;"></div>
    </div>
</div>
