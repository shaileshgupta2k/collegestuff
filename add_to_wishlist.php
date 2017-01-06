<?php

session_start();
include("config.php");

if(isset($_POST["add_wishlist_id"]))
{
    $productID = $_POST['add_wishlist_id'];
    $personID = $_SESSION['login_user'];
    $query = "SELECT person_id, product_id FROM wishlist where person_id = $personID and product_id = $productID";
    $result = mysqli_query($connection, $query);
    if(mysqli_num_rows($result) != 0)
    {
        echo "0";
        exit;
    }else
    {
        //now finally insert product to wishlist the product
        $query = "INSERT INTO wishlist(person_id, product_id) VALUES($personID, $productID)";
        $result = mysqli_query($connection, $query);
        echo "1";
        exit;
    }
    exit;
}

?>