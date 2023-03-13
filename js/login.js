$(document).ready(function() {
    // listen for form submission
    $('#login-form').submit(function(event) {
      event.preventDefault(); // prevent default form submission
  
      // get form data
      var username = $('#username').val();
      var password = $('#password').val();
  
      // send data to server using AJAX
      $.ajax({
        type: 'POST',
        url: 'login.php',
        data: { username: username, password: password },
        dataType: 'json',
        success: function(response) {
          if (response.success) {
            // redirect to profile page
            window.location.href = 'profile.php';
          } else {
            // display error message
            alert(response.message);
          }
        },
        error: function() {
          // display error message
          alert('Error connecting to server.');
        }
      });
    });
  });