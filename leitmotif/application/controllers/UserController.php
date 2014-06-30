<?php

require_once(APPLICATION_PATH . '/controllers/BaseController.php');

class UserController extends BaseController
{
	var $model;
	var $errorlogin;
	var $titleView;
	
	protected $_idkey = 'notodefine';
	protected $_controllername = 'user';
	
    public function init()
    {
        /* Load DB_model */

		require_once(APPLICATION_PATH . '/models/ModelLeitmotif.php');
		$this->model = new ModelLeitmotif();		
		$this->titleView = "Gestion Usuarios";
    }

    public function indexAction()
    {
    	$this->view->titleView = $this->titleView;
    	$auth = Zend_Auth::getInstance();
    	if($auth->hasIdentity()) {
    		if ($auth->getIdentity()->admin == '1' )
			{
				$this->view->users = $this->model->queryUser();
			}
			else
			{
				return $this->_helper->redirector->gotoRoute(array(
					'controller' => 'user' , 'action' => 'edituser', 'idUser' => $auth->getIdentity()->idUser ),
					'default', true); 
			}
    	}

    }

    public function loginAction(){
    	$this->view->titleView = $this->titleView;
      	$this->_helper->viewRenderer->setNoRender();
    	if($this->getRequest()->isPost()){
    		$data = $this->getRequest()->getPost();

    		$adapter = new Zend_Auth_Adapter_DbTable(
					Zend_Db_Table_Abstract::getDefaultAdapter());

			$adapter->setTableName('TblUser')
					->setIdentityColumn('username')
					->setCredentialColumn('password');

			$adapter->setIdentity($data['username'])
					->setCredential($data['password']);;

			
			if($data['username'] != null){
				$auth = Zend_Auth::getInstance();

				$result = $auth->authenticate($adapter);

				if($result->isValid()) {
					try{
						$user = (array)$adapter->getResultRowObject(array(
							'idUser',
							'username',
							'password',
							'firstname',
							'lastname'
						));
						
						if ($user['password'] == $data['password'])
						{
							$storage = $auth->getStorage();
							$storage->write((object)$user);
							return $this->_helper->redirector->gotoRoute(array('controller' => 'index', 'action' => 'index'),'default', true);
						}
						else
						{
							$this->errorlogin = "Password incorrect!!";
							return $this->_helper->redirector->gotoRoute(array('controller' => 'user', 'action' => 'index'),'default', true);
						}
					}catch (Exception $e) {
						$auth->clearIdentity();
						$this->errorlogin = 'Loggin Failure';
					}
				}else{
					$this->errorlogin = 'user does not exist!';	
					return $this->_helper->redirector->gotoRoute(array('controller' => 'user', 'action' => 'index'),'default', true);	    		
				}
			}else{

					$this->errorlogin = 'user does  not exist!';
					return $this->_helper->redirector->gotoRoute(array('controller' => 'user', 'action' => 'index'),'default', true);
			}
    	}
    	
    }
    
	public function logoutAction() {
		Zend_Auth::getInstance()->clearIdentity();
		
		return $this->_helper->redirector->gotoRoute(array(
						'controller' => 'index', 'action' => 'index'),
						'default', true);
	}
	
	public function printerrorloginAction()
	{
		 $this->view->errorlogin = $this->errorlogin;
	}
	
	public function createForm($tableid,$idValue,$fk,$fkvalue)
	{
		if ($tableid == 'idUser')
		{
			require_once(APPLICATION_PATH . '/forms/user.php');
			return new Form_User();
		}
		
	}
	
   public function edituserAction()
    {
    	$this->save('idUser','edit','index');
    }
    
    public function adduserAction()
    {
    	$this->save('idUser','add','index');
    }
    
	public function deleteuserAction()
    {
    	$this->delete('idUser','index');
    }
    
 
}
 
?>