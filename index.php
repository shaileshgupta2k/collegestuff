<?php

session_start();
include("config.php");
include("functions.php");

if(isset($_COOKIE["login_user"]))
{
    $_SESSION['login_user'] = $_COOKIE["login_user"];
}
if(isset($_SESSION['login_user']))
{
    $userID = $_SESSION['login_user'];
    $user_details = getUserDetails($userID);
    $name = $user_details["name"];
}
if(isset($_POST["buySelect"]) && $_SERVER["REQUEST_METHOD"] == "POST")
{
    header("location: buy.php?college=". $_POST['buySelect']);
}

////////////////////////////////////
//          publish ad
////////////////////////////////////
if(isset($_POST["postAd"]) && $_SERVER["REQUEST_METHOD"] == "POST")
{

    if(!isset($userID))
    {
        $name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
        $phone = filter_input(INPUT_POST, "phone", FILTER_SANITIZE_NUMBER_INT);
        if(!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) || !preg_match("/^[a-zA-Z ]*$/", $_POST["name"]) || strlen($_POST["phone"]) < 10)
        {
            $success = 0;
            $alert = "Oops, Your email, name or phone was not correct. Please fill in correct details.";
            goto endPublish;
        }
    }
    $title = filter_input(INPUT_POST, "title", FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING);
    $price = filter_input(INPUT_POST, "price", FILTER_SANITIZE_NUMBER_INT);
    $college_id = $_POST["sellSelect"];

    //check is user already exists
    $query = "SELECT id FROM admin WHERE email='$email'";
    $result = mysqli_query($connection, $query);
    if(mysqli_num_rows($result) != 0)
    {
        $success = 1;
        $alert = "Oo, User account already exists. Please use another email.";

    }else
    {
        if(is_uploaded_file($_FILES['product_image']['tmp_name']))
        {
            mt_srand(make_seed());
            $randval = mt_rand();   

            $imgFile = $_FILES['product_image']['name'];
            $tmp_dir = $_FILES['product_image']['tmp_name'];
            $imgSize = $_FILES['product_image']['size'];
            $upload_dir = 'product_images/';
            $imgExt = strtolower(pathinfo($imgFile,PATHINFO_EXTENSION));
            $userpic = $randval.".".$imgExt;
            move_uploaded_file($tmp_dir,$upload_dir.$userpic);
            if(!isset($userID))
            {
                $hash = md5(rand(0,1000));
                $password = rand(10000,50000);
                $enc_password = md5($password);
                $query = "INSERT INTO admin (name, password, email, hash, college_id) VALUES('$name', '$enc_password', '$email', '$hash', $college_id)";
                $result = mysqli_query($connection, $query); 
                $userID = $connection->insert_id;
                //////////////////////////////
                //send verification mail
                /////////////////////////////
                $to      = $email; // Send email to our user
                $subject = 'Signup | Verification'; // Give the email a subject 
                $message = "<h2>Thanks for signing up!</h2>
                <p>Your account has been created. You can login with the following credentials after you have activated your account by pressing the url below.</p>
                ------------------------<br>
                Username: $name<br>
                Password: $password<br>
                ------------------------<br>
                <p>Please click this link to activate your account:<a href='verify.php?email=$email&hash=$hash'>Click Me to verify</a> </p>";
                sendMail($to, $message, $subject);

                $success = 1;
                $alert = "You accounted has been created. Please verify your account from your email. Your Ad will be live once verified from our end. Thank you.";
            }

            $query = "INSERT INTO products(price, title, description, date_of_posting, college_id, person_id, image_name) VALUES($price, '$title', '$description', NOW(), $college_id, $userID, '$userpic')";
            $result = mysqli_query($connection, $query);

            if(isset($userID))
            {
                $success = 1;
                $alert = "Your Ad will be live once verified from our end. Thank you.";
            }

        }
    }
}
endPublish:

?>

