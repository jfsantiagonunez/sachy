<?php

	class Ivaldi_View_Helper_Table {
		private $view;

		function table($title, $columns, $data, array $options=array(),
				array $img=array(), $sort='', $order='ASC', $id='id') {
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
			$list = array();
			$list['0']='Low';
			$list['1']='Medium';
			$list['2']='High';
			
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
						} else if($metadata[$dbname]['DATA_TYPE'] == 'tinyint') {
							$res .= '<td>';
							if($entry[$dbname] == 1) {
								$res .= 'Yes';
							} else {
								$res .= 'No';
							}
							$res .= "</td>\n";
						} 			 
						else if($dbname == 'link') {
							$res .= '<td> <a href="'. $entry[$dbname] .'"> <img src="img/icons/event.png" class="logo" align="top"/> </a> </td>';
						} 
						else if ( ($dbname == 'important') || ($dbname == 'urgent') ) {
							$res .= '<td>' . $list[$entry[$dbname]] . '</td>';
						} 
						else {
							$res .= '<td>' . $entry[$dbname] . "</td>\n";
						}
					}
					else 
					{
						//$res .= '<td>' . $entry[$dbname] . "</td>\n";
						$res .= '<td>' . $entry[$dbname] . "</td>\n";
					}
				}
				/* print links/options */
				if(is_array($options)) {
					/* counter for images */
					$i = 0;
					foreach($options as $option) {
						$option[$id] = $entry[$id];
						/* create link and image path dynamically */
						if($option['action'] != "execute"){ 
							$res .= '<td><a href="'
									. $this->view->url($option, 'default', true) 
									. '"';
						}else{
							$res .= '<td><a href="javascript:generate('.$option[$id].');"';
						}

						if($option['action'] == "delete") $res .= ' onclick="return confirm(\'Are you sure you want to delete?\');" ';
						$res .=	'><img src="'
								. $this->view->baseUrl()
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
