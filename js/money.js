add_money_btn = 0;
// Передать деньги
function add_money()
{
	var money_summa, money_comment, money_from;
	var accruals_data = {};
	 
	if(add_money_btn==1)
	{
		return;
	}
	
	$('.td_error').html('');
	
	$('.input_text').removeClass('light_error_input')
	
	money_summa = $('#money_summa').val();
	//money_type = $('#money_type').val();
	money_comment = $('#money_comment').val();
	money_from = $('#money_from').val();
	add_type = $('input[name="payments_type"]:checked').val();
	
	$('.add_form .add_paymenst_accrual_item[select=1]').each(function()
	{
		tmp_id = $(this).attr('accrual_id');
		accruals_data[tmp_id] = tmp_id;
		 
	})
	
	loading_btn('add_money_btn')
	
	add_money_btn = 1;
	
	$.post('/ajax/ajaxMoney.php', 
	{   
		mode : 'add_money',
		user_id : user_id,
		money_summa : money_summa,
		money_comment : money_comment,
		money_from : money_from	,
		accruals_data : $.toJSON(accruals_data),
		add_type : add_type
	},
	function(data){ 
		
		add_money_btn = 0;
		
		loading_btn('add_money_btn', 1)
		
		if(data['error'])
		{
			$.each(data['error'], function(i, j){
				error_text = '';
						 
				if(i=='money_summa')
				{ 
					error_text = 'Введите значение суммы';
					light_error_input('money_summa');
				}
				if(i=='accrual_data')
				{ 
					error_text = 'Выберите начисления';
					 
				}
				
				$("#"+i).next().html(error_text);
			
			})
		}
		else if(data['success']==1)
		{ 
			if(add_type==2)
			{
				$('.add_paymenst_accrual_item[select=1]').each(function()
				{
					$(this).remove();
				})
				if(!$('.add_paymenst_accrual_item').size())
				{
					$('#accrual_add_cont').html('<b>Начислений нет.</b>');
				}
				
				get_money_accruals_result_block(user_id);
			}
			$('#money_summa').val('0.00');
			$('#money_type').val(0);
			$('#money_from').val('');
			$('#money_comment').val('');
			get_money_item(data['inserted_money_id'], 1);
		}
		
	}, 'json');
}


// Получает форму для редактирования клиента
function get_money_item(money_id, prepend)
{
	$.post('/ajax/ajaxMoney.php', 
	{   
		mode : 'get_money_item',
		money_id : money_id,
		user_id : user_id
	},
	function(data){ 
		
		if(prepend)
		{
			$('#no_money').remove()
			$('#money_list').prepend(data);
			 
		}
		else
		{
			$('#money_'+money_id).replaceWith(data)
		}
		
	});
}

add_money_accrual_btn = 0;

function add_money_accrual()
{
	var summa, desc, accrual_type
	
	if(add_money_accrual_btn)
	{
		return false;
	}
	
	$('.td_error').html('');
	
	accrual_type = $('#accrual_type').val();
	
	summa = $('#accruals_summa').val();
	
	desc = $('#accruals_desc').val();
	
	loading_btn('add_money_accrual_btn')
	
	add_money_accrual_btn = 1;
	
	$.post('/ajax/ajaxMoney.php', 
	{   
		mode : 'add_money_accrual',
		user_id : user_id,
		accrual_type : accrual_type,
		summa : summa,
		desc : desc
	},
	function(data){ 
		
		add_money_accrual_btn = 0;
		
		loading_btn('add_money_accrual_btn', 1)
		
		if(data['error'])
		{
			 if(data['error']['summa']==1)
			 { 
				 $('#accruals_summa').next().html('Некорректно указана сумма')
			 }
		}
		else if(data['success']==1)
		{ 
			$('#accruals_summa').val('0.00');
			$('#accrual_type').val(1);
			$('#accruals_desc').val('');
			get_accrual_item(data['accrual_id']);
			$('.no_contents').remove();
			get_money_accruals_result_block(user_id)
		}
		
	}, 'json');
}

function get_accrual_item(accrual_id, is_replace)
{
	$.post('/ajax/ajaxMoney.php', 
	{   
		mode : 'get_accrual_item',
		accrual_id : accrual_id
	},
	function(data){ 
		
		if(data)
		{
			if(is_replace)
			{
				$('#accrual_'+accrual_id).replaceWith(data);
			}
			else
			{
				$('#accruals_list').prepend(data);
			}
		}
		
	});
}

money_report_btn = {};
function add_money_report(money_id)
{
	var report_text;
	 
	if(money_report_btn[money_id]==1)
	{
		return;
	}
	
	report_text = $('#money_report_input_'+money_id).val();
	
	money_report_btn[money_id] = 1;
	
	$('#money_report_input_'+money_id).html('');
	
	loading_btn('add_money_report_'+money_id)
	
	$.post('/ajax/ajaxMoney.php', 
	{   
		mode : 'add_money_report',
		money_id : money_id,
		report_text : report_text
	},
	function(data){ 
		
		money_report_btn[money_id] = 0;
		
		loading_btn('add_money_report_'+money_id, 1)
		 
		if(data['error'])
		{  
			if(data['error']['report_text']=='1')
			{   
				$('#money_report_input_'+money_id).focus()
			}
		}
		if(data['success']==1)
		{
			$('#money_report_input_'+money_id).val('');
			
			get_money_report_list(money_id);
		}
		
	}, 'json');
}

