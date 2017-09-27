$(document).ready(function () {
 
	$('.sub > a').click(function()//open up and close the rest
	{
		var cl=$(this).attr("class");
		
		if ($(this).attr("class")!= 'active')
		{
			
			$(this).next().slideToggle();
			$('.mydata').hide();
			$('#'+cl).show();
		}
		return false;
	});
	
    $('.mini-menu > ul > li > a').click(function()
	{
	   $('.mini-menu > ul > li > a, .sub a').removeClass('active');
	  
	   $(this).addClass('active');
	}),
       $('.sub ul li a').click(function()
	   {
		$('.sub ul li a').removeClass('active');
		$(this).addClass('active');
		var cl=$(this).attr("class");
		
		$('#'+cl).show();
		
	});
});