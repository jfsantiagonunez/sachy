<?php
class Form_Albaran extends Zend_Form

{


   public function __construct($option=null)
   {
       parent::__construct($option);
       $this->setMethod('post');
       
       require_once(APPLICATION_PATH . '/models/ModelCliente.php');
	   $modelCliente = new ModelCliente();
       $tiendas = (array)$modelCliente->queryAll('TblStockDeposito');
		
  
		$list = array();
		$list['0'] = 'salida';
		$list['1'] = 'entrada';
		
        $entrada=$this->CreateElement('select','entrada', array(
					'label' => 'Entrada',
					'multiOptions'             => $list,
    				'required'                 => true,
    				'registerInArrayValidator' => false));

 

       $idDeposito=$this->CreateElement('select','idDeposito', array(
					'label' => 'Tienda',
					'multiOptions'             => $tiendas,
    				'required'                 => true,
    				'registerInArrayValidator' => false));
       
       $cliente = $this->CreateElement('text','cliente',array(
					'value' => 'Cliente'));

       $submit=$this->CreateElement('submit','submit')
                       ->setLabel('Nuevo Albaran');
       
       
       $this->addElements(array(
               $entrada,
               $idDeposito,
               $cliente,
               $submit
       ));

       $this->setDecorators(array(
               'FormElements',
               array(array('data'=>'HtmlTag'),array('tag'=>'table')),
               'Form'
       ));

   }

}

?>