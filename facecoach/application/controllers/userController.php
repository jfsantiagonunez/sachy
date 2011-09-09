<?php
class UserController extends Zend_Controller_Action
{
	var $model;
	var $errorlogin;
	var $titleView;
	
    public function init()
    {
        /* Load DB_model */

		require_once(APPLICATION_PATH . '/models/TblUser.php');
		$this->model = new TblUser();		
		$this->titleView = "Login";
    }

    public function indexAction()
    {
    	$this->view->titleView = $this->titleView;
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
						
							return $this->_helper->redirector->gotoRoute(array('controller' => 'vision', 'action' => 'index', 'user'  => $data['idUser']),'default', true);
						}
						else
						{
							$this->errorlogin = "Password incorrect!!";
							return $this->_helper->redirector->gotoRoute(array('controller' => 'user', 'action' => 'index'),'default', true);
						}
					}catch (Exception $e) {
						$auth->clearIdentity();
						echo 'Loggin Failure';
					}
				}else{
					echo 'user does not exist!';	
					return $this->_helper->redirector->gotoRoute(array('controller' => 'user', 'action' => 'index'),'default', true);	    		
				}
			}else{
					echo 'user does  not exist!';
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
		echo $this->errorlogin;
	
	}
	public function newuserAction()
	{
		$this->saveuser('add');
	}
	
	public function editAction()
	{
		$this->saveuser('edit');
	}
	
	function saveuser($type)
	{
		require_once(APPLICATION_PATH . '/forms/user.php');
		$form = new Form_User();
		
		
		$idUser = $this->getRequest()->getParam('idUser');
		
		if ($this->getRequest()->isPost()) {
		try{
			/* determine type and save in the db */
			if($form->isValid($this->getRequest()->getPost() ) )
			{
				$data = $form->getValues();	

				if($type == 'add') 
				{
					$res = $this->model->createUser($data['username'],$data['password'],$data['firstname'],$data['lastname']);
					if ( $res != '0' )
					{
						$user_id = $res;
						return $this->_helper->redirector->gotoRoute(array(
							'controller' => 'calendar', 'action' => 'add', 'idUser_id' => $idUser ),
							'default', true);
					}
					else
					{ 
						$this->view->error = "Username already exists";
					}
				}
				
				if($type == 'edit')
				{
					$data['idUser'] = $idUser;
					$res = $this->model->update($data, $idUser);
				
					if ( $res == 1) {
									return $this->_helper->redirector->gotoRoute(array(
						'controller' => 'setting', 'action' => 'index', 'idUser' => $idUser ),
						'default', true); 
					}else{
						$this->view->error = 'Response' . $res;	
					}
				}
			}
			}
			catch(Exception $e){
				$this->view->error = $e->getMessage();
			}
		}
		/* pass the form to the view */
		if($type == 'edit') $form->populate($this->model->get($idUser)->toArray());
		$this->view->form = $form;
	}
	
  
	public function coacherslistAction()
    {
    	
    	
    	$auth = Zend_Auth::getInstance();
    	if($auth->hasIdentity()) 
    	{
    		// Query Vision of the user
			$idUser = $auth->getIdentity()->idUser;
			$this->view->idUser = $idUser;
			$this->view->mycoachers = $this->model->getCoachersUserKey($idUser,'1');
			$this->view->othercoachers = $this->model->getCoachersUserKey($idUser,'0');
    	}
    	else
    	{
    		$this->view->NoLoginMessage = 'Login, and define your coachers';
    	}
    }
	
    public function selectcoacherAction()
    {
    	$this->selectCoacher('yes');
    }
    
    public function unselectcoacherAction()
    {
    	$this->selectCoacher('no');
    }
    
    function selectCoacher($action)
    {
		$auth = Zend_Auth::getInstance();
    	if($auth->hasIdentity()) 
    	{
    		// Query Vision of the user
			$idUser = $auth->getIdentity()->idUser;
			$this->view->idUser = $idUser;
			if (!isset($idUser))
			{
				$this->view->NoLoginMessage = 'Login, and define your coachers';
			}
			
			if ($action == 'yes' )
			{
				$idCoacher = $this->getRequest()->getParam('idUser');
					
				if (isset($idCoacher))
				{
					$this->model->linkCoacher($idUser,$idCoacher);
				}
			}
			else
			{
				$idCoacher = $this->getRequest()->getParam('idCoacher');
					
				if (isset($idCoacher))
				{
					$this->model->unlinkCoacher($idUser,$idCoacher);
				}
			}
			
			return $this->_helper->redirector->gotoRoute(array(
							'controller' => 'user', 'action' => 'coacherslist' ),
							'default', true); 
    	}
    	else
    	{
    		$this->view->NoLoginMessage = 'Login, and define your coachers';
    	}
    }
}
 
?>