$(document).ready(function() {
 
  alert("Started...");
    // listen for form submission
    $('#login-form').submit(function(event) {
      event.preventDefault(); // prevent default form submission
     
      // get form data
      var username = $('#username').val();
      var password = $('#password').val();
 
      // send data to server using AJAX
      $.ajax({
        type: 'POST',
        url: './php/login.php',
        data: { username: username, password: password },
        dataType: 'json',
        success: function(response) {
          if (response.success) {
            // redirect to profile page
            localStorage.setItem('session_key',response.session_id);            
            //alert(response.session_id);
            window.location.href = 'profile.html';

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