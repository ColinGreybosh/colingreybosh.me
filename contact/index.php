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

                <?php
                    require 'vendor/autoload.php';
                    use Mailgun\Mailgun;
                    
                    if ($_POST['send']) {
                        $name = $_POST['name'];
                        echo '<p><strong>Name: </strong>'.name.'</p>';
                        $email = $_POST['email'];
                        echo '<p><strong>Email: </strong>'.email.'</p>';
                        $message = $_POST['message'];
                        echo '<p><strong>Message: </strong>'.message.'</p>';
                        $captcha = $_POST['g-recaptcha'];
                        echo '<p><strong>Captcha: </strong>'.captcha.'</p>';
                    }
                ?>

                <form method="post">

                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" placeholder="John Doe" required>

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" placeholder="johndoe@gmail.com" required>

                    <label for="message">Message:</label>
                    <textarea name="message" id="message" name="message" required></textarea>

                    <div class="doubleColumn">
                        
                        <div class="g-recaptcha" data-sitekey="6LfvBBsUAAAAAKeIEmOKMPEGyRg--uClpXwYZx24"></div>

                        <input type="submit" id="send" id="send" value="Send Message">

                    </div>

                </form>

            </div>
        </div>
    </div>
</body>
</html>