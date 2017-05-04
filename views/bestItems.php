<?php 
if (!defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.'); 
?>
<style>    
    .best-voted-item p.votes {
      width: 100%;
      text-align: center;
      margin: 7px 0 0 0;
    }
    
    .best-voted-item p.votes i {
      font-size: 20px;
    }
</style>

<div class="box location">
    <h3><strong><?php _e("Best voted Listings", 'vote-and-review') ; ?></strong></h3>
    <div>
    <?php
        $count = 0;
        View::newInstance()->_erase('items');
        View::newInstance()->_erase('item');
            
        
        foreach($results as $item_vote):
        
            $avg_vote = $item_vote['avg_vote'];
            $total    = $item_vote['num_votes'];
            $item    = Item::newInstance()->findByPrimaryKey($item_vote['item_id']);
            View::newInstance()->_exportVariableToView('item', $item ) ;
            $user    = User::newInstance()->findbyPrimaryKey(osc_item_user_id());
            View::newInstance()->_exportVariableToView('user', $user) ;

            $premium = '';
            if(osc_item_is_premium()) { $premium = 'premium'; }
            
            $conn = getConnection();
            $image = $conn->osc_dbFetchresult("SELECT * FROM %st_item_resource WHERE fk_i_item_id = %d LIMIT 1", DB_TABLE_PREFIX, $item_vote['item_id']);
            $imageurl = osc_base_url().$image['s_path'].$image['pk_i_id'].'.'.$image['s_extension'];
            
    ?>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 best-voted-item <?php echo $premium; ?>">
            <div class="best-voted-header">
                <p class="votes">
                    <i title="<?php echo __('Without interest', 'voting'); ?>" data-vote="1" data-star="<?php var_votingStars(1, $avg_vote); ?>" id="vote_1" class="fa <?php var_votingStars(1, $avg_vote); ?>"></i>
                    <i title="<?php echo __('Uninteresting', 'voting'); ?>" data-vote="2" data-star="<?php var_votingStars(2, $avg_vote); ?>" id="vote_2" class="fa <?php var_votingStars(2, $avg_vote); ?>"></i>
                    <i title="<?php echo __('Interesting', 'voting'); ?>" data-vote="3" data-star="<?php var_votingStars(3, $avg_vote); ?>" id="vote_3" class="fa <?php var_votingStars(3, $avg_vote); ?>"></i>
                    <i title="<?php echo __('Very interesting', 'voting'); ?>" data-vote="4" data-star="<?php var_votingStars(4, $avg_vote); ?>" id="vote_4" class="fa <?php var_votingStars(4, $avg_vote); ?>"></i>
                    <i title="<?php echo __('Essential', 'voting'); ?>" data-vote="5" data-star="<?php var_votingStars(5, $avg_vote); ?>" id="vote_5" class="fa <?php var_votingStars(5, $avg_vote); ?>"></i>
                    <br />
                    <span style="position:relative; top:-5px;padding-right: 4px; padding-left: 4px; margin-bottom: 3px;"><?php echo $total;?> <?php _e('Votes', 'vote-and-review');?> (&#216; <?php echo $avg_vote;?>)</span>
                </p>
                <span class="location"> 
                    <i class="fa fa-map-marker"></i> <?php echo osc_item_city(); ?>
                    <?php if( osc_item_region()!='' ) { ?>
                    (<?php echo osc_item_region(); ?>)
                    <?php } ?>
                </span>
                <span class="banner_premium best"> <img src="<?php echo osc_plugin_url('vote-and-review/assets/images/premium.png'); ?>premium.png" title="Premium"> </span>
                <div class="best-voted-seller-verification" style="position: absolute; z-index: 10; top: 0; left: 0;">
                <?php var_verificatedSeller(osc_item_user_id()); ?>
                </div>
            </div>
            <figure>
                <?php if( osc_images_enabled_at_items() ) { ?>
                <?php if(osc_count_item_resources()) { ?>

                    <a class="listing-thumb" href="<?php echo osc_item_url() ; ?>" title="<?php echo osc_esc_html(osc_item_title()) ; ?>"><img src="<?php echo $imageurl; ?>" title="" alt="<?php echo osc_esc_html(osc_item_title()) ; ?>" class="img-responsive"></a>

                <?php } else { ?>
                <a class="listing-thumb" href="<?php echo osc_item_url() ; ?>" title="<?php echo osc_esc_html(osc_item_title()) ; ?>"><img src="<?php echo osc_current_web_theme_url('images/no_photo.gif'); ?>" title="" alt="<?php echo osc_esc_html(osc_item_title()) ; ?>" class="img-responsive"></a>
                <?php } ?>
                <?php } ?>               
            </figure>
            <div class="listing-attr">            
                <p><a href="<?php echo osc_item_url(); ?>"><?php echo osc_highlight(osc_item_title(), 35); ?></a></p>
                <?php if( osc_price_enabled_at_items() ) { ?>
                <div class="best-voted-item-price">
                    <span class="currency-value"> <?php echo osc_format_price(osc_item_price()); ?></span>
                    <div style="clear: both;"></div>
                </div>
                <?php } ?>
            </div>
        </div>
    <?php
            $count++;
            View::newInstance()->_erase('item');
        endforeach;
    ?>
        
    </div>
</div>
