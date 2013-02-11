
<script type='text/javascript'>
function deleteajax() {

	if (arguments.length < 1 )
	{
		return;
	}

    if ( confirm('Seguro que quieres borrar?') )
    {
        dojo.xhrGet({
        	// The URL to request, the argument of the function.
        	url: arguments[0],
        	handleAs: "text",
        	// The method that handles the request's successful result
        	// Handle the response any way you'd like!
        	load: function(result) {
        		dojo.byId("listadomovimientos").innerHTML = result;
        	},
        	error: function(error) {
                dojo.byId("listadomovimientos").innerHTML = "Error borrando movimiento.. Refresque la pagina...";
            }
  		  });
        
    }
    return false;
}

</script>

<?php

class Ivaldi_View_Helper_Table {
	private $view;

	function table($title, $columns, $data, array $options=array(),
	array $img=array(), $sort='', $order='ASC', $id='id', $controller ='' ) {
			
		$url = 'http://'.$_SERVER['SERVER_NAME'].'/gestion/public/';

		/* get stuff from the url */
		$req = Zend_Controller_Front::getInstance()->getRequest();
		/* page we are on */
		$page = $req->getParam('page');
		/* column we are sorting on */
		$tmp = $req->getParam('sort');
		/* only override if set */
		if($tmp != '') {
			$sort = $tmp;
		}

		/* asc/desc */
		$tmp = $req->getParam('order');
		/* only override if set */
		if($tmp != '') {
			$order = $tmp;
		}

		/* set baseurl */
		$baseurl = $this->view->BaseUrl();
			
		/* if sort is set to a known column */
		if(isset($sort) && isset($columns[$sort])) {
			/* sort asc by default */
			if(!isset($order)) {
				$order = 'ASC';
			}
			/* sort the data by that */
			//Ignore sorting $data->order($sort . ' ' . $order);
		}

		/* get field types */
		try {
			$metadata = $data->getTable()->info();
			$metadata = $metadata['metadata'];
		} catch(Exception $e) {
			/* dirty hack for supporting UNION */
		}
			
		/* start the paginator */
		$paginator = Zend_Paginator::factory($data);
			
		/* set page if set */
		if(isset($page)) {
			$paginator->setCurrentPageNumber($page);
		} else {
			$paginator->setCurrentPageNumber(1);
		}
		$res = "<table class=\"dbtable\">\n<caption>$title</caption>\n<thead>\n<tr>\n";
		/* iterate over column headers */
		foreach($columns as $dbname => $header) {
			$res .= '<th scope="col" class="sortable" onclick="window.location.href=\'';
			/* if this is the column we're sorting on and we're
			 * not sorting asc, make a link that allows desc sorting
			 */
			if(isset($sort) && $sort == $dbname
			&& $order != 'desc') {
				$res .= $this->view->url(array(
							'sort' => $dbname,
							'order' => 'desc')
				);
			} else {
				$res .= $this->view->url(array(
							'sort' => $dbname,
							'order' => 'asc')
				);
			}
			$res .= '\';">' . $header;
			if(isset($sort) && $sort == $dbname) {
				$res .= '<img src="' . $baseurl	. 'img/table/';
				$res .= strtolower($order) == 'asc'
				? 'asc.png" alt="asc" />'
				: 'desc.png" alt="desc" />';
			}
			$res .= "</th>\n";
		}
		/* add empty cells above options*/
		if(is_array($options)) {
			foreach($options as $option) {
				$res .= "<th></th>\n";
			}
		}
			
		$res .= "</tr>\n</thead>\n<tbody>";
		/* iterate over data */
		foreach($paginator as $entry) {
			$res .= "<tr>\n";
			/* print every column */
			foreach($columns as $dbname => $header) {
				if(isset($metadata[$dbname])) {

					if($metadata[$dbname]['DATA_TYPE'] == 'datetime') {
						$date = new Zend_Date($entry[$dbname]);
						$res .= '<td>' . $date->toString('dd-MM-YYYY HH:mm')
						. "</td>\n";
					} else if($metadata[$dbname]['DATA_TYPE'] == 'date') {
						$date = new Zend_Date($entry[$dbname]);
						$res .= '<td>' . $date->toString('dd-MM-YYYY')
						. "</td>\n";
					} else if($metadata[$dbname]['DATA_TYPE']
					== 'decimal') {
						$res .= '<td>&euro; ' . $entry[$dbname]
						. "</td>\n";
					}

					else if($dbname == 'estado') {
						$theUrl ='';
							
						$nombretabla='albaran';
						$theIcon = '';
						if($entry[$dbname] == 1) {
							$theUrl = $this->view->url(array('controller' => $controller, 'action' => 'edit', $id => $entry[$id] ), 'default', true);
							$theIcon ='img/icons/view.png';
							$res .= '<td><a title="Ver" href="'. $theUrl.'"> <img src="'. $theIcon.'" class="logo" align="top"/> </a>';
							$theUrl = $this->view->url(array('controller' => $controller, 'action' => 'delete', $id => $entry[$id] ), 'default', true);
							$theIcon ='img/icons/delete.png';
							$res .= '<a title="Borrar" onclick="return confirm(\'Estas seguro que quieres borrar?\');" href="'. $theUrl.'"> <img src="'. $theIcon.' " class="logo" align="right"/> </a></td>';
							
						} else {
							$theUrl = $this->view->url(array('controller' => $controller, 'action' => 'edit', $id => $entry[$id] ), 'default', true);
							$theIcon ='img/icons/edit.png';
							$res .= '<td><a title="Editar" href="'. $theUrl.'"> <img src="'. $theIcon.'" class="logo" align="left"/> </a>';

							$theUrl = $this->view->url(array('controller' => $controller, 'action' => 'delete', $id => $entry[$id] ), 'default', true);
							$theIcon ='img/icons/delete.png';
							$res .= '<a title="Borrar" onclick="return confirm(\'Estas seguro que quieres borrar?\');" href="'. $theUrl.'"> <img src="'. $theIcon.' " class="logo" align="right"/> </a></td>';
						}

					}
					else if($dbname == 'entrada') {
						if($entry[$dbname] == 0) {
							// Salida
							$res .= '<td> <img src="img/icons/salida.png" class="logo" align="top"/> </td>';
						} else if($entry[$dbname] == 1) {
							$res .= '<td> <img src="img/icons/entrada.png" class="logo" align="top"/> </td>';
						} else {
							$res .= '<td>Error</td>';
						}
					}
					else if($dbname == 'tipoDescuento') {
						$tipo=$entry[$dbname];
						if($tipo == 0) {
							// Salida
							$res .= '<td>TOTAL FACTURA</td>';
						} else if($tipo == 1) {
							$res .= '<td>'.$entry['calidad'].'</td>';
						} else if($tipo == 2) {
							$res .= '<td> OTRAS </td>';
						} else {
							$res .= '<td>Error</td>';
						}
					}
					else if($metadata[$dbname]['DATA_TYPE'] == 'tinyint') {
						$res .= '<td>';
						if($entry[$dbname] == 1) {
							$res .= 'Si';
						} else {
							$res .= 'No';
						}
						$res .= "</td>\n";
					}

					else {
						$res .= '<td>' . $entry[$dbname] . "</td>\n";
					}
				}
				else
				{
					if($dbname == 'entrada') {
						if($entry[$dbname] == 0) {
							// Salida
							$res .= '<td> <img src="img/icons/salida.png" class="logo" align="top"/> </td>';
						} else if($entry[$dbname] == 1) {
							$res .= '<td> <img src="img/icons/entrada.png" class="logo" align="top"/> </td>';
						} else {
							$res .= '<td>Error</td>';
						}
					}
					else if($dbname == 'estado') {
						$theUrl ='';
							
						$nombretabla='albaran';
						$theIcon = '';
						if($entry[$dbname] == 1) {
							$theUrl = $this->view->url(array('controller' => $controller, 'action' => 'edit', $id => $entry[$id] ), 'default', true);
							$theIcon ='img/icons/view.png';
							$res .= '<td><a title="Ver" href="'. $theUrl.'"> <img src="'. $theIcon.'" class="logo" align="top"/> </a></td>';
						} else {
							$theUrl = $this->view->url(array('controller' => $controller, 'action' => 'edit', $id => $entry[$id] ), 'default', true);
							$theIcon ='img/icons/edit.png';
							$res .= '<td><a title="Editar" href="'. $theUrl.'"> <img src="'. $theIcon.'" class="logo" align="left"/> </a>';

							$theUrl = $this->view->url(array('controller' => $controller, 'action' => 'delete', $id => $entry[$id] ), 'default', true);
							$theIcon ='img/icons/delete.png';
							$res .= '<a title="Borrar" onclick="return confirm(\'Estas seguro que quieres borrar?\');" href="'. $theUrl.'"> <img src="'. $theIcon.' " class="logo" align="right"/> </a></td>';
						}

					}
					else
					{
						$res .= '<td>' . $entry[$dbname] . "</td>\n";
					}
				}
			}
			/* print links/options */
			if(is_array($options)) {
				/* counter for images */
				$i = 0;
				foreach($options as $option) {
					$option[$id] = $entry[$id];
					/* create link and image path dynamically */
					$ayuda= '';
					if (isset($option['ayuda']))
					$ayuda=$option['ayuda'];
					if($option['action'] != "execute"){
						$res .= '<td><a title="'.$ayuda.'" href="'
						. $this->view->url($option, 'default', true)
						. '"';
					}else{
						$res .= '<td><a  href="javascript:generate('.$option[$id].');"';
					}

					if(strncmp($option['action'], "deleteajax",10) == 0 ) $res .= ' onclick="return deleteajax(\''.$this->view->url($option, 'default', true).'\');" ';

					if(strncmp($option['action'], "delete",6) == 0 ) $res .= ' onclick="return confirm(\'Estas seguro que quieres borrar?\');" ';
					$res .=	'><img src="'
					. $url
					. $img[$i] . '" alt="'
					. $img[$i] . "\" /></a></td>\n";
					$i++;
				}
			}
			$res .= "</tr>\n";
		}
		$res .= "</tbody>\n";

		if($paginator->getPages()->pageCount > 1) {
			$res .= "<tfoot>\n<tr>\n<td colspan=\""
			. (sizeof($columns) + sizeof($options)) . "\">\n";

			/* print the paginationcontrol */
			$res .= $this->view->paginationControl($paginator, 'Sliding',
						'table_pagination.phtml');

			$res .= "</td>\n</tr>\n</tfoot>\n";
		}

		$res .= "</table>\n";

		return $res;
	}

	/* automatically called by the view */
	function setView(Zend_View_Interface $view) {
		$this->view = $view;
	}
}
