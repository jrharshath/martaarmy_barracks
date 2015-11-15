$(function( ) {
	
	$('#signup-form button').click(function(e) {
		e.preventDefault();

		var name = $('#name').val();
		var email = $('#email').val();
		var stoptoadopt = $('#stoptoadopt').val();
		var comment = $('#comment').val();

		$('#signup-form button').prop('disabled', true);

		$.ajax({
		  url:     "ajax/register-iframe.php",
		  type: "POST",
		  data:    {name: name, email: email, stoptoadopt: stoptoadopt, comment: comment},
		  dataType: 'json',
		  
		  success: function(d) {
			switch(d.status) {
			case 'success':
				$('#signup-form').slideUp();
				$('#success-message').slideDown();
				break;

			case 'noname':
				showError('Oops! A name is required...');
				break;
			case 'bademail':
				showError('Email seems invalid. Check it again?');
				break;
			case 'already':
				showMessage("We think you're already signed up. If that's not right, contact us at themartaarmy@gmail.com");
				$('#signup-form').slideUp();
				break;
			case 'nocomment':
				showError('Please leave a comment for us!');
				break;
			case 'nostoptoadopt':
				showError("Oops, looks like the bus stop address is empty!");
				break;
			case 'failjoinop':
			case 'fail':
			default:
				showError("Oops, something broke on our side. Please try again later, and if it doesn't work, please let us know at themartaarmy@gmail.com!", 0);
				break;
			}
			$('#signup-form button').prop('disabled', false);
		},
		error: function(jqXHR, textStatus, errorThrown) {
			showError("Oops, something broke on our side. Please try again later, and if it doesn't work, please let us know at themartaarmy@gmail.com!", 0);
			$('#signup-form button').prop('disabled', false);
 		}});
	});

	function showError(msg, delay) {
		if(delay === undefined) { delay = 3000; }

		var $msg = $('#error-message');
		$msg.html(msg).slideDown();
		if(delay !== 0) {
			setTimeout(function() { $msg.slideUp(); }, delay);
		}
	}

	function showMessage(msg) {
		var $msg = $('#success-message');
		$msg.html(msg).slideDown();
	}
});
