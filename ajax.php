<?php if (!defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');

/**
 *  Recive and save votes from frontend.
 */

$votedUserId = (Params::getParam("userId") == '')       ? null : Params::getParam("userId");
$itemId      = (Params::getParam("itemId") == '')       ? null : Params::getParam("itemId");
$iVote       = (Params::getParam("vote") == '')         ? null : Params::getParam("vote");
$sReview     = (Params::getParam("review") == '')       ? null : Params::getParam("review");

$userId = osc_logged_user_id();
$hash   = '';

// Vote Users
if (isset($iVote) && is_numeric($iVote) && isset($votedUserId) && is_numeric($votedUserId)) {
    if ($iVote<=5 && $iVote>=1) {
        if (var_canVoteUser($votedUserId, $userId)) {
            v_a_r::newInstance()->_voteUser($votedUserId, $userId, $iVote, $sReview);
        }
    }

    $aux_vote  = v_a_r::newInstance()->_voteUser_getAvg($votedUserId);
    $aux_count = v_a_r::newInstance()->_voteUser_getNum($votedUserId);
    
    $vote['vote']  = $aux_vote['vote'];
    $vote['total'] = $aux_count['total'];
    $vote['userId'] = $votedUserId;
    $vote['can_vote'] = true;
    
    if (!osc_is_web_user_logged_in() || !var_canVoteUser($votedUserId, $userId)) {
        $vote['can_vote'] = false;
    }

    require 'views/voteUsers.php';
}

// Vote Items
if (isset($iVote) && is_numeric($iVote) && isset($itemId) && is_numeric($itemId)) {
    if ($iVote <= 5 && $iVote >= 1) {
        if ($userId == 0) {
            $userId = 'NULL';
            $hash   = $_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR'];
            $hash = sha1($hash);
        } else {
            $hash = null;
        }

        $open = osc_get_preference('open', 'vote_and_review');
        $user = osc_get_preference('user', 'vote_and_review');
        
        if ($open == 1) {
            if (var_canVoteItem($itemId, $userId, $hash)) {
                v_a_r::newInstance()->_voteItem($itemId, $userId, $iVote, $sReview, $hash);
            }
        } else if ($user == 1 && is_null($hash)) {
            if (var_canVoteItem($itemId, $userId, $hash)) {
                v_a_r::newInstance()->_voteItem($itemId, $userId, $iVote, $sReview, $hash);
            }
        }
    }
    // return updated voting
    $item = Item::newInstance()->findByPrimaryKey($itemId);
    View::newInstance()->_exportVariableToView('item', $item);    
    
    if (osc_is_this_category('vote-and-review', osc_item_category_id())) {
        
        $aux_vote  = v_a_r::newInstance()->_voteItem_getAvg(osc_item_id());
        $aux_count = v_a_r::newInstance()->_voteItem_getNum(osc_item_id());
        
        $vote['vote']  = $aux_vote['vote'];
        $vote['total'] = $aux_count['total'];
        $vote['can_vote'] = true;
        
        if (osc_get_preference('user', 'vote_and_review') == 1) {
            if (!osc_is_web_user_logged_in()) {
                $vote['can_vote'] = false;
            }
        }
        if (!var_canVoteItem(osc_item_id(), osc_logged_user_id(), $hash) ){
            $vote['can_vote'] = false;

        }        
    }
    
    require 'views/voteItems.php';
}
?>
