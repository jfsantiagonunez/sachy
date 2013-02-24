<?php
require_once(APPLICATION_PATH . '/controllers/BaseController.php');

class ContabilidadController extends BaseController
{
	var $model;
	var $modelCliente;
	var $titleView;
	protected $_idkey = 'idFactura';
	protected $_controllername = 'contabilidad';


	public function init()
	{

		$this->titleView='Gestion Contabilidad';
		require_once(APPLICATION_PATH . '/models/ModelFactura.php');
		$this->model = new ModelFactura();
		require_once(APPLICATION_PATH . '/models/ModelCliente.php');
		$this->modelCliente = new ModelCliente();
	}

	public function indexsubaction()
	{

		if($this->getRequest()->isPost())
		{

			$data = $this->getRequest()->getPost();
			if ( isset($data['anterior'] ) )
			{
				$this->view->anterior = $data['anterior'];
			}
			else
			{
				$this->view->anterior = $this->fecha(1);
			}
			if ( isset($data['posterior'] ) )
			{
				$this->view->posterior = $data['posterior'];
			}
			else
			{
				$this->view->posterior = $this->fecha(-1);
			}
		}
		else
		{
			$this->view->anterior = $this->fecha(-1);
			$this->view->posterior = $this->fecha(1);
		}
		$this->view->asientos = $this->model->getLatestAsientos($this->view->anterior,$this->view->posterior);
	}


	function fecha($rango)
	{
		$time = time();
			
		$day = (int) date('d',$time);
			
			
		$seg = $day % 5;
		if ($rango == 1)
		{
			$seg = 5 - $seg;
		}
		$offset = $rango * $seg;

		$fecha = date("Y-m-d", mktime(0, 0, 0, date("m")  , date("d") + $offset, date("Y")));

		return $fecha;
	}

	public function imprimirAction()
	{
		$this->_helper->layout->disableLayout();
		$anterior = $this->getRequest()->getParam('anterior');
		$posterior = $this->getRequest()->getParam('posterior');
		$rango = $anterior . '-' . $posterior;

		$movimientos = $this->model->getLatestAsientosExportarCsv($anterior,$posterior)->toArray();

		$cabecera = array( 'numerofactura','fecha','ordencliente','condicionespago','vencimiento','baseimponible','tipoiva','iva','total',
					'cuentaventa','nombre','banco','cuentabancaria','dc','sucursal');

		$this->view->fname = './export/contabilidad_'.$rango.".csv";

		
		$fp = fopen($this->view->fname, 'w');
		
		fputcsv($fp,$cabecera);
		
		foreach($movimientos as $movimiento)
		{
			fputcsv($fp, $movimiento);
		}
		fclose($fp);
	}


	public function imprimirAction2()
	{
		$this->_helper->layout->disableLayout();
		$anterior = $this->getRequest()->getParam('anterior');
		$posterior = $this->getRequest()->getParam('posterior');
		$rango = $anterior . '-' . $posterior;

		$movimientos = $this->model->getLatestAsientos($anterior,$posterior)->toArray();

		$this->view->fname = 'contabilidad_'.$rango.".csv";

		echo "CUCU";
		/*$fp = fopen($this->view->fname, 'w');
		 foreach($movimientos as $movimiento)
		 {
			fputcsv($fp, $movimiento);
			}
			fclose($fp);*/
		ob_start();

		// output Stream fŸr fputcsv
		$fp = fopen("php://output", "w");

		if (is_resource($fp))
		{
			echo "HOLA";

			foreach($movimientos as $movimiento)
			{
				fputcsv($fp, $movimiento);
				// fputcsv($fp, $aryLine, ';', '"');
			}

			$strContent = ob_get_clean();

			// Excel SYLK-Bug
			// http://support.microsoft.com/kb/323626/de
			$strContent = preg_replace('/^ID/', 'id', $strContent);

			$strContent = utf8_decode($strContent);
			$intLength = mb_strlen($strContent, 'utf-8');

			// length
			header('Content-Length: ' . $intLength);

			// kein fclose($fp);

			$this->view->content =  $strContent;
			exit(0);
		}
		ob_end_clean();
		echo "FIN";
		exit(1);

	}

}
?>
