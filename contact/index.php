<!DOCTYPE html>
<html lang="en">

<?php
    // Display errors on web page
    ini_set('display_errors', 0);
    
    // Use the composer autoloader
    require 'vendor/autoload.php';

    // Use the recaptcha library
    require_once 'includes/recaptchalib.php';

    // Use the Mailgun PHP library
    use Mailgun\Mailgun;

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
    <meta name="theme-color" content="#C72400">


    <title>Contact | Colin Greybosh</title>
    <link rel="stylesheet" href="/css/normalize.min.css">
    <link rel="stylesheet" href="/css/main.min.css"  media="screen">
    <link rel="stylesheet" href="/css/contact.min.css"  media="screen">
    <link rel="stylesheet" href="/css/ubuntu.css">
    <link rel="icon" href="/favicon.png">
    <script src='https://www.google.com/recaptcha/api.js' defer></script>

</head>

<body>
    <header>
        <nav>
            <h1>Colin Greybosh</h1>
            <div class="nav-item"><a href="/">Home</a></div>
            <div class="nav-item"><a href="/resume">Résumé</a></div>
            <div class="nav-item"><a href=".">Contact</a></div>
        </nav>
    </header>

    <div class="container text">
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

    <footer>
        <div>
            <div class="social">
                <a href="https://www.facebook.com/ColinGreybosh">
                    <svg version="1.1" id="Facebook" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 128 128" enable-background="new 0 0 128 128" xml:space="preserve">
                        <title>Facebook</title>
                        <desc>Click to be taken to my Facebook page.</desc>
                        <g>
                            <circle id="facebook-back" fill="#262626" cx="64" cy="64" r="64"/>
                            <path id="facebook-facebook" fill="#FFFFFF" d="M95.1367,29H32.8638C30.7295,29,29,30.729,29,32.8638v62.2729
                            C29,97.2705,30.7295,99,32.8638,99h33.5249V71.8926h-9.1221v-10.565h9.1221v-7.7905c0-9.0415,5.5224-13.9648,13.5888-13.9648
                            c3.8623,0,7.1827,0.2876,8.1504,0.4165v9.4487l-5.5927,0.0024c-4.3877,0-5.2364,2.0845-5.2364,5.1431v6.7446h10.461l-1.3623,10.565
                            h-9.0987V99h17.8379C97.2705,99,99,97.2705,99,95.1367V32.8638C99,30.729,97.2705,29,95.1367,29z"/>
                        </g>
                    </svg>
                </a>
                <a href="https://twitter.com/ColinGreybosh">
                    <svg version="1.1" id="Twitter" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 128 128" enable-background="new 0 0 128 128" xml:space="preserve">
                        <title>Twitter</title>
                        <desc>Click to be taken to my Twitter profile.</desc>
                        <g>
                            <circle id="twitter-back" fill="#262626" cx="64" cy="64" r="64"/>
                            <path id="twitter-twitter" fill="#FFFFFF" d="M99.8398,41.7695c-2.6367,1.17-5.4707,1.96-8.4462,2.3155
                                c3.0351-1.8204,5.3681-4.7022,6.4658-8.1363c-2.8408,1.6851-5.9883,2.9097-9.3379,3.5689
                                c-2.6826-2.8584-6.5049-4.6436-10.7344-4.6436c-8.123,0-14.7065,6.584-14.7065,14.7051c0,1.1533,0.1303,2.2754,0.3808,3.3516
                                c-12.2221-0.6133-23.0581-6.4678-30.311-15.3648c-1.2661,2.1714-1.9912,4.6978-1.9912,7.3931c0,5.1015,2.5962,9.6025,6.542,12.2397
                                c-2.4107-0.0757-4.6783-0.7378-6.6607-1.8393c-0.0014,0.0615-0.0014,0.123-0.0014,0.1855c0,7.125,5.0693,13.0684,11.7968,14.4199
                                c-1.2343,0.336-2.5337,0.5157-3.8745,0.5157c-0.9477,0-1.8691-0.0928-2.7671-0.2637c1.8716,5.8418,7.3023,10.0937,13.7378,10.2119
                                c-5.0332,3.9453-11.374,6.2959-18.2646,6.2959c-1.187,0-2.357-0.0693-3.5073-0.2051c6.5083,4.1729,14.2377,6.6065,22.5429,6.6065
                                c27.0498,0,41.8418-22.4082,41.8418-41.8418c0-0.6377-0.0146-1.2715-0.0429-1.9024C95.375,47.3086,97.8691,44.7188,99.8398,41.7695
                                z"/>
                        </g>
                    </svg>
                </a> 
                <a href="https://github.com/ColinGreybosh">
                    <svg version="1.1" id="Github" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 128 128" enable-background="new 0 0 128 128" xml:space="preserve">
                    <title>Github</title>
                    <desc>Click to be taken to my Github profile.</desc>
                    <g>
                        <circle id="github-back" fill="#262626" cx="64" cy="64" r="64"/>
                        <g id="github-github">
                            <path fill-rule="evenodd" clip-rule="evenodd" fill="#FFFFFF" d="M64,29.0449c-19.791,0-35.8398,16.0449-35.8398,35.8399
                                c0,15.8349,10.2695,29.2695,24.5097,34.0088c1.791,0.331,2.4492-0.7784,2.4492-1.7247c0-0.8544-0.0332-3.6777-0.0488-6.6728
                                c-9.9707,2.168-12.0752-4.2285-12.0752-4.2285c-1.6299-4.1426-3.9795-5.2451-3.9795-5.2451
                                c-3.2519-2.2237,0.2456-2.1778,0.2456-2.1778c3.5982,0.253,5.4937,3.6953,5.4937,3.6953
                                c3.1963,5.4776,8.3847,3.8936,10.4297,2.9776c0.3222-2.3164,1.2509-3.8975,2.2754-4.793
                                c-7.96-0.9053-16.3291-3.9785-16.3291-17.7119c0-3.9126,1.4004-7.1104,3.6933-9.6196c-0.3721-0.9034-1.5996-4.5484,0.3477-9.4854
                                c0,0,3.0088-0.9624,9.8574,3.6743c2.8594-0.7939,5.9258-1.1924,8.9707-1.206c3.0459,0.0136,6.1143,0.4121,8.9785,1.206
                                c6.8408-4.6367,9.8467-3.6743,9.8467-3.6743c1.9502,4.937,0.7236,8.582,0.3516,9.4854c2.2978,2.5092,3.6884,5.707,3.6884,9.6196
                                c0,13.7666-8.3847,16.7959-16.3652,17.6846c1.2852,1.1113,2.4297,3.2929,2.4297,6.6367c0,4.7949-0.0401,8.6543-0.0401,9.8349
                                c0,0.9541,0.6456,2.0713,2.4629,1.7198c14.2315-4.7442,24.4883-18.1739,24.4883-34.0039
                                C99.8408,45.0898,83.7939,29.0449,64,29.0449z"/>
                            <path fill="#FFFFFF" d="M41.7354,80.5029c-0.0791,0.1787-0.3594,0.2315-0.6153,0.1104c-0.2597-0.1172-0.4057-0.3604-0.3213-0.5391
                                c0.0772-0.1836,0.3575-0.2344,0.6172-0.1113C41.6768,80.0801,41.8247,80.3242,41.7354,80.5029L41.7354,80.5029z M41.2939,80.1758"
                                />
                            <path fill="#FFFFFF" d="M43.1865,82.123c-0.1709,0.1583-0.5049,0.084-0.7314-0.166c-0.2354-0.25-0.2788-0.584-0.1055-0.7461
                                c0.1768-0.1562,0.5-0.083,0.7354,0.167C43.3193,81.6309,43.3652,81.9619,43.1865,82.123L43.1865,82.123z M42.8447,81.7559"/>
                            <path fill="#FFFFFF" d="M44.5996,84.1865c-0.2197,0.1533-0.5791,0.0088-0.8008-0.3095c-0.2197-0.3184-0.2197-0.7012,0.0049-0.8545
                                c0.2227-0.1524,0.5762-0.0147,0.8013,0.3017C44.8242,83.6475,44.8242,84.0303,44.5996,84.1865L44.5996,84.1865z M44.5996,84.1865"
                                />
                            <path fill="#FFFFFF" d="M46.5356,86.1797c-0.1967,0.2178-0.6147,0.1592-0.9213-0.1367c-0.3135-0.2881-0.4004-0.6992-0.2041-0.916
                                c0.2002-0.2168,0.6211-0.1553,0.9287,0.1386C46.6504,85.5537,46.7451,85.9668,46.5356,86.1797L46.5356,86.1797z M46.5356,86.1797"
                                />
                            <path fill="#FFFFFF" d="M49.2061,87.3389c-0.087,0.2802-0.4893,0.4072-0.8956,0.2881c-0.4052-0.1231-0.6699-0.4512-0.5888-0.7344
                                c0.084-0.2832,0.4892-0.4151,0.8984-0.2881C49.0254,86.7266,49.29,87.0527,49.2061,87.3389L49.2061,87.3389z M49.2061,87.3389"/>
                            <path fill="#FFFFFF" d="M52.1396,87.5527c0.0098,0.2959-0.3339,0.5411-0.7602,0.5469c-0.4282,0.0098-0.7759-0.2305-0.7798-0.5215
                                c0-0.2978,0.3359-0.54,0.7647-0.5478C51.79,87.0225,52.1396,87.2598,52.1396,87.5527L52.1396,87.5527z M52.1396,87.5527"/>
                            <path fill="#FFFFFF" d="M54.8691,87.0889c0.0508,0.2881-0.2451,0.5849-0.6679,0.6631c-0.417,0.0761-0.8018-0.1016-0.8545-0.3877
                                c-0.0518-0.2959,0.25-0.5918,0.6645-0.669C54.4355,86.623,54.8154,86.7959,54.8691,87.0889L54.8691,87.0889z M54.8691,87.0889"/>
                        </g>
                    </g>
                    </svg>
                </a>
                <a href="https://www.linkedin.com/in/ColinGreybosh">
                    <svg version="1.1" id="Linkedin" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 128 128" enable-background="new 0 0 128 128" xml:space="preserve">
                        <title>LinkedIn</title>
                        <desc>Click to be taken to my LinkedIn page.</desc>
                        <g>
                            <circle id="linkedin-back" fill="#262626" cx="64" cy="64" r="64"/>
                            <g id="linkedin-linkedin">
                                <path fill="#FFFFFF" d="M29.0752,51.7471h14.8686V99.54H29.0752V51.7471z M36.5137,27.9893c4.7514,0,8.6084,3.8593,8.6084,8.6137
                                    c0,4.7554-3.857,8.6143-8.6084,8.6143c-4.7705,0-8.6172-3.8589-8.6172-8.6143C27.8965,31.8486,31.7432,27.9893,36.5137,27.9893"/>
                                <path fill="#FFFFFF" d="M53.2622,51.7471H67.5v6.5337h0.2041c1.9805-3.7574,6.8272-7.7198,14.0537-7.7198
                                    c15.0391,0,17.8184,9.896,17.8184,22.7671V99.54H84.7246V76.2979c0-5.542-0.0957-12.6719-7.7187-12.6719
                                    c-7.7286,0-8.9082,6.04-8.9082,12.2754V99.54H53.2622V51.7471z"/>
                            </g>
                        </g>
                    </svg>
                </a>
            </div>
            <p>Copyright &copy; <?php echo date("Y"); ?> Colin Greybosh</p>
        </div>
    </footer>
</body>
</html>
