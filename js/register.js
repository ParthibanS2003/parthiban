$(document).ready(function() {
     //alert("welcome");
    // handle form submission
    $('#signup-form').submit(function(e) {
      e.preventDefault(); // prevent default form submission
  
      // get form data
      var formData = {
        username: $('#username').val(),
        email: $('#email').val(),
        password: $('#password').val(),
        confirm_password: $('#confirm-password').val()
      };
  
      // send form data to server
      $.ajax({
        url: './php/register.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
          if (response.success) {
            alert('Signup successful!');
            window.location.href = 'login.html';
          } else {
            alert(response.message);
          }
        },
        error: function() {
          alert('Error submitting form!');
        }
      });
  
    });
  
  });