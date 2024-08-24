<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    function isDeliverableUsingSMTP($email)
    {
        $domain = explode('@', $email)[1];
        $mxRecords = [];
        $deliverable = false;

        // Get MX records for the domain
        getmxrr($domain, $mxRecords);

        foreach ($mxRecords as $mxRecord) {
            $mxHost = $mxRecord;
            $timeout = 5; // Set a timeout for the SMTP connection

            // $smtpConnection = @fsockopen($mxHost, 25, $errno, $errstr, $timeout);
            $smtpConnection = @stream_socket_client("tcp://$mxHost:25", $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT);
            // $smtpConnection = stream_socket_client("tls://" . $mxHost . ":587", $errno, $errstr, $timeout);

            if ($smtpConnection) {
                stream_set_timeout($smtpConnection, $timeout);
                $response = fgets($smtpConnection);

                // Send the "HELO" command
                fputs($smtpConnection, "HELO yourdomain.com\r\n");
                $response = fgets($smtpConnection);

                // Send the "MAIL FROM" command
                fputs($smtpConnection, "MAIL FROM: <apsraghuvanshi85@gmail.com>\r\n");
                $response = fgets($smtpConnection);

                // Send the "RCPT TO" command
                fputs($smtpConnection, "RCPT TO: <$email>\r\n");
                $response = fgets($smtpConnection);

                // Close the SMTP connection
                fputs($smtpConnection, "QUIT\r\n");
                fclose($smtpConnection);
                // Check if the response indicates successful delivery
                if (strpos($response, '250') === 0) {
                    $deliverable = true;
                    break;
                }
            }
        }

        return $deliverable;
    }

    // $email = 'akuhn8889@dysartstudents.org';
    $json_data = file_get_contents('php://input');
    // Deserialize the JSON data
    $data = json_decode($json_data, true);
    $captchaSecretKey = '6Ldp2wUiAAAAAN04XHtJEQhoH4dKdUeHr8CK1C_F';
    $captchaResponse = $data['g-recaptcha-response'];
    $email = $data['email'];

    // Send a POST request to Google reCAPTCHA API
    $verificationUrl = 'https://www.google.com/recaptcha/api/siteverify';
    $data = array(
        'secret' => $captchaSecretKey,
        'response' => $captchaResponse
    );

    $options = array(
        'http' => array(
            'header' => 'Content-type: application/x-www-form-urlencoded',
            'method' => 'POST',
            'content' => http_build_query($data)
        )
    );

    $context = stream_context_create($options);
    $response = file_get_contents($verificationUrl, false, $context);

    $captchaResult = json_decode($response);
    // if ($captchaResult->success) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            if (isDeliverableUsingSMTP($email)) {
                $res = ['status' => 200, 'msg' => 'Email is deliverable.'];
                echo json_encode($res);
            } else {
                $res = ['status' => 201, 'msg' => 'Email is not deliverable.'];
                echo json_encode($res);
            }
        } else {
            $res = ['status' => 201, 'msg' => 'Email is not valid.'];
            echo json_encode($res);
        }
    // }
    //  else {
    //     $res = ['status' => 201, 'msg' => 'reCAPTCHA verification failed.'];
    //     echo json_encode($res);
    // }
}
