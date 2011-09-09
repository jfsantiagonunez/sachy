<?php
	class Form_Task extends Zend_Form {

		public function init() {
			$this->setMethod('post');
			$this->setAction($this->getView()->url());

			$this->addElement('text', 'titleTask', array(
					'label' => 'Title',
					'required' => true
			));

			$this->addElement('textarea', 'descTask', array(
					'label' => 'Description',
					'required' => true
			));
			
			$list = array();
			$list['0']='Low';
			$list['1']='Medium';
			$list['2']='High';
			
			$listProgress = array();
			$listProgress['0']='To Do';
			$listProgress['1']='In Progress';
			$listProgress['2']='Done';
			
			$this->addElement('select','important', array(
					'label' => 'Importance',
					'multiOptions'             => $list,
    				'required'                 => true,
    				'registerInArrayValidator' => false)
			);
			
			$this->addElement('select','urgent', array(
					'label' => 'Urgency',
					'multiOptions'             => $list,
    				'required'                 => true,
    				'registerInArrayValidator' => false)
			);
			
			$this->addElement('select','progress', array(
					'label' => 'Progress',
					'multiOptions'             => $listProgress,
    				'required'                 => true,
    				'registerInArrayValidator' => false)
			);
			
			$this->addElement('submit', 'submit', array(
					'label' => 'Save'
			));
		}
	}
?>