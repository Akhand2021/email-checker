<?php
include('vendor/autoload.php');
$domain="0-mail.com";

use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\DNSCheckValidation;
use Egulias\EmailValidator\Validation\MultipleValidationWithAnd;
use Egulias\EmailValidator\Validation\RFCValidation;
use ElliotJReed\DisposableEmail\Email;

$validator = new EmailValidator();
$email = 'apsraghuvanshi85@gmail.com';
$multipleValidations = new MultipleValidationWithAnd([
    new RFCValidation(),
    new DNSCheckValidation()
]);
//ietf.org has MX records signaling a server with email capabilities
$res = $validator->isValid($email, $multipleValidations); //true
// $res = $validator->isValid("example@example.com", new RFCValidation());

function isDeliverableUsingSMTP($email) {
    $domain = explode('@', $email)[1];
    $mxRecords = [];
    $deliverable = false;

    // Get MX records for the domain
    getmxrr($domain, $mxRecords);

    foreach ($mxRecords as $mxRecord) {
	    $mxHost = $mxRecord;
        $timeout = 5; // Set a timeout for the SMTP connection

        $smtpConnection = fsockopen($mxHost, 25, $errno, $errstr, $timeout);
    //     $smtpConnection = @stream_socket_client("tcp://$mxHost:25", $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT);

        if ($smtpConnection) {
            stream_set_timeout($smtpConnection, $timeout);
            $response = fgets($smtpConnection);
            
            // Send the "HELO" command
            fputs($smtpConnection, "HELO yourdomain.com\r\n");
            $response = fgets($smtpConnection);
            
            // Send the "MAIL FROM" command
            fputs($smtpConnection, "MAIL FROM: <your-email@yourdomain.com>\r\n");
            $response = fgets($smtpConnection);
            // Send the "RCPT TO" command
            fputs($smtpConnection, "RCPT TO: <$email>\r\n");
            $response = fgets($smtpConnection);
            
            echo $response;
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

$email = 'apsraghuvanshi85@gmail.com';
if (isDeliverableUsingSMTP($email)) {
    echo "Email is deliverable.";
} else {
    echo "Email is not deliverable.";
}

die;


// if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
//     if ((new Email('./domain.txt'))->isDisposable($email)) {
//         echo 'This is a disposable / temporary email address <br>';
//     }
// } else {
//     echo 'This is not a valid email address <br>';
// }

// var_dump($res);
// $emailParts = explode('@', $email);
//     $domain = strtolower($emailParts[1]);
// 	$mxRecords = dns_get_record($domain, DNS_MX);
// return !empty($mxRecords);

// single_type_dns_get_record($domain, DNS_A);
// single_type_dns_get_record($domain, DNS_CNAME);
// single_type_dns_get_record($domain, DNS_HINFO);
// single_type_dns_get_record($domain, DNS_CAA);
// single_type_dns_get_record($domain, DNS_MX);
// single_type_dns_get_record($domain, DNS_NS);
// single_type_dns_get_record($domain, DNS_PTR);
// single_type_dns_get_record($domain, DNS_SOA);
// single_type_dns_get_record($domain, DNS_TXT);
// single_type_dns_get_record($domain, DNS_AAAA);
// single_type_dns_get_record($domain, DNS_SRV);
// single_type_dns_get_record($domain, DNS_NAPTR);
// single_type_dns_get_record($domain, DNS_A6);
// single_type_dns_get_record($domain, DNS_ALL);
// single_type_dns_get_record($domain, DNS_ANY);

// function single_type_dns_get_record($domain, $type){
// 	echo "-------------".$type."-------------<br>";
// 	$res=dns_get_record($domain, $type);
// 	foreach($res as $ar){
// 		foreach($ar as $key=>$val){
//             if(gettype($val) == 'array'){
//                 echo "<pre>";
//                 print_r($val);
//             }else{
// 				echo $key.":".$val."</br>";
//             }
// 		}
// 		echo "</br>";
// 	}
// }

?>
