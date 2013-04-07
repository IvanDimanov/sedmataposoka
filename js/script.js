/* This file contains common JS functionalities for All project HTML files */

/*(function () {
  
  // Shorter debug function
  function log(msg) {
    console.log(msg);
  }
  
  var n1 = 1;
  
  function sum(n2) { 
	n2++
	return n2;
  }
  
  log(sum(n1))
  
})()*/
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