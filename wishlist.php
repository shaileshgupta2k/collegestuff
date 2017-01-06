<?php

session_start();
include("config.php");
include("functions.php");

// for alerting user about page changes
$alert = "sdfsd";
$success = 2;

if(isset($_SESSION['login_user'])){
        $userID = $_SESSION['login_user'];
        $user_details = getUserDetails($userID);
}
else{
    header('location: index.php');
}   
    
?>

<!doctype html>
<html>
    <head>
        <title><?php echo $pageTitle; ?></title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/css/bootstrap.min.css" integrity="sha384-AysaV+vQoT3kOAXZkl02PThvDr8HYKPZhNT5h/CXfBThSRXQ6jW5DO2ekP5ViFdi" crossorigin="anonymous">
     
        <style type="text/css">
            option:nth-child(odd){
                background: grey;
                color: white;
            }
            .user-row {
                margin-bottom: 14px;
            }

            .user-row:last-child {
                margin-bottom: 0;
            }
            .dropdown-user {
                margin: 13px 0;
                padding: 5px;
                height: 100%;
            }
            .dropdown-user:hover {
                cursor: pointer;
            }

            .table-user-information > tbody > tr {
                border-top: 1px solid rgb(221, 221, 221);
            }
            .table-user-information > tbody > tr:first-child {
                border-top: 0;
            }
            .table-user-information > tbody > tr > td {
                border-top: 0;
            }
            .toppad{
                margin-top:20px;
            }
            @media screen and (min-width: 768px){
                .vdivide{
                      border-right: 2px solid gray;
                }
            }

        </style>
        <script type="text/javascript">
            
            navigator.geolocation.getCurrentPosition(function(location) {
              console.log(location.coords.latitude);
              console.log(location.coords.longitude);
              console.log(location.coords.accuracy);
            });
            
            function removeFromProduct(id){
                $.ajax({
                  type:'POST',
                  url:'remove_from_database.php',
                  data:'delete_ad_id='+id,
                  success:function(data) {
                    if(data) {  
                        document.getElementById("alertUser").style.display="block";
                        document.getElementById("showAlertMessage").innerHTML = "Deleted Successfully from your inventory!";
                        setTimeout(function(){location.href='wishlist.php'; }, 0);
                    } else {
                        document.getElementById("alertUser").style.display="block";
                        document.getElementById("showAlertMessage").innerHTML = "There was some problem in deleting. Try after sometime.";
                        setTimeout(function(){location.href='wishlist.php'; }, 0);
                    }
                  }
               });
            }
            
            function removeFromWishlist(id){
                $.ajax({
                  type:'POST',
                  url:'remove_from_database.php',
                  data:'delete_wishlist_id='+id,
                  success:function(data) {
                    if(data) {
                        document.getElementById("alertUser").style.display="block";
                        document.getElementById("showAlertMessage").innerHTML = "Deleted Successfully from your inventory!";
                        setTimeout(function(){location.href='wishlist.php'; }, 0);
                    } else { 
                        document.getElementById("alertUser").style.display="block";
                        document.getElementById("showAlertMessage").innerHTML = "There was some problem in deleting. Try after sometime.";
                        setTimeout(function(){location.href='wishlist.php'; }, 0);
                    }
                  }
               });
            }
            function hideAlert(){
                document.getElementById("hideAlert").style.display = "none";
            }
            
        </script>
    </head>
    <body>

<!--        page header -->
        <?php require 'header.php'; ?>
<!--        end of page header-->
        
    <div class="jumbotron jumbotron-fluid" style="padding: 10px; position:relative; top: -20px">
          <div class="container">
            <div class="row">
                <div class="vdivide col-xs-12 col-md-6">
                    <p class="lead">Do you know? Knowledge is the real wealth. So, buy more and learn more.</p>
                    <p><a href="buy.php" class="btn btn-primary">Buy</a></p>
                </div>  
                <div class="col-xs-12 col-md-6">
                    <p class="lead">Nothing much to do? Sell your old stuff to your needy junior at a resonable price :)</p>
                    <p><a href="index.php" class="btn btn-primary">Sell</a></p>
                </div> 
            </div>
            
          </div>
        </div>
    <div class="container mb-3">
        
