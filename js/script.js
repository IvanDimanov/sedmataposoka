$(document).ready(function () {	
	// layout columns	
	setTimeout("takeColumnHeight()", 200);
	
	// add three dots to events
	$( ".eventDescr" ).each(function() {
		var eventsString = $(this).text();
		var eventCharacters = eventsString.length;
		var item=150;
		continue_text = '...';
		if(eventCharacters>item)
		{
			sliceEvent=eventsString.substring(0, item - continue_text.length);
			sliceEvent+=continue_text;
			$(this).text(sliceEvent);
		}
	});	

	//datepicker	
	var date = new Date();
	var m = date.getMonth(), d = date.getDate(), y = date.getFullYear();
	var location = document.location.href;
	$("#datepicker").datepicker({
		minDate: new Date(y, m, d),
		dateFormat: 'dd-mm-yy',
		onSelect: function(dateText, inst) {
			var dateNumber=dateText.substring(0,2);;
			var today=$(".ui-state-highlight").html();
			var sendNumber=(dateNumber-today);			
			var searchRegLocation = /\/[a-zA-Z]{2}\//;
			var searchSubstring=location.match(searchRegLocation);	
			var findIndex=location.indexOf(searchSubstring);
			var toRegLocationIndex=findIndex+4;
			var currentLocation=location.substring(0,toRegLocationIndex);
			window.location.href=currentLocation+'search/dateSearch/'+sendNumber;
		}	
	});
	
	// show no events txt for selected date
	function getLastPathSegment(url) {
		var match = url.match(/\/([^\/]+)\/?$/);
		if (match) {
			return(match[1]);
		}
	}
	
	noEventsTxt=$(".noItems").hide();
	var endPath = getLastPathSegment(location);
		if (endPath == "NaN") {
			$(noEventsTxt).show();
			$(".pagging").hide();
		}
		
	/* paging */

	var i=0, n=0, eventsPerPage=1, dotPagesLimit=10, hideChildren=eventsPerPage-1;
	// hide children after hideChildren var
	$('.subCategoryEventHolder .subCategoryEvent:gt('+hideChildren+')').hide();		
	var allEvents=$( ".subCategoryEvent" ).length;
	$(".pagging").append('<span class="prev" data-page='+eventsPerPage+'><<</span>');
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
	var pageLength=$(".page").length;
	if (pageLength==1){$(".pagging .next").hide();}
	
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
	
	// validate contact form

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

		//ajax call php page
	
	function clearInputs(data){
		$(".form :input").each(function(){
		$(this).val('');
		});
	}

	function disabledFormElements(data){
		$(".form :input").each(function(){
			$(this).prop( "disabled", true );
		});
		$(".form :textarea").each(function(){
			$(this).prop( "disabled", true );
		});
	}	
	
	function enabledFormElements(data){
		$(".form :input").each(function(){
			$(this).prop( "disabled", false );
		});
		$(".form :textarea").each(function(){
			$(this).prop( "disabled", false );
		});
	}

	var bttnContactForm=$(".formContact .bttn").val();
	var bttnBeforeSendTxt=sending;
	
	$.ajax({
	type: "POST",
	url: "contacts.php",
	data: {'firstName': name, 'lastName': lname,'mailFrom': email, 'message' : message},
	beforeSend: function() {
		disabledFormElements()
		$(".formContact .bttn").val(bttnBeforeSendTxt);
	},
	success: function( response ) {
		enabledFormElements();
		$(".formContact .bttn").val(bttnContactForm);		
		if (result[0]!="''"){
			$('.formResponse').html(contactFormErrorMsg).show();
		}
		else{
			$('.formResponse').html(contactFormSuccessMsg).show();
		}
	}
	})	
		
		/*$.post("contacts.php", {'firstName': name, 'lastName': lname,'mailFrom': email, 'message' : message},  function(response) {
		if (response==true)
		{
		$('.formResponse').html('Успешно изпращане на формата.').show();
		}
		else
		{
		$('.formResponse').html('Възникна проблем, моля опитайте пак.').show();
		}
		});
		*/
		return false;
	});

	function IsEmail(email) {
		var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;		
		return regex.test(email);
	}	
	
	//slides image number check
	initializeSliders();
	
});

function initializeSliders(){

/* workshop slides */

	$('#slides').slidesjs({
	width: 488,
	height: 164,
		callback: {
	      loaded: function(number) {	        
	        var count_element = $('#slides:visible img').length
			if (count_element==1)
			{
				$("#slides .slidesjs-navigation").hide();
				$("#slides .slidesjs-pagination").hide();
				$("#slides .slidesjs-slide").css("left", "0px");
			}

	      },
	      start: function(number) {
	        
	      },
	      complete: function(number) {
	      	
	      }
	    }
	});
	}
	
// search validation and submit

function searchByWord() {
	$('#searchHolder').removeClass("err");
	// regex for latin and cyrillic words bigger than two letters
	var searchReg = /^[\u0400-\u04FFa-zA-Z0-9- ]{2,50}$/;
	var searchVal = $("#searchTxt").val();
	if (!searchReg.test(searchVal))
	{
	$('#searchHolder').addClass("err");
	return false;
	}
	else{
		var location = document.location.href;
		var searchRegLocation = /\/[a-zA-Z]{2}\//;
		var searchSubstring=location.match(searchRegLocation);	
		var findIndex=location.indexOf(searchSubstring);
		var toRegLocationIndex=findIndex+4;
		var currentLocation=location.substring(0,toRegLocationIndex);
		document.formSearch.action =currentLocation+'search/wordSearch/';
		return true;
	}

}	

function takeColumnHeight(){
	var allColumnHeight=$(".wrapMain").height();
	$(".wrapMainMiddle").css("min-height", (allColumnHeight-20))
}