// Получает список отчета
function get_money_report_list(money_id)
{
	$('#money_report_proc_'+money_id).html('<img src="/img/loading5.gif" />')
	 
	$.post('/ajax/ajaxMoney.php', 
	{   
		mode : 'get_money_report_list',
		money_id : money_id
	},
	function(data){ 
		
		$('#money_report_proc_'+money_id).html('')
		
		if(data)
		{
			$('#money_report_list_'+money_id).html(data)
		}
	});
}


// Удалить переданные финансы
function delete_money(money_id)
{
	loading_btn('delete_money_btn_'+money_id);
	 
	$.post('/ajax/ajaxMoney.php', 
	{   
		mode : 'delete_money',
		money_id : money_id
	},
	function(data){ 
		
		loading_btn('delete_money_btn_'+money_id, 1);
		
		// Удаление успешно
		if(data==1)
		{ 
			
			// Вывод уведомления
			$('#money_'+money_id).html('<td colspan="7"><div class="success">Финансы удалены | <a href="javascript:;" onclick="restore_money('+money_id+')">Восстановить</a> | <a href="javascript:;" onclick="$(\'#money_'+money_id+'\').remove();">Скрыть</a></div></td>');
		}
		
		
	});
}

// Восстановить задание
function restore_money(money_id)
{
	// Картинка процесса
	//$('#money_proc_'+money_id).html('<img src="/img/loading5.gif">');
	
	$.post('/ajax/ajaxMoney.php', 
	{   
		mode : 'restore_money',
		money_id : money_id
	},
	function(data){ 
		
	//	$('#money_proc_'+money_id).html('');
		
		// Восстановление успешно
		if(data==1)
		{
			get_money_item(money_id);
		}
		
	});
}

money_actual_page = 1;
// Выводит больше контактов
function get_more_money()
{
	var page;
	
	page = money_actual_page + 1;
	 
	$.post('/ajax/ajaxMoney.php', 
	{   
		mode : 'get_more_money',
		user_id : user_id,
		page : page
		
	},
	function(data){ 
		
		$('#money_list').append(data);
		
		
		// Актаульная страница
		money_actual_page++;
		
		if(money_actual_page>=pages_count)
		{
			$('#more_money_btn').hide();
		}
	});
}

// Принять деньги
function confirm_money(money_id)
{
	loading_btn('confirm_money_btn_'+money_id);
	 
	$.post('/ajax/ajaxMoney.php', 
	{   
		mode : 'confirm_money',
		money_id : money_id
		
	},
	function(data){ 
		
		loading_btn('confirm_money_btn_'+money_id, 1);
		
		if(data['success']==1)
		{
			$('#money_confirm_btn_bl_'+money_id).remove();
			$('#money_'+money_id).removeClass('not_confirm');
			$('#money_'+money_id+' .not_confirm').remove();
			
			get_new_money_count();
			
			if(data['new_money_count']>=1)
			{
				$('#new_payments_count').html('(+ '+data['new_money_count']+')');
			}
			else
			{
				$('#new_payments_count').html('');
			}
		}
	},'json');
}

function money_payments_filter()
{
	var user;
	pars = [];
	
	var user_id = $('#money_user_id').val();
	//var operation = $('#payments_operation').val();

	parseInt(user_id) ? pars.push('id='+user_id) : '';
	
	accruals ? pars.push('accruals=1') : '';
	payments ? pars.push('payments=1') : '';
	
	query = pars.join('&');
	
	if(query)
	{
		document.location.href = '/money?'+query;
	}
	else
		document.location.href = '/money';
	
}

function get_money_accruals_result_block(user_id)
{
	$.post('/ajax/ajaxMoney.php', 
	{   
		mode : 'get_money_accruals_result_block',
		user_id : user_id
		
	},
	function(data){ 
		
		if(data)
		{
			$('#money_accruals_result').html(data)
		}
		else
		{
			$('#money_accruals_result').html('')
		}
		
	});
}

function confirm_accrual(accrual_id)
{
	loading_btn('confirm_accrual_btn_'+accrual_id);
	
	$.post('/ajax/ajaxMoney.php', 
	{   
		mode : 'confirm_accrual',
		accrual_id : accrual_id
		
	},
	function(data){ 
		
		loading_btn('confirm_accrual_btn_'+accrual_id, 1);
		
		if(data['success']==1)
		{
			$('#accrual_'+accrual_id).removeClass('not_confirm_row');
			$('#confirm_accrual_btn_'+accrual_id).remove();
			get_new_money_count();

		}
		if(data['new_accruals_count']>=1)
			{
				$('#new_accruals_count').html('(+ '+data['new_accruals_count']+')');
			}
			else
			{
				$('#new_accruals_count').html('');
			}

		
	}, 'json');
}

