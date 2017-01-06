<?php

session_start();
include("config.php");
include("functions.php");

if(isset($_SESSION['login_user']) && isset($_GET["pid"]))
{
    $userID = $_SESSION['login_user'];
    $pID = $_GET["pid"];
    
    $query = "SELECT COUNT(id) FROM products WHERE person_id = $userID AND id = $pID";
    $result = mysqli_query($connection, $query);
    if(mysqli_num_rows($result) == 0)
        header('location: index.php');
}else
{
    header('location: index.php');
}

//Get User and Product details from the database
$user_details = getUserDetails($userID);
$prod_details = getProduct($pID, $userID);
$name = $row["name"];

if(isset($_POST["updateAd"]) && $_SERVER["REQUEST_METHOD"] == "POST")
{
    $person_id = $_SESSION['login_user'];
    $title = filter_input(INPUT_POST, "title", FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING);
    $price = filter_input(INPUT_POST, "price", FILTER_SANITIZE_NUMBER_INT);
    $college_id = $_POST["collegeSelectOptionSell"];  
    
    if(is_uploaded_file($_FILES['product_image']['tmp_name']) & isset($_POST["product_image"]))
    {
        //delete old image
        unlink("product_images/".$prod_details["image_name"]);
        
        //Now get information about the new image
        function make_seed()
        {
          list($usec, $sec) = explode(' ', microtime());
          return $sec + $usec * 1000000;
        }
        mt_srand(make_seed());
        $randval = mt_rand();   
        
        $imgFile = $_FILES['product_image']['name'];
        $tmp_dir = $_FILES['product_image']['tmp_name'];
        $imgSize = $_FILES['product_image']['size'];
        $upload_dir = 'product_images/';
        $imgExt = strtolower(pathinfo($imgFile,PATHINFO_EXTENSION));
        $userpic = $randval.".".$imgExt;
        move_uploaded_file($tmp_dir,$upload_dir.$userpic);
        
        $query = "UPDATE products SET image_name = '$userpic', approved = '0' WHERE id = $pID";
        $result = mysqli_query($connection, $query);
    }
    $query = "UPDATE products SET price = $price, title = '$title', description='$description', date_of_posting = NOW(), college_id = $college_id, approved = '0' WHERE id = $pID";
    $result = mysqli_query($connection, $query);
}
    

?>

<!doctype html>
<html>
    <head>
        <title><?php echo $pageTitle; ?></title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/css/bootstrap.min.css" integrity="sha384-AysaV+vQoT3kOAXZkl02PThvDr8HYKPZhNT5h/CXfBThSRXQ6jW5DO2ekP5ViFdi" crossorigin="anonymous">
     
        <style type="text/css">
            option:nth-child(odd){
                background: #f2f4f7;
            }
            jumbotron{
                padding: 0px;
            }
        </style>
        <script type="text/javascript">
            <?php
            if(isset($_POST["uploadNewAd"]))
            {
                echo "$(document).ready(function(){" . "$('#greet_user').modal();});";
            }
            ?>
            
            function update() 
            {
                var len = document.getElementById("description").value.length;
                document.getElementById("showLength").innerHTML = 200 - len;
            }
            function checkDesc()
            {
                var email = document.getElementById("description").value;
                if(email.length < 100)
                {
                    document.getElementById("updateAd").disabled = true;
                    $('#description').popover('show');
                    setTimeout(function(){$('#description').popover('hide');}, 2000)
                }else
                {
                    document.getElementById("updateAd").disabled = false;
                    $('#description').popover('hide');
                }
            }
            
        </script>
    </head>
    <body>

<!--        page header -->
        <?php require 'header.php'; ?>
<!--        end of page header-->
        
        <div class="container">
            <!--        for alerting user for profile updates-->
            <?php 
                if(isset($_POST["updateAd"])){?>
            <div class="alert col-xs-4 offset-xs-4 alert-success alert-dismissible fade in" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
              <span>Your ad has been updated successfully.</span>
            </div>
            <?php } ?>
    <!--        end of user alert -->

            <div class="row mb-2">
                <div class="col-md-6 col-xs-12 offset-md-3">
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
                                            <input type="text" name="name" id="name" class="form-control" placeholder="Name" required  <?php if(isset($userID)) echo "disabled";?> value="<?php echo $user_details['name']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form-group">
                                            <label for="phone" class="sr-only">Phone</label>
                                            <input type="number" name="phone" id="phone" class="form-control" placeholder="Phone" required <?php if(isset($userID)) echo "disabled";?> value="<?php echo $user_details['phone']; ?>"> 
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="email" class="sr-only">Email</label>
                                    <input type="email" name="email" id="email" class="form-control" placeholder="Email" required <?php if(isset($userID)) echo "disabled";?> value="<?php echo $user_details['email']; ?>">
                                </div>
                                <?php if(isset($userID)) echo "<small>You cannot change these details. Please visit profile to change them.</small>"; ?>
                                <hr>
                                <span>Now please enter your Ad details.</span><br>
                                <div class="row">
                                    <div class="col-xs-8">
                                        <div class="form-group">
                                            <label for="title" class="sr-only">Title</label>
                                            <input type="text" name="title" id="title" class="form-control" placeholder="Title" required value="<?php echo $prod_details['title']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-xs-4">
                                        <div class="form-group">
                                            <label for="price" class="sr-only">Price</label>
                                            <input type="number" name="price" id="price" class="form-control" placeholder="Price" required value="<?php echo $prod_details['price']; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="description" class="sr-only">Description</label>
                                    <textarea class="form-control" name="description" id="description" required rows="2" placeholder="Ad Description" onkeyup="update();" maxlength="200" onblur="checkDesc();" data-toggle="popover" data-trigger="manual" title="Error" data-content="Enter a good description please, with minimum char-length of 100." data-placement="top" tabindex="0"><?php echo trim(stripslashes(htmlentities($prod_details['description']))); ?>
                                    </textarea>
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
                                    <input type="submit" class="btn" name="updateAd" value="Update Ad" id="updateAd">
                                </div>
                            </form>
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