$(document).ready(function () {
  $('.sub > a').click(function(){
     if ($(this).attr('class') != 'active'){
       $('.sub ul').slideUp();
	   $(this).next().slideToggle();
	 }
      return false;
  });
       $('.mini-menu > ul > li > a').click(function(){
	   $('.mini-menu > ul > li > a, .sub a').removeClass('active');
	   $(this).addClass('active');
	}),
       $('.sub ul li a').click(function(){
	   $('.sub ul li a').removeClass('active');
	   $(this).addClass('active');
	});
});