$(document).ready(function () {
	var i=0, n=0, eventsPerPage=1, dotPagesLimit=10, hideChildren=eventsPerPage-1;
	// hide children after hideChildren var
	$('.subCategoryEventHolder .subCategoryEvent:gt('+hideChildren+')').hide();		
	var allEvents=$( ".subCategoryEvent" ).length;
	var pagesNumber = Math.ceil( allEvents/eventsPerPage );
	while(i<pagesNumber) {		
		i++;
		n=(i-1)*eventsPerPage;
		$(".pagging").append('<span class="page" data-page='+n+'>'+i+'</span>');
	}
	// add dots for long paging
	if(pagesNumber > dotPagesLimit){
		var dotContentAfter='<span class="dotHolderFirst">...</span>';
		var dotContentBefore='<span class="dotHolderLast">...</span>';		
		var dotElementFirst='.pagging .page:nth-child(4)';		
		var dotElementLast='.pagging .page:nth-last-child(4)';		
		var dotElementFullFirst=$(dotContentAfter).insertAfter(dotElementFirst).hide();
		var dotElementFullLast=$(dotContentBefore).insertBefore(dotElementLast);
		$('.pagging .page').hide();
		$('.pagging .page:nth-child(2)').show();
		$('.pagging .page:nth-child(3)').show();
		$('.pagging .page:nth-child(4)').show();
		$('.pagging .page:nth-last-child(1)').show();
		$('.pagging .page:nth-last-child(2)').show();
		$('.pagging .page:nth-last-child(3)').show();			
		$(".pagging .active").show();
	}
	
	$(".pagging").append('<span class="next" data-page='+eventsPerPage+'>>></span>');
	$(".pagging .page:nth-child(2)").addClass("active");
	$(".pagging .active").prev('.pagging .prev').hide();
	
	$('.pagging .page').click(function () {		
		$(".pagging .page").removeClass("active");
		$(this).addClass("active");
		$(".page.active").show();
		// showing/hide arows
		$(".pagging .next").show();
		$(".pagging .active").next('.next').hide();
		$(".pagging .prev").show();
		$(".pagging .active").prev('.prev').hide();
	});

	$('.next').click(function () {
		$(this).siblings('.pagging .page.active').removeClass('active').nextAll('.page:first').addClass('active');
		// showing/hide arows
		$(".pagging .active").next('.next').hide();
		$(".pagging .prev").show();			
	});	

	$('.pagging .prev').click(function () {
		$(this).siblings('.page.active').removeClass('active').prevAll('.page:first').addClass('active');
		// showing/hide arows
		$(".pagging .active").prev('.prev').hide();
		$(".pagging .next").show();			
	});		
	
	$('.prev, .next, .page').click(function () {		
		var page_to_display_start = $(this).data('page') + 1;
		var page_to_display_end = page_to_display_start + eventsPerPage;
		$(".subCategoryEvent").hide();
		
		var i=0;
		$( ".subCategoryEvent" ).each(function () {				
			i++;
			if ( i >= page_to_display_start && i<page_to_display_end) {					
				$(this).show();
				}				 
			});	
			
		// add dots for long paging
		if(pagesNumber > dotPagesLimit){
			$('.pagging .page').hide();
			$('.pagging .page:nth-child(2)').show();
			$('.pagging .page:nth-child(3)').show();
			$('.pagging .page:nth-child(4)').show();
			$('.pagging .page:nth-last-child(1)').show();
			$('.pagging .page:nth-last-child(2)').show();
			$('.pagging .page:nth-last-child(3)').show();			
			$('.pagging .page:nth-last-child(4)').show();			
			$(".pagging .page.active").show();
			$(".pagging .page.active").nextAll('.page:first').show();
			$(".pagging .page.active").prevAll('.page:first').show();
			if ((($(".pagging .page.active").html())*1)-(($(dotElementFirst).html())*1) > 2)
				{
					$(dotElementFullFirst).show();
				}
			else
				{
					$(dotElementFullFirst).hide();
				}
			if ((($(dotElementLast).html())*1)-(($(".pagging .page.active").html())*1)> 2)
				{
					$(dotElementFullLast).show();
				}
			else
				{
					$(dotElementFullLast).hide();
				}	
		}

		$('.pagging .next').data('page', page_to_display_end-1);
		$('.pagging .prev').data('page', page_to_display_start-1 - eventsPerPage);

	});
});

$('#contactSubmit').click(function(){
	$('.formContacts .inputHolder').removeClass("err");
   var name = $('#name').val();
   var lname = $('#lname').val();
   var email = $('#email').val();
   var message = $('#message').val();
   if(name== ''){
	  $('#name').parent(".inputHolder").addClass("err");
	  return false;
	}
   if(lname== ''){
	  $('#lname').parent(".inputHolder").addClass("err");
	  return false;
	}			
	if(email== ''){
	   $('#email').parent(".inputHolder").addClass("err");
	   return false;
	}
	if(IsEmail(email)==false){
		$('#email').parent(".inputHolder").addClass("err");
		return false;
	}

	if(message== ''){
		$('#message').parent(".inputHolder").addClass("err");
		return false;
	}
	//ajax call php page Tedi da kaje kude otiva
	$.post("send.php", $("#contactform").serialize(),  function(response) {
	$('#contactform').fadeOut('slow',function(){
		$('#success').html(response);
		$('#success').fadeIn('slow');
	   });
	 });
	 return false;
  });

function IsEmail(email) {
var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;		
return regex.test(email);
}

// banner function
$(function() {
  $('#slides').slidesjs({
	width: 488,
	height: 164,
	play: {
	  active: true,
	  auto: true,
	  interval: 4000,
	  swap: true
	}
  });
});