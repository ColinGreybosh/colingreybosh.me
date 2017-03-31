<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=yes, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta name="description" content="Welcome to my website! My name is Colin Greybosh, and I am an aspiring programmer and hobbyist web designer from Pennsylvania." />
    <title>Contact</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/contact.css">
    <link rel="icon" href="CTGicon.png">
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

                <form method="post" action="index.php">

                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name">

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email">

                    <label for="message">Message:</label>
                    <textarea name="message" id="message" name="message"></textarea>

                    <div class="doubleColumn">
                        
                        <div class="g-recaptcha" data-sitekey="6LfvBBsUAAAAAKeIEmOKMPEGyRg--uClpXwYZx24"></div>

                        <input type="submit" id="send" id="send" value="Send Message">

                    </div>

                </form>

            </div>
        </div>
    </div>

    <?php
        foreach ($_POST as $key => $value) {
            echo '<p><strong>' . $key.':</strong> '.$value.'</p>';
        }
    ?>
</body>
</html>
