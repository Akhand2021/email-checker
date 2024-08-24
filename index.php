<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Email Checker</title>
  <link rel="stylesheet" href="style.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>

</head>

<body>
  <div class="loading" style="display: none;">
    <p>Please wait</p>
    <span><i></i><i></i></span>
  </div>
  <div class="container">
    <h1>Email Verification</h1>
    <form id="emailForm">
      <input type="text" class="email-input" placeholder="Enter your email">
      <button type="submit" class="submit-btn">Verify</button>
      <div class="captcha-container">
      <div class="g-recaptcha" data-sitekey="6Ldp2wUiAAAAACQyvUEDeabdcbm1pwWzn8gzjaPg"></div>
      </div>
      <span id="message"></span>
    </form>
  </div>

  <script src="script.js"></script>

</body>

</html>