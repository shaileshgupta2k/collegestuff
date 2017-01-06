<?php

function printCollegeList()
{
    include("config.php");
    
    $query = "SELECT college_id, college_name FROM colleges ORDER BY college_name";
    $result = mysqli_query($connection, $query);
    echo "<option>Select College</option>";
    while($row = $result->fetch_assoc())
    {
        $college_id = $row["college_id"];
        $college_name = $row["college_name"];
        echo "<option value='$college_id'>$college_name</option>";
    }
}

function getUserDetails($userID)
{
    include("config.php");
    
    $query = "SELECT * FROM admin, colleges WHERE id = $userID and admin.college_id = colleges.college_id";
    $result = mysqli_query($connection, $query);
    $row = $result->fetch_assoc();
    return $row;
}

function getProduct($pID, $userID = null)
{
    include("config.php");
    
    $query = "SELECT * FROM admin, products WHERE products.person_id = admin.id AND products.id = $pID";
    $result = mysqli_query($connection, $query);
    $row = $result->fetch_assoc();
    return $row;
}

function make_seed()
{
    list($usec, $sec) = explode(' ', microtime());
    return $sec + $usec * 1000000;
}

function sendMail($to, $bodyContent, $subject)
{
    require 'PHPMailer-master/PHPMailerAutoload.php';
 
    $mail = new PHPMailer;
    $mail->isSMTP();                            // Set mailer to use SMTP
    $mail->Host = 'smtp.gmail.com';             // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                     // Enable SMTP authentication
    $mail->Username = 'info.collegestuff@gmail.com';          // SMTP username
    $mail->Password = 'college123'; // SMTP password
    $mail->SMTPSecure = 'tls';                  // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;                          // TCP port to connect to

    $mail->setFrom('info.collegestuff@gmail.com', 'CollegeStuff');
    $mail->addAddress($to);   // Add a recipient

    $mail->isHTML(true);  // Set email format to HTML

    $mail->Subject = $subject;
    $mail->Body    = $bodyContent;

    /*if(!$mail->send()) {
        echo 'Message could not be sent.';
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        echo 'Message has been sent';
    }*/
    $mail->send();
}

?>