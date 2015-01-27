/* Main calls to the server */
$( document ).ready(function() {

	/* Log in user */
	$('#form-admin-login').on('submit', function(event){
		event.preventDefault();
		var $login_form  = $('#form-admin-login'),
			name         = $login_form.find('#inputName').val(),
			pass         = $login_form.find('#inputPassword').val(),
			/* crypted pass */
			pass_cripted = CryptoJS.SHA3(pass).toString();
			
		  $.ajax({
			type: 'POST',
			/* TODO: take current URL */
			url : 'http://localhost/sedmataposoka/rest_api/login',
			data: {
			  "name"    : name,
			  "password": pass_cripted
			}
		  })
		  .done(function (statusText, status, jqXHR) {
			  console.log('success');
		  })
		  .fail(function (jqXHR, status, statusText) {
			console.log('error');
		  });
		  
	  });
	  
});