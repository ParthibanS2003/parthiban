$(document).ready(function() {
	// get the current user's username from local storage
	var username = localStorage.getItem('username');

	// populate the username field in the form
	$('#username').val(username);

	// get the user's profile data from MongoDB
	$.ajax({
		url: 'getProfileData.php',
		type: 'POST',
		data: { username: username },
		dataType: 'json',
		success: function(data) {
			// populate the form with the user's profile data
			$('#age').val(data.age);
			$('#dob').val(data.dob);
			$('#contact-address').val(data.contact_address);
		},
		error: function() {
			alert('Failed to get user profile data.');
		}
	});

	// handle form submission
	$('#profile-form').submit(function(event) {
		event.preventDefault();

		// get the form data
		var age = $('#age').val();
		var dob = $('#dob').val();
		var contactAddress = $('#contact-address').val();

		// send the updated profile data to MongoDB
		$.ajax({
			url: 'updateProfileData.php',
			type: 'POST',
			data: {
				username: username,
				age: age,
				dob: dob,
				contactAddress: contactAddress
			},
			success: function() {
				alert('Profile updated successfully.');
			},
			error: function() {
				alert('Failed to update profile.');
			}
		});
	});
});