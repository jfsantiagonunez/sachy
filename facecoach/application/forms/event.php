<?php
	class Form_Event extends Zend_Form {

		public function init() {
			$this->setMethod('post');
			$this->setAction($this->getView()->url());

			$this->addElement('text', 'title', array(
					'label' => 'Title',
					'required' => true
			));
			
			$this->addElement('select','idCalendar', array(
					'label' => 'Calendar',
    				'required'                 => true,
    				'registerInArrayValidator' => false)
			);

			$this->addElement('text', 'where', array(
					'label' => 'Where',
					'required' => false
			));
	
			$this->addElement('text', 'startdate', array(
					'label' => 'Start Date (YYYY-MM-DD)',
					'required' => true
			));
			
			$this->addElement('text', 'enddate', array(
					'label' => 'End Date (YYYY-MM-DD)',
					'required' => true
			));

			$this->addElement('text', 'starttime', array(
					'label' => 'Start Time (HH:MM)',
					'required' => true
			));
			
			$this->addElement('text', 'endtime', array(
					'label' => 'End Time (HH:MM)',
					'required' => true
			));
	
			$this->addElement('submit', 'submit', array(
					'label' => 'Save'
			));
		}


		
		public function addTaskCalendar($titleTask,$calendars)
		{
			$this->addElement('text', 'title', array(
					'label' => 'Title',
					'required' => true,
					'value' => $titleTask
			));
			$this->addElement('select','idCalendar', array(
					'label' => 'Calendar',
					'multiOptions'             => $calendars,
    				'required'                 => true,
    				'registerInArrayValidator' => false)
			);
		}
		
	}
?>