<?php

include("functions.php");
include("config.php");
session_start();

if(isset($_SESSION["login_user"]))
{
    header("location: index.php");
}

// for logging user in
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["signin"])) 
{   
    $username = filter_input(INPUT_POST, "lemail", FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, "lpassword", FILTER_SANITIZE_STRING);
    $username = mysqli_real_escape_string($connection, $username);
    $password = mysqli_real_escape_string($connection, $password);
    $password = md5($password);

    $query = "SELECT id FROM admin WHERE email = '$username' and password = '$password'";
    $result = mysqli_query($connection, $query);
    $row = mysqli_fetch_row($result);
    if(mysqli_num_rows($result) != 0) 
    {
        $query = "SELECT id FROM admin WHERE email = '$username' and active='1'";
        $result = mysqli_query($connection, $query);
        if(mysqli_num_rows($result) == 0)
        {
            $success = 0;
            $alert = "It seems that you have not activated your account. Please activate it, or click on forgot password to again send verification mail";
        }else
        {
            $_SESSION['login_user'] = $row[0];
            if(isset($_POST["remember"]))
                setcookie("login_user", $row[0], time() + (86400 * 30), "/");
            header("location: index.php");
            exit;
        }
        
    }else 
    {   
        $alert = "Your Login Name or Password is invalid";
        $success = 0;
    }
}else if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["signup"])) 
{   
    if(isset($_POST["captcha"]) && $_POST["captcha"] == "robot")
    {
        $alert = "Intruder. Robots cannot sign up";
        $success = 0;
    }else
    {
        $name = strip_tags(trim($_POST['sname']));
        $email = strip_tags(trim($_POST['semail']));
        $password = strip_tags(trim($_POST['spassword']));
        $phone = $_POST["sphone"];
        $gender = $_POST["gender"];
        $college = $_POST["college"];
        
        $name = mysqli_real_escape_string($connection, $name);
        $email = mysqli_real_escape_string($connection, $email);
        $password = mysqli_real_escape_string($connection, $password);
        
        if(!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            $alert = "Email address not valid";
            $success = 0;
        }else
        {
            $query = "SELECT id FROM admin WHERE email='$email'";
            $result = mysqli_query($connection, $query);

            if (mysqli_num_rows($result) == 0) 
            {
                $password = md5($password);
                $hash = md5(rand(0, 1000));

                //////////////////////////////
                //send verification mail
                /////////////////////////////

                $to      = $email; // Send email to our user
                $subject = 'Signup | Verification'; // Give the email a subject 
                $message = "<h2>Thanks for signing up!</h2>
                <p>Your account has been created. You can login with the following credentials after you have activated your account by pressing the url below.</p>
                ------------------------<br>
                Username: $email<br>
                Name: $name<br>
                ------------------------<br>
                <p>Please click this link to activate your account:<a href='http://collegestuff.000webhostapp.com/verify.php?email=$email&hash=$hash'>Click Me to verify</a> </p>";
                sendMail($to, $message, $subject);

                $sql = "INSERT INTO admin (email, password, phone, name, college_id, hash) VALUES('$email', '$password', $phone, '$name', $college, '$hash')";
                $result = mysqli_query($connection, $sql);
                $alert = "Successfully registered, please login to continue. If you did not get email verification, click forgot password.";
                $success = 1;
            }
            else{
                $alert = "User already exists";
                $success = 0;
            }
        }
        
        
    }
}else if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["fsubmit"]))
{
    $email = filter_input(INPUT_POST, "femail", FILTER_SANITIZE_EMAIL);
    $query = "SELECT * FROM admin WHERE email='$email'";
    $result = mysqli_query($connection, $query);
    if(mysqli_num_rows($result) == 0)
    {
        $success = 0;
        $alert = "No such account exists. Please sign up";
    }else
    {
        $row = $result->fetch_assoc();
        if($row["active"] == 0)
        {
            //////////////////////////////
            //send verification mail
            /////////////////////////////
            $to      = $row["email"]; // Send email to our user
            $subject = 'Signup | Verification'; // Give the email a subject 
            $message = "<h2>Thanks for signing up!</h2>
            <p>Your account has been created. You can login with the following credentials after you have activated your account by pressing the url below.</p>
            ------------------------<br>
            Username:". $row['email']."<br>
            ------------------------<br>";
            
            $message .= "<p>Please click this link to activate your account:<a href='http://collegestuff.000webhostapp.com/verify.php?email=".$row['email']."&hash=".$row['hash']."'>Click Me to verify</a> </p>";
            
            sendMail($to, $message, $subject);
            
            $success = 0;
            $alert = "It seems your account has not been verified yet. We have again sent the verification mail to you. Please verify it.";
        }else
        {
            $password = rand(10000, 50000);
            $email = $row["email"];
            $query = "UPDATE admin SET password='$password'  WHERE email='$email'";
            $result = mysqli_query($connection, $query);
            
            //////////////////////////////
            //Send forgot password
            /////////////////////////////
            $to      = $email; // Send email to our user
            $subject = 'Password in Mail'; // Give the email a subject 
            $message = "<h2>Thanks for reaching us! Your password is below. Please login using this password and RESET this password as soon as possible.</h2>
            ------------------------<br>
            Username: $password<br>
            ------------------------<br>
            <p>Thank You! </p>";
            sendMail($to, $message, $subject);
            
            $success = 1;
            $alert = "Your password has been sent to your registered mail.";
        }
    }
}

?>

<!doctype html>
<html>
    <head>
        <title><?php echo $pageTitle; ?></title>
        
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/css/bootstrap.min.css" integrity="sha384-AysaV+vQoT3kOAXZkl02PThvDr8HYKPZhNT5h/CXfBThSRXQ6jW5DO2ekP5ViFdi" crossorigin="anonymous">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>

        <link href='http://fonts.googleapis.com/css?family=Titillium+Web:400,300,600' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">

        <style type="text/css">
            .panel-login {
                border-color: #ccc;
                -webkit-box-shadow: 0px 2px 3px 0px rgba(0,0,0,0.2);
                -moz-box-shadow: 0px 2px 3px 0px rgba(0,0,0,0.2);
                box-shadow: 0px 2px 3px 0px rgba(0,0,0,0.2);
            }
            .panel-login>.panel-heading {
                color: #00415d;
                background-color: #fff;
                border-color: #fff;
                text-align:center;
            }
            .panel-login>.panel-heading a{
                text-decoration: none;
                color: #666;
                font-weight: bold;
                font-size: 15px;
                -webkit-transition: all 0.1s linear;
                -moz-transition: all 0.1s linear;
                transition: all 0.1s linear;
            }
            .panel-login>.panel-heading a.active{
                color: #029f5b;
                font-size: 18px;
            }
            .panel-login>.panel-heading hr{
                margin-top: 10px;
                margin-bottom: 0px;
                clear: both;
                border: 0;
                height: 1px;
                background-image: -webkit-linear-gradient(left,rgba(0, 0, 0, 0),rgba(0, 0, 0, 0.15),rgba(0, 0, 0, 0));
                background-image: -moz-linear-gradient(left,rgba(0,0,0,0),rgba(0,0,0,0.15),rgba(0,0,0,0));
                background-image: -ms-linear-gradient(left,rgba(0,0,0,0),rgba(0,0,0,0.15),rgba(0,0,0,0));
                background-image: -o-linear-gradient(left,rgba(0,0,0,0),rgba(0,0,0,0.15),rgba(0,0,0,0));
            }
            .panel-login input[type="text"],.panel-login input[type="email"],.panel-login input[type="password"] {
                height: 45px;
                border: 1px solid #ddd;
                font-size: 16px;
                -webkit-transition: all 0.1s linear;
                -moz-transition: all 0.1s linear;
                transition: all 0.1s linear;
            }
            .panel-login input:hover,
            .panel-login input:focus {
                outline:none;
                -webkit-box-shadow: none;
                -moz-box-shadow: none;
                box-shadow: none;
                border-color: #ccc;
            }
            .btn-login {
                background-color: #59B2E0;
                outline: none;
                color: #fff;
                font-size: 14px;
                height: auto;
                font-weight: normal;
                padding: 14px 0;
                text-transform: uppercase;
                border-color: #59B2E6;
            }
            .btn-login:hover,
            .btn-login:focus {
                color: #fff;
                background-color: #53A3CD;
                border-color: #53A3CD;
            }
            .forgot-password {
                text-decoration: underline;
                color: #888;
            }
            .forgot-password:hover,
            .forgot-password:focus {
                text-decoration: underline;
                color: #666;
            }

            .btn-register {
                background-color: #1CB94E;
                outline: none;
                color: #fff;
                font-size: 14px;
                height: auto;
                font-weight: normal;
                padding: 14px 0;
                text-transform: uppercase;
                border-color: #1CB94A;
            }
            .btn-register:hover,
            .btn-register:focus {
                color: #fff;
                background-color: #1CA347;
                border-color: #1CA347;
            }
        </style>
        <script type="text/javascript">
            $(function() {
                $('#login-form-link').click(function(e) {
                    $("#login-form").delay(100).fadeIn(100);
                    $("#register-form").fadeOut(100);
                    $('#register-form-link').removeClass('active');
                    $("#fpassword-form").fadeOut(100);
                    $('#fpassword-form').removeClass('active');
                    $(this).addClass('active');
                    e.preventDefault();
                });
                $('#register-form-link').click(function(e) {
                    $("#register-form").delay(100).fadeIn(100);
                    $("#login-form").fadeOut(100);
                    $('#login-form-link').removeClass('active');
                    $("#fpassword-form").fadeOut(100);
                    $('#fpassword-form').removeClass('active');
                    $(this).addClass('active');
                    e.preventDefault();
                });
                $('#fpassword').click(function(e) {
                    $("#fpassword-form").delay(100).fadeIn(100);
                    $("#login-form").fadeOut(100);
                    $("#register-form").fadeOut(100);
                    $('#register-form-link').removeClass('active');
                    $('#login-form-link').removeClass('active');
                    $(this).addClass('active');
                    e.preventDefault();
                });
            });
            
            function checkPhone()
            {
                var phoneNo = document.getElementById("sphone").value;
                var phoneMatch = /^\d{10}$/;  
                if(!phoneNo.match(phoneMatch))
                {
                    document.getElementById("signup").disabled = true;
                    $('#sphone').popover('show');
                    setTimeout(function(){$('#sphone').popover('hide');}, 2000)  
                }else
                {
                    document.getElementById("signup").disabled = false;
                    $('#sphone').popover('hide');
                }
            } 
            function checkEmail()
            {
                var email = document.getElementById("semail").value;
                var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
                if(!email.match(mailformat))
                {
                    document.getElementById("signup").disabled = true;
                    $('#semail').popover('show');
                    setTimeout(function(){$('#semail').popover('hide');}, 2000)
                }else
                {
                    document.getElementById("signup").disabled = false;
                    $('#semail').popover('hide');
                }
            }
            
        </script>
    </head>
    <body>

        <!--        header for page -->
        <?php require 'header.php'; ?>
        <!--    end of header -->
        
        <div class="container mt-3">
            <div class="row">
                <div class="col-xs-12 col-md-6 offset-md-3">
                    <?php if(isset($success)){?>
                    <div class="alert <?php if(isset($success) && $success == 1){ echo 'alert-success';}else {echo 'alert-danger';}?> alert-dismissible fade in" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                        <span><?php echo $alert; ?></span>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 offset-md-3 col-xs-12">
                    <div class="card panel-login">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-6">
                                    <a href="#" class="active" id="login-form-link">Login</a>
                                </div>
                                <div class="col-xs-6">
                                    <a href="#" id="register-form-link">Register</a>
                                </div>
                            </div>
                            <hr>
                        </div>
                        <div class="card-block mt-2">
                            <div class="row">
                                <div class="col-lg-12">
                                    <form class="form-horizontal" id="login-form" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" role="form" style="display: block;">
                                        <!-- Sign In Form -->
                                        <!-- Text input-->
                                        <div class="control-group">
                                            <label class="control-label sr-only" for="lemail">Email:</label>
                                            <div class="controls">
                                            <input required="" id="lemail" name="lemail" type="text" class="form-control" class="input-medium" required placeholder="Email">
                                            </div>
                                        </div>
                                        <br>
                                        <!-- Password input-->
                                        <div class="control-group">
                                            <label class="control-label sr-only" for="lpassword">Password:</label>
                                            <div class="controls">
                                            <input required id="lpassword" name="lpassword" class="form-control" type="password" required placeholder="Password" class="input-medium">
                                            <p><a href="#" class="btn" id="fpassword">Forgot Password?</a></p>
                                            </div>
                                        </div>

                                        <!-- Multiple Checkboxes (inline) -->
                                        <div class="control-group">
                                            <label class="control-label" for="remember"></label>
                                            <div class="controls">
                                                <label class="checkbox inline" for="remember">
                                                <input type="checkbox" name="remember" id="remember" value="remember">
                                                Remember me
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Button -->
                                        <div class="control-group float-xs-right">
                                            <label class="control-label" for="signin"></label>
                                            <div class="controls">
                                            <input type="submit" id="signin" name="signin" class="btn btn-success" value="Sign In">
                                            </div>
                                        </div>
                                    </form>

                                    <form class="form-horizontal" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" style="display:none;" role="form" id="register-form">
                                        <!-- Sign Up Form -->
                                        <!-- Text input-->
                                        <div class="control-group">
                                            <label class="control-label sr-only" for="sname">Name:</label>
                                            <div class="controls">
                                            <input id="sname" class="form-control" name="sname" type="text" placeholder="Name" class="input-large" required>
                                            </div>
                                        </div>
                                        <br>
                                        <!-- Text input-->
                                        <div class="control-group">
                                            <label class="control-label sr-only" for="semail">Email:</label>
                                            <div class="controls">
                                            <input type="email" name="semail" id="semail" class="form-control input-large" placeholder="Email" required onblur="checkEmail();" data-toggle="popover" data-trigger="manual" title="Error" data-content="Please enter a valid email id." data-placement="top" tabindex="0">
                                            </div>
                                        </div>
                                        <br>
                                        <!-- Password input-->
                                        <div class="control-group">
                                            <label class="control-label sr-only" for="spassword">Password:</label>
                                            <div class="controls">
                                            <input id="spassword" name="spassword" class="form-control" type="password" placeholder="Password" class="input-large" required>
                                            </div>
                                        </div>
                                        <br>
                                        <!-- Text input-->
                                        <div class="control-group"> 
                                            <label class="control-label sr-only" for="sphone">Phone Number:</label>
                                            <div class="controls">
                                            <input id="sphone" class="form-control input-large" name="sphone" type="number" placeholder="Phone No" required onblur="checkPhone();" data-toggle="popover" data-trigger="manual" title="Error" data-content="Please enter a valid phone number, without any prefixes or service codes." data-placement="top" tabindex="0">
                                            <small>Don't worry, we won't share it with anyone</small>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="control-group">
                                            <label class="control-label sr-only" for="college">Select College:</label>
                                            <select class="form-control" id="college" name="college" required>
                                            <?php
                                            printCollegeList();
                                            ?>
                                            </select>
                                        </div>
                                        <br/>
                                        <div class="row">
                                            <div class="col-xs-6">
                                                <div class="control-group">
                                                    <label class="control-label" for="gender">Gender:</label>
                                                    <div class="controls">
                                                    <label class="radio inline" for="gender">
                                                    <input type="radio" name="gender" id="gender" value="m" checked="checked">Male</label>
                                                    <label class="radio inline" for="humancheck-1">
                                                    <input type="radio" name="gender" id="gender" value="f">Female</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xs-6">
                                                <div class="control-group">
                                                    <label class="control-label" for="captcha">Humanity Check:</label>
                                                    <div class="controls" id="captcha">
                                                        <label class="radio inline" for="captcha-r">
                                                        <input type="radio" name="captcha" id="captcha-r" value="robot" checked>I'm a Robot</label>
                                                        <label class="radio inline" for="captcha-r">
                                                        <input type="radio" name="captcha" id="captcha-r" value="human">I'm Human</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>        
                                        <!-- Button -->
                                        <div class="control-group float-xs-right">
                                            <label class="control-label" for="signup"></label>
                                            <div class="controls">
                                            <button type="submit" id="signup" name="signup" class="btn btn-success" id="signup">Sign Up</button>
                                            </div>
                                        </div>
                                    </form>
                                    <form class="form-horizontal" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" style="display:none;" role="form" id="fpassword-form">
                                        <div class="control-group">
                                            <label class="control-label sr-only" for="femail">Email:</label>
                                            <div class="controls">
                                            <input id="femail" name="femail" class="form-control" type="email" placeholder="Email" class="input-large" required>
                                            </div>
                                        </div>
                                        <div class="control-group float-xs-right">
                                            <label class="control-label" for="fsubmit"></label>
                                            <div class="controls">
                                            <button type="submit" id="fsubmit" name="fsubmit" class="btn btn-success">Send Password</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!--page footer-->
        <?php require 'footer.php'; ?>
        <!--/ end of page footer-->
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js" integrity="sha384-3ceskX3iaEnIogmQchP8opvBy3Mi7Ce34nWjpBIwVTHfGYWQS9jwHDVRnpKKHJg7" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.3.7/js/tether.min.js" integrity="sha384-XTs3FgkjiBgo8qjEjBk0tGmf3wPrWtA6coPfQDfFEY8AnYJwjalXCiosYRBIBZX8" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/js/bootstrap.min.js" integrity="sha384-BLiI7JTZm+JWlgKa0M0kGRpJbF2J8q+qreVrKBC47e3K6BW78kGLrCkeRX6I9RoK" crossorigin="anonymous"></script>
        
    </body>
</html>