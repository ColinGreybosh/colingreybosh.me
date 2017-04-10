<?php
    // Display errors on web page
    ini_set('display_errors', 0);

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

    // Use the composer autoloader
    require 'vendor/autoload.php';
    // Use the recaptcha library
    require_once 'includes/recaptchalib.php';
    // Use the Mailgun PHP library
    use Mailgun\Mailgun;

    // Instantiate a new Mailgun client using the
    // secret API key contained in an .ini file
    $mgClient = new Mailgun($mgSecret);
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

        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

        $message = filter_var($_POST['message'], FILTER_SANITIZE_STRING);

        $captcha = $_POST['g-recaptcha-response'];

        $htmlBody = 
            '<p><b>From: </b>'.$name.' <i>&lt;<a href="mailto:"'.$email.' target="_top">'.$email.'</a>&gt;</i></p>
            <p><b>Message:</b></p>
            <p>'.$message.'</p>';

        if ($_POST['g-recaptcha-response']) 
        {
            $response = $reCaptcha->verifyResponse(
              $_SERVER['REMOTE_ADDR'],
              $_POST['g-recaptcha-response']
            );
        }

        // Once the captcha response is confirmed
        // this code will execute
        if ($response != null && $response->success)
        {
            $response = $mgClient->sendMessage('mg.colingreybosh.me', array(
                'from'    => 'contact@colingreybosh.me',
                'to'      => $recipient,
                'subject' => 'Message from contact form.',
                'html'    => $htmlBody
            ));  

            if ($response->http_response_body->message == "Queued. Thank you.") {
                $popup = '<p id="was-sent">Your message has been sent!</p>';
            }

            if ($response->http_response_body->message != "Queued. Thank you.") {
                $popup = '<p id="has-error">Something went wrong! Your message was not sent.</p>';
            }
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

                    <div class="response">
                        <?php 
                            echo $popup;

                            $variables = array_keys(get_defined_vars());

                            for ($i = 0; $i < sizeof($variables); $i++) {
                                unset($variables[$i]);
                            }
                            unset($variables, $i);
                        ?>
                    </div>

                </form>

            </div>

        </div>

    </div>

</body>
</html>
