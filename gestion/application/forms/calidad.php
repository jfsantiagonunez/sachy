<?php
	class Form_Calidad extends Zend_Form {

		public function init() {
			$this->setMethod('post');
			$this->setAction($this->getView()->url());

			$this->addElement('text', 'calidad', array(
					'label' => 'Calidad',
					'required' => true
			));
			
			$this->addElement('text', 'descripcion', array(
					'label' => 'Descripcion',
					'required' => true
			));
			
			require_once(APPLICATION_PATH . '/models/ModelCatalogo.php');
			$model = new ModelCatalogo();
			
			$marcas = $model->queryToDisplay('TblMarca');
			
			$categorias = $model->queryToDisplay('TblCategoria');
			
			$this->addElement('select','idMarca', array(
					'label' => 'Marcas',
					'multiOptions'             => $marcas,
    				'required'                 => true,
    				'registerInArrayValidator' => false)
			);
			
			$this->addElement('select','idCategoria', array(
					'label' => 'Categoria',
					'multiOptions'             => $categorias,
    				'required'                 => true,
    				'registerInArrayValidator' => false)
			);
			
			$this->addElement('submit', 'submit', array(
					'label' => 'Save'
			));
		}
	}
?>