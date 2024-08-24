function validateEmail(email) {
    var emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
    return emailRegex.test(email);
  }
  $('#emailForm').submit(function(event) {
    event.preventDefault();

    const email = $('.email-input').val();
    const apiUrl = 'email-validator.php';
    const recaptchaResponse = $('#g-recaptcha-response').val();
    if (!email) {
      $("#message").text("Please enter email!");
      $("#message").css('color', 'red');
      return false;
    }else{
        $("#message").text("");
    }
     if (!validateEmail(email)) {
      $("#message").text("Please enter valid email!");
      $("#message").css('color', 'red');
      return false;
    }else{
        $("#message").text("");
    }
    if(!recaptchaResponse){
        $("#message").text("Invalid Captcha!");
        $("#message").css('color', 'red');
        return false;
    }else{
        $("#message").text("");
    }
    $.ajax({
      url: apiUrl,
      method: 'POST',
      contentType: 'application/json',
      data: JSON.stringify({
        email,
        'g-recaptcha-response': recaptchaResponse 
      }),
      beforeSend: function() {
        $(".submit-btn").attr("disabled", true);
        $(".submit-btn").text("Loading...");
        $(".loading").show();
        $(".container").addClass("blur");
      },
      success: function(data) {
        var res = JSON.parse(data);
        // Handle the response data as needed
        if (res.status === 200) {
          $("#message").text(res.msg);
          $("#message").css('color', 'green');
        } else if (res.status === 201) {
          $("#message").text(res.msg);
          $("#message").css('color', 'red');
        } else {
          $("#message").text('Unknown Error!');
        }
      },
      error: function(xhr, textStatus, errorThrown) {
        console.error('Request failed:', textStatus, errorThrown);
      },
      complete: function() {
        $(".loading").hide();
        $(".submit-btn").removeAttr("disabled");
        $(".submit-btn").text("Verify");
        $(".container").removeClass("blur");
      }
    });

  });