<?php

session_start();
include("config.php");
include("functions.php");

if(!isset($_GET["pid"]))
{
    header('location: index.php');
    exit;
}

//Get User and Product details from the database
$pID = $_GET["pid"];
$prod_details = getProduct($pID, $userID);

//If product not approved, send user back to the index page
if($prod_details["approved"] == "0")
{
    header('location: index.php');
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>

        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>CollegeStuff</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/css/bootstrap.min.css" integrity="sha384-AysaV+vQoT3kOAXZkl02PThvDr8HYKPZhNT5h/CXfBThSRXQ6jW5DO2ekP5ViFdi" crossorigin="anonymous">
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet">
        <style>


        </style>
        
        <script type="text/javascript">
            
            <?php 
            
            if(!isset($_SESSION["login_user"]))
            {
                $alert = "Oo, you need to login to add this product to your wishlist.<br> <a href='login_signup.php' class='btn'>Login | Signup</a>";
                echo "function addToWishlist(id){\$(document).ready(function(){\$('#alertAlreadyInWishlist').modal();});}";  
            }else
            {
                echo "function addToWishlist(id){
                $.ajax({
                  type:'POST',
                  url:'add_to_wishlist.php',
                  data:'add_wishlist_id='+id,
                  success:function(data) {
                    if(data) 
                    {
                        if(data == 0)
                            {
                                $(document).ready(function(){\$('#alertAlreadyInWishlist').modal();});;
                            }
                    } else {  }
                  }
               });
            }";
            }
            
            ?>
            
            
        </script>
    </head>

    <body>

        <!--        page header -->
        <?php require 'header.php'; ?>
        <!--        end of page header-->

        <div class="container mb-3">
            <div class="col-md-8 offset-md-2 col-xs-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                    <h4 class="float-xs-left"><?php echo $prod_details["title"]; ?></h4>
                    <a href="#" class="btn btn-primary bg-success float-xs-right" onclick='addToWishlist(this.id)' id="<?php echo $prod_details['id']; ?>" >Add to Wishlist</a>
                    </div>
                    <div class="card-block">
                        <div class="row">
                            <div class="col-md-6 col-xs-12 text-xs-center">
                                <img src="product_images\<?php echo $prod_details['image_name']; ?>" style="width:300px; height: 300px;" alt="Product Image" class="img-fluid">
                            </div>
                            <div class="col-md-6 col-xs-12">
                                <h4 class="card-title">Price <?php echo $prod_details['price'];?></h4>
                                <div class="card-text bg-faded" style="padding: 10px; box-sizing:border-box">
                                    <p style="text-decoration: underline">Contact details</p> 
                                    <p>Name: <?php echo $prod_details['name']; ?></p>
                                    <p>Phone: <?php echo $prod_details['phone']; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2 col-md-10 offset-md-1">
                            <p class="card-text bg-faded"><?php echo $prod_details['description']; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--        page header -->
        <?php require 'footer.php'; ?>
        <!--        end of page header-->
        
        <!-- Modal for alerting user that product is already in the wishlist-->
        <div class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" id="alertAlreadyInWishlist">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <h6><?php echo $alert; ?></h6>
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