function delete_accrual(accrual_id)
{
	loading_btn('delete_accrual_btn_'+accrual_id);
	
	$.post('/ajax/ajaxMoney.php', 
	{   
		mode : 'delete_accrual',
		accrual_id : accrual_id
		
	},
	function(data){ 
		
		loading_btn('delete_accrual_btn_'+accrual_id, 1);
		
		if(data==1)
		{
			$('#accrual_'+accrual_id).html('<td colspan="7"><div class="success">Начисление успешно удалено | <a href="javascript:;" onclick="restore_accrual(\''+accrual_id+'\')" class="link">Восстановить</a> |<a href="javascript:;" onclick="$(\'#accrual_'+accrual_id+'\').remove()" class="link">Скрыть</a></div></td>');
			get_money_accruals_result_block(user_id);
			//$('#confirm_accrual_btn_'+accrual_id).remove();
		}
		else if(data=='-1')
		{
			$('#accrual_'+accrual_id).removeClass('not_confirm');
			$('#delete_accrual_btn_'+accrual_id).remove();
		}

		
	});
}

function restore_accrual(accrual_id)
{
	loading_btn('confirm_accrual_btn_'+accrual_id);
	
	$.post('/ajax/ajaxMoney.php', 
	{   
		mode : 'restore_accrual',
		accrual_id : accrual_id
		
	},
	function(data){ 
		
		loading_btn('confirm_accrual_btn_'+accrual_id, 1);
		
		if(data==1)
		{
			get_accrual_item(accrual_id, 1);
			get_money_accruals_result_block(user_id);
		}

		
	});
}

function get_new_money_count()
{
	$.post('/ajax/ajaxMoney.php', 
	{   
		mode : 'get_new_money_count'
		
	},
	function(data){ 
		
		if(data>0)
			{
				$('#new_money_count').html('(+ '+data+')');
			}
			else
			{
				$('#new_money_count').html('');
			}

		
	}, 'json');
}

accruals_actual_page = 1;
// Выводит больше контактов
function get_more_accruals()
{
	var page;
	
	page = accruals_actual_page + 1;
	 
	$.post('/ajax/ajaxMoney.php', 
	{   
		mode : 'get_more_accruals',
		user_id : user_id,
		page : page
		
	},
	function(data){ 
		
		$('#accruals_list').append(data);
		
		
		// Актаульная страница
		accruals_actual_page++;
		
		if(accruals_actual_page>=pages_count)
		{
			$('#more_accruals_btn').hide();
		}
	});
}

function select_accrual_item(elem, accrual_id)
{
	 
	if($(elem).attr('select')!=1)
	{
		accrual_item_select(elem);
		 
	}
	else 
	{
		accrual_item_unselect(elem);
	}
	
	check_for_all_selected_accruals();
	recount_accrual_sum();
}

function accrual_item_select(elem)
{
	$(elem).addClass('accrual_select');
	$(elem).attr('select', 1);
}
function accrual_item_unselect(elem)
{
	$(elem).removeClass('accrual_select');
	$(elem).attr('select', 0);
}

function accrual_select_all()
{
	var select_all;
	
	select_all = $('#accrual_select_all_chbx').attr('checked')=='checked' ? 1:0;
	
	if(select_all==1)
	{
		 $('.add_paymenst_accrual_item').each(function(){
			 accrual_item_select(this)
		 })
	}
	else if(select_all==0)
	{
		 $('.add_paymenst_accrual_item').each(function(){
			  accrual_item_unselect(this)
		 })
	}
	
	recount_accrual_sum();
}

function check_for_all_selected_accruals()
{ 
	if($('.add_paymenst_accrual_item[select=0]').size())
	{ 
	 	$('#accrual_select_all_chbx').removeAttr('checked');
	}
	else
	{
		$('#accrual_select_all_chbx').attr('checked', 'checked');
	}
}
function recount_accrual_sum()
{
	var result_sum = 0.00;
	var type, summa;
	
	$('.add_paymenst_accrual_item[select=1]').each(function(){
		
		type = $(this).attr('type');
		summa = parseFloat($(this).attr('summa'));
		
		if(type==3)
		{
			result_sum -= summa;
		}
		else if(type==1 || type==2)
		{
			result_sum += summa;
		}
	})
	
	result_sum = Math.round(result_sum*100)/100
	
	result_sum = sumProcess(num_format(result_sum),' ','.');
	
	$('#acc_sum_result').html(result_sum)
}

function payments_add_type_proc()
{
	 if($('input[name="payments_type"]:checked').val()==2)
	{
		$('#add_p_accrual_row').show();
		$('#add_p_summa_row').hide();
	}
	else if($('input[name="payments_type"]:checked').val()==1)
	{
		$('#add_p_accrual_row').hide();
		$('#add_p_summa_row').show();
	}
}