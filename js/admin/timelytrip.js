$(function() {
	var $curr_modal_stop = null;

	$(document).on('click', '#soldiers-table td span.stop', function() {
		var $stop = $(this);
		var stopname = $stop.find('span.name').text();
		var stopid = $stop.find('span.stopid').text();
		var agency = $stop.find('span.agency').text().trim();
		var given = $stop.find('span.given').text()==='true';
		var nameonsign = $stop.find('span.nameonsign').text();
		var abandoned = $stop.hasClass('abandoned');

		var $m = $('#stopdetail-modal');

		$m.find('.stopname input').val(stopname).refreshLabel();
		$m.find('.stopid input').val(stopid).refreshLabel();
		$m.find('.agency select').val(agency);
		$m.find('.given input').prop('checked', given).trigger('change');
		$m.find('.nameonsign input').val(nameonsign).refreshLabel();
		$m.find('.abandoned input').prop('checked', abandoned);

		$curr_modal_stop = $stop;

		$m.modal();
	});

	$('#stopdetail-modal .given input').change(function() {
		var checked = $(this).prop('checked');
		var $nameonsign = $('#stopdetail-modal .nameonsign');
		if(checked) { $nameonsign.slideDown(); } else { $nameonsign.slideUp(); }
	});

	$('#stopdetail-modal .stopdetail-submit').click(function() {
		var $m = $('#stopdetail-modal');
		
		var userid = parseInt($curr_modal_stop.closest('tr').attr('data-userid'));
		var id = $curr_modal_stop.find('span.id').text();

		var stopname = $m.find('.stopname input').val();
		var stopid = $m.find('.stopid input').val().trim();
		
		if(stopid.length==0) { $m.find('#agency select').val(''); };
		var agency = $m.find('.agency select').val().trim();

		var given = $m.find('.given input').prop('checked');
		var nameonsign = $m.find('.nameonsign input').val().trim();
		var abandoned = $m.find('.abandoned input').prop('checked');

		var data = { userid: userid, id: id,
			stopname: stopname,
			stopid: stopid,
			agency: agency,
			given: given,
			nameonsign: nameonsign,
			abandoned: abandoned
		};

		$.ajax({
		  url: "../ajax/admin/update-stopdetail.php",
		  type: "POST",
		  data: data,
		  dataType: 'json',
		  
		  success: function(d) {
			switch(d.status) {
			case 'success':
				// update stop
				$curr_modal_stop.toggleClass('nostopid', stopid.length===0);
				$curr_modal_stop.find('span.name').text(stopname);
				$curr_modal_stop.find('span.stopid').text(stopid);
				$curr_modal_stop.find('span.agency').text(agency);
				$curr_modal_stop.find('span.given').text(given ? 'true' : 'false');
				$curr_modal_stop.find('span.nameonsign').text(nameonsign);
				$curr_modal_stop.toggleClass('abandoned', abandoned);

				var $curr_td = $curr_modal_stop.closest('td');
				var $new_td = null;

				switch(d.stop_classification) {
				case 'notgiven':
					$new_td = $curr_modal_stop.closest('tr').find('td.notgiven-td');
					break;
				case 'notask':
					$new_td = $curr_modal_stop.closest('tr').find('td.notask-td');
					break;
				}

				if($curr_td[0]!=$new_td[0]) {
					$curr_modal_stop.fadeOut(function() {
						$curr_modal_stop.detach().appendTo($new_td[0]).fadeIn();
					});
				}
					
				$m.modal('hide');
				break;

			case 'agencynull':
				alert('Agency cannot be left empty or blank.');
				break;

			case 'given_nameonsign':
				alert('If stop is marked as "given", then name on sign must also be filled.');
				break;

			case 'missing':
			default:
				console.log(d);
				alert('Oops, an error occurred: '+d.status);
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			console.log(textStatus, errorThrown);
			alert('Oops, an error occurred.');
 		}});
	});

	$('#stopdetail-modal button.get-sign').click(function() {
		var $m = $('#stopdetail-modal');

		var name = $m.find('.nameonsign input').val().trim();
		var stopid = $m.find('.stopid input').val().trim();
		var agency = $m.find('.agency select').val();

		if(name.length==0 || stopid.length==0 || agency.length==0) {
			alert('Stop ID, Agency or Name on Sign missing. Add them and "Update" the stop first.');
			return;
		}

		var url = '../bussign/index_army.php?agency=MARTA&sid='+stopid+'&stopNameOverride=&adopter='+name+'&rank=&weblogo=';
		window.open(url);
	});

	$('#new-soldier-button').click(function() {
		var $m = $('#newsoldier-modal');

		$m.find('#soldiername input').val('');
		$m.find('#soldieremail input').val('');
		$m.find('#soldierbusstop input').val('');
		$m.find('#soldiernotes input').val('');
		$m.find('#soldierphone input').val('');

		$m.modal();
	});

	$('#newsoldier-submit').click(function() {
		var $m = $('#newsoldier-modal');
		
		var name = $m.find('#soldiername input').val().trim();
		var email = $m.find('#soldieremail input').val().trim();
		var phone = $m.find('#soldierphone input').val().trim();
		var stoptoadopt = $m.find('#soldierbusstop input').val().trim();
		var notes = $m.find('#soldiernotes input').val().trim();

		var data = {
			name: name, email: email, phone: phone, stoptoadopt: stoptoadopt, notes: notes
		};

		$.ajax({
		  url: "../ajax/admin/register-timelytrip-soldier.php",
		  type: "POST",
		  data: data,
		  dataType: 'json',
		  
		  success: function(d) {
			switch(d.status) {
			case 'success':
				
				alert('done! refresh your window.');	
				$m.modal('hide');
				break;

			case 'bademail':
				alert('The email address looks invalid');
				break;

			case 'already':
				alert('The user is already registered by this email!');
				break;

			case 'failure':
			case 'failjoinop':
				alert('Some strange error has occurred. Make note of this user on a spreadsheet and send it to Harshath.');
				break;

			default:
				console.log(d);
				alert('Oops, an error occurred: '+d.status);
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			console.log(textStatus, errorThrown);
			alert('Oops, an error occurred.');
 		}});
	});

	$curr_modal_soldier = null;

	$('.addstoplink').click(function() {
		$curr_modal_soldier = $(this).closest('tr');

		var $m = $('#addstop-modal');
		$m.find('.stopname input').val('');
		$m.find('.stopid input').val('');
		$m.find('.agency select').val('');

		$m.modal();
	});

	$('#addstop-modal .addstop-submit').click(function() {
		var userid = $curr_modal_soldier.attr('data-userid');
		var $m = $('#addstop-modal');
		var stopname = $m.find('.stopname input').val().trim();
		var stopid = $m.find('.stopid input').val().trim();
		var agency = $m.find('.agency select').val();

		if(stopid.length!=0 && agency.length==0) {
			alert('Select agency along with stop id');
			return;
		}

		var data = { userid: userid, stopname: stopname, stopid: stopid, agency: agency };

		$.ajax({
		  url: "../ajax/admin/adopt-new-stop.php",
		  type: "POST",
		  data: data,
		  dataType: 'json',
		  
		  success: function(d) {
			switch(d.status) {
			case 'success':
				alert('done! refresh your window.');
				$m.modal('hide');
				break;

			default:
				console.log(d);
				alert('Oops, an error occurred: '+d.status);
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			console.log(textStatus, errorThrown);
			alert('Oops, an error occurred.');
 		}});
		
	});

	$('#select-all-soldiers').click(function(e) {
		e.preventDefault();
		$('#soldiers-table td.selection input[type=checkbox]').prop('checked', true).change();
		return false;
	});

	$('#select-none-soldiers').click(function(e) {
		e.preventDefault();
		$('#soldiers-table td.selection input[type=checkbox]').prop('checked', false).change();
		return false;
	});

	$('#select-nosign-all-soldiers').click(function(e) {
		e.preventDefault();
		$('#soldiers-table td.notgiven-td:not(:empty)')
			.closest('tr').find('td.selection input[type=checkbox]').prop('checked', true).change();
		return false;
	});

	$('#select-nosign-none-soldiers').click(function(e) {
		e.preventDefault();
		$('#soldiers-table td.notgiven-td:not(:empty)')
			.closest('tr').find('td.selection input[type=checkbox]').prop('checked', false).change();
		return false;
	});

	$('#select-sign-notask-all-soldiers').click(function(e) {
		e.preventDefault();
		$('#soldiers-table td.notask-td:not(:empty)')
			.closest('tr').find('td.selection input[type=checkbox]').prop('checked', true).change();
		return false;
	});

	$('#select-sign-notask-none-soldiers').click(function(e) {
		e.preventDefault();
		$('#soldiers-table td.notask-td:not(:empty)')
			.closest('tr').find('td.selection input[type=checkbox]').prop('checked', false).change();
		return false;
	});

	$(document).on('change', '#soldiers-table td.selection input[type=checkbox]', function() {
		var $cb = $(this);
		$cb.closest('tr').toggleClass('soldier-selected', $cb.prop('checked'));
	})

	$('#get-emails').click(function() {
		$selectedSoldiers = $('#soldiers-table td.selection input[type=checkbox]:checked').closest('tr');
		if($selectedSoldiers.length==0) { 
			alert('No soldiers selected!');
			return;
		}

		var $emails = $selectedSoldiers.find('td.user-data span.email').clone();

		var $modal = $('#email-list-modal');
		$modal.find('.modal-body').empty().append($emails);
		$modal.modal();
	});


	$curr_modal_soldier = null;

	$('.user-data a.soldier-name').click(function(e) {
		e.preventDefault();

		var $details = $(this).closest('.user-data');

		var name = $details.find('a.soldier-name').text();
		var email = $details.find('span.email').text();
		var phone = $details.find('span.phone').text();
		var notes = $details.find('span.notes').text();
		var joindate = $details.closest('tr').find('td.join-date').text();

		var $m = $('#soldier-details-modal');

		$m.find('.soldiername input').val(name).refreshLabel();
		$m.find('.soldieremail input').val(email).refreshLabel();
		$m.find('.soldierphone input').val(phone).refreshLabel();
		$m.find('.soldiernotes textarea').val(notes).refreshLabel();
		$m.find('.soldierjoindate input').val(joindate).refreshLabel();

		$curr_modal_soldier = $details;

		$m.modal();

		return false;
	});

	$('#soldier-details-modal .update-soldierdetails').click(function() {
		if($curr_modal_soldier == null) { return; }

		var $m = $('#soldier-details-modal');

		var name = $m.find('.soldiername input').val();
		var email = $m.find('.soldieremail input').val();
		var phone = $m.find('.soldierphone input').val();
		var notes = $m.find('.soldiernotes textarea').text();
		var joindate = $m.find('.soldierjoindate input').val();

		var data = { name: name, email: email, phone: phone, notes: notes, joindate: joindate };

		$.ajax({
		  url: "../ajax/admin/update-soldierdetail.php",
		  type: "POST",
		  data: data,
		  dataType: 'json',
		  
		  success: function(d) {
			switch(d.status) {
			case 'success':
				// update soldier
				
				
				$curr_modal_soldier.find('a.soldier-name').text(name);
				$curr_modal_soldier.find('span.email').text(email);
				$curr_modal_soldier.find('span.phone').text(phone);
				$curr_modal_soldier.find('span.notes').text(notes);
				$curr_modal_soldier.closest('tr').find('td.join-date').text(joindate);

				$curr_modal_soldier.toggleClass('hasnotes', notes.length===0);

				$curr_modal_soldier = null;
					
				$m.modal('hide');
				break;

			case 'agencynull':
				alert('Agency cannot be left empty or blank.');
				break;

			case 'given_nameonsign':
				alert('If stop is marked as "given", then name on sign must also be filled. (and vice-versa).');
				break;

			case 'missing':
			default:
				console.log(d);
				alert('Oops, an error occurred: '+d.status);
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			console.log(textStatus, errorThrown);
			alert('Oops, an error occurred.');
 		}});
	})

})