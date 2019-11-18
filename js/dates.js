function add_zero_to_date(string)
{
	string = ''+string
	 
	if(string.length==1)
	{
		return '0'+string
	}
	else
	{
		return string;
	}
}