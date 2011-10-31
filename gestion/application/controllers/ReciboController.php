<?php
require_once(APPLICATION_PATH . '/controllers/BaseController.php');

class ReciboController extends BaseController
{
	var $model;
	var $modelCliente;
	var $titleView;
	protected $_idkey = 'idFactura';
	protected $_controllername = 'recibo';
	
	
    public function init()
    {

		$this->titleView='Gestion Recibos';
		require_once(APPLICATION_PATH . '/models/ModelFactura.php');
		$this->model = new ModelFactura();
		require_once(APPLICATION_PATH . '/models/ModelCliente.php');
		$this->modelCliente = new ModelCliente();
    }
    
    public function indexsubaction()
    {   
    	$this->titleView='Gestion Facturas'; 

    	if($this->getRequest()->isPost())
       	{
       		
    		$data = $this->getRequest()->getPost();
    		if ( isset($data['anterior'] ) )
    		{
    			$this->view->anterior = $data['anterior'];
    		}
    		else
    		{
    			$this->view->anterior = $this->fecha('0');
    		}
       		if ( isset($data['posterior'] ) )
    		{
    			$this->view->posterior = $data['posterior'];
    		}
    		else
    		{
    			$this->view->posterior = $this->fecha('1');
    		}
       	}
       	else
       	{
    		$this->view->anterior = $this->fecha('0');
    		$this->view->posterior = $this->fecha('1');
       	}
    	$this->view->recibos = $this->model->getLatestRecibos($this->view->anterior,$this->view->posterior);
    }
    
    public function noreciboAction()
    {
    	$idFactura = $this->getRequest()->getParam('idFactura');
    	
    	if (isset($idFactura))
    	{
    		$factura = $this->model->queryId('idFactura',$idFactura);
    		if (isset($factura))
    		{
    			$factura['recibo'] = '0';
    			$factura->save();
    		}
    	}
		return $this->_helper->redirector->gotoRoute(array(
					'controller' => 'recibo' , 'action' => 'index' ),
					'default', true);
    }
    
    function fecha($rango)
    {
    	
	$valor = time();
    	if ( $rango == '0')
    	{
    		//$hoy->sub( DateInterval::createFromDateString( '5 days'));
    	}
    	else
    	{
	// Sumar 5 dias
	    		$valor += (5*24*60*60);
    	}

       	
    	$day = (int) date('d',$valor);
       	$seg = round($day/5);
       	if ($seg==0)
       		$day = 1;
       	else
       		$day = 5*$seg;
       	
       	if ( $rango == '0')
       	{
       		$day++;
       	}
       	
			
    $hoy = date("Y-m-d", mktime(0, 0, 0, date("m")  , $day, date("Y")));

    		return $hoy;	
    }
    
	public function imprimirAction()
	{
		$this->_helper->layout->disableLayout();
		require_once('Zend/Pdf.php'); 
		$pdf = new Zend_Pdf(); 

		// Add new page to the document 
		$size = '595:842:';
		$page = $this->createPage($pdf,$size);
		
		$yinit = 775;	  $ymin = 35;

		$y = $yinit; 
			
		$lh = 15; $xc = 25;	
		
		$movimientosporpagina = floor( ($yinit-$ymin-$lh)  / $lh ); // Approx 2 lineas cada 25. Tamano fuente. Reseva una linea para imprimir numero linea
		$anterior = $this->getRequest()->getParam('anterior');
		$posterior = $this->getRequest()->getParam('posterior');
		$movimientos = $this->model->getLatestRecibos($anterior,$posterior);
		$numeromovimentos = $movimientos->count();
		$numeropaginas = ceil( $numeromovimentos / $movimientosporpagina);
		$pagina = 1;
		
		$this->imprimircabecera($page,$pagina,$numeropaginas,$anterior,$posterior);
		
		foreach($movimientos as $movimiento)
		{
			if ($y<($ymin+(4*$lh)))
			{				
				$pagina++;
				$page = $this->createPage($pdf,$size);
				$this->imprimircabecera($page,$pagina,$numeropaginas,$anterior,$posterior);
				$y = $yinit;
			}
			
			$header = $movimiento['numerofactura'] . ' - ' . $movimiento['vencimiento'] . ' - ' . sprintf("[%6.02f Euros]      - ",$movimiento['total']). $movimiento['nombre'];
			$page->drawText($header, $xc, $y, 'UTF-8');
			$y-=$lh;
			
			$bancos = '[' . $movimiento['condicionespago']. '] - '. $movimiento['banco'] . ' - ' . $movimiento['sucursal'];
			$page->drawText($bancos, $xc, $y, 'UTF-8');
			$y-=$lh;
			
			$cuenta = $movimiento['cuentabancaria'] . ' - DC [' . $movimiento['dc'] . ']';
			$page->drawText($cuenta, $xc, $y, 'UTF-8');
			$y-=$lh;
			
			$page->drawText(' ', $xc, $y, 'UTF-8');
			$y-=$lh;		

		}

		$this->view->pdfData = $pdf->render(); 
		$this->view->rango = $anterior . '-' . $posterior;

	}
	
