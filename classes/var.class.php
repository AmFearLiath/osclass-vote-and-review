<?php
class v_a_r extends DAO {
    
    /* 
    * System related 
    */
    
    private static $instance ;
    
    public static function newInstance() {
        if (!self::$instance instanceof self) {
            self::$instance = new self ;
        }
        return self::$instance ;
    }
    
    function __construct() {
        parent::__construct();
    }
    
    /* 
    * Config related 
    */
    
    //Table for votes on Items
    public function _var_t_item() {
        return DB_TABLE_PREFIX.'t_votes_reviews_items';
    }
    
    //Table for votes on users
    public function _var_t_user() {        
        return DB_TABLE_PREFIX.'t_votes_reviews_user';
    }
    
    //Install plugin    
    public function _var_install($opts = '') {
        
        
        if ($opts == '') { 
            $opts = self::_var_opt();
            $file = osc_plugin_resource('vote-and-review/assets/create_table.sql');
            $sql = file_get_contents($file);
            if (!$this->dao->importSQL($sql)){
                throw new Exception( "Error while import SQL:<br>".$file );
            } 
        }        
        foreach ($opts AS $k => $v) {
            osc_set_preference($k, $v[0], $v[1], $v[2]);
        }
        
        return true;            
    }
    
    //Uninstall plugin
    public function _var_uninstall() {
        
        $pref = self::_var_sect();        
        Preference::newInstance()->delete(array("s_section" => $pref));
                
        $this->dao->query('DROP TABLE '.$this->_var_t_item());    
        $this->dao->query('DROP TABLE '.$this->_var_t_user());    
    }
    
    public static function _var_sect() {
        return 'vote_and_review';
    }
    
    public static function _var_opt() {
        
        $pref = self::_var_sect();        
        $opts = array(
            'load_FA'                   => array('1', $pref, 'BOOLEAN'),
            'load_bxSlider'             => array('1', $pref, 'BOOLEAN'),
            'load_sellerVerification'   => array('0', $pref, 'BOOLEAN'),
            'user_voting'               => array('0', $pref, 'BOOLEAN'),
            'item_voting'               => array('1', $pref, 'BOOLEAN'),
            'open'                      => array('0', $pref, 'BOOLEAN'),
            'user'                      => array('1', $pref, 'BOOLEAN')
        );
        return $opts;
    }
    
    /* Import old data */
    public function _var_import() {
        
        $this->dao->select('*');
        $this->dao->from(DB_TABLE_PREFIX.'t_voting_item');
        $item = $this->dao->get();
        $item = $item->result();
        
        if (is_array($item)) {
            foreach($item as $k => $v) {
                
                $this->dao->select('count(*) as total');
                $this->dao->from($this->_var_t_item());
                $this->dao->where('fk_i_item_id', $v['fk_i_item_id']);
                $this->dao->where('fk_i_user_id', $v['fk_i_user_id']);
                if ($v['s_hash']) {
                    $this->dao->where('s_hash', $v['s_hash']);
                }
                $checkItem = $this->dao->get();
                $checkItem = $checkItem->row();
                
                if ($checkItem['total'] == '0') {
                    $insertItem = array(
                        'fk_i_item_id'  => $v['fk_i_item_id'],
                        'fk_i_user_id'  => $v['fk_i_user_id'],
                        'i_vote'        => $v['i_vote'],
                        's_hash'        => $v['s_hash']
                    );                    
                    if (!$this->dao->insert($this->_var_t_item(), $insertItem)) {
                        return false;    
                    }   
                }   
            }
        }
        
        $this->dao->select('*');
        $this->dao->from(DB_TABLE_PREFIX.'t_voting_user');
        $user = $this->dao->get();
        $user = $user->result();
        
        if (is_array($user)) {
            foreach($user as $k => $v) {
                
                $this->dao->select('count(*) as total');
                $this->dao->from($this->_var_t_user());
                $this->dao->where('i_user_voted', $v['i_user_voted']);
                $this->dao->where('i_user_voter', $v['i_user_voter']);
                $checkUser = $this->dao->get();
                $checkUser = $checkUser->row();
                
                if ($checkUser['total'] == '0') {
                    $insertUser = array(
                        'i_user_voted'  => $v['i_user_voted'],
                        'i_user_voter'  => $v['i_user_voter'],
                        'i_vote'        => $v['i_vote']
                    );
                    
                    if (!$this->dao->insert($this->_var_t_user(), $insertUser)) {
                        return false;        
                    }  
                }   
            }
        }        
        return true;
    }
    
