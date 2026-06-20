<?php



// $recaptchaToken = $_POST['recaptcha_token'] ?? '';

// $secretKey = "6LfTshIsAAAAAC6ROZggYE6P0alFtdZEJo-ceAZC";

// $verifyUrl = "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$recaptchaToken";

// $response = file_get_contents($verifyUrl);
// $responseData = json_decode($response);

// if (!$responseData->success || $responseData->score < 0.5) {
//     echo "reCAPTCHA Failed";
//     exit;
// }




if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $firstName = $_POST['firstName'] ?? '';
    $phone     = $_POST['phone'] ?? '';
    $email     = $_POST['email'] ?? '';
    $subject   = $_POST['subject'] ?? '';
    $message   = $_POST['message'] ?? '';

    // ZeptoMail API URL
    $url = "https://api.zeptomail.com/v1.1/email";

    // Your ZeptoMail API Token (must start with ZAP_)
    $apiToken = "wSsVR610/BX3C/srmTf7J+46nFxVBgigF0l/2Vf07XP9GarEosc8nhacV1CmHfQcRzE8E2YUor4hnksEgzsK2tt5n1kECSiF9mqRe1U4J3x17qnvhDzIWGpYlhSNKooIxwxrn2lgFMsj+g==";

    /*--------------------------------------------------------------
      1. SEND EMAIL TO ADMIN
    --------------------------------------------------------------*/

    $payloadAdmin = [
    "from" => [
        "address" => "noreply@truelixir.co.in",
        "name" => "TruElixir Life Science Contact Form"
    ],
    "to" => [
        [
            "email_address" => [
                "address" => "Ifo@kyrrox.in",
                "name" => "Admin"
            ]
        ],
        [
            "email_address" => [
                "address" => "prathamesh.pitale@gmail.com",
                "name" => "Web Admin"
            ]
        ]
    ],
        "subject" => "New Contact Enquiry - $subject",
       "htmlbody" => "
        <div style='
            font-family: Arial, sans-serif;
            background: #f5f6fa;
            padding: 25px;
            color: #333;'>

    <!-- Header -->
    <div style='
        background: #ffffff;
        padding: 20px 25px;
        border-radius: 10px;
        border-left: 4px solid #4A90E2;
        margin-bottom: 20px;
    '>
        <h2 style='margin: 0; font-size: 22px; color: #4A90E2;'>
            📩 New Contact Enquiry from Kyrrox
        </h2>
        <p style='margin: 6px 0 0; color: #777; font-size: 14px;'>
            A new contact request has been submitted from your website.
        </p>
    </div>

    <!-- Details Box -->
    <div style='
        background: #ffffff;
        padding: 20px 25px;
        border-radius: 10px;
        border: 1px solid #e5e5e5;
    '>

        <h3 style='margin-top: 0; font-size: 18px; color:#444;'>Enquiry Details</h3>

        <table style='width: 100%; border-collapse: collapse; font-size: 15px;'>

            <tr>
                <td style='padding: 8px 0; width:180px; color:#555;'><strong>Name:</strong></td>
                <td style='padding: 8px 0;'>$firstName</td>
            </tr>

            <tr>
                <td style='padding: 8px 0; color:#555;'><strong>Phone:</strong></td>
                <td style='padding: 8px 0;'>$phone</td>
            </tr>

            <tr>
                <td style='padding: 8px 0; color:#555;'><strong>Email:</strong></td>
                <td style='padding: 8px 0;'>$email</td>
            </tr>

            <tr>
                <td style='padding: 8px 0; color:#555;'><strong>Subject:</strong></td>
                <td style='padding: 8px 0;'>$subject</td>
            </tr>

            <tr>
                <td style='padding: 8px 0; vertical-align: top; color:#555;'><strong>Message:</strong></td>
                <td style='padding: 8px 0;'>
                    <div style='
                        background:#f3f4f6;
                        padding:12px 15px;
                        border-radius:6px;
                        color:#444;
                        line-height:1.6;
                        border:1px solid #e2e2e2;
                        font-size:14px;
                    '>
                        $message
                    </div>
                </td>
            </tr>

        </table>

    </div>

    <!-- Footer note -->
    <p style='text-align:center; margin-top: 25px; font-size: 13px; color:#888;'>
        This is an automated notification from TruElixir Life Science Pvt. Ltd. website.
    </p>

</div>
"

    ];

    // SEND EMAIL TO ADMIN
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "accept: application/json",
        "authorization: Zoho-enczapikey $apiToken",
        "content-type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payloadAdmin));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $responseAdmin  = curl_exec($ch);
    $errorAdmin     = curl_error($ch);
    $httpCodeAdmin  = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    // If admin mail fails → stop here
    if ($errorAdmin || $httpCodeAdmin < 200 || $httpCodeAdmin >= 300) {

        file_put_contents(__DIR__ . "/zepto_api_error_log.txt",
            "ADMIN MAIL ERROR: HTTP: $httpCodeAdmin\nResponse: $responseAdmin\n\n",
            FILE_APPEND
        );

        echo "Mail Error: Could not send to admin";
        exit;
    }

    /*--------------------------------------------------------------
      2. SEND THANK-YOU EMAIL TO USER
    --------------------------------------------------------------*/

    $payloadUser = [
        "from" => [
            "address" => "noreply@Kyrroxin",
            "name" => "Kyrrox"
        ],
        "to" => [[
            "email_address" => [
                "address" => $email,
                "name" => $firstName
            ]
        ]],
        "subject" => "Thank you for contacting us!",
        "htmlbody" => "
            <h2>Hello $firstName,</h2>
            <p>Thank you for contacting <strong>TruElixir Life Science Pvt. Ltd.</strong>.</p>
            <p>We have received your message:</p>
            <blockquote>$message</blockquote>
            <p>Our team will get back to you shortly.</p>
            <br>
            <p>Warm regards,<br><strong>TruElixir Life Science Team</strong></p>
        "
    ];

    // SEND EMAIL TO USER
    $ch2 = curl_init($url);
    curl_setopt($ch2, CURLOPT_POST, true);
    curl_setopt($ch2, CURLOPT_HTTPHEADER, [
        "accept: application/json",
        "authorization: Zoho-enczapikey $apiToken",
        "content-type: application/json"
    ]);
    curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode($payloadUser));
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);

    $responseUser  = curl_exec($ch2);
    $errorUser     = curl_error($ch2);
    $httpCodeUser  = curl_getinfo($ch2, CURLINFO_HTTP_CODE);

    curl_close($ch2);

    // Log user email status
    file_put_contents(__DIR__ . "/zepto_api_error_log.txt",
        "USER MAIL: HTTP: $httpCodeUser\nResponse: $responseUser\nError: $errorUser\n\n",
        FILE_APPEND
    );

    // Even if user email fails → still return success  
    // (because admin email is most important)
    echo "success";
    exit;

} else {
    echo "Mail Error: Invalid Request";
    exit;
}

?>
