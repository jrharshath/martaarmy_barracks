$(function( ) {
	$('#join-now-btn').click(function(e) {
		e.preventDefault();
		$("#signup-form").slideToggle();
	});

	$('#signup-form').submit(function(e) {
		e.preventDefault();

		var name = $('#reg-name').val();
		var email = $('#reg-email').val();
		var password = $('#reg-password').val();
		var address = $('#reg-address').val();
		// opt: check for invalids, blanks etc

		$.ajax({
		  url:     "ajax/register.php",
		  type: "POST",
		  data:    {name: name, email: email, password: password, address: address},
		  dataType: 'json',
		  
		  success: function(d) {
			switch(d.status) {
			case 'success':
				// todo: $('#signup-form input').disable();
				showMessage('Done! You can now login by clicking <a href="index.php">here.</a>');
				break;

			case 'noname':
				$('#reg-name').closest('.float-label')
					.showError('Name is required.', 4000);
				break;
			case 'bademail':
				$('#reg-email').closest('.float-label')
					.showError('Email seems invalid. Check it again?', 4000);
				break;
			case 'already':
				$('#reg-email').closest('.float-label')
					.showError('This email is already registered. Try logging in?', 4000);
				break;
			case 'nopassword':
				$('#reg-password').closest('.float-label')
					.showError('Password is required.', 4000);
				break;
			case 'fail':
			default:
				$('#reg-address').closest('.float-label')
					.showError('Oops, something broke. Try again?', 4000);
				break;
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			$('#reg-address').closest('.float-label')
					.showError('Oops, something broke. Try again?', 4000);
 		}});
	});

	function showMessage(msg) {
		var $msg = $('#msg');
		$msg.html(msg).slideDown();
	}
});