    /* Check for possible imports */
    public function _var_checkImport() {

        $this->dao->select('count(*) as items');
        $this->dao->from(DB_TABLE_PREFIX.'t_voting_item');
        $item = $this->dao->get();        

        $this->dao->select('count(*) as users');
        $this->dao->from(DB_TABLE_PREFIX.'t_voting_user');
        $user = $this->dao->get();        
        
        if (!$item && !$user) { return false; }

        $items = $item->row(); $users = $user->row();
        return array('items' => $items['items'], 'users' => $users['users']);    
    }
    
    /* 
    * Item related 
    */
    
    //Set Vote for items   
    function _voteItem($item, $user, $vote, $review, $hash) {
        $set = array(
            'fk_i_item_id'  => (int)$item,
            'i_vote'        => (int)$vote,
            's_hash'        => is_null($hash) ? "" : "$hash",
            's_review'      => "$review"
        );
        if ($user != 'NULL' && is_numeric($user)) {
            $set['fk_i_user_id']  = $user;
        }
        if (class_exists('um_f')) { 
            $this->var_notification('item', $item, $user, $vote, $review);
        }
        return $this->dao->insert($this->_var_t_item(), $set);
    } 
    
    //Delete votes for items
    function _voteItem_delete($item) {
        if (is_numeric($item)) { return $this->dao->delete($this->_var_t_item(), 'fk_i_item_id = '.$item); }
        return false;
    }
    
    //Get Number of votes for items
    function _voteItem_getNum($item) {
        if (is_numeric($item)) {
            $this->dao->select('count(*) as total');
            $this->dao->from($this->_var_t_item());
            $this->dao->where('fk_i_item_id', (int)$item);
            $result = $this->dao->get();
            
            if (!$result) { return array(); }

            return $result->row();
        } else {
            return array('total' => 0);
        }
    }
    
    //Get average votes of items
    function _voteItem_getAvg($item) {
        if (is_numeric($item)) {
            $this->dao->select('format(avg(i_vote),1) as vote');
            $this->dao->from($this->_var_t_item());
            $this->dao->where('fk_i_item_id', (int)$item);
            $result = $this->dao->get();
            
            if (!$result) { return array(); }

            return $result->row();
        } else {
            return array('vote' => 0);
        }
    }
    
    //Get Votes of items
    function _voteItem_getVotes($item) {
        if (is_numeric($item)) {
            $this->dao->select('i_vote');
            $this->dao->from($this->_var_t_item());
            $this->dao->where('fk_i_item_id', (int)$item);
            $result = $this->dao->get();
            
            if (!$result) { return array(); }
            
            return $result->result();
        } else {
            return false;
        }
    }
    
    //Get Reviews of items
    function _voteItem_getReviews($item) {
        if (is_numeric($item)) {
            $this->dao->select('s_review');
            $this->dao->from($this->_var_t_item());
            $this->dao->where('fk_i_item_id', (int)$item);
            $result = $this->dao->get();
            
            if (!$result) { return array(); }
            
            return $result->result();
        } else {
            return false;
        }
    }
    
    //Check if item is already voted
    function _voteItem_isRated($item, $hash, $user = null) {
        if (is_numeric($item) && ($user == null || is_numeric($user))) {
            $this->dao->select('i_vote');
            $this->dao->from($this->_var_t_item());
            $this->dao->where('fk_i_item_id', (int)$item );
            
            if ($user == null) { $this->dao->where('fk_i_user_id IS NULL'); } 
            else { $this->dao->where('fk_i_user_id', (int)$user); }

            $this->dao->where('s_hash', (string)$hash);
            $result = $this->dao->get();
            
            if (!$result) { return array(); }

            return $result->row();
        } else {
            return array();
        }
    }
    
