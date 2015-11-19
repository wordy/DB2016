<?php
App::uses('AppModel', 'Model');
/**
 * EventInfo Model
 *
 */
class EventInfo extends AppModel {

public $order = 'id DESC';

    function beforeSave($options = array()){
        $uid = CakeSession::read('Auth.User.id');
        
        $this->data[$this->alias]['user_id'] = $uid;
        
        return true;
    }































//EOF
}
