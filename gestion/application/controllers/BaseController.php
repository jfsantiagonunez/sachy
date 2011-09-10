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
    
     
    public function autocompletecalidadAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
        $match = trim($this->_getParam('qcalidad', ''),'*');
 
        $resultados = $this->getCalidadAuto($match);
        
    	$data = new Zend_Dojo_Data('calidad', $resultados,'calidad');
 
        // Send our output
        $this->_helper->autoCompleteDojo($data);
    }
    
    
 	public function autocompletecolorAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
        $match = trim($this->_getParam('qcolor', ''),'*');
 		$matchcalidad = trim($this->_getParam('qcalidad', ''),'*');
        $resultados = $this->getColorAuto($match,$matchcalidad);
        
    	$data = new Zend_Dojo_Data('color', $resultados,'color');
 
        // Send our output
        $this->_helper->autoCompleteDojo($data);
    }
    
	public function autocompletetipoenvaseAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
        $match = trim($this->_getParam('qtipoenvase', ''),'*');
        $color = trim($this->_getParam('qcolor', ''),'*');
 		$calidad = trim($this->_getParam('qcalidad', ''),'*');
        $resultados = $this->getTipoEnvaseAuto($match,$calidad,$color);
        $matches = array();

        foreach ($resultados as $match )
        {
        	$matchn = array('tipoenvase' => (string)$match['tipoenvase']);
        	$matches[] = $matchn;
        }
        
    	$data = new Zend_Dojo_Data('tipoenvase', $matches,'tipoenvase');
 
        // Send our output
        $this->_helper->autoCompleteDojo($data);
    }
    
    public function getCalidadAuto($id)
    {
    	return $this->model->queryAutocompletionCalidades($id)->toArray();
    }
    
	public function getColorAuto($id,$calidad)
    {
    	return $this->model->queryAutocompletionColor($id,$calidad)->toArray();
    }
    
	public function getTipoEnvaseAuto($id,$calidad,$color)
    {
    	return $this->model->queryAutocompletionTipoEnvase($id,$calidad,$color)->toArray();
    }
    //Experiment
    
 	public function addmovimientoajaxAction()
   	{
   	   	$this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
        $data = $this->getRequest()->getParams();

		if (isset($data) && isset($data['format']) && ($data['format']=='ajax'))
		{
			
			$this->model->createMovimiento($data);

		}
		$this->listmovimientosreturn($data);
		
   	}
   	
   	public function listmovimientosAction()
   	{
        $data = $this->getRequest()->getParams();

   		$this->listmovimientosreturn($data);	
   	}
   	
   	
	public function deleteajaxmovimientoAction()
   	{
   		$this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
        
        $data = $this->getRequest()->getParams();

        $idValue = $data['idMovimiento'];
        if (isset($idValue))
        {
        	$this->model->delete('idMovimiento',$idValue);
        }
        
   		$this->listmovimientosreturn($data);	
   	}
   	

   	
   	public function listmovimientosreturn(array $data)
   	{

		if (isset($data))
		{
			$this->view->controller = 'albaran';
			$this->view->idKey = 'idAlbaran';
			$tabla = 'TblAlbaran';
			
			if (isset($data['idFactura'])&&(!isset($data['idAlbaran'])))
			{
				$this->view->controller = 'factura';
				$this->view->idKey = 'idFactura';
				$tabla = 'TblFactura';
			}
			$this->view->idkeyvalue=$data[$this->view->idKey];
			$this->view->objeto = $this->model->queryID($tabla,$this->view->idkeyvalue);
			
			$this->view->movimientos = $this->model->queryMovimientos($this->view->idKey,$this->view->idkeyvalue);
			echo $this->view->render('base/listmovimientos.ajax.phtml') ;
		}
		else
		{
			echo 'Introduzca movimientos';
		}
   	}
   	
   		function generateSelect( $selectname, $id ,array $data ,$default, $tag=null)
	{
		
		$select = '<select name="' . $selectname . '" id="'. $selectname . '" style="width:100px" >';

		if (isset($tag))
		{
			foreach ($data as $option ) 
			{
				$selected = '';
				if (strcmp($default , $option[$id]) == 0 )
				{
					$selected = 'SELECTED';
				}

                 $select.='<option '.$selected.' value="'.$option[$id].'">'. $option[$tag].'</option>';
			}
		}
		else
		{
			$keys= array_keys($data);
			
			foreach($keys as $key)
			{
				$selected = '';
				if (strcmp( $default, $key) == 0 )
				{
					$selected = 'SELECTED';				
				}

				$select.='<option '.$selected.' value="'.$key.'">'. $data[$key].'</option>';
			}
		}

		$select .= '</select>';
		return $select;
	}
	
	function createPage($pdf,$size)
	{
		$page = $pdf->newPage($size); //Zend_Pdf_Page::SIZE_A4 595:842:
		$pdf->pages[] = $page; 
		$page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 12);
		return $page;
	}
		
	function imprimirMovimientos($documento,$cliente,$key, $page,$pdf)
	{
			
		$yinit = 655;	  $ymin = 350;
		$esfactura = true;
		if ($key == 'idAlbaran')
		{
			$esfactura = false;
			$yinit = 500;
			$ymin = 25;
		}
		$y = $yinit; 
			
		$lh = 12; $xc = 20;	$xcl = 60;	$xct = 97;	$xte = 135;
		$xctt = 195;	$xu = 250;	$xdes = 278;	$xp = 452;	$xtp = 525;
		
		$movimientosporpagina = floor( ($yinit-$ymin-$lh)  / $lh ); // Approx 2 lineas cada 25. Tamano fuente. Reseva una linea para imprimir numero linea
		
		$movimientos = $this->model->queryMovimientos($key,$documento[$key]);
		$numeromovimentos = $movimientos->count();
		
		if ($esfactura)
		{
			$albaranes = $this->model->queryAlbaranesPorFactura($documento['idFactura']);
			$numeromovimentos += $albaranes->count();
		}
		
		$numeropaginas = ceil( $numeromovimentos / $movimientosporpagina);
		$pagina = 1;
		$estealbaran = 'ZZZZZ';
		foreach($movimientos as $movimiento)
		{
			if ($esfactura)
			{
				if (strcmp($estealbaran,$movimiento['idAlbaran']) != 0 )
				{
					$estealbaran = $movimiento['idAlbaran'];
					
					if ($estealbaran == '')
					{
						// AQUI EL NUMERO DE LINEAS PUEDE CRECER
						// PERO COMO LOS MOVIMIENTOS ESTAN ORDENADOS POR idalbaran ascendente
						// ESTAREMOS A TIEMPO DE CORREGIR EL NUMERO PAGINAS
						
						// Recalcular paginas
						$numeromovimentos++;
						$numeropaginas = ceil( $numeromovimentos / $movimientosporpagina);
						$page->drawText('---  Referencias Sin Albaran  ---', $xdes, $y, 'UTF-8');
					}
					else
					{
						$albaran = $this->model->queryID('idAlbaran',$estealbaran);
						$page->drawText('---  N. Albaran: ' .$albaran['numeroalbaran'] .'  ---', $xdes, $y, 'UTF-8');
					}
				
					$y-=$lh;
					if ($y<($ymin+$lh))
					{
						$page->drawText('Pagina '.$pagina.'/'.$numeropaginas , $xdes, $y,'UTF-8');
						$pagina++;
						$size = $page->getWidth() . ':' . $page->getHeight() .':';
						$page = $this->createPage($pdf,$size);
						$this->imprimirCabecera($documento,$cliente,$page);
						$y = $yinit;
					}
				}
			}
			$page->drawText($movimiento['calidad'], $xc, $y, 'UTF-8');
			$page->drawText($movimiento['color'], $xcl, $y,'UTF-8');
			$page->drawText(sprintf("%2.3f",$movimiento['tipoenvase']), $xte, $y,'UTF-8');
			$page->drawText(sprintf("%4d",$movimiento['tcantidad']), $xct, $y,'UTF-8');
			$tipoenvase = (int)$movimiento['tipoenvase'];
			$cantidad = (int)$movimiento['tcantidad'];
			$unidad = 'U';
			if ($tipoenvase > 1 ) 
			{ 
				$cantidad*=$tipoenvase;
				$unidad = 'L';
			}
			$page->drawText(sprintf("%5d",$cantidad),  $xctt, $y,'UTF-8');
			$page->drawText($unidad, $xu, $y,'UTF-8');
			$page->drawText($movimiento['descripcion'], $xdes, $y,'UTF-8');
			$descuento = (int) $movimiento['descuento'];
			$precio = $movimiento['precio'];
			$descu = '';
			if ($descuento > 0 )
			{
				$descu = '('.$descuento.'%)';
			}
			$page->drawText(sprintf("%3.02f %s",$precio,$descu), $xp, $y,'UTF-8');
			
											
			$page->drawText(sprintf("%7.02f",(float)$movimiento['tprecio']),$xtp,$y,'UTF-8');
			
			$y-=$lh;
			if ($y<($ymin+$lh))
			{
				$page->drawText('Pagina '.$pagina.'/'.$numeropaginas , $xdes, $y,'UTF-8');
				$pagina++;
				$size = $page->getWidth() . ':' . $page->getHeight() .':';
				$page = $this->createPage($pdf,$size);
				$this->imprimirCabecera($documento,$cliente,$page);
				$y = $yinit;
			}
		}
		$page->drawText('Pagina '.$pagina.'/'.$numeropaginas /*.'/'.$numeromovimentos*/, $xdes, $ymin,'UTF-8');
		
		
		return $page;
	}
	
	function imprimirPaginaReferencia($page)
	{
			
		/*$page->drawText("100,-", 100, 10, 'UTF-8');
		$page->drawText("200,-", 200, 10, 'UTF-8');
		$page->drawText("300,-", 300, 10, 'UTF-8');
		$page->drawText("400,-", 400, 10, 'UTF-8');
		$page->drawText("500,-", 500, 10, 'UTF-8');
		
		$page->drawText("-,100", 10, 100, 'UTF-8');
		$page->drawText("-,200", 10, 200, 'UTF-8');
		$page->drawText("-,300", 10, 300, 'UTF-8');
		$page->drawText("-,400", 10, 400, 'UTF-8');
		$page->drawText("-,500", 10, 500, 'UTF-8');
		$page->drawText("-,600", 10, 600, 'UTF-8');
		$page->drawText("-,700", 10, 700, 'UTF-8');
		$page->drawText("-,800", 10, 800, 'UTF-8');*/
			
		$width  = $page->getWidth();
    	$height = $page->getHeight();
    	
    	//$page->drawText('Width: '.$width.' Height:'.$height, $width/2, $height/2, 'UTF-8');
		for($i=0;$i<2;$i++)
		{
			$stepx = 0;
			$stepy = 0;
			$x = 25; 
			$y= 25;
			if ($i==0)
				// Print horizontal lines
				$stepy=25;
			else
				$stepx=25;
			
			for (;$x<$width && $y<$height; )
			{
				$x2 = $width;
				$y2 = $height;
				if ($stepx != 0)
				{
					$x2 = $x;
				}
				if ($stepy != 0)
				{
					$y2 = $y;
				}
				if (($x%100==0) || ($y%100==0))
					$page->setLineWidth(1); 
				else
					$page->setLineWidth(0.5); 
				$page->drawLine($x, $y, $x2,$y2); 
				$x+=$stepx;
				$y+=$stepy;
			}
		
		}
	
	}
	
}
?>