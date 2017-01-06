<?php

session_start();
include("config.php");
include("functions.php");

if(isset($_GET['email']) && !empty($_GET['email']) AND isset($_GET['hash']) && !empty($_GET['hash']))
{
    $email = filter_input(INPUT_GET, "email", FILTER_SANITIZE_EMAIL);
    $hash = filter_input(INPUT_GET, "hash", FILTER_SANITIZE_STRING);
    $search = mysqli_query($connection, "SELECT email, hash, active FROM admin WHERE email='$email' AND hash='$hash' AND active='0'");
    $match  = mysqli_num_rows($search);
    
    if($match > 0)
    {
        mysqli_query($connection, "UPDATE admin SET active='1' WHERE email='$email' && hash='$hash'");
        $alert = "Your account has been activated, you can now login";
    }else
    {
        $alert = "The url is either invalid or you already have activated your account.";
    }
        
}else
{
    $alert = "Invalid approach, please use the link that has been send to your email.";
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>

        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>eCommerce Product Detail</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/css/bootstrap.min.css" integrity="sha384-AysaV+vQoT3kOAXZkl02PThvDr8HYKPZhNT5h/CXfBThSRXQ6jW5DO2ekP5ViFdi" crossorigin="anonymous">
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet">
        <link href="css/footer.css" rel="stylesheet">
        <style>
        </style>
    </head>

    <body>
        <div id="wrapper">
        <!--        page header -->
        <?php require 'header.php'; ?>
        <!--        end of page header-->

        <div class="container mb-3">
            <div class="col-md-4 offset-md-4 col-xs-12">
                <div class="card">
                    <p>
                        <?php 
                        if($match > 0)
                            echo "Welcome! Your account has been successfully verified. Please login now to avail all the facilities.";
                        else
                            echo $alert;
                        ?>
                    </p>
                </div>
            </div>
        </div>

        <!--        page header -->
        <?php require 'footer.php'; ?>
        <!--        end of page header-->
</div>
    </body>
</html>
