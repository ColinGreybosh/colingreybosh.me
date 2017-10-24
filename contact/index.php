<!DOCTYPE html>
<html lang="en">

<?php
    // Use the composer autoloader
    require 'vendor/autoload.php';
    // Use the recaptcha library
    require_once 'includes/recaptchalib.php';
    // Use the Mailgun PHP library
    use Mailgun\Mailgun;

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

    // Instantiate a new Mailgun client using the
    // secret API key contained in an .ini file
    //$mgClient = new Mailgun($mgSecret);
    $mgClient = Mailgun::create($mgSecret);
    unset($mgSecret);
    $mgDomain = 'mg.colingreybosh.me';

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

        $textBody =
            'From: '. $name .' <'. $email .'>\nMessage:\n'. $message; 

        $htmlBody =
            '<html><p><b>From: </b>'. $name .' <i>&lt;<a href="mailto: '. $email .'" target="_top">'. $email .'</a>&gt;</i></p>
            <p><b>Message:</b></p>
            <p>'. $message .'</p></html>';

        if ($captcha)
        {
            $response = $reCaptcha->verifyResponse(
              $_SERVER['REMOTE_ADDR'],
              $_POST['g-recaptcha-response']
            );
        }

        // Once the captcha response is confirmed
        // this code will execute
        if ($response->success)
        {
            $formSubmitted = false;
            $success = true;

            $messageParams = array(
                'from'    => 'contact@colingreybosh.com',
                'to'      => 'colingreybosh@gmail.com',
                'subject' => 'Message From Contact Form',
                'text'    => $textBody,
                'html'    => $htmlBody);

            try {
                $mgClient->messages()->send('mg.colingreybosh.me', $messageParams);
            } catch (Exception $e) {
                 $success = false;
            }

            $formSubmitted = true;
        }
    }
?>

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=yes, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta name="description" content="Want to contact me? Send me an email using my contact form!"/>
    <meta name="author" content="Colin Greybosh">
    <meta name="robots" content="index, follow">
    <meta name="revisit-after" content="3 days">

    <title>Contact | Colin Greybosh</title>
    <link rel="stylesheet" href="/css/main.min.css"  media="screen">
    <link rel="stylesheet" href="/css/contact.min.css"  media="screen">
    <link rel="icon" href="/CTGicon.png">
    <link href="/css/ubuntu.css" rel="stylesheet">
    <script src='https://www.google.com/recaptcha/api.js' defer></script>

</head>

<body>

    <div class="content">

        <header class="name">
            <h1>Colin Greybosh</h1>
        </header>

        <nav>

            <a href="/"       id="navLeft"  >Home</a>
            <a href="."       id="navCenter">Contact</a>
            <a href="/resume" id="navRight" >Résumé</a>

        </nav>

        <div class="container">

            <h2>Contact Me!</h2>

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
                        if ($formSubmitted)
                        {
                            $popupText = ($success) ? '<p id="was-sent">Your message has been sent!</p>' : 
                                                      '<p id="has-error">Something went wrong! Your message was not sent.</p>';
                            echo $popupText;
                        } 

                        $variables = array_keys(get_defined_vars());

                        for ($i = 0; $i < sizeof($variables); $i++) 
                        {
                            unset($variables[$i]);
                        }
                        unset($variables, $i);
                    ?>
                </div>

            </form>

        </div>

    </div>

</body>
</html>
