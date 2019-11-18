<?

function get_href_parameters_part_for_pages()
{
	$true_pars = array('key', 'fid', 'act', 'navto');
	
	foreach($_GET as $i => $j)
	{
		if(in_array($i, $true_pars))
		{
			$pars[] = $i.'='.$j;
		}
	}
	
	if($pars)
	{
		return '?'.implode('&', $pars).'&p=';
	}
	else
	{
		return '?p=';
	}
}

// Страничность
// in - ссылка страниц, тек страницуущая страница, кол-во полей, лимит 
// out - блок страниц
function fill_pages($href, $p, $count_rows, $limit_on_page)
{
	$pages_template = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/pages/pages_block.tpl');
	
	$pages_a = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/pages/pages_a.tpl');
	
	$pages_current = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/pages/pages_current.tpl');
	
	$pages_first = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/pages/pages_first.tpl');
	
	$pages_last = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/pages/pages_last.tpl');
	
	$pages_next = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/pages/pages_next.tpl');
	
	$pages_prev = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/pages/pages_prev.tpl');
	
	if(!$href)
	{
		$href = get_href_parameters_part_for_pages();
	}
	
	// Кол-во страниц
	$pages_count=ceil($count_rows/$limit_on_page);
    
	// Если страниц больше 1
	if($pages_count>1)
	{
		// Если номер страницы просматриваемый меньше 3
		if($p<=3)
		{
			// Собираем список страниц
			for($i=1;($i<=$pages_count)&&($i<=5);$i++)
			{
				// Текущая
				if($i==$p)
				{		
					$where = array('{P}');
					
					$what = array($p);
					
					$pages.= str_replace($where, $what, $pages_current);
				}
				// Ссылка на страницу
				else
				{
					$p_href = $href.$i;
					
					$where = array('{P}', '{HREF}');
					
					$what = array($i, $p_href);
					
					$pages.= str_replace($where, $what, $pages_a);
					 
				}
			}
		}
	 	
		// Если разность кол-ва страниц меньше 2 и кол-во страниц больше 5
		if(($pages_count-$p<2)&&($pages_count>=5))
		{
			for($i=$pages_count-4;$i<=$pages_count;$i++)
			{
				if($i==$p)
				{
					$where = array('{P}');
					
					$what = array($p);
					
					$pages.= str_replace($where, $what, $pages_current);
				}
				else
				{
					$p_href = $href.$i;
					
					$where = array('{P}', '{HREF}');
					
					$what = array($i, $p_href);
					
					$pages.= str_replace($where, $what, $pages_a);
				}
			}
		}
		
		// Если кол-во страниц больше 5
		if(($pages_count>5)&&($pages_count-$p>=2)&&($p>3)&&($p<=$pages_count-2))
		{
			for($i=-2;$i<=2;$i++)
			{
				if($i==0)
				{
					$where = array('{P}');
					
					$what = array($p);
					
					$pages.= str_replace($where, $what, $pages_current);
				}
				else
				{
					$num=$p+$i;
					 
					$p_href = $href.$num;
					
					$where = array('{P}', '{HREF}');
					
					$what = array($num, $p_href);
					
					$pages.= str_replace($where, $what, $pages_a);
				}
									   
			}
		}
		
		// Если кол-во страниц меньше 5
		if($pages_count<5)
		{
			$pages = '';
			$num = 1;
			for($i=1;$i<=$pages_count;$i++)
			{
				if($i==$p)
				{
					$where = array('{P}');
					
					$what = array($p);
					
					$pages.= str_replace($where, $what, $pages_current);
				}
				else
				{
					$p_href = $href.$i;
					
					$where = array('{P}', '{HREF}');
					
					$what = array($i, $p_href);
					
					$pages.= str_replace($where, $what, $pages_a);
				}
			}
		}
		
		// Выводим стрелку Назад
		if($p!=1)
		{
			$p_href = $href.($p-1);
			
			$where = array('{P}', '{HREF}');
					
			$what = array(($p-1), $p_href);
			
			$arrPrev = str_replace($where, $what, $pages_prev);
		}
		// Выводим стрелку вперед
		if($p!=$pages_count)
		{
			$p_href = $href.($p+1);
			
			$where = array('{P}', '{HREF}');
					
			$what = array($p+1, $p_href);
			
			$arrNext = str_replace($where, $what, $pages_next);
		}
		
		// Выводим ссылку на первую страницу					
		if($p>3 && $pages_count > 5)
		{
			$p_href = $href.'1';
			
			$where = array('{P}', '{HREF}');
			
			$what = array(1, $p_href);
			
			$firstpage = str_replace($where, $what, $pages_first);
		}
		
		// Выводим ссылку на последнюю страницу
		if($p<=$pages_count-3 && $pages_count > 5)
		{
			$p_href = $href.$pages_count;
			
			$where = array('{P}', '{HREF}');
			
			$what = array($pages_count, $p_href);
			
			$lastpage = str_replace($where, $what, $pages_last);
		}
				
	}
	
	$PARS['{ARR_PREV}'] = $arrPrev;
    $PARS['{PAGES}'] = $pages;
	$PARS['{ARR_NEXT}'] = $arrNext;
	$PARS['{ARR_FIRST}'] = $firstpage;
	$PARS['{ARR_LAST}'] = $lastpage;       
	
	if(!$pages)
	{
		return '';
	}
	
    $pages_template=fetch_tpl($PARS,$pages_template);
	
	return 	$pages_template;
}
?>