<?php
	class Form_User extends Zend_Form {

		public function init() {
			$this->setMethod('post');
			$this->setAction($this->getView()->url());

			$this->addElement('text', 'firstname', array(
					'label' => 'Nombre',
					'required' => true
			));
			
			$this->addElement('text', 'lastname', array(
					'label' => 'Apellido',
					'required' => true
			));
			
			$this->addElement('text', 'username', array(
					'label' => 'Id Usuario',
					'required' => true
			));

			$this->addElement('text', 'password', array(
					'label' => 'Clave',
					'required' => true
			));
			
			require_once(APPLICATION_PATH . '/models/ModelUser.php');
			$model = new ModelUser();
			
			$list = $model->queryToDisplay('TblStockDeposito');
			
			
			$listadmin = array();
			$listadmin['0'] = 'Normal';
			$listadmin['1'] = 'Admin';
			
						
			$this->addElement('select','idDeposito', array(
					'label' => 'Tienda',
					'multiOptions'             => $list,
    				'required'                 => true,
    				'registerInArrayValidator' => false)
			);
			
			$this->addElement('select','admin', array(
					'label' => 'Perfil',
					'multiOptions'             => $listadmin,
    				'required'                 => true,
    				'registerInArrayValidator' => false)
			);
			
			
			$this->addElement('submit', 'submit', array(
					'label' => 'Save'
			));
			$this->setDecorators(array(
    			'FormElements',
    			array('HtmlTag', array('tag' => 'table')),
    			'Form'
				));
			$this->setElementDecorators(array(
  	  			'ViewHelper',
    			'Errors',
 	  	 		array(array('data' => 'HtmlTag'), array('tag' => 'td')),
    			array('Label', array('tag' => 'td')),
   		 		array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
				));
		}
	}
?>