    //Get rated items
    function _voteItem_getRatings($category_id = null, $order = 'desc', $num = 5) {
        $sql  = 'SELECT fk_i_item_id as item_id, format(avg(i_vote),1) as avg_vote, count(*) as num_votes, '.DB_TABLE_PREFIX.'t_item.fk_i_category_id as category_id ';
        if (!is_null($category_id)) {
            $sql .= ', '.DB_TABLE_PREFIX.'t_category.fk_i_parent_id as parent_category_id ';
        }
        $sql .= 'FROM '.$this->_var_t_item().' ';
        $sql .= 'LEFT JOIN '.DB_TABLE_PREFIX.'t_item ON '.DB_TABLE_PREFIX.'t_item.pk_i_id = '.$this->_var_t_item().'.fk_i_item_id ';
        $sql .= 'LEFT JOIN '.DB_TABLE_PREFIX.'t_category ON '.DB_TABLE_PREFIX.'t_category.pk_i_id = '.DB_TABLE_PREFIX.'t_item.fk_i_category_id ';
        if (!is_null($category_id)) {
            $sql .= 'WHERE '.DB_TABLE_PREFIX.'t_item.fk_i_category_id = '.$category_id.' ';
            $sql .= 'OR '.DB_TABLE_PREFIX.'t_category.fk_i_parent_id = '.$category_id.' ';
            $sql .= ' AND ';
        } else{
            $sql .= 'WHERE ';
        }
        $sql .= ''.DB_TABLE_PREFIX.'t_item.b_active = 1 ';
        $sql .= 'AND '.DB_TABLE_PREFIX.'t_item.b_enabled = 1 ';
        $sql .= 'AND '.DB_TABLE_PREFIX.'t_item.b_spam = 0 ';
        $sql .= 'AND ('.DB_TABLE_PREFIX.'t_item.b_premium = 1 || '.DB_TABLE_PREFIX.'t_category.i_expiration_days = 0 ||DATEDIFF(\''.date('Y-m-d H:i:s').'\','.DB_TABLE_PREFIX.'t_item.dt_pub_date) < '.DB_TABLE_PREFIX.'t_category.i_expiration_days) ';
        $sql .= 'AND '.DB_TABLE_PREFIX.'t_category.b_enabled = 1 ';
        $sql .= 'GROUP BY item_id ORDER BY avg_vote '.$order.', num_votes '.$order.' LIMIT 0, '.$num;

        $result = $this->dao->query($sql);
        
        if (!$result) { return array(); }

        return $result->result();
    }
    
    /* 
    * User related 
    */
    
    //Set Vote for users
    function _voteUser($voted, $voter, $vote, $review) {
        $set = array(
            'i_user_voted'  => (int)$voted,
            'i_user_voter'  => (int)$voter,
            'i_vote'        => (int)$vote,
            's_review'      => "$review"
        );        
        if (class_exists('um_f')) { 
            $this->var_notification('user', $voted, $voter, $vote, $review);
        }
        return $this->dao->insert($this->_var_t_user(), $set);
    }
    
    //Delete votes for users
    function _voteUser_delete($user) {
        if (is_numeric($user)) {
            $aux  = $this->dao->delete($this->_var_t_user(), 'i_user_voted = '.$user);
            $aux2 = $this->dao->delete($this->_var_t_user(), 'i_user_voter = '.$user);
            return ($aux && $aux2);
        }
        return false;
    }
    
    //Get Number of votes for users
    function _voteUser_getNum($user) {
        if (is_numeric($user)) {
            $this->dao->select('count(*) as total');
            $this->dao->from($this->_var_t_user());
            $this->dao->where('i_user_voted', (int)$user); 
            $result = $this->dao->get();
            
            if (!$result) { return array(); }

            return $result->row();
        } else {
            return array('total' => 0);
        }
    }
    
    //Get average votes of users 
    function _voteUser_getAvg($user) {
        if (is_numeric($user)) {
            $this->dao->select('format(avg(i_vote),1) as vote');
            $this->dao->from($this->_var_t_user());
            $this->dao->where('i_user_voted', (int)$user);
            $result = $this->dao->get();
            
            if (!$result) { return array(); }

            return $result->row();
        } else {
            return array('vote' => 0);
        }
    }
    
    //Get Reviews of users
    function _voteUser_getReviews($user) {
        if (is_numeric($user)) {
            $this->dao->select('s_review');
            $this->dao->from($this->_var_t_user());
            $this->dao->where('i_user_voted', (int)$user);
            $result = $this->dao->get();
            
            if (!$result) { return array(); }
            
            return $result->result();
        } else {
            return false;
        }
    }
    
