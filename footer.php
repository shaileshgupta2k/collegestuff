<?php

if(isset($_POST["getupdates"]) && $_SERVER["REQUEST_METHOD"] == "POST")
{
    $updatesemail = filter_input(INPUT_POST, "updatesemail", FILTER_SANITIZE_EMAIL);
    $to      = $updatesemail; // Send email to our user
    $subject = 'Signup'; // Give the email a subject 
    $message = "<h2>Thanks for signing up!</h2>
    <p>We will keep you in touch with all the awesome features we shall launch in the feature. In the meantime you can avail the facilities provided by us:</p>
    <br>
    <ul>
        <li>Increase reachability, by posting your ad. You college students see the ad and contact you</li>
        <li>We help you to connect with your college-mates</li>
        <li>You can even buy college material if you like, just go to our buyer section</li>
    </ul><br>
    <p>Thanks,<br>With regards from CollegeStuff </p>";
    sendMail($to, $message, $subject);
}

?>

<div id="footer">
    <hr style="border: 1px solid black">
<div class="container mb-1">
    <div class="row">
    <div class="col-md-6 col-xl-5">
        <div class="row">
            <p><strong>&copy; CollegeStuff 2016</strong></p>
        </div>
        <div class="row">
            <p>Contact us at: <a href="mailto:info.collegestuff@gmail.com" target="_black">info.collegestuff@gmail.com</a></p>
        </div>
    </div>
    <div class="col-md-6 col-xl-5 offset-xl-2">
        <p><strong>Stay up-to-date</strong></p>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <div class="input-group">
                <input type="email" class="form-control" placeholder="Email" required name="updatesemail">
                <span class="input-group-btn">
                    <button class="btn btn-primary" type="submit" name="getupdates">Sign up</button>
                </span>
            </div>
        </form>
    </div>
    </div>
</div></div>