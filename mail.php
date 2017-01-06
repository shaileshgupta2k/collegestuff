<?php
$message = "<h2>Thanks for signing up!</h2>
                <p>Your account has been created. You can login with the following credentials after you have activated your account by pressing the url below.</p>
                ------------------------<br>
                Username: '.$name.'<br>
                Password: '.$password.'<br>
                ------------------------<br>
                <br>
                "."
                <p>Please click this link to activate your account:<a href='verify.php?email=$email&hash=$hash'>Click Me to verify</a> </p>";
           
include("functions.php");
 sendMail("shaileshgupta94@gmail.com", $message, "Welcome");

?>