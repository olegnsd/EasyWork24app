<!DOCTYpE html pUBliC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<LINK rel=stylesheet type=text/css href="/css/style_new.css" />
<script src="/js/jquery-1.8.3.min.js"></script>
<script src="/js/disk.js"></script>
<script src="/js/functions.js"></script>
</head>
<body>
<style>
body, html
{
	padding:0px;
	margin:0px;
	background:none;
	background:none !important
}
</style> 
</div>

<div class="doc_edit_top">
<div class="gic">EW24</div>

<div class="wrap">
<table cellpadding="0" cellspacing="0">
<tr>
	<td>Сохранить изменения в документе?</td>
    <td><a class="save_doc_btn" href="javascript:;" onclick="Disk.update_doc_version('{FILE_ID}')" id="update_doc_version_btn">Сохранить</a></td>
    <td><a href="javascript:;" class="с_save_doc_btn" onclick="Disk.cancel_update_doc_version('{FILE_ID}')">Отменить</a></td>
</tr>
</table>
   
 
</div>

</div>
<iframe src="{URL}" style="border:none; height:100%; width:100%"></iframe> 

<script>
setTimeout(function(){$('.doc_edit_top').fadeIn(300)}, 2000);
</script>
</body>
</html>


 
