<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_money.php';

if($_GET['act']==1)
{
	show_money();
}
else
{
	show_acc();
}

function show_money()
{
global $site_db;	
$sql = "SELECT * FROM tasks_users_money
		WHERE money_deleted=0 order by money_date DESC";

$res = $site_db->query($sql);
		
while($row=$site_db->fetch_array($res))
{
	$sql = "SELECT * FROM tasks_users WHERE user_id='".$row['money_to_user_id']."'";
	 
	$row1 = $site_db->query_firstrow($sql);
	
	$name = $row1['user_surname'].' '.$row1['user_name'];
	
	$money_summa = '';
	$money_summa1 = '';
	if($row['has_accruals'])
	{
		// Получаем начисления, которые вошли в выплату
		$accrual_arr = get_accruals_arr_for_money($row['money_id']);
		
		foreach($accrual_arr as $accrual_data)
		{  
			// Если штраф
			if($accrual_data['type_id']==3)
			{
				$money_summa -= $accrual_data['summa'];
			}
			else
			{
				$money_summa += $accrual_data['summa'];
			}
		}
		
		$money_summa1 = $money_summa;
		$money_summa = number_format($money_summa, 2, '.', ' ');
		 
		
	}
	else
	{
		$money_summa = number_format($row['money_summa'], 2, '.', ' ');
		$money_summa1 = $row['money_summa'];
	}
	
	$n .= "-------------------------------------------------------<br>";
	$n .= '<b>'.datetime($row['money_date'],'%d.%m.%Y').'</b> <br>'.$name.' '.$money_summa.' руб';
	$n .= "<br>".$row['money_comment'];
	$n .= "<br>-------------------------------------------------------<br>";
	
	$sum += $money_summa1;
}

echo 'ВЫПЛАТЫ<br>Всего: ',number_format($sum, 2, '.', ' ').' руб <br><br>';

echo $n;
}


function show_acc()
{
	global $site_db;
$sql = "SELECT * FROM tasks_money_accruals i
		LEFT JOIN tasks_money_accruals_types j ON i.type_id=j.type_id
		WHERE deleted=0 order by accrual_id DESC";

$res = $site_db->query($sql);
		
while($row=$site_db->fetch_array($res))
{
	$sql = "SELECT * FROM tasks_users WHERE user_id='".$row['to_user_id']."'";
	 
	$row1 = $site_db->query_firstrow($sql);
	
	$name = $row1['user_surname'].' '.$row1['user_name'];
	
	$st = $row['paid'] ? 'Оплачено' : 'Не оплачено';
	$n .= "-------------------------------------------------------<br>";
	$n .= '<b>'.datetime($row['date'],'%d.%m.%Y').'</b> - '.$row['type_name'].' - '.$st.'<br>'.$name.' '.number_format($row['summa'], 2, '.', ' ').' руб<br>'.$row['description'];
	$n .= "<br>-------------------------------------------------------<br>";
	
	$sum += $row['summa'];
}

echo 'НАЧИСЛЕНИЯ <br>Всего: ',number_format($sum, 2, '.', ' ').' руб <br><br>';

echo $n;
}
?>