<!--        for alerting user for profile updates-->
        <div class="alert col-xs-12 col-sm-12 col-md-6 col-lg-6 offset-xs-0 offset-sm-0 offset-md-3 offset-lg-3 alert-warning alert-dismissible fade in" role="alert" style="display:none;" id="alertUser">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <span id="showAlertMessage" class="text-primary"></span>
        </div>
<!--        end of user alert -->
        
      <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 offset-xs-0 offset-sm-0 offset-md-3 offset-lg-3 toppad" >
              <div class="card">
                  <div class="card-header">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item"><a class="nav-link active text-primary" href="#wishlist" role="tab" data-toggle="tab">Wishlist</a></li>
                        <li class="nav-item"><a class="nav-link text-primary" href="#ads" role="tab" data-toggle="tab">Your ads</a></li>
                    </ul>
                  </div>
                  <!-- Tab panes -->
                  <?php
                  
                    $query = "SELECT wishlist.id, products.title, products.date_of_posting FROM products, wishlist WHERE products.id = wishlist.product_id && wishlist.person_id = $userID products.approved = '1'";
                    $result = mysqli_query($connection, $query);
        
                  ?>
                    <div class="tab-content">
                          <div role="tabpanel" class="tab-pane fade in active py-1" id="wishlist">
                                <table class="table">
                                  <thead>
                                    <tr>
                                      <th>#</th>
                                      <th>Product Title</th>
                                      <th>Date Posted</th>
                                      <th>Delete</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                <?php
                                      
                                    $counter = 1;
                                    if(mysqli_num_rows($result) != 0)
                                    {
                                        while($row = mysqli_fetch_row($result))
                                        {
                                            echo "<tr>";
                                            echo "<td>$counter</td>";
                                            echo "<td>".$row[1]."</td>";
                                            echo "<td>".$row[2]."</td>";
                                            echo "<td><a class='removeAd' onclick='removeFromWishlist(this.id)' href='#' id='". $row[0] . "' class='text-danger'>remove X</a></td>";
                                            echo "</tr>";
                                            $counter++;
                                        }
                                    }else
                                    {
                                        echo "<tr><td colspan='4'><p class='text-xs-center text-danger'>No items in your wishlist</p></td></tr>";
                                    }
                                    
                                ?>
                                  </tbody>
                                </table>
                          </div>
                          <div role="tabpanel" class="tab-pane fade py-1" id="ads" style="padding: 10px;">
                            <table class="table">
                                  <thead>
                                    <tr>
                                      <th>#</th>
                                      <th>Product Title</th>
                                      <th>Date Posted</th>
                                      <th>Update</th>
                                      <th>Delete</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                <?php

                                    $query = "SELECT * FROM products where person_id = $userID and products.approved = '1'";
                                    $result = mysqli_query($connection, $query);
                                    $counter = 1;
                                    if(mysqli_num_rows($result) != 0)
                                    {
                                        while($row = mysqli_fetch_row($result))
                                        {
                                            echo "<tr>";
                                            echo "<td>$counter</td>";
                                            echo "<td>".$row[2]."</td>";
                                            echo "<td>".$row[5]."</td>";
                                            echo "<td><a href='update_ad.php?pid=" . $row[0] . "'>Update</a></td>";
                                            echo "<td><a class='removeAd' onclick='removeFromProduct(this.id)' href='#' id='". $row[0] . "' class='text-danger'>remove X</a></td>";
                                            echo "</tr>";
                                            $counter++;
                                        }
                                    }else
                                    {
                                        echo "<tr><td colspan='5'><p class='text-xs-center text-danger'>No product in your repository</p></td></tr>";
                                    }
                                    
                                ?>
                                  </tbody>
                                </table>
                          </div>
                     </div>
<!--end of tab panes-->
                
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