<?php
class BaseController extends Zend_Controller_Action
{
	protected $_idkey = 'id';
	protected $model;

	public function init()
    {
		
    }

    public function indexAction()
    {
   		$this->indexbaseAction();
    }
    
    
    public function indexbaseAction()
    {
   		$this->view->titleView = $this->titleView;
    	$auth = Zend_Auth::getInstance();
    	if($auth->hasIdentity()) 
    	{
			$this->indexsubaction();
			
    	}
    	else
    	{
    		return $this->_helper->redirector->gotoRoute(array(
							'controller' => 'index', 'action' => 'index'),
							'default', true );
    	}
    }

    public function editAction()
	{
		$this->save($this->_idkey,'edit','index');
	}
	
	public function addAction()
	{
		$this->save($this->_idkey,'add','index');
	}
	
	public function save($idkey,$type,$action,$fk=null,$fkvalue=null)
	{

		$idValue = $this->getRequest()->getParam($idkey);
		
		$form = $this->createForm($idkey,$idValue,$fk,$fkvalue);
		
		if ($this->getRequest()->isPost()) {
		try{
			/* determine type and save in the db */
			if($form->isValid($this->getRequest()->getPost() ) )
			{
				$data = $form->getValues();	

				if($type == 'add') 
				{
					if (isset($fk))
					{
						$data[$fk] = $fkvalue;
					}
					$res = $this->model->create($idkey,$data);
					
					if ( isset($res) )
					{
						
						return $this->retornardeaccion($this->_controllername, $action, $fk, $fkvalue);
					}
					else
					{ 
						$this->view->error = "Valor ya existe o invalido";
					}
				}
				
				if($type == 'edit')
				{
					$data[$idkey] = $idValue;

					if ( $this->model->update($idkey,$data, $idValue) > 0)
					{
						
						return $this->retornardeaccion($this->_controllername, $action, $fk, $fkvalue); 
					}
					else{
						$this->view->error = 'No se pudo actualizar' ;	
					}
				}
			}
			}
			catch(Exception $e){
				$this->view->error = $e->getMessage();
			}
		}
		/* pass the form to the view */
		if($type == 'edit') $form->populate($this->model->queryID($idkey,$idValue)->toArray());
		$this->view->form = $form;
	}
    
    public function deleteAction()
    {
    	$this->delete($this->_idkey,'index');
    	
    }
    
    public function delete($idkey,$action,$fk=null,$fkvalue=null)
    {
    	$idValue = $this->getRequest()->getParam($idkey);
		
		$this->model->delete($idkey,$idValue);
		
		return $this->retornardeaccion($this->_controllername, $action, $fk, $fkvalue); 
    }
    
    function retornardeaccion($controller, $action, $fk, $fkvalue)
    {
    
    	if (isset($fk))
		{
			return $this->_helper->redirector->gotoRoute(array(
						'controller' => $this->_controllername , 'action' => $action, $fk => $fkvalue ),
						'default', true);
		}
		else
		{
			
			return $this->_helper->redirector->gotoRoute(array(
					'controller' => $this->_controllername , 'action' => $action),
					'default', true);
		}
    }
    
     

	
}
?>