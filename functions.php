<?php
/**
* Show form to vote an item. (itemDetail)
*/
function var_voteItem($premium = false) {
    
    if (osc_is_this_category('vote-and-review', osc_item_category_id()) && osc_get_preference('item_voting', 'vote_and_review') == '1') {
        $aux_vote  = v_a_r::newInstance()->_voteItem_getAvg(osc_item_id());
        $aux_count = v_a_r::newInstance()->_voteItem_getNum(osc_item_id());
        $vote['vote']  = $aux_vote['vote'];
        $vote['total'] = $aux_count['total'];

        $hash   = '';
        if(osc_logged_user_id() == 0) {
            $hash = $_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR'];
            $hash = sha1($hash);
        } else {
            $hash = null;
        }

        $vote['can_vote'] = true;
        if(osc_get_preference('user', 'vote_and_review') == 1) {
            if(!osc_is_web_user_logged_in()) {
                $vote['can_vote'] = false;
            }
        }
        if(!var_canVoteItem(osc_item_id(), osc_logged_user_id(), $hash)){
            $vote['can_vote'] = false;
        }
        require 'views/detailItems.php';
     }
}

/**
 * Check if user can vote an item
 *
 * @param string $itemId
 * @param string $userId
 * @param string $hash
 * @return bool
 */
function var_canVoteItem($item, $user, $hash) {
    if ($user == 'NULL' || (string)$user === "0") {
        $result = v_a_r::newInstance()->_voteItem_isRated($item, $hash);
    } else {
        $result = v_a_r::newInstance()->_voteItem_isRated($item, $hash, $user);
    }

    if (count($result) > 0) {
        return false;
    } else if (osc_logged_user_id() != 0 && osc_logged_user_id() == osc_item_user_id()) {
        return false;
    } else {
        return true;
    }
}

/**
 * Return layout optimized for main web page, with the best items voted with a limit
 *
 * @param int $num number of items
 */
function var_bestRatedItem($num = 5) {
    if (osc_get_preference('item_voting', 'vote_and_review') == 1) {
        $filter = array(
            'order'       => 'desc',
            'num_items'   => $num
        );
        $results = var_getVotesItem($filter);
        if (count($results) > 0) {
            $locale  = osc_current_user_locale();
            require 'views/bestItems.php';
        }
    }
}

/**
 * Return an array of item votes with given filters
 * <code>
 * array(
 *          'category_id' => (integer_category_id),
 *          'order'       => ('desc','asc'),
 *          'num_items'   => (integer)
 *      );
 * </code>
 * @param type $array_filters
 * @return array of item votes
 */
function var_getVotesItem($array_filters) {
    $category_id = null;
    $order       = 'desc';
    $num         = 5;
    if(isset($array_filters['category_id'])){
        $category_id = $array_filters['category_id'];
    }
    if(isset($array_filters['order'])){
        $order = strtolower($array_filters['order']);
        if( !in_array($order, array('desc', 'asc') ) ){
            $order = 'desc';
        }
    }
    if(isset($array_filters['num_items'])){
        $num = (int)$array_filters['num_items'];
    }

   return v_a_r::newInstance()->_voteItem_getRatings($category_id, $order, $num);
}

/**
 * hook delete_item
 * @param type $itemID
 */
function var_delItem($item) {
    return v_a_r::newInstance()->_voteItem_delete($item);
}

/**************************************************************************
 *                          VOTE USERS
 *************************************************************************/

/**
 * Show form to vote a seller if item belongs to a registered user. (itemDetail)
 *
 * @param type $item item array or userId
 */
function var_voteUser($item=null, $user=true) {
    $userId = null;

    if ($item == null) {
        $userId = osc_item_user_id();
    } else if(is_numeric($item) ) {
        $userId = $item;
    } else if( is_array($item) ) {
        $userId = $item['fk_i_user_id'];
    } else {
        exit;
    }

    if (osc_get_preference('user_voting', 'vote_and_review') == 1 && is_numeric($userId) && isset($userId) && $userId > 0) {

        $aux_vote  = v_a_r::newInstance()->_voteUser_getAvg($userId);
        $aux_count = v_a_r::newInstance()->_voteUser_getNum($userId);
        $vote['vote']   = $aux_vote['vote'];
        $vote['total']  = $aux_count['total'];
        $vote['userId'] = $userId;

        $vote['can_vote'] = false;
        if (osc_is_web_user_logged_in() && var_canVoteUser($userId, osc_logged_user_id())) {
            $vote['can_vote'] = true;
        }
        
        $item_size = '35px';
        require 'views/detailUsers.php';
        /*    
        if ($user) {
            $item_size = '35px';
            require 'views/detailUsers.php';    
        } else {
            $item_size = '35px';
            require 'views/detailItems.php';    
        }
        */
    }
}

/**
 * Check if user can vote
 *
 * @param type $userVotedId
 * @param type $userId
 * @return type
 */
function var_canVoteUser($user, $voter) {
    $result = array();
    if (isset($user) && is_numeric($user) && isset($voter) && is_numeric($voter) && $voter != $user) {
        $result = v_a_r::newInstance()->_voteUser_isRated($user, $voter);
        if (count($result) > 0) { return false; } 
        else { return true; }
    } else {
        return false;
    }
}

/**
 * Return layout optimized for sidebar at main web page, with the best user voted with a limit
 *
 * @param int $num number of users
 */
function var_bestRatedUser($num = 4) {
    if (osc_get_preference('user_voting', 'vote_and_review') == 1) {
        $filter = array(
            'order'       => 'desc',
            'num_items'   => $num
        );
        $results = var_getVotesUser($filter);
        if (count($results) > 0) {
            $locale  = osc_current_user_locale();
            require 'views/bestUsers.php';
        }
    }
}

/**
 * Return an array of votes with given filters
 * <code>
 * array(
 *          'order'       => ('desc','asc'),
 *          'num_items'   => (integer)
 *      );
 * </code>
 * @param type $array_filters
 * @return type
 */
function var_getVotesUser($array_filters) {
    $order       = 'desc';
    $num         = 5;
    if (isset($array_filters['order'])){
        $order = strtolower($array_filters['order']);
        if( !in_array($order, array('desc', 'asc') ) ){ $order = 'desc'; }
    }
    if(isset($array_filters['num_items'])){
        $num = (int)$array_filters['num_items'];
    }

   return v_a_r::newInstance()->_voteUser_getRatings($order, $num);
}

/**
 * hook delete
 * @param type $userID
 */
function var_delUser($userId) {
    v_a_r::newInstance()->_voteUser_delete($userId);
}

/**
 * Print star img src
 *
 * @param type $star
 * @param type $avg_vote
 * @return type
 */
function var_votingStars($star, $avg_vote) {
    $path = osc_base_url().'/oc-content/plugins/'.  osc_plugin_folder(__FILE__);
    $star_ok = 'fa-star';
    $star_no = 'fa-star-o';
    $star_md = 'fa-star-half-o';

    if ($avg_vote >= $star) {
        echo $star_ok;
    } else {
        $aux = 1+($avg_vote - $star);
        if ($aux <= 0){
            echo $star_no;
            return true;
        }
        if ($aux >=1) {
            echo $star_no;
        } else {
            if ($aux <= 0.5){
                echo $star_md;
            } else{
                echo $star_ok;
            }
        }
    }
}

function var_verificatedSeller($userID) {
    if (osc_get_preference('load_sellerVerification', 'vote_and_review') == '1') {
        $detail = v_a_r::newInstance()->var_checkVerificatedSeller($userID);
        require 'views/verificatedSeller.php';
    } else {
        return;
    }
}

?>