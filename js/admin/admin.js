/* Main calls to the server */
$( document ).ready(function() {

	/*Separated url between log in and log out*/
	var url_origin = window.location.origin,
		path_array = (window.location.pathname).split('/'),
		url_login  = url_origin + '/' + path_array[1] + '/rest_api/login';
		
	/* Log in user */
	$('#form-admin-login').on('submit', function(event){

		event.preventDefault();

		var $login_form      = $('#form-admin-login'),
			name             = $login_form.find('#inputName').val(),
			pass             = $login_form.find('#inputPassword').val(),
			/* crypted pass */
			pass_cripted     = CryptoJS.SHA3(pass, { outputLength: 256 }).toString(),
			captcha_response = $('#g-recaptcha-response').val();

		  $.ajax({
			type: 'POST',
			url : url_login,
			data: {
			  "name"           : name,
			  "password"       : pass_cripted,
			  "captchaResponse": captcha_response
			}
		  })
		  .done(function (statusText, status, jqXHR) {
		  	/*Load admin home page*/
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

		$.ajax({
			type: 'DELETE',
			url : url_login,
			data: {}
		})
		.done(function (statusText, status, jqXHR) {
		 	window.location.reload();
		})
		.fail(function (jqXHR, status, statusText) {
			// TODO: notify user for a problem
		});
		  
	  });

	/* Load thoughts data */
	$.ajax({
		type: 'GET',
		url : url_origin + '/' + path_array[1] + '/rest_api/thoughts',
		data: {}
	})
	.done(function (statusText, status, jqXHR) {

	 	var thoughts_object = JSON.parse(jqXHR.responseText);
	 	for(var property in thoughts_object) {		 		
		 	$('#thoughts_data').find('tbody').append('<tr>'
        			+'<td>' + thoughts_object[property].author_id + '</td>'	        			
        			+'<td>' + thoughts_object[property].author.bg + '</td>'
        			+'<td>' + thoughts_object[property].text.bg + '</td>'
        			+'<td>' + thoughts_object[property].startDate + ' Date</td>'
        			+'<td>' + thoughts_object[property].endDate + '</td>'
			        +'<td>'
			            +'<button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#myModal">Edit</button>'
			            +'<button type="button" class="btn btn-default btn-xs">Delete</button>'
			        +'</td>'
      			+'</tr>');
	 	}

	})
	.fail(function (jqXHR, status, statusText) {
		$('#thoughts_data').find('tbody').append('<tr><td>There are no available thoughts</td></tr>');
	});

});