    //Check if user is already voted
    function _voteUser_isRated($user, $voter) {
        if (is_numeric($user) && is_numeric($voter)) {
            $this->dao->select('i_vote');
            $this->dao->from($this->_var_t_user());
            $this->dao->where('i_user_voted', (int)$user);
            $this->dao->where('i_user_voter', (int)$voter);

            $result = $this->dao->get();
            if (!$result) { return array(); }

            return $result->row();
        } else {
            return array();
        }
    }
    //Get rated users
    function _voteUser_getRatings($order = 'desc', $num = 5) {
        $sql  = 'SELECT i_user_voted as user_id, format(avg(i_vote),1) as avg_vote, count(*) as num_votes ';
        $sql .= 'FROM '.$this->_var_t_user().' ';
        $sql .= 'LEFT JOIN '.DB_TABLE_PREFIX.'t_user ON '.DB_TABLE_PREFIX.'t_user.pk_i_id = '.$this->_var_t_user().'.i_user_voted ';
        $sql .= 'WHERE ';
        $sql .= ''.DB_TABLE_PREFIX.'t_user.b_active = 1 ';
        $sql .= 'AND '.DB_TABLE_PREFIX.'t_user.b_enabled = 1 ';
        $sql .= 'GROUP BY user_id ORDER BY avg_vote '.$order.', num_votes '.$order.' LIMIT 0, '.$num;

        $result = $this->dao->query($sql);
        
        if (!$result) { return array(); }

        return $result->result();
    }
    
    /*
    * Seller Verification
    */
    
    //Check for verificated seller
    function var_checkVerificatedSeller($userId) {
        
        if(!is_numeric($userId)){ return false; }
        
        $this->dao->select();
        $this->dao->from(DB_TABLE_PREFIX.'t_seller_verification');
        $this->dao->where('fk_i_user_id', $userId);

        $result = $this->dao->get();
        if( !$result ) { return array(); }
        
        return $result->row();
    }
    
    function var_notification($type, $id, $voter, $vote, $review) {

        if ($type == 'item') {           
            
            $from  = User::newInstance()->findByPrimaryKey($voter);            
            $item  = Item::newInstance()->findByPrimaryKey($id);
            View::newInstance()->_exportVariableToView('item', $item);            
            
            $link   = '<a href="'.osc_item_url().'" >'.osc_item_url().'</a>';
            
            $itemID = $id; $to_userID = osc_item_user_id();
            
            $content = array();
            $content[] = array('{TO_NAME}', '{LINK}', '{FROM_NAME}', '{GET_VOTE}', '{GET_REVIEW}', '{PAGE_TITLE}');        
            $content[] = array(osc_item_contact_name(), $link, $from['s_name'], $vote, $review, osc_page_title());
                
        } elseif ($type == 'user') {             
            
            $from = User::newInstance()->findByPrimaryKey($voter);                        
            $to   = User::newInstance()->findByPrimaryKey($id);
            View::newInstance()->_exportVariableToView('user', $to);            
            
            $link = '<a href="'.osc_user_public_profile_url().'" >'.__('See your profile', 'vote-and-review').'</a>';

            $itemID = ''; $to_userID = $id;
            
            $content = array();
            $content[] = array('{TO_NAME}', '{LINK}', '{FROM_NAME}', '{GET_VOTE}', '{GET_REVIEW}', '{PAGE_TITLE}');        
            $content[] = array($to['s_name'], $link, $from['s_name'], $vote, $review, osc_page_title());
            
        }
        
        $title = __('New review', 'vote-and-review');
        $body  = __('Hello {TO_NAME},','vote-and-review').'<br />
               '.__('There is a new review for you','vote-and-review').'<br /><br />{LINK}<br /><br />
               '.__('From','vote-and-review').': {FROM_NAME}<br />
               '.__('Vote','vote-and-review').': {GET_VOTE}<br />
               '.__('Review','vote-and-review').': {GET_REVIEW}<br /><br />
               '.__('Best regards','vote-and-review').'<br />{PAGE_TITLE}';
                
        $body = osc_mailBeauty($body, $content);
        
        um_f::newInstance()->_send(
            array( 
                'id' => $itemID,
                'from_user' => '0', 
                'to_user' => $to_userID, 
                'notification_title' => $title, 
                'notification_content' => $body                        
            )
        );
    }   
}
?>