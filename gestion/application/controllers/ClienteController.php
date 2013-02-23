<?php

require_once(APPLICATION_PATH . '/controllers/BaseController.php');

class ClienteController extends BaseController
{
	var $model;
	var $modelAlbaran;
	var $titleView;
	protected $_idkey = 'idCliente';
	protected $_controllername = 'cliente';
	
    public function init()
    {

		$this->titleView='Gestion Clientes';
		require_once(APPLICATION_PATH . '/models/ModelCliente.php');
		$this->model = new ModelCliente();
		
		require_once(APPLICATION_PATH . '/models/ModelAlbaran.php');
		$this->modelAlbaran = new ModelAlbaran();
    }

    
    public function indexAction()
    {
   		$this->indexbaseAction();
    }
    
    public function indexsubaction()
    {   
    	$this->titleView='Gestion Clientes';
    	$tableid = $this->model->getTableId();
    	
       	if($this->getRequest()->isPost())
       	{
       		
    		$data = $this->getRequest()->getPost();
    		if ( isset($data['cliente']))
    		{
    			return $this->_helper->redirector->gotoRoute(array(
					'controller' => 'cliente' , 'action' => 'index', 'cliente' => $data['cliente'] ),
					'default', true);
    		}
    	}
    	else
    	{
    		$value = $this->getRequest()->getParam('cliente');
    	   	if ( isset($value))
    		{
    			$this->view->tituloBusqueda = 'Clientes que coinciden con ['.$value.']';
    			$this->view->clientes = $this->model->query($tableid,$value);
    			return;
    		}
    		
    		$value = $this->getRequest()->getParam('idReferencia');
    	   	if ( isset($value))
    		{
    			$this->view->consumo = '1';
    			$ref = $this->modelAlbaran->queryID('TblReferencia',$value)->toArray();
    			$this->view->tituloBusqueda = 'Clientes que consumen ['.$ref['calidad'].'-'.$ref['color'].'-'.$ref['tipoenvase'].'](unidades) en '.date('Y');
    			$this->view->clientes = $this->modelAlbaran->queryReferenciasClientes($value);
    			return;
    		}
    		$this->view->tituloBusqueda = 'Todos Clientes';
			$this->view->clientes = $this->model->queryAll($tableid);
    		
    	}
    }
    
	
	public function createForm($idkey,$idValue,$fk,$fkvalue)
	{
		if ($idkey == 'idCliente')
		{
			require_once(APPLICATION_PATH . '/forms/cliente.php');
			$form = new Form_Cliente();
			$form->initWith($idValue);
			return $form;
		}
		else if ($idkey == 'idDescuento')
		{
			require_once(APPLICATION_PATH . '/forms/descuento.php');
			return new Form_Descuento();
		}
		
	}
	
	public function indexdescuentoAction()
	{

		$value = $this->getRequest()->getParam('idCliente');
		if (isset($value))
		{
			$this->view->idCliente = $value;
			$cliente = $this->model->queryID('TblCliente',$value);
			$this->view->titleView='Gestion Descuentos Cliente <br/>[' . $cliente['nombre'] .']';
			$this->view->descuentos = $this->model->queryFK('TblDescuento',$value);
		}
	}
	
	
	public function adddescuentoAction()
	{
		$idCliente = $this->getRequest()->getParam('idCliente');
		if (isset($idCliente))
		{
			$this->save('idDescuento','add','indexdescuento','idCliente',$idCliente);
		}
		else
		{
			echo 'NO CLIENTE';
		}
	}
	
	public function editdescuentoAction()
	{
		$idCliente = $this->getRequest()->getParam('idCliente');
		if (isset($idCliente))
		{
			$this->save('idDescuento','edit','indexdescuento','idCliente',$idCliente);
		}
		else
		{
			echo 'NO CLIENTE';
		}
	}
    
