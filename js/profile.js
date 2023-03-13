$(document).ready(function() {
	// get the current user's username from local storage
	var session_key = localStorage.getItem('session_key');
	alert(session_key);
	// populate the username field in the form
	//$('#username').val(username);

	// get the user's profile data from MongoDB
	$.ajax({
		url: './php/profile.php',
		type: 'GET',
		data: { session_key: session_key},
		dataType: 'json',
		success: function(data) {
			// populate the form with the user's profile data

			if(data.success)
			{
				$('#username').val(data.username);
				$('#dob').val(data.dob);
				$('#contact-address').val(data.contactAddress);
			}else{
				localStorage.removeItem('session_key');
				window.location.href = './login.html';
			}
			//alert(data.session_key);
			//alert(data.message);
		},
		error: function() {
			alert('Failed to get user profile data.');
		}
	});

	// handle form submission
	$('#profile-form').submit(function(event) {
		event.preventDefault();
		alert("hi");
		var session_key = localStorage.getItem('session_key');
		// get the form data
		var age = $('#age').val();
		var dob = $('#dob').val();
		var username = $('#username').val();
		var contactAddress = $('#contact-address').val();

		// send the updated profile data to MongoDB
		$.ajax({
			url: './php/profile.php',
			type: 'POST',
			data: {
				session_key: session_key,
				username: username,
				age: age,
				dob: dob,
				contactAddress: contactAddress
			},
			success: function(data) {

				alert(data.message);
				alert('Profile updated successfully.');
			},
			error: function() {
				alert('Failed to update profile.');
			}
		});
	});
});
