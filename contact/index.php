<?php
    // Display errors on web page
    ini_set('display_errors', 1);

    // Initialize the variable $ini with the array 
    // returned from parsing the config.ini file 
    $ini = parse_ini_file('includes/config.ini');

    // $ini is not null, initialize these variables 
    // with the values contained within config.ini
    if (isset($ini))
    {
        $rcSecret = $ini['recaptcha'];
        $mgSecret = $ini['mailgun'];
        $recipient = $ini['recipient'];
    }

    // Use the composer loader
    require 'vendor/autoload.php';
    // Use the recaptcha library
    require_once 'includes/recaptchalib.php';
    // Use guzzle
    use GuzzleHttp\Client as GuzzleClient;
    use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
    // Use the Mailgun PHP library
    use Mailgun\Mailgun;

    // Create a Mailgun method with the secret key and Guzzle
    $client = new \Http\Adapter\Guzzle6\Client(); 
    $mg = new \Mailgun\Mailgun($mgSecret, $client);
    unset($mgSecret);


    // Initialize variables for reCAPTCHA
    $response = null;
    $reCaptcha = new ReCaptcha($rcSecret);
    unset($rcSecret);

    
    // If the captcha response is a success 
    // and the user clicked the send button
    if (isset($_POST['send'])) 
    {
        $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
        echo '<p><strong>Name: </strong>'.$name.'</p>';

        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        echo '<p><strong>Email: </strong>'.$email.'</p>';

        $message = filter_var($_POST['message'], FILTER_SANITIZE_STRING);
        echo '<p><strong>Message: </strong>'.$message.'</p>';

        $captcha = $_POST['g-recaptcha-response'];
        echo '<p><strong>Captcha: </strong>'.$captcha.'</p>';

        $htmlBody = 
        '
            <p><b>From: </b>'.$name.' <'.$email.'></p></br>

            <p><b>Message:</b></p>
            <p>'.$message.'</p>
        ';

        if ($_POST['g-recaptcha-response']) 
        {
            $response = $reCaptcha->verifyResponse(
              $_SERVER['REMOTE_ADDR'],
              $_POST['g-recaptcha-response']
            );
        }

        if ($response != null && $response->success)
        {
            $messageBuilder = $mg->MessageBuilder();

            $messageBuilder->setFromAddress('contact@colingreybosh.me');
            $messageBuilder->addToRecipient($recipient);
            $messageBuilder->setSubject('Message from website contact form.');
            $messageBuilder->setHtmlBody($htmlBody);

            $mg->post('mg.colingreybosh.me', $messageBuilder->getMessage());        
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=yes, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta name="description" content="Welcome to my website! My name is Colin Greybosh, and I am an aspiring programmer and hobbyist web designer from Pennsylvania." />
    <title>Contact</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/contact.css">
    <link rel="icon" href="../CTGicon.png">
    <link href="https://fonts.googleapis.com/css?family=Ubuntu:300,400" rel="stylesheet">
    <script src='https://www.google.com/recaptcha/api.js'></script>
</head>

<body>

    <div class="content">

        <div class="name">
            <p>Colin Greybosh</p>
        </div>

        <nav class="nav">

            <a href="..">
                <p>Home</p>
            </a>

            <a href="" id="navCenter">
                <p>Contact</p>
            </a>

            <a href="../resume">
                <p>Résumé</p>
            </a>

        </nav>

        <div class="body">

            <div class="main">
            
                <p>Have any questions? Feel free to send me an email using this form I provided below.</p>

                <form method="post">

                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" placeholder="John Doe" required>

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" placeholder="johndoe@gmail.com" required>

                    <label for="message">Message:</label>
                    <textarea name="message" id="message" name="message" required></textarea>

                    <div class="doubleColumn">
                        
                        <div class="g-recaptcha" data-sitekey="6LfvBBsUAAAAAKeIEmOKMPEGyRg--uClpXwYZx24"></div>

                        <input type="submit" id="send" name="send" value="Send Message">

                    </div>

                </form>

            </div>

        </div>

    </div>

</body>
</html>
