<?php if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.'); ?>
<style>    
    .best-voted-user p.votes {
      width: 100%;
      text-align: center;
      margin: 7px 0 0 0;
    }
    
    .best-voted-user p.votes i {
      font-size: 20px;
    }
</style>

<div class="box location">
    <h3><strong><?php _e("Best voted user", 'vote-and-review') ; ?></strong></h3>
    <div>
    <?php
        $count = 0;
        foreach($results as $user_vote):
            $avg_vote = $user_vote['avg_vote'];
            $total    = $user_vote['num_votes'];
            
            $user = User::newInstance()->findbyPrimaryKey($user_vote['user_id']);
            View::newInstance()->_exportVariableToView('user', $user);
    ?>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 best-voted-user">
            <div class="best-voted-header">
                <?php 
                    if (profile_picture_return()) {
                        $user_image = profile_picture_return();
                    } else {
                        $user_image = '<img class="img-responsive" src="http://www.gravatar.com/avatar/'.md5(strtolower(trim(osc_user_email()))).'?s=400&d='.osc_current_web_theme_url('images/default.gif').'" />';
                    } 
                ?>
                <figure>
                    <?php echo $user_image; ?>                    
                    <div class="informations">    
                        <i class="fa fa-user"></i> <a href="<?php echo osc_user_public_profile_url(); ?>"><?php echo osc_user_name(); ?></a><br />
                        <i class="fa fa-map-marker"></i> <?php echo osc_user_city(); ?> <?php if( osc_user_region()!='' ) { echo '('.osc_user_region().')'; } ?>
                    </div>
                </figure>
                <div class="best-voted-seller-verification" style="position: absolute; z-index: 10; top: 0; left: 0;">
                    <?php var_verificatedSeller(osc_user_id()); ?>
                </div>
                <p class="votes">
                    <i title="<?php echo _e('Not worth believing', 'vote-and-review'); ?>" data-vote="1" data-star="<?php var_votingStars(1, $avg_vote); ?>" id="user_vote_1" class="fa <?php var_votingStars(1, $avg_vote); ?>"></i>
                    <i title="<?php echo _e('Problematic', 'vote-and-review'); ?>" data-vote="2" data-star="<?php var_votingStars(2, $avg_vote); ?>" id="user_vote_2" class="fa <?php var_votingStars(2, $avg_vote); ?>"></i>
                    <i title="<?php echo _e('Quite well', 'vote-and-review'); ?>" data-vote="3" data-star="<?php var_votingStars(3, $avg_vote); ?>" id="user_vote_3" class="fa <?php var_votingStars(3, $avg_vote); ?>"></i>
                    <i title="<?php echo _e('Good Dealer', 'vote-and-review'); ?>" data-vote="4" data-star="<?php var_votingStars(4, $avg_vote); ?>" id="user_vote_4" class="fa <?php var_votingStars(4, $avg_vote); ?>"></i>
                    <i title="<?php echo _e('Great Dealer', 'vote-and-review'); ?>" data-vote="5" data-star="<?php var_votingStars(5, $avg_vote); ?>" id="user_vote_5" class="fa <?php var_votingStars(5, $avg_vote); ?>"></i>
                    <br />
                    <span style="position:relative; top:-5px;padding-right: 4px; padding-left: 4px; margin-bottom: 3px;"><?php echo $total;?> <?php _e('Votes', 'vote-and-review');?> (&#216; <?php echo $avg_vote;?>)</span>
                </p>
            </div>
            <div class="listing-attr">            
                <p><a href="<?php echo osc_item_url(); ?>"><?php echo osc_highlight(osc_item_title(), 35); ?></a></p>

            </div>
        </div>
    <?php
            $count++;
            View::newInstance()->_erase('user') ;
        endforeach;
    ?>
    </div>
</div>