	public function deletedescuentoAction()
    {
    	$idCliente = $this->getRequest()->getParam('idCliente');
    	$this->delete('idDescuento','indexdescuento','idCliente',$idCliente);
    }
    
    
    public function convertAction()
    {
    	$ventas = array();
    	/*
    	$ventas[4] = 'FLUIMED,SL';
$ventas[105] = 'Vcia Container Depot SL';
$ventas[108] = 'Mercadona SA';
$ventas[122] = 'Ayuntament d Aldaia';
$ventas[136] = 'Friopuerto Valencia SL';
$ventas[150] = 'Garcia Sanjaime SL';
$ventas[155] = 'ISTOBAL,S.A.';
$ventas[158] = 'Pavasal SA';
$ventas[163] = 'BRONCES LEVANTE,S.L.';
$ventas[170] = 'Promotest SL';
$ventas[185] = 'DROGERêA ORTê GALLARC,S.L.';
$ventas[197] = 'Soldagas SL';
$ventas[207] = 'Chema Ballester SA';
$ventas[209] = 'JL Gandara y CIA SL';
$ventas[216] = 'SUM.NAVALES PALONES';
$ventas[222] = 'Pinturas Hempel SL';
$ventas[226] = 'Sendra Font Juan Antonio';
$ventas[275] = 'Recomar SA';
$ventas[277] = 'Exma Diputacion Provincial';
$ventas[280] = 'Francisco Just SL';
$ventas[352] = 'Cardyfrem SL';
$ventas[375] = 'Same Shipsuppliers SL';
$ventas[385] = 'Talleres Muros SL';
$ventas[439] = '$ventas Mostrador Visa';
$ventas[443] = '$ventas Contado';
$ventas[508] = 'Cnes Mecanicas Roypa SL';
$ventas[529] = 'Plazas de Toros de la Ribera';
$ventas[556] = 'Bous al Carrer SL';
$ventas[562] = 'COINMAQ,S.L.';
$ventas[573] = 'Amarradores del Puerto';
$ventas[586] = 'Servic y Color Ondara SL';
$ventas[616] = 'Covalsum SL';
$ventas[639] = 'Ascensores Carbonell SA';
$ventas[670] = 'FERRER BONAFONT, VICENTE';
$ventas[691] = 'SERV.MARêTIMO PROVINCIAL,G.C.';
$ventas[783] = 'Valenciana de Serv ITV SA';
$ventas[813] = 'Miguel Rivera Autocar SL';
$ventas[834] = 'Recamb Colon Catarroja SL';
$ventas[837] = 'Selev Naval SL';
$ventas[890] = 'DELTA VALENCIA,S.L.';
$ventas[908] = 'Forja Garcia sl';
$ventas[1042] = 'Herrajes La Costera SL';
$ventas[1051] = 'PACHICHA,C.B.';
$ventas[1076] = 'Union Naval Containers SL';
$ventas[1116] = 'Cavero Angulo Jose Luis';
$ventas[1135] = 'MADERAS VILA MARTê,S.L.';
$ventas[1197] = 'Soldagas SL';
$ventas[1200] = 'Moya Pe±arubia Jose';
$ventas[1204] = 'TALLERES DE LA GUIA,S.L.';
$ventas[1221] = 'Estelles Inoxtean Emilio';
$ventas[1230] = 'Garbaport SL';
$ventas[1248] = 'IND.QUêMICAS DEL VALLéS,S.A.';
$ventas[1252] = 'TRANSBELLES,S.L.';
$ventas[1284] = 'Cofr de pescadores Valencia';
$ventas[1312] = 'CASAR DE BURBIA,SL';
$ventas[1387] = 'GARAJE RICART,S.L.';
$ventas[1392] = 'Nautica Aza SL';
$ventas[1393] = 'Ferreteria Limar SL';
$ventas[1399] = 'RMN Distr y Almacenaje';
$ventas[1401] = 'Prama Mant y serv Ind SL';
$ventas[1420] = 'NIXEL 2005 SL';
$ventas[1489] = 'TALLERES CERVERO,S.L.';
$ventas[1502] = 'Tecdemar Promot NaVAL sl';
$ventas[1530] = 'Pascual Aranandis Mtnez SL';
$ventas[1561] = 'Ferbol Pintores SL';
$ventas[1575] = 'Transports SA';
$ventas[1581] = 'SOL, LLUNA I MAR,CB';
$ventas[1590] = 'TERMINALES PORTUARIAS,S.L.';
$ventas[1614] = 'LABERINTUS DE SINGN,S.L.';
$ventas[1636] = 'Francisco Rueda SL';
$ventas[1637] = 'COPISTERIA SANCHIS,SL';
$ventas[1641] = 'Reamar SL';
$ventas[1655] = 'Sumlevinter Industrial SL';
$ventas[1664] = 'UTE S Y D AGUAS XXXIV';
$ventas[1672] = 'Manexval Levante SL';
$ventas[1682] = 'Cadagua SA';
$ventas[1696] = 'Sum Grales Escombrera SA';
$ventas[1697] = 'Cu±at Guillen Amador';
$ventas[1712] = 'Fenix Atracciones SL';
$ventas[1722] = 'Efectos Navales SL';
$ventas[1729] = 'VERîNICA MAYEL,C.B.';
$ventas[1744] = 'HNOS.MELIA ORTOLI,C.B.';
$ventas[1746] = 'Nueve Ana y Onremo CB';
$ventas[1750] = 'ADDAX TEAM,SL';
$ventas[1751] = 'ACCION LOGISTICA PUERTO, S.L.';
$ventas[1754] = 'Palop Calatayud Juan Enri';
$ventas[1755] = 'BECSA';
$ventas[1766] = 'Arco Proy Urbanisticos SL';
$ventas[1767] = 'UTE EDAR PINEDO III';
$ventas[1769] = 'UTE SyD Aguas 68';
$ventas[1770] = 'Victoria Hortensioa CB';
$ventas[1772] = 'SALAZAR RODRIGUEZ, ISMAEL';
$ventas[1773] = 'REVALCAR,S.C.';
$ventas[1774] = 'Ruiz Decolor SL';
$ventas[1789] = 'King Marine SA';
$ventas[1790] = 'Techno Parts Direct SL';
$ventas[1791] = 'Eurofreno SM';
$ventas[1792] = 'HNOS. PELLICER,CB';
$ventas[1796] = 'SANCHEZ PALERO,MANUEL';
$ventas[1800] = 'Ma±ez Palacios Rafael';
$ventas[1801] = 'Tanev Jelev Iordan';
$ventas[1803] = 'Inversiones Real Xatur sl';
$ventas[1806] = 'NEUMEC SILLA,C.B.';
$ventas[1807] = 'CARROCERIAS GUEVARA, S.L.L.';
$ventas[1809] = 'Campos Lleo M Vicenta';
$ventas[1811] = 'SUMINISTROS DICA,S.L.U.';
$ventas[1812] = 'CARPINTERIA METALICA LUZ,S.L.';
$ventas[1813] = 'SALVAT LOGêSTICA,S.A.';
$ventas[1814] = 'AGROMECANICA GIMENO, S.L.';
$ventas[1815] = 'CORTELL Y ORTS, S.L.';
$ventas[1816] = 'HERRAJES MASOTA, S.L.';
$ventas[1817] = 'SUMINISTRES I SERIGRAFIA YCEL, S.L.';
$ventas[1818] = 'GRANALLADO Y TRATAM. DE SUPERF. S.A.';
$ventas[1819] = 'PARDOS GARCêA, ALEJANDRO';
$ventas[1820] = 'I.E.S. BLASCO IBA„EZ';
$ventas[1821] = 'FERRETERIA ALMENAR, C.B.';
$ventas[1822] = 'JOAN STUPIMEAM';
$ventas[1823] = 'PINTURAS TRICOJEP, S.L.U.';
$ventas[1824] = 'ALVA, S.L.';
$ventas[1825] = 'ANJOMAR, C.B.';
$ventas[1826] = 'ACCESORIOS DE PINTURA TMS';
$ventas[1827] = 'MARIOSAI 4, S.L.';
$ventas[1828] = 'DISFRIMUR, S.L.';
$ventas[1829] = 'LOGIFRUIT, S.L.';
$ventas[1830] = 'YOSIFOV ZOROV, HRISTO';
$ventas[1831] = 'SUN SYSTEM FRAGA 1';
$ventas[1832] = 'MARTINEZ BELDA, OSCAR';
$ventas[1833] = 'INMOBLES GILBAEZA MODERCO, S.L.';
$ventas[1834] = 'ROLLERLUX, S.L.';
$ventas[1835] = 'MERCADONA OLIVERAL';
$ventas[1836] = 'WIND INFORMATICA, S.L.';
$ventas[1837] = 'TRANSPORTES BRULL';
$ventas[1838] = 'SCOMYTEC SEGURIDAD, S.L.';
$ventas[1839] = 'TECNOURBAN LEGAL AND CONSULTING, S.L.';
$ventas[1840] = 'PALENCIA CAMPOS, JUAN DAVID';
$ventas[1841] = 'PINTULAC, S.L.U.';
$ventas[1842] = 'QSM, C.B.';
$ventas[1843] = 'PACECO ESPA„A,S.A.';
$ventas[1844] = 'TRANSPORTES SALINAS';
$ventas[1845] = 'MANUEL BOSCH BOSCH';
$ventas[1846] = 'TALLER DE EMPLEO RUTAS SEGURASIII';
$ventas[1847] = 'PINTURAS CRUCEIRA';
$ventas[1848] = 'JESUS MIRALLES SORIANO';
$ventas[1849] = 'TALLERES JUAN HINOJOSA,S.L.';
$ventas[1850] = 'THE GARAJE FILMS,S.L.';
$ventas[1851] = 'JAVIER MARI GONZALO';
$ventas[1852] = 'HELP IN TANKS,SL';
$ventas[1853] = 'TRASEJAR,SL';
$ventas[1854] = 'BATEHX GOMEZ,S.L.';
$ventas[1855] = 'GARAJE A.GUIMERA,S.L.';
$ventas[1856] = 'RDA MOTOR,C.B.';
$ventas[1857] = 'ROTULOS 3JJJ,S.L.';
$ventas[1858] = 'JOSE MARI GONZALO';
$ventas[1859] = 'AZVI,S.A.';
$ventas[1860] = 'FRANCISCO NAVARRO NAVARRO';
$ventas[1861] = 'PINTURA MURAL Y MARINA MONZO,SL';
$ventas[1862] = 'VICENTE GIL CORTS';
$ventas[1863] = 'ROSA AMPARO CIFRE BAR MESOII';
$ventas[1864] = 'STAND MONTAJES Y EVENTOS';
$ventas[1865] = 'SUCESORES DE ALCALA Y AVINYO,S.L.';
$ventas[1866] = 'DIETRANS,S.L.';
$ventas[1867] = 'CORREDURIAS SEGUROS VYAFERRER,SL';
$ventas[1868] = 'COBRA INTAL. Y SERVIC.,SA';
$ventas[1869] = 'RODAJES LEVANTE,S.A.';
$ventas[1870] = 'PASARELA ILUMINACION,SL';
$ventas[1873] = 'ASTILLEROS LOHA,S.L.';
$ventas[1874] = 'FCO JAVIER LOPEZ LOPEZ';
$ventas[1875] = 'PAHU,C.B.';
$ventas[1876] = 'JUAN FCO FERRER';
$ventas[1877] = 'EDIFIC. PLAZA SAN JAIME,7,CB';
$ventas[1878] = '$ventas CONTADO PUERTO';
$ventas[1879] = '$ventas CONTADO CULLERA';
$ventas[1880] = 'FALLA LA TRILLAORA';
$ventas[1881] = 'REPARACION DE CONTAINERS,SL';
$ventas[1882] = 'LEMARA CONSTRUC,SL';
$ventas[1883] = 'PASAMAR MORENO, J.ANTONIO';
$ventas[1884] = 'GAVINES GEST,SL';
$ventas[1885] = 'SAFETY-KLEEN ESPA„A,SAU';
$ventas[1886] = 'IBEROTECNICA DEL CLIMA,SLU';
$ventas[1887] = 'SEAMENS CLUB';
$ventas[1888] = 'WINGER VALENC.ARRENDAMIENTO';
$ventas[1889] = 'DOCKS LOGISTICS SPAIN,SA';
$ventas[1890] = 'POVEDA,F.S.A.';
$ventas[1891] = 'SILVIA ALMENDRO SANCHIS';
$ventas[1892] = 'AHMAD ALAL EMBARCACION';
$ventas[1893] = 'CARLOS MANZANARES SAINZ';
$ventas[1894] = 'COLAS Y PINTURAS TERZA,SL';
$ventas[1895] = 'JAVIER SANCHO ROCAFULL';
$ventas[1896] = 'FERTILIZANTES DEL TURIA,SA';
$ventas[1897] = 'VICENTE E ISABEL HERMANOS';
$ventas[1898] = 'ASES INTEGRAL DE MONT. Y SERVIC.';
$ventas[1899] = 'FRANCISCO GOMEZ SERRANO';
$ventas[1900] = 'SISCO BARCA BISCOCHO';
$ventas[1901] = 'PINTURAS URE„A,S.L.';
$ventas[1902] = 'SISTEMOS ELECTRICOS VALENCIANOS,SL';
$ventas[1903] = 'FRANC LEVANTINA,SL';
$ventas[1904] = 'PABLO RUIZ DE HANZA';
$ventas[1905] = 'SERVISOR,SL';
$ventas[1906] = 'TERMINAL CONTENEDORES VACIOS,SA';
$ventas[1907] = 'FUNDACION NAO VICTORIA';
$ventas[1908] = 'CENTRO OCUPACIONAL IVADIS';
$ventas[1909] = 'FRANCISCO ARAGO,SL';
$ventas[1910] = 'JAVIER SEGOVIA VELASCO';
$ventas[1911] = 'NOATUM TERMINAL GRANELES VCIA,SA';
$ventas[1912] = 'REAL BENLLOCH,SA';
$ventas[1913] = 'EXCAVACIONES DIEGO SANCHEZ';
$ventas[1914] = 'MAGNANIM SINERGIAS,SA';
$ventas[1915] = 'FUNDACION N»SRA DEL ESPINO';
$ventas[1916] = 'DASNAU,SL';
$ventas[1917] = 'INTERCONTAINER';
$ventas[1918] = 'SERVAL CONDUCTOS Y CLIMATIZ.';
$ventas[1919] = 'EXTINVAL';
$ventas[1920] = 'DAVID SERRANO ROCA';
$ventas[1921] = 'ELECTROBETON,S.A.';
$ventas[1922] = 'AYUNTAMIENTO DE ALMASSERA';
$ventas[1923] = 'CASH & CARRY';
$ventas[1924] = 'ROPER CATALU„A,SL';
$ventas[1925] = 'VO-EVENT';
$ventas[1926] = 'EMILIO LLOSA FERRANDIS';*/
	$ventas[691] = 'SERVICIO MARITIMO PROVINCIAL G.C.';

$ventas[783] = 'VALENCIANA DE SERVICIOS I.T.V.,S.A.';

$ventas[813] = 'MIGUEL RIVERA AUTOCARES,S.L.';

$ventas[834] = 'RECAMBIOS COLON CATARROJA,S.A.';

$ventas[837] = 'SELEV NAVAL,S.L.';

$ventas[890] = 'DELTA VALENCIA,S.L.';

$ventas[908] = 'FORJA GARCIA,S.L.U.';

$ventas[1042] = 'HERRAJES LA COSTERA,S.L.';

$ventas[1051] = 'PACHICHA,C.B.';

$ventas[1076] = 'UNION NAVAL CONTAINERS,S.L.';

$ventas[1116] = 'CAVERO ANGULO, JOSE LUIS';

$ventas[1135] = 'MADERAS VILA MARTI,S.L.L.';

$ventas[1197] = 'SOLDAGAS,S.L.';

$ventas[1200] = 'Moya Peå±arubia Jose';

$ventas[1204] = 'TALLERES DE LA GUIA,S.L.';

$ventas[1221] = 'ESTELLES , EMILIO (INOXTEAM)';

$ventas[1230] = 'GARBAPORT, S.L.';

$ventas[1248] = 'INDUSTRIAS QUIMICAS DEL VALLES,S.A.';

$ventas[1252] = 'TRANSBELLES,S.L.';

$ventas[1284] = 'COFRADIA DE PESCADORES DE VCIA.';

$ventas[1312] = 'CASAR DE BRUBIA,S.L.';

$ventas[1387] = 'GARAGE RICARD,S.A.';

$ventas[1392] = 'NAUTICA AZA,S.L.U.';

$ventas[1393] = 'FERRETERIA LIMAR,S.L.';

$ventas[1399] = 'RMN DISTRIBUCION Y ALMACENAJE, S.L.';

$ventas[1401] = 'PRAMA MANTEN.Y SERV.IND.,S.L.';

$ventas[1420] = 'NIXEL 2005,S.L.';

$ventas[1489] = 'TALLERES CERVERî,S.L.';

$ventas[1502] = 'TECDEMAR PROMOTORA NAVAL,S.L.';

$ventas[1530] = 'PASCUAL ARNANDIS MARTINEZ';

$ventas[1561] = 'Ferbol Pintores SL';

$ventas[1575] = 'TRANSPORTS,S.A.';

$ventas[1581] = 'SOL LLUNA Y MAR ,C.B.';

$ventas[1590] = 'TERMINALES PORTUARIAS,S.A.';

$ventas[1614] = 'LABERINTUS DESIGN,S.L.';

$ventas[1636] = 'FRANCISCO RUEDA,S.L.';

$ventas[1637] = 'COPISTERIA SANCHIS,S.L.';

$ventas[1641] = 'REAMAR,S.L.';

$ventas[1655] = 'SUMLEVINTER INDUSTRIAL,S.L.';

$ventas[1672] = 'MANEXVAL LEVANTE,S.L.';

$ventas[1682] = 'CADAGUA,S.A.';

$ventas[1696] = 'SUMINISTROS GRALES. DE ESCOMBRERAS,S.A.';

$ventas[1697] = 'CU„AT GUILLEN,AMADOR';

$ventas[1712] = 'FENIX ATRACCIONES,S.L.';

$ventas[1722] = 'EFECTOS NAVALES,S.L.';

$ventas[1729] = 'VERONICA MAYEL,C.B.';

$ventas[1744] = 'HERMANOS MELIA ORTOLA,C.B.';

$ventas[1746] = 'NUEVE ANA Y ONREMO,,C.B.';

$ventas[1750] = 'ADDAX TEAM,S.L.';

$ventas[1751] = 'ACCIîN LOGêSTICA PUERTO,S.L.';

$ventas[1754] = 'PALOP CALATAYUD, JUAN ENRIQUE';

$ventas[1755] = 'BECSA';

$ventas[1766] = 'ARCO PROYECTOS URBANISTICOS,S.L.';

$ventas[1767] = 'UTE EDAR PINEDO III';

$ventas[1769] = 'UTE SYD AGUAS 68-CULLERA';

$ventas[1770] = 'VICTORIA HORTENSIA,C.B.';

$ventas[1772] = 'SALAZAR RODRIGUEZ, ISMAEL';

$ventas[1773] = 'REVALCAR,S.C.';

$ventas[1774] = 'RUIZ DEKOCOLOR,S.L.';

$ventas[1789] = 'KING MARINE,S.A.';

$ventas[1790] = 'Techno Parts Direct SL';

$ventas[1791] = 'EUROFRENO, S.L..';

$ventas[1792] = 'HERMANOS PELLICER,C.B.';

$ventas[1796] = 'SANCHEZ PALERO, MANUEL';

$ventas[1800] = 'MA„EZ PALACIOS, RAFAEL';

$ventas[1801] = 'Tanev Jelev Iordan';

$ventas[1803] = 'Inversiones Real Xatur sl';

$ventas[1806] = 'NEUMEC SILLA,C.B.';

$ventas[1807] = 'CARROCERIAS GUEVARA,S.L.L.';

$ventas[1809] = 'CAMPOS LLEO, M» VICENTA';

$ventas[1811] = 'SUMINISTROS DICA,S.L.';

$ventas[1812] = 'CARPINTERIA METALICA LUZ,SL';

$ventas[1813] = 'SALVAT LOGISTICA,S.A.';

$ventas[1814] = 'AGROMECANICA GIMENO,SL';

$ventas[1815] = 'CORTELL Y ORTS,S.L.';

$ventas[1816] = 'HERRAJES MASOTA, S.L.';

$ventas[1817] = 'SUMINISTRES Y SERIGRAFIA YCEL,S.L.';

$ventas[1818] = 'GRANALLADO Y TRATAMIENT. DE LA SUPERFICIE,S.A';

$ventas[1819] = 'ALEJANDRO PARDOS GARCIA';

$ventas[1820] = 'I.E.S. BLASCO IBA„EZ';

$ventas[1821] = 'FERRETERIA ALMENAR,C.B.';

$ventas[1822] = 'JOAN STUPIMEAM';

$ventas[1823] = 'PINTURAS TRICOJEP,S.L.U.';

$ventas[1824] = 'ALVA,S.L.';

$ventas[1825] = 'ANJOMAR,C.B.';

$ventas[1826] = 'ACCESORIOS DE PINT. TMS';

$ventas[1827] = 'MARIOSAI,4,S.L.';

$ventas[1828] = 'DISFRIMUR,SL';

$ventas[1829] = 'LOGIFRUIT,S.L.';

$ventas[1830] = 'YOSIFOV ZOROV,HRISTO';

$ventas[1831] = 'SUN SYSTEM FRAGA1';

$ventas[1832] = 'MARTINEZ BELDA,OSCAR';

$ventas[1833] = 'INMOBLES GILBAEZA MODERCO, S.L.';

$ventas[1834] = 'ROLLERLUX,S.L.';

$ventas[1835] = 'MERCADONA,S.A.';

$ventas[1836] = 'WIND INFORMATICA,S.L.';

$ventas[1837] = 'TRANSPORTES BRULL,S.L.';

$ventas[1838] = 'SCOMYTEC SEGURIDAD,S.L.';

$ventas[1839] = 'TECNOURBAN LEGAL AND CONSULTING,S.L.';

$ventas[1840] = 'PALENCIA CAMPOS,JUAN DAVID';

$ventas[1841] = 'PINTULAC,SLU';

$ventas[1842] = 'QSM, C.B.';

$ventas[1843] = 'PACECO ESPA¥A,S.A.';

$ventas[1844] = 'TRANSPORTES SALINAS,SL';

$ventas[1845] = 'BOSCH BOSCH, MANUEL';

$ventas[1846] = 'TALLER DE EMPLEO RUTAS SEGURASIII';

$ventas[1847] = 'PINTURAS CRUCEIRA,S.L.';

$ventas[1848] = 'MIRALLES SORIANO, JESUS';

$ventas[1849] = 'TALLERES JUAN HINOJOSA,S.L.';

$ventas[1850] = 'THE GARAJE FILMS,SL';

$ventas[1851] = 'MARI GONZALO,JAVIER';

$ventas[1852] = 'HELP IN TANKS,S.L.';

$ventas[1853] = 'TRASEJAR,S.L.';


$ventas[1855] = 'GARAJE A.GUIMERA';

$ventas[1856] = 'RDA MOTOR,C.B.';

$ventas[1857] = 'ROTULOS 3JJJ,SL';

$ventas[1858] = 'MARI GONZALO,JOSE';

$ventas[1859] = 'AZVI,S.A.';

$ventas[1860] = 'NAVARRO NAVARRO,FRANCISCO';

$ventas[1861] = 'PINTURA MURAL Y MARINA MONZO,SL';

$ventas[1862] = 'GIL CORTS,VICENTE';

$ventas[1863] = 'AMPARO CIFRE,BAR MESOII';

$ventas[1864] = 'STAND MONTAJES Y EVENTOS,SL';

$ventas[1865] = 'SUCESORES DE ALCALA Y AVINYO,S.L.';

$ventas[1866] = 'DIETRANS,SL';

$ventas[1867] = 'CORREDURIAS SEGUROS VYAFERRER,SL';

$ventas[1868] = 'COBRA INSTAL.Y SERVIC,SA';

$ventas[1869] = 'RODAJES LEVANTE,S.A.';

$ventas[1870] = 'PASARELA ILUMINACION,S.L.';

$ventas[1873] = 'ASTILLEROS LOHA,S.L.';

$ventas[1874] = 'FCO JAVIER LOPEZ LOPEZ';

$ventas[1875] = 'PAHU,C.B.';

$ventas[1876] = 'JUAN FCO FERRER';

$ventas[1877] = 'EDIFIC. PLAZA SAN JAIME,7,CB';

$ventas[1878] = 'CONTADOS PUERTO';

$ventas[1880] = 'FALLA LA TRILLAORA';

$ventas[1881] = 'REPARACION DE CONTAINERS,S.L.';

$ventas[1882] = 'LEMARA CONSTRUCCIONES,SL';

$ventas[1883] = 'PASAMAR MORENO,JOSE ANTONIO';

$ventas[1884] = 'GAVINES GEST,S.L.';

$ventas[1885] = 'SAFETY KLEEN ESPA„A,S.A.';

$ventas[1886] = 'IBEROTECNICA DEL CLIMA';

$ventas[1887] = 'SEAMENS CLUB';

$ventas[1888] = 'WINGER VALENC.ARRENDAMIENTO';

$ventas[1889] = 'DOCKS COMERCIALES DE VALENCIA,S.A';

$ventas[1890] = 'POVEDA FSA';

$ventas[1891] = 'ALMENDROS SANCHêS, SILVIA';

$ventas[1892] = 'EMBARC.AHMAD ALAL';

$ventas[1893] = 'CARLOS MANZANARES SAINZ';

$ventas[1894] = 'COLAS Y PINTURAS TERZA,S.L.';

$ventas[1895] = 'SANCHO ROCAFULL,JAVIER';

$ventas[1896] = 'FERTILIZANTES DEL TURIA,SA';

$ventas[1897] = 'VICENTE E ISABEL HERMANOS';

$ventas[1898] = 'ASES INTEGRAL DE MONT. Y SERVIC.';

$ventas[1899] = 'FRANCISCO GOMEZ SERRANO';

$ventas[1900] = 'SISCO BARCA BISCOCHO';

$ventas[1901] = 'PINTURAS URE„A,S.L.';

$ventas[1902] = 'SISTEMAS ELECTRICOS VALENCIANOS,SL';

$ventas[1903] = 'FRANC LEVANTINA';

$ventas[1904] = 'PABLO RUIZ DE HANZA';

$ventas[1905] = 'SERVISOR,SL';

$ventas[1906] = 'TERMINAL DE CONTENEDORES VACIOS,SA';

$ventas[1907] = 'FUNDACION NAO VICTORIA';

$ventas[1908] = 'CENTRO OCUPACIONAL IVADIS';

$ventas[1909] = 'FRANCISCO ARAGO,S.L.';

$ventas[1910] = 'SEGOVIA VELASCO, JAVIER';

$ventas[1911] = 'NOATUM TERMINAL GRANELES VCIA,SA';

$ventas[1912] = 'REAL BENLLOCH,SA';

$ventas[1913] = 'EXCAVACIONES DIEGO SANCHEZ';

$ventas[1914] = 'MAGNANIM SINERGIAS,SA';

$ventas[1915] = 'FUNDACION Nå»SRA DEL ESPINO';

$ventas[1916] = 'DASNAU,SL';

$ventas[1917] = 'INTERCONTAINER,S.A.';

$ventas[1918] = 'SERVAL CONDUCTOS Y CLIMATIZ.';

$ventas[1919] = 'EXTINVAL';

$ventas[1920] = 'DAVID SERRANO ROCA';

$ventas[1921] = 'ELECTROBETON,S.A.';

$ventas[1922] = 'AYUNTAMIENTO DE ALMASSERA';

$ventas[1923] = 'CASH & CARRY';

$ventas[1924] = 'ROPER CATALUÌÔA,SL';

$ventas[1925] = 'VO-EVENT';

$ventas[1926] = 'EMILIO LLOSA FERRANDIS';
		$totals = 0;
		$failed = 0;
		$keyvalueformat = "%04d";
		foreach($ventas as $key => $value)
		{
			$update = array();
			$keyformated = sprintf($keyvalueformat , $key);
			$update['cuentaventa']=$keyformated;
			$result = $this->model->updateDataWhereLike('TblCliente',$update,'nombre',$value);
			if ( $result == 0 )
			{
				echo '['.$keyformated.'] -> ['.$value.'] Failed <br>';
				$failed++;
			}
			$totals++;
		}
		echo 'Import Ventas : Totals '.$totals.'   Failed:'.$failed.'<br>'; 

	$compras400 = array();
/*$compras400[101] = 'Pinturas Hempel SA';
$compras400[111] = 'Pinturas Ure±a SL';
$compras400[120] = 'Industrias Titan SA';
$compras400[162] = 'Jose A Garcia SL';
$compras400[170] = 'Garcia Sanjaime SL';
$compras400[181] = 'Macy SA';
$compras400[201] = 'MONGAY,S.A.';
$compras400[287] = 'CRC Industries Iberia SLU';
$compras400[301] = 'Recubrimientos Maper SL';
$compras400[340] = 'BARBOSA,S.L.';
$compras400[341] = 'JEIVSA BROCHAS Y PINCELES,S.L.';
$compras400[351] = 'Sh metal graf SL';
$compras400[367] = 'CASALMA,S.L.';
$compras400[378] = 'Indukern SL';
$compras400[381] = 'SUMLEVINTER INDUSTRIAL,S.L.';
$compras400[386] = 'KELSIA';
$compras400[413] = 'Plasticos Garpeber SL';
$compras400[415] = 'Decoraciones Valtor SL';
$compras400[422] = 'QUIMIBASE 2000,S.L.';
$compras400[423] = 'SIFA EUROPA,S.L.';
$compras400[424] = 'Promotest SL';
$compras400[425] = 'PINTURAS LA PAJARITA';
$compras400[426] = 'SIKA S.A.U.';
$compras400[427] = 'VAL-SUR,SL';
$compras400[428] = 'BORDADOS ROSMAR';
$compras400[429] = 'DROGUERIA ORTI GALLACH';*/
	
	$compras400[101] = 'PINTURAS HEMPEL,S.A.U.';
$compras400[111] = 'PINTURAS URE„A,S.L.';

$compras400[120] = 'INDUSTRIAS TITAN,S.A.';

$compras400[162] = 'JOSE ANTONIO GARCIA,S.L.';

$compras400[170] = 'GARCIA SANJAIME,S.L.';

$compras400[181] = 'PINTURAS MACY,S.A.';

$compras400[201] = 'MONGAY, S.A.';

$compras400[287] = 'CRC INDUSTRIES IBERIA,S.L.U.';

$compras400[301] = 'RECUBRIMIENTOS MAPER,S.L.';

$compras400[340] = 'BARBOSA E HIJOS,S.L.';

$compras400[341] = 'JEIVSA BROCHAS Y PINCELES,S.L.';

$compras400[351] = 'S.H.METALGRAF,S.L.';

$compras400[367] = 'CASALMA,S.L.';

$compras400[378] = 'INDUKERN,S.A.';

$compras400[381] = 'SUMLEVINTER INDUSTRIAL,S.L.';

$compras400[386] = 'KELSIA, S.L.';

$compras400[413] = 'PLASTICOS GARPEBER,S.L.';

$compras400[415] = 'DECORACIONES VALTOR,S.L.';

$compras400[422] = 'QUIMIBASE 2000,S.L.';

$compras400[423] = 'SIFA EUROPA,S.L.';

$compras400[424] = 'PROMOTEST,S.L.';

$compras400[425] = 'PINTURAS LA PAJARITA';

		$totals = 0;
		$failed = 0;
		foreach($compras400 as $key => $value)
		{
			$update = array();
			$keyformated = sprintf($keyvalueformat , $key);
			$update['cuentacompra']=$keyformated;
			$result = $this->model->updateDataWhereLike('TblCliente',$update,'nombre',$value);
			if ( $result == 0 )
			{
				echo '['.$keyformated.'] -> ['.$value.'] Failed <br>';
				$failed++;
			}
			$totals++;
		}
		echo 'Import Compras : Totals '.$totals.'   Failed:'.$failed.'<br>'; 

    }
}
?>