<?php
    include "./init.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>

<div class="logout-container">

    <?php
        session_unset();
        session_destroy();
    ?>

    <p class="logout-text">You have successfully been logged out.</p>
    <p class="logout-text">Have a great day!</p>
    <a href="/login.html">Log back in</a>

</div>
    
</body>
</html>
