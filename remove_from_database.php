<?php

include("config.php");

// for deleting add from inventory
if(isset($_POST["delete_ad_id"]))
{
    $id = $_POST['delete_ad_id'];
    $query = "SELECT image_name FROM products WHERE id = $id";
    $result = mysqli_query($connection, $query);
    $row = mysqli_fetch_row($result);
    $imageName = $row[0];

    //now finally delete the product
    $query = "DELETE FROM products WHERE id = $id";
    $result = mysqli_query($connection, $query);
    unlink("product_images/".$imageName);
    echo "deleted";
}

// for deleting ad from wishlist
else if(isset($_POST["delete_wishlist_id"]))
{
    $id = $_POST['delete_wishlist_id'];
    $query = "DELETE FROM wishlist WHERE id = $id";
    $result = mysqli_query($connection, $query);
    echo "deleted";
}


?>