<?
if($_SESSION['user_id']==29)
{
	$sql = "SELECT * FROM tasks_users_files_access WHERE file_id > 0";
	
	$res = $site_db->query($sql);
 	
	while($row=$site_db->fetch_array($res, 1))
	{
		$sql = "SELECT * FROM tasks_users_files WHERE file_id='".$row['file_id']."'";
		
		$folder_data = $site_db->query_firstrow($sql);
		
		$sql = "UPDATE tasks_users_files_access SET folder_id = '".$folder_data['folder_id']."' WHERE id=".$row['id']."";
		
		$site_db->query($sql);
	}
}
?>