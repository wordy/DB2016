<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Controller', 'Controller');
    


/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
    public $theme = "Cakestrap";

    public $components = array(
        'DebugKit.Toolbar',
    	'Session',
    	'RequestHandler',
        'Auth' => array(
            'loginRedirect' => array('controller' => 'tasks', 'action' => 'compile'),
            'logoutRedirect' => array('controller' => 'users', 'action' => 'login'),
            'authError' => 'You will need to sign in to view that page',
            //'authError'=> null,
            'authorize' => array('Controller'),
            'ajaxLogin' => '/Elements/user/unauth_ajax_redirect',
        ),
        
    );
    
	public $helpers = array(
		'Session', 'Html', 'Js', 'Form', 'Ops', 'Filepicker.Filepicker',
	);

   public function isAuthorized($user) {
        // Admin can access every action
        if (isset($user['user_role_id']) && $user['user_role_id'] >= 500) {
            return true;
        }
        
        // This (temporarily) allows CC+ to access all actions
        /*
        if (isset($user['user_role_id']) && $user['user_role_id'] >= 200) {
            return true;
        }*/

// Default deny
        if($this->request->is('ajax')) {
            $this->response->type('json');
            $this->response->statusCode(401);
            return json_encode(array('status' => 'ERROR', 'message' => 'Unauthorized'));
            //$this->response->send();
            //$this->_stop();
        }        
        
        $this->Session->setFlash(__('Your permissions don\'t allow you to access that.'), 'flash/auth_error');
        //$this->Auth->authError(__('Whoops, it looks like your permissions don\'t allow you to access that.'));
        return false;
    }
   
    public function beforeFilter(){
        //parent::beforeFilter();
        
        // Allow All to see the root index (for example)
        $this->Auth->allow('display','info');
          
        
        //$this->Auth->autoRedirect = false;
        
        if ($this->request->is('ajax')) {
            Configure::write('debug', 0);
            $this->autoRender = false;
            $this->layout = 'ajax';
        }
                
$this->Auth->flash['key']='auth';
$this->Auth->flash['element']='auth_error';
        
    }
    /*
    public function redirect($url, $status = null, $exit = true) {
        // this statement catches not authenticated or not authorized ajax requests
        // AuthComponent will call Controller::redirect(null, 403) in those cases.
        // with this we're making sure that we return valid JSON responses in all cases
        if($this->request->is('ajax') && $status == 403) {
            $this->response = new CakeResponse(array('code' => 'code'));
            $this->response->send();
            return $this->_stop();
        }
        return parent::redirect($url, $status, $exit);
    }
*/


    public function sayHello(){
        $rs =  $this->Task->Team->find('list');
        
        return $rs;
        
    }









}