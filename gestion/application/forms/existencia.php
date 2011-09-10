<?php
	class Form_Existencia extends Zend_Form {

		public function init() {
			$this->setMethod('post');
			$this->setAction($this->getView()->url());

			require_once(APPLICATION_PATH . '/models/ModelCatalogo.php');
			$model = new ModelCatalogo();
			
			$depositos = $model->queryViewLocations();
			$keys = array_keys($depositos);
			foreach ($keys as $key)
			{
				$this->addElement('text', $key, array(
					'label' => $depositos[$key],
					'required' => false
				));
			}
			
			
			
			$this->addElement('submit', 'submit', array(
					'label' => 'Save'
			));
			
			
		}
	}
?>