<?php

session_start();
include("config.php");
include("functions.php");

if(isset($_SESSION['login_user']))
{
    $userID = $_SESSION['login_user'];
    $user_details = getUserDetails($userID);
}

if(isset($_GET["college"]))
    $sort_college_id = $_GET["college"];
else
    $sort_college_id = 14;

//do pagination
$query = "SELECT * FROM products, colleges WHERE colleges.college_id = products.college_id && colleges.college_id = $sort_college_id";
    
$result = mysqli_query($connection, $query);
$rec_count = mysqli_num_rows($result);

$limit = 12;            

if(isset($_GET["page"]))
    $page = $_GET["page"];
else
    $page = 0;

$offset = $page * $limit;
if($offset < $rec_count)
    $next = $page + 1;
if($page > 0)
    $prev = $page - 1;

?>

<!DOCTYPE HTML>
<html>
    <head>
        <title><?php echo $pageTitle; ?></title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/css/bootstrap.min.css" integrity="sha384-AysaV+vQoT3kOAXZkl02PThvDr8HYKPZhNT5h/CXfBThSRXQ6jW5DO2ekP5ViFdi" crossorigin="anonymous">
        <link href="css/footer.css" rel="stylesheet">
        <style type="text/css">
            option:nth-child(odd){
                background: #f2f4f7;
            }
            .products-item:hover{
                background: #f4f4f4;
            }

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
        <div id="wrapper">
        <div class="container">
            <div class="row mb-2">
                <div class="col-xs-12 bg-faded" style="padding: 20px;">
                <form class="form-inline" method="get" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
     
                    <select class="custom-select mr-2" name="sortby">
                      <option selected>Sort By:</option>
                      <option value="1">Date</option>
                      <option value="2">Price: High to Low</option>
                      <option value="3">Price: Low to High</option>
                    </select>
                    <div class="form-group">
                        <label for="selectCollege"> </label>
                            <select class="form-control custom-select" id="selectCollege" name="college" data-live-search="true">
                            <?php
                                printCollegeList();
                            ?>  
                        </select>
                    </div>
                    <input type="hidden" name="page" value="<?php echo $page; ?>">
                    <input type="submit" class="btn btn-primary" name="sort" value="Sort">
                </form>
            </div>
            </div>
            <div class="row">
                
                <?php
                
                    function createCard($card_details){
                        $productID = $card_details["id"];
                        $card = "<a style='text-decoration:none;' href='product.php?pid=".$card_details['id']."' target='_blank'><div class='col-xl-3 col-md-6 col-xs-12'>";
                        $card .= "<div class='card products-item'>";
                        $card .= "<img src=product_images\\" . $card_details['image_name'] . " class='card-img-top img-fluid'>";
                        $card .= "<div class='card-block'>";
                        $card .= "<h4 class='card-title'>" . $card_details['title'] . "</h4>";
                        $card .= "<p class='card-text'>Price: " . $card_details['price']. " ₹</p>";
                        
                        $card .= "<p><a href='#' onclick='addToWishlist(this.id)' id='$productID' class='btn btn-primary float-xs-right'>Wishlist It</a></p>";
                        $card .= "</div></div></div></a>";
                        echo $card;
                    }
                
                    function showEmpty(){
                        $card = "<div class='col-xl-3 col-md-6 col-xs-12 offset-xl-4 offset-md-3'>";
                        $card .= "<div class='card card-block'>";
                        $card .= "<h5 class='card-title text-justify'>Oo, no products under this college. Wanna sell product in this college? Hit Post Free Ad button below to get going.</h5>";
                        
                        $card .= "<p><a href='index.php' class='btn btn-primary float-xs-left mt-2'>Post Free Ad</a>";
                        $card .= "</div></div>";
                        echo $card;      
                    }
                    
                
                    if(isset($_GET["sortby"])){
                        switch($_GET["sortby"]){
                            case 1:
                                $query = "SELECT * FROM products, colleges WHERE colleges.college_id = products.college_id && colleges.college_id = $sort_college_id && products.approved = 1 ORDER BY date_of_posting DESC LIMIT $limit OFFSET $offset";
                                break;
                            case 2:
                                    $query = "SELECT * FROM products, colleges WHERE colleges.college_id = products.college_id && colleges.college_id = $sort_college_id && products.approved = 1 ORDER BY price DESC LIMIT $limit OFFSET $offset";
                                    break;
                            case 3:
                                    $query = "SELECT * FROM products, colleges WHERE colleges.college_id = products.college_id && colleges.college_id = $sort_college_id && products.approved = 1 ORDER BY price LIMIT $limit OFFSET $offset";
                                    break;
                            default: 
                                    $query = "SELECT * FROM products, colleges WHERE colleges.college_id = products.college_id && colleges.college_id = $sort_college_id && products.approved = 1 LIMIT $limit OFFSET $offset";
                        }
                    }else{
                        $query = "SELECT * FROM products, colleges WHERE colleges.college_id = products.college_id && colleges.college_id = $sort_college_id && products.approved = 1 LIMIT $limit OFFSET $offset";
                    }
                    
                    $result = mysqli_query($connection, $query);
                    if(mysqli_num_rows($result) < 1){
                        showEmpty();
                    }
                    else{
                        while($row = $result->fetch_assoc()){
                                createCard($row);
                        }
                    }
                    
                ?>
            </div>
            <?php if($page > 0){ ?>
            <div class="row text-xs-center mt-3">
                <nav aria-label="Page navigation ">
                  <ul class="pagination">
                    <?php if($page >= 1){ ?>
                    <li class="page-item ">
                      <a class="page-link" href="<?php echo "buy.php?college=". $_GET["college"] . '&page=' . $prev; ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                        <span class="sr-only">Previous</span>
                      </a>
                    </li>
                    <?php } ?>
                    <li class="page-item"><a class="page-link" href="#"><?php if($page == 0) echo '1'; else echo $page; ?></a></li>
                    <?php if($offset < $rec_count){ ?>
                    <li class="page-item">
                      <a class="page-link" href="<?php echo "buy.php?college=". $_GET["college"] . '&page=' . $next; ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                        <span class="sr-only">Next</span>
                      </a>
                    </li>
                    <?php } ?>
                  </ul>
                </nav>
            </div>
            <?php } ?>
        </div>

        <!--page footer-->
        <?php require 'footer.php'; ?>
        <!--/ end of page footer-->
        </div>

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