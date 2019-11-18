<div id="import_add_form">
<br />
Для импорта контрагентов, <a href="/upload/import_clients.csv" class="link">загрузите пример структуры файла импорта.</a>

<br /><br /><br />
<a href="javascript:;" id="client_imp_upload" class="link">Выбрать файл для импорта</a> <span style="color:#666">(формат файла .csv*)</span>
</div>

<div id="import_preview"></div>


<script>

new AjaxUpload($('#client_imp_upload'), {  
		  		    action: '/ajax/ajaxClients.php?mode=import_clients',  
		  		    name: 'uploadfile',  
		  		    onSubmit: function(file, ext){
						
						if (!(ext && /^(csv|xlsx)$/i.test(ext))){  
							// check for valid file extension  
							alert('Ошибка. Допускаются только файлы Word Excel')
							return false;  
						} 
					},     
		  		    onComplete: function(file, response_data){  
						
						response = response_data;
						
						if(response=='0')
						{
							alert('Ошибка. Допускаются только файлы форматов .csv');
						}
						else if(response=='2')
						{
							alert('Произошла ошибка при загрузке файла');
						}
						else
						{ 
							$('#import_add_form').hide();
							$('#import_preview').html(response_data);
							
						}
							
					}
			}); 
			
</script>