	function imprimircabecera($page,$pagina,$numeropaginas,$anterior,$posterior)
	{
		$cabecera = 'Pinturas Santiago. Vencimientos ['.$anterior.'-'.$posterior.'] Pagina '.$pagina.'/'.$numeropaginas;
		$page->drawText($cabecera , 100, 800,'UTF-8');
	}
	
	public function imprimirreciboAction()
	{
		$idFactura = $this->getRequest()->getParam('idFactura');
		$factura = $this->model->queryID('idFactura',$idFactura);
		$cliente =  $this->modelCliente->queryID('idCliente',$factura['idCliente']);
		
		$yinit = 775;	  	$y = $yinit; 	$lh = 20; $xc = 50;	
		
		$this->_helper->layout->disableLayout();
		require_once('Zend/Pdf.php'); 
		$pdf = new Zend_Pdf(); 

		// Add new page to the document 
		$size = '595:842:';
		$page = $this->createPage($pdf,$size);
		
		$page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 25);
		$page->drawText('PINTURA SANTIAGO', $xc, $y, 'UTF-8');
		$y-=30;
		
		$page->drawText('Recibo ['.$factura['numerofactura'] . ']', $xc, $y, 'UTF-8');
		$y-=30;
		$page->drawText(sprintf("Importe [ %6.02f euros ]",$factura['total']), $xc, $y, 'UTF-8');
		$y-=30;
		
		$page->drawText('Fecha Libramiento ['.$factura['fecha'].']',$xc,$y,'UTF-8');
		$y-=30;
		$page->drawText('Vencimiento ['. $factura['vencimiento'] .']',$xc,$y,'UTF-8');
		$y-=30;
		
		$page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 12);
		$page->drawText($cliente['nombre'],$xc,$y,'UTF-8');
		$y-=$lh;
		$direccion = $cliente['direccionfact'];
		$cp = $cliente['codigopostalfact'];
		$ciudad = $cliente['ciudadfact'];
		if ($direccion == '' )
		{
			$direccion = $cliente['direccion'];
			$cp = $cliente['codigopostal'];
			$ciudad = $cliente['ciudad'];
		}
		$page->drawText($direccion,$xc,$y,'UTF-8');
		$y-=$lh;
		$page->drawText($cp .' - '. $ciudad,$xc,$y,'UTF-8');
		$y-=$lh;
		
		
				
		$page->drawText($cliente['banco'].'-'.$cliente['sucursal'],$xc,$y,'UTF-8');
		$y-=$lh;
		
		$page->drawText('  ',$xc,$y,'UTF-8');
		$y-=$lh;
		
		$page->drawText($cliente['cuentabancaria'] . '     - DC [' . $cliente['dc'].']',$xc,$y,'UTF-8');
		$y-=$lh;	
		
		
		$this->view->pdfData = $pdf->render(); 
		$this->view->rango = $factura['numerofactura'];
		
	}
}
?>
