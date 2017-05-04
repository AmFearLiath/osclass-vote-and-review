<?php if (!defined('OC_ADMIN') || OC_ADMIN!==true) exit('Access is not allowed.'); ?>
<style>
    p.code {
        padding: 8px;
        background-color: #F3F3F3;
        border: 1px solid #DDD;
    }
    p.code span{
        display: block;
    }

    h2{ position:relative; }
    h2 span.anchor{ position:absolute; top:-80px;}
    a.gotop{
        font-size: 14px;
        font-style: italic;
        padding-left: 15px;
        text-decoration: underline;
        cursor:pointer;
    }
    #settings_form ul {
        list-style-type: disc;
    }
    #content-page ul li {
        padding: 4px;
    }
</style>
<script type="text/javascript">
    $(document).ready(function(){
        $('a.gotop').click(function(){
            $('html, body').animate({ scrollTop: 0 }, 'slow');
        });
    });
</script>
<div id="settings_form" style="padding-left: 15px; padding-right: 15px;">
    <h2><?php _e('Information', 'vote-and-review') ; ?></h2>
    <p>
        <?php _e('This Plugin is a remake of the Plugin <strong><em>Rating by OSClass</em></strong>.', 'vote-and-review'); ?>
        </p>
    <p>
        <ul>
            <li><?php _e('Reviews for items and users now supported.', 'vote-and-review') ; ?></li>
            <li><?php _e('Use Font Awesome instead of images for the stars', 'vote-and-review') ; ?></li>
            <li><?php _e('Reviews are shown in a vertical slider, based on jQuery.bxSlider.js', 'vote-and-review') ; ?></li>
            <li><?php _e('Font Awesome and bxSlider can be activated separately', 'vote-and-review') ; ?>.</li>
            <li><?php _e('Best rated items now improved and moved to main page instead of sidebar', 'vote-and-review') ; ?>.</li>
        </ul>
    </p>

    <br /><hr /><br />
    
    <h2><?php _e('Plugin information', 'vote-and-review') ; ?></h2>
    <p>
        <?php _e('This plugin adds a rating system and allows users to vote among them the quality of the item and quality sellers', 'vote-and-review') ; ?>.
        </p>
    <p>
        <ul>
            <li><?php _e('Easy plugin configuration.', 'vote-and-review') ; ?></li>
            <li><?php _e('Vote items, can be enabled and disabled. It can be configured what kind of users can vote items, registered users only or guest too', 'vote-and-review') ; ?></li>
            <li><?php _e('Vote users, can be enabled and disabled. Only registered users can vote sellers', 'vote-and-review') ; ?></li>
            <li><?php _e('Allows to show the best rated items or users at frontend, adding some extra code at your template', 'vote-and-review') ; ?>.</li>
        </ul>
    </p>

    <br /><hr /><br />

    <h2><?php _e('Frequently asked questions', 'vote-and-review'); ?></h2>
    <ul>
        <li><a href="#show_best_rated"><?php _e('How can I show best rated listings or users?', 'vote-and-review') ;?></a></li>
        <li><a href="#change_place_votes"><?php _e('How can I add the votes in my template?', 'vote-and-review') ;?></a></li>
        <li><a href="#rating_templates"><?php _e('How to change the way the votes are displayed?', 'vote-and-review'); ?></a></li>
    </ul>

    <br /><hr /><br />


    <h2><span class="anchor" id="show_best_rated"></span><?php _e('How can I show the best rated listings or users?', 'vote-and-review') ;?><a class="gotop"><?php _e('Start of page', 'vote-and-review'); ?></a></h2>
    <p><?php _e('You can display the best rated listings or users list, wherever you want', 'vote-and-review') ; ?>.</p>
    <p><?php _e('By adding this line of code, you can show the listings or users at main web page, <span style="text-decoration: line-through;">in the sidebar</span>', 'vote-and-review') ; ?></p>

    <p><b><?php _e('Listings', 'vote-and-review');?></b></p>
    <p class="code">
        <?php echo htmlentities('<?php'); ?> var_bestRatedItem(<b><?php _e('NUMBER_OF_LISTINGS', 'vote-and-review');?></b>); ?><br>
    </p>
    <p><i><?php _e('Note: replace NUMBER_OF_ITEMS with a number of items you want to list', 'vote-and-review') ; ?></i></p>
    <em><?php _e('Edit main.php (located under root theme folder) This example shows my implementation in theme osclasswizards', 'vote-and-review') ; ?></em>
    <p class="code">
        <span style="padding-left: 10px;"><?php echo htmlentities("<?php if (function_exists('var_bestRatedItem')) { ?>"); ?></span>
        <span style="padding-left: 30px;"><?php echo htmlentities('<div style="margin-bottom: 20px;">'); ?></span>
        <span style="padding-left: 50px;"><?php echo htmlentities('<?php'); ?> var_bestRatedItem(4); ?></span>
        <span style="padding-left: 50px;"><?php echo htmlentities('<div style="clear: both;"></div>'); ?></span>
        <span style="padding-left: 30px;"><?php echo htmlentities('</div>'); ?></span>
        <span style="padding-left: 10px;"><?php echo htmlentities('<?php } ?>'); ?></span>
    </p>

    <!p><b><?php _e('Users', 'vote-and-review');?></b></p>

    <p class="code"><span><?php echo htmlentities('<?php'); ?> var_bestRatedUser(<b><?php _e('NUMBER_OF_USERS', 'vote-and-review');?></b>); ?></span></p>
    <p><i><?php _e('Note: replace NUMBER_OF_USERS with a number of users you want to list', 'vote-and-review') ; ?></i></p>
    <em><?php _e('Edit main.php (located under root theme folder)', 'vote-and-review') ; ?></em>
    <p class="code">
        <span style="padding-left: 10px;"><?php echo htmlentities("<?php if (function_exists('var_bestRatedUser')) { ?>"); ?></span>
        <span style="padding-left: 30px;"><?php echo htmlentities('<div style="margin-bottom: 20px;">'); ?></span>
        <span style="padding-left: 50px;"><?php echo htmlentities('<?php'); ?> var_bestRatedUser(4); ?></span>
        <span style="padding-left: 50px;"><?php echo htmlentities('<div style="clear: both;"></div>'); ?></span>
        <span style="padding-left: 30px;"><?php echo htmlentities('</div>'); ?></span>
        <span style="padding-left: 10px;"><?php echo htmlentities('<?php } ?>'); ?></span>
    </p>
    
    <br /><hr /><br />

    <h2><span class="anchor" id="change_place_votes"></span><?php _e('How can I add the votes in my template?', 'vote-and-review') ; ?><a class="gotop"><?php _e('Start of page', 'vote-and-review'); ?></a></h2>
    <p><b><?php _e('Items', 'vote-and-review') ; ?></b></p>

    <p><?php _e('You need to add this line in the sidebar of your theme, found in items.php or items-sidebar.php (located under root theme folder) :', 'vote-and-review' ) ; ?><br></p>
    <p class="code"><span><?php echo htmlentities('<?php'); ?> if (function_exists('var_voteItem')) { var_voteItem(); } ?></span></p>

    <p><b><?php _e('Users', 'vote-and-review') ; ?></b></p>

    <p><?php _e('To show votes on the public user profile, modify the user-public-profile.php (located under root theme folder) and place this line where you want', 'vote-and-review'); ?></p>

    <p class="code"><span><?php echo htmlentities('<?php'); ?> if (function_exists('var_voteUser')) { var_voteUser(osc_user_id()); } ?></span></p>

    <br /><hr /><br />

    <h2><span class="anchor" id="rating_templates"></span><?php _e('How to change the way the votes are displayed?', 'vote-and-review'); ?><a class="gotop"><?php _e('Start of page', 'vote-and-review'); ?></a></h2>

    <p><h3><?php _e('If you want to modify the templates, you can find them in the plugin folder', 'vote-and-review');?>.</h3></p>
    <p><b>views/voteItems.php</b> <?php _e('Template for rate and review items', 'vote-and-review') ; ?></p>
    <p><b>views/voteUsers.php</b> <?php _e('Template for rate and review users', 'vote-and-review') ; ?></p>
    <br>
    <p><b>views/bestItems.php</b>  <?php _e('Template to show best rated items', 'vote-and-review') ; ?></p>
    <p><b>views/bestUsers.php</b>  <?php _e('Template to show best rated users', 'vote-and-review') ; ?></p>
    <br>
    <p><b>views/detailItems.php</b> <?php _e('Wrapper for rating items (Not neccessary to edit in most case)', 'vote-and-review') ; ?></p>
    <p><b>views/detailUsers.php</b> <?php _e('Wrapper for rating users (Not neccessary to edit in most case)', 'vote-and-review') ; ?></p>

    <hr />

</div>