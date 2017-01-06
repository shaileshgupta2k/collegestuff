<?php

session_start();
include("config.php");

if(isset($_POST["ad_id"]))
{
    $productID = $_POST['ad_id'];
    $query = "UPDATE products SET approved = '1' WHERE id = $productID";
    $result = mysqli_query($connection, $query);
    if(mysqli_num_rows($result) != 0)
    {
        echo "0";
        exit;
    }else
    {
        echo "1";
        exit;
    }
    exit;
}

?>