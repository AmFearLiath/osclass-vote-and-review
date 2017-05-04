<?php 
if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');

if (osc_premium_user_id()) { $item_id = osc_premium_user_id(); } 
else { $item_id = osc_item_user_id(); }

$item_id = osc_user_id();
$avg_vote = $vote['vote'];
$tot_vote = $vote['total'];
$reviews = '';

$conn = getConnection();
$rev = $conn->osc_dbFetchresults("SELECT * FROM %st_votes_reviews_user WHERE i_user_voted = %d AND s_review != '' ORDER BY `dt_review` DESC", DB_TABLE_PREFIX, $item_id);

foreach($rev AS $k => $v) {
    $user = User::newInstance()->findbyPrimaryKey($v['i_user_voter']);
    $date = date("d.m.Y", strtotime($v['dt_review']));
    $time = date("H:i:s", strtotime($v['dt_review']));
    $reviews .= '
        <div id="review" class="slide review_item">
            <p class="review_name">
            <a href="'.osc_user_public_profile_url($v['i_user_voter']).'">'.$user['s_name'].'</a><br />
            '.__('Vote', 'voting').': '.str_repeat('<i class="fa fa-star"></i>', $v['i_vote']).' <small class="review_date">'.sprintf(__('on %s at %s', 'vote-and-review'), $date, $time).'</small>
            </p>
            
            <p class="review_content">'.$v['s_review'].'</p>
        </div>
    ';
}

$_userId = null;

if (isset($vote['userId'])) { $_userId = $vote['userId']; } 
elseif (Params::getParam('userId')!=''){ $_userId = Params::getParam('userId'); }

if ($_userId == null) { exit; }

 
?>
        <div class="user_votes_stars">         
            <div class="user_vote_results">
                <?php $avg_vote = $vote['vote']; ?>
                <i title="<?php echo _e('Not worth believing', 'vote-and-review'); ?>" data-vote="1" data-star="<?php var_votingStars(1, $avg_vote); ?>" id="user_vote_1" class="fa <?php var_votingStars(1, $avg_vote); ?>"></i>
                <i title="<?php echo _e('Problematic', 'vote-and-review'); ?>" data-vote="2" data-star="<?php var_votingStars(2, $avg_vote); ?>" id="user_vote_2" class="fa <?php var_votingStars(2, $avg_vote); ?>"></i>
                <i title="<?php echo _e('Quite well', 'vote-and-review'); ?>" data-vote="3" data-star="<?php var_votingStars(3, $avg_vote); ?>" id="user_vote_3" class="fa <?php var_votingStars(3, $avg_vote); ?>"></i>
                <i title="<?php echo _e('Good Dealer', 'vote-and-review'); ?>" data-vote="4" data-star="<?php var_votingStars(4, $avg_vote); ?>" id="user_vote_4" class="fa <?php var_votingStars(4, $avg_vote); ?>"></i>
                <i title="<?php echo _e('Great Dealer', 'vote-and-review'); ?>" data-vote="5" data-star="<?php var_votingStars(5, $avg_vote); ?>" id="user_vote_5" class="fa <?php var_votingStars(5, $avg_vote); ?>"></i>    
                <?php if ($tot_vote > 0) { ?>
                <p><?php echo _e('Votes', 'vote-and-review'); ?>: <?php echo $tot_vote; ?> - <?php echo _e('Average', 'vote-and-review'); ?>: <?php echo $avg_vote; ?></p>
                <?php } else { ?>
                <p><?php echo _e('No votes yet', 'vote-and-review'); ?></p>
                <?php } ?>
            </div>                        
            <div class="user_vote_review">
                <form id="user_vote_review" name="vote_review" data-vote="" method="post" style="display: none;" action="<?php echo osc_base_url(true); ?>?page=ajax&action=custom&ajaxfile=<?php echo dirname(osc_plugin_folder(__FILE__)).'/ajax.php'?>">
                    <input type="hidden" name="userId" id="vote_review_user" value="<?php echo $item_id; ?>" />
                    <input type="hidden" name="vote" id="user_vote_review" value="" />
                    <p><?php echo __('Please let other users know what you think about this seller.', 'vote-and-review'); ?></p>
                    <label id="user_vote_review_rate"><?php echo _e('Your Vote', 'vote-and-review'); ?>: <span></span></label><br />
                    <label id="user_vote_review_text"><?php echo _e('Your Review', 'vote-and-review'); ?></label>
                    <textarea name="review" placeholder="<?php echo _e('Please exercise friendly and constructive.', 'vote-and-review'); ?>"></textarea><br /><br />
                    <button type="submit" class="btn btn-success"><?php echo _e('Send', 'vote-and-review'); ?></button>
                </form>
            </div>                        
            <div id="user_vote_reviews">
                <?php echo $reviews; ?>
            </div>
            <?php if( $vote['can_vote'] ) { ?>
            <script type="text/javascript">
            $(function(){
                $('.user_vote_results i').hover(
                    function(){                        
                        var item = $(this),
                            vote = item.data('vote'),
                            star = item.data('star');
                        
                        item.parent().removeClass("clicked");
                        
                        for(i=1; i<=vote; i++) {
                            var el  = $('i#user_vote_'+i);                                                        
                            if (el.hasClass('fa-star')) { el.css({'color': 'rgba(250,39,39,0.7)', 'cursor': 'pointer'}); } 
                            else { el.addClass('fa-star').removeClass(star).css({'color': 'rgba(250,39,39,0.7)', 'cursor': 'pointer'}); }    
                        }   
                    },
                    function(){                        
                        var item = $(this),
                            vote = item.data('vote'),
                            star = item.data('star');
                        if (!item.parent().hasClass("clicked")) {   
                            for(i=1; i<=vote; i++) {
                                var el  = $('i#user_vote_'+i);
                                if (star == 'fa-star') { el.css({'color': 'rgba(200,39,39,0.7)', 'cursor': 'default' }); } 
                                else { el.addClass(star).removeClass('fa-star').css({'color': 'rgba(200,39,39,0.7)', 'cursor': 'default'}); }                                
                            }
                        }    
                    }
                );
                
                $('.user_vote_results i').click(function(){                    
                    var item   = $(this),
                        star   = item.data('star'),
                        vote   = item.data('vote'),
                        pvote  = $("#user_vote_review_rate span").html();
                    
                    item.parent().addClass("clicked");
                    
                    for(i=1; i<=vote; i++) {
                        var el  = $('i#user_vote_'+i);                                                        
                        if (el.hasClass('fa-star')) { el.css({'color': 'rgba(250,39,39,0.7)', 'cursor': 'pointer'}); } 
                        else { el.addClass('fa-star').removeClass(star).css({'color': 'rgba(250,39,39,0.7)', 'cursor': 'pointer'}); }    
                    }
                    
                    if (!pvote || pvote == vote || !$("form#user_vote_review").is(":visible")) {   
                        $("form#user_vote_review").attr("data-vote", vote).slideToggle("fast");
                    }
                    $("#user_vote_review_rate span").html(vote);
                    $("input#user_vote_review").val(vote);
                });
                
                $("form#user_vote_review").on("submit", function(event){
                    var form   = $(this), 
                        action = form.attr("action"),
                        method = form.attr("method"),
                        data   = form.serialize();
                        
                    event.preventDefault();
                    console.log("Submit");
                    $.ajax({
                        type: method,
                        url: action,
                        data: data,
                        beforeSend: function(){
                            $('#user_voting_plugin').hide();
                            $('#user_voting_loading').fadeIn('slow');
                        },
                        success: function(data){
                            $('#user_voting_loading').fadeOut('slow', function(){
                                $('#user_voting_plugin').html(data).fadeIn('slow', function(){
                                    var userWidth = $("#user_vote_reviews").width(),        
                                        userHeight = 0;

                                    $("#user_vote_reviews").children().each(function(){
                                        userHeight = userHeight + $(this).outerHeight(true) - 20;
                                    });

                                    if (userHeight > 200) {
                                        $('#user_vote_reviews').bxSlider({
                                            mode: 'vertical',
                                            autoControls: true,
                                            ticker: true,
                                            useCSS: false,
                                            tickerHover: true,
                                            minSlides: 1,
                                            maxSlides: 3,
                                            speed: 8000,
                                            adaptiveHeight: false,
                                            slideMargin: 0,
                                            slideWidth: userWidth
                                        });
                                    }
                                });
                            });
                        }
                    });    
                });
            });
            </script>
            <?php } ?>
        </div>     