/* Main calls to the server */
$( document ).ready(function() {

	/* Log in user */
	$('#form-admin-login').on('submit', function(event){

		event.preventDefault();

		var $login_form  = $('#form-admin-login'),
			name         = $login_form.find('#inputName').val(),
			pass         = $login_form.find('#inputPassword').val(),
			/* crypted pass */
			pass_cripted = CryptoJS.SHA3(pass, { outputLength: 256 }).toString(),
			url			 = window.location.origin + '/sedmataposoka/rest_api/login';

		  $.ajax({
			type: 'POST',
			url : url,
			data: {
			  "name"    : name,
			  "password": pass_cripted
			}
		  })
		  .done(function (statusText, status, jqXHR) {
			window.location.reload();
		  })
		  .fail(function (jqXHR, status, statusText) {
			response_data = JSON.parse(jqXHR.responseText);
			if (response_data.captcha_required) {
				$('.captcha').show();
			}
		  });

	  });
	  

	/* Log in user */
	$('#log_out').on('click', function(event){

		var url = window.location.origin + '/sedmataposoka/rest_api/login';

		$.ajax({
			type: 'DELETE',
			url : url,
			data: {}
		})
		.done(function (statusText, status, jqXHR) {
		 	window.location.reload();
		})
		.fail(function (jqXHR, status, statusText) {
			// TODO: notify user for a problem
		});
		  
	  });	  
});