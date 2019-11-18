<?php 
// Возвращает верхнюю панель управления
function fill_top_panel($o)
{
	global $site_db, $current_client_id;
	
	$top_panel_tpl = file_get_contents('templates/top_panel.tpl');
	
	$sql = "SELECT i.client_name, j.type_name FROM ".CLIENTS_TB." i
			LEFT JOIN ".CLIENTS_TYPES_DATA." j ON i.client_organization_type_id=j.type_id 
			WHERE client_id='$current_client_id'"; 
	
	$row = $site_db->query_firstrow($sql);
	
	$PARS['{CLIENT_ID}'] = $current_client_id;
	
	$PARS['{CLIENT_NAME}'] = $row['client_name'];
	
	$PARS['{CLIENT_TYPE_NAME}'] = $row['type_name'];
	
	
	return fetch_tpl($PARS, $top_panel_tpl);
}

?>