<?php

include("config.php");
include("functions.php");

if (!isset($_SERVER['PHP_AUTH_USER']) && !isset($_SERVER['PHP_AUTH_PW'])) 
{
    header("WWW-Authenticate: Basic realm=\"Please enter your username and password to proceed further\"");
    header("HTTP/1.0 401 Unauthorized");
    print "Oops! It require login to proceed further. Please enter your login detail\n";
    exit;
} else {
    if ($_SERVER['PHP_AUTH_USER'] == 'admin' && $_SERVER['PHP_AUTH_PW'] == 'admin@123') 
    {
?>
<!DOCTYPE html>
<html lang="en">
    <head>

        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>CollegeStuff</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/css/bootstrap.min.css" integrity="sha384-AysaV+vQoT3kOAXZkl02PThvDr8HYKPZhNT5h/CXfBThSRXQ6jW5DO2ekP5ViFdi" crossorigin="anonymous">
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet">
        
        <script>
            function removeFromProduct(id){
                $.ajax({
                  type:'POST',
                  url:'remove_from_database.php',
                  data:'delete_ad_id='+id,
                  success:function(data) {
                    if(data) {  
                        document.getElementById("alertUser").style.display="block";
                        document.getElementById("showAlertMessage").innerHTML = "Deleted Successfully from your inventory!";
                        setTimeout(function(){location.reload(); }, 0);
                    } else {
                        document.getElementById("alertUser").style.display="block";
                        document.getElementById("showAlertMessage").innerHTML = "There was some problem in deleting. Try after sometime.";
                        setTimeout(function(){location.reload(); }, 0);
                    }
                  }
               });
            }
            function approveProduct(id)
            {
                $.ajax({
                  type:'POST',
                  url:'approve_product.php',
                  data:'ad_id='+id,
                  success:function(data) {
                    if(data) { 
                        document.getElementById("alertUser").style.display="block";
                        document.getElementById("showAlertMessage").innerHTML = "Approved";
                    } else {
                        document.getElementById("alertUser").style.display="block";
                        document.getElementById("showAlertMessage").innerHTML = "There was some problem in approving. Try after sometime.";
                    }
                  }
               });
            }
        </script>
    </head>

    <body>
     
            <!--        page header -->
            <?php require 'header.php'; ?>
            <!--        end of page header-->
            
            <div class="container">
                
                <!--        for alerting user for profile updates-->
            <div class="alert col-xs-12 col-sm-12 col-md-6 col-lg-6 offset-xs-0 offset-sm-0 offset-md-3 offset-lg-3 alert-warning alert-dismissible fade in" role="alert" style="display:none;" id="alertUser">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
              <span id="showAlertMessage" class="text-primary"></span>
            </div>
    <!--        end of user alert -->
                
                <div class="col-xs-12">
                    <table class="table table-responsive">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Price</th>
                                <th>Date Posted</th>
                                <th>Img</th>
                                <th>Delete</th>
                                <th>Approve</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $query = "SELECT * FROM products WHERE approved = '0'";
                            $result = mysqli_query($connection, $query);
                            $counter = 1;
                            while($row = $result->fetch_assoc())
                            {
                                echo "<tr>";
                                echo "<td>$counter</td>";
                                echo "<td>".$row["title"]."</td>";
                                echo "<td>".$row["description"]."</td>";
                                echo "<td>".$row["price"]."</td>";
                                echo "<td>".$row["date_of_posting"]."</td>";
                                echo "<td>"."<a href='#'  target='_blank'><img src='product_images/" .$row["image_name"]."' style='width:100px; height:100px'></a>"."</td>";
                                echo "<td><a class='removeAd text-danger' onclick='removeFromProduct(this.id)' href='#' id='". $row["id"] . "'>remove X</a></td>";
                                echo "<td><a onclick='approveProduct(this.id)' href='#' id='". $row["id"] . "' class='text-success'>Approve</a></td>";
                                echo "</tr>";
                                $counter++;
                            }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!--        page header -->
            <?php require 'footer.php'; ?>
            <!--        end of page header-->
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js" integrity="sha384-3ceskX3iaEnIogmQchP8opvBy3Mi7Ce34nWjpBIwVTHfGYWQS9jwHDVRnpKKHJg7" crossorigin="anonymous"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.3.7/js/tether.min.js" integrity="sha384-XTs3FgkjiBgo8qjEjBk0tGmf3wPrWtA6coPfQDfFEY8AnYJwjalXCiosYRBIBZX8" crossorigin="anonymous"></script>
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/js/bootstrap.min.js" integrity="sha384-BLiI7JTZm+JWlgKa0M0kGRpJbF2J8q+qreVrKBC47e3K6BW78kGLrCkeRX6I9RoK" crossorigin="anonymous"></script>
  
    </body>
</html>

    
    
        <?php
        exit;
    } else {
        header("WWW-Authenticate: Basic realm=\"Please enter your username and password to proceed further\"");
        header("HTTP/1.0 401 Unauthorized");
        print "Oops! It require login to proceed further. Please enter your login detail\n";
        exit;
    }
}

?>