<!DOCTYPE HTML>
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
            .jumbotron{
                padding-top: 10px;
                padding-bottom: 10px;
                background: inherit !important;
            }
        </style>
        <script type="text/javascript">
            function update() {
                var len = document.getElementById("description").value.length;
                document.getElementById("showLength").innerHTML = 200 - len;
                
            }
            <?php
                if(isset($success))
                { 
                    echo "$(document).ready(function(){"."$('#alert').modal();});";
                }
            ?>
            function checkPhone()
            {
                var phoneNo = document.getElementById("phone").value;
                var phoneMatch = /^\d{10}$/;  
                if(!phoneNo.match(phoneMatch))
                {
                    document.getElementById("postAdver").disabled = true;
                    $('#phone').popover('show');
                    setTimeout(function(){$('#phone').popover('hide');}, 2000)  
                }else
                {
                    document.getElementById("postAdver").disabled = false;
                    $('#phone').popover('hide');
                }
            } 
            function checkEmail()
            {
                var email = document.getElementById("email").value;
                var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
                if(!email.match(mailformat))
                {
                    document.getElementById("postAdver").disabled = true;
                    $('#email').popover('show');
                    setTimeout(function(){$('#email').popover('hide');}, 2000)
                }else
                {
                    document.getElementById("postAdver").disabled = false;
                    $('#email').popover('hide');
                }
            } 
            function checkDesc()
            {
                var email = document.getElementById("description").value;
                if(email.length < 100)
                {
                    document.getElementById("postAdver").disabled = true;
                    $('#description').popover('show');
                    setTimeout(function(){$('#description').popover('hide');}, 2000)
                }else
                {
                    document.getElementById("postAdver").disabled = false;
                    $('#description').popover('hide');
                }
            }
        </script>
    </head>
    <body class="body" style="background: #f9f9f9">

        <!--        header for page -->
        <?php require 'header.php';?>
        <!--    end of header -->
        
        <div class="container">
            <div class="row">
                <div class="col-md-5 col-xs-12">
                    <div class="row text-xs-center">
                        <p class="text-justify text-primary" style="font-size: 120%; padding: 10px;">We help you sell your college stuff in your college itself. Why go anywhere when you have hundreds of students to buy and sell from!</p>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 text-xs-center">
                            <img src="img/front_index.png" class="img-fluid">
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="card mt-1" style="box-shadow: gray -4px 4px 4px">
                            <div class="card-header bg-success"><span class="text-white">Buy Now</span></div>
                                <div class="card-block">
                                    <div class="card-text">
                                        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                                            <div class="form-group">
                                            <label for="buySelect">Select College</label>
                                                <div class="input-group">
                                                    <select class="form-control" id="buySelect" name="buySelect">
                                                        <?php
                                                        printCollegeList();
                                                        ?>
                                                    </select>
                                                    <span class="input-group-btn">
                                                    <input class="btn btn-secondary" type="submit" name="collegeSelected" value="Go!">
                                                    </span>
                                                </div>
                                            </div>
                                        </form>  
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
                <div class="col-md-6 col-xs-12 offset-md-1">
                <div class="card" style="box-shadow: gray 4px 4px 4px">
                    <div class="card-header bg-primary">
                    <span class="text-white">Post an ad in seconds. For Free.</span>
                    </div>
                    <div class="card-block">
                        <div class="card-text">
                            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-xs-6">
                                        <div class="form-group">
                                            <label for="name" class="sr-only">Name</label>
                                            <input type="text" name="name" id="name" class="form-control" placeholder="Name" required  <?php if(isset($userID)) echo "disabled"; ?> value="<?php echo $user_details['name']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form-group">
                                            <label for="name" class="sr-only">Phone</label>
                                            <input type="number" name="phone" id="phone" class="form-control" placeholder="Phone" required <?php if(isset($userID)) echo "disabled";?> value="<?php echo $user_details['phone']; ?>" onblur="checkPhone();" data-toggle="popover" data-trigger="manual" title="Error" data-content="Please enter a valid phone number, without any prefixes or service codes." data-placement="top" tabindex="0"> 
                                        </div>
                                        <br>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="email" class="sr-only">Email</label>
                                    <input type="email" name="email" id="email" class="form-control" placeholder="Email" required <?php if(isset($userID)) echo "disabled";?> value="<?php echo $user_details['email']; ?>" onblur="checkEmail();" data-toggle="popover" data-trigger="manual" title="Error" data-content="Please enter a valid email id." data-placement="top" tabindex="0">
                                </div>
                                <?php if(isset($userID)) echo "<small>You cannot change these details. Please visit profile to change them.</small>"; ?>
                                <hr>
                                <span>Now please enter your Ad details.</span><br>
                                <div class="row">
                                    <div class="col-xs-8">
                                        <div class="form-group">
                                            <label for="title" class="sr-only">Title</label>
                                            <input type="text" name="title" id="title" class="form-control" placeholder="Title" required>
                                        </div>
                                    </div>
                                    <div class="col-xs-4">
                                        <div class="form-group">
                                            <label for="price" class="sr-only">Price</label>
                                            <input type="number" name="price" id="price" class="form-control" placeholder="Price" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="description" class="sr-only">Description</label>
                                    <textarea class="form-control" name="description" id="description" required rows="2" placeholder="Ad Description" onkeyup="update();" maxlength="200" onblur="checkDesc();" data-toggle="popover" data-trigger="manual" title="Error" data-content="Enter a good description please, with minimum char-length of 100." data-placement="top" tabindex="0"></textarea>
                                    <small class="form-text text-muted">Please enter a brief description of your product. (Chars left: <span id="showLength" class="text-success">200</span>)</small>
                                </div>
                                <div class="form-group">
                                    <label for="product_image">Product image:</label>
                                    <input type="file" class="form-control-file" id="product_image" aria-describedby="fileHelp" name="product_image" required>
                                    <small id="fileHelp" class="form-text text-muted">Please upload a good quality image of your product.</small>
                                </div>
                                <div class="form-group">
                                    <label for="sellSelect" class="sr-only">Select College</label>
                                    <div class="input-group">
                                        <select class="form-control" id="sellSelect" name="sellSelect">
                                            <?php
                                            printCollegeList();
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group float-xs-right">
                                    <input type="submit" class="btn" name="postAd" value="Post Ad" id="postAdver">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
  
        <div class="container marketing mt-3 text-xs-center">
            <div class="row" style="margin-bottom : 50px;">
                <div class="col-lg-4">
                    <img class="img-circle img-responsive" src="img/post-an-ad.png" alt="Generic placeholder image" width="140" height="140" style="margin-bottom: 20px;">
                    <h2>Post an Ad</h2>
                    <p class="text-justify">Wanna sell your college stuff? Fill out the form above, even without signing up and get your old stuff online.</p>
                </div><!-- /.col-lg-4 -->
                <div class="col-lg-4">
                    <img class="img-circle img-responsive" src="img/handshake.png" alt="Generic placeholder image" width="140" height="140" style="margin-bottom: 20px;">
                    <h2>Fix a Deal</h2>
                    <p class="text-justify">Students come here and check if they can find any useful material at resonable price. If they like they contact you for further enquiries.</p>
                </div><!-- /.col-lg-4 -->
                <div class="col-lg-4">
                    <img class="img-circle img-responsive" src="img/sell.png" alt="Generic placeholder image" width="140" height="140" style="margin-bottom: 20px;">
                    <h2>Sell</h2>
                    <p class="text-justify">Fix the deal, and Voila! your stuff is sold online in seconds. Could not get better than this. Post new ads to sell more.</p>
                </div>
            </div><!-- /.row -->
        </div>
        
        <!--page footer-->
        <?php require 'footer.php'; ?>
        <!--/ end of page footer-->
        
        <div class="modal fade" id="alert" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                    <h6 class="modal-title" id="myModalLabel"><?php echo $alert;?></h6>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
                </div>
            </div>
        </div>
        

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js" integrity="sha384-3ceskX3iaEnIogmQchP8opvBy3Mi7Ce34nWjpBIwVTHfGYWQS9jwHDVRnpKKHJg7" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.3.7/js/tether.min.js" integrity="sha384-XTs3FgkjiBgo8qjEjBk0tGmf3wPrWtA6coPfQDfFEY8AnYJwjalXCiosYRBIBZX8" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/js/bootstrap.min.js" integrity="sha384-BLiI7JTZm+JWlgKa0M0kGRpJbF2J8q+qreVrKBC47e3K6BW78kGLrCkeRX6I9RoK" crossorigin="anonymous"></script>

    </body>
</html>