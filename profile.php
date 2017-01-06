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

//for changing user password
if(isset($_POST["formChangePassword"]) && $_SERVER["REQUEST_METHOD"] == "POST")
{
    $userID = $_SESSION['login_user'];
    $query = "SELECT password FROM admin WHERE id = $userID";
    $result = mysqli_query($connection, $query);
    $row = mysqli_fetch_row($result);
        
    $password = $row[0];
    $oldpassword = filter_var(trim($_POST["oldpassword"]), FILTER_SANITIZE_STRING);
    $newpassword = filter_var(trim($_POST["newpassword"]), FILTER_SANITIZE_STRING);
    $retypepassword = filter_var(trim($_POST["retypepassword"]), FILTER_SANITIZE_STRING);
    
    $oldpassword = md5($oldpassword);
    $newpassword = md5($newpassword);
    $retypepassword = md5($retypepassword);
    
    if(isset($password) && isset($newpassword) && isset($retypepassword))
    {
        if($password == $oldpassword && $newpassword == $retypepassword)
        {
            $query = "UPDATE admin SET password='$newpassword' WHERE id = $userID";
            $result = mysqli_query($connection, $query);
            $row = mysqli_fetch_row($result);
            
            // for alerting user about page changes
            $alert = "Password Changed Successfully";
            $success = 1;
        }
        else{
            $success = 0;
            $alert = "Error: Passwords did not match, or you entered something different.";
        }
    }else
    {
        $success = 0;
        $alert = "Password fields cannot be empty";
    }
        
}

//for updating user details
if(isset($_POST["formUpdateUserDetails"]) && $_SERVER["REQUEST_METHOD"] == "POST")
{
    if(isset($_POST["collegeSelectOption"]) || isset($_POST["changephone"]))
    {
        $userID = $_SESSION['login_user'];
        $college = htmlspecialchars(filter_var($_POST["collegeSelectOption"], FILTER_SANITIZE_STRING));
        $changephone = filter_input(INPUT_POST, "changephone", FILTER_SANITIZE_NUMBER_INT);

        $confirm_password = htmlspecialchars(filter_var(trim($_POST["confirmpassword"]), FILTER_SANITIZE_STRING));
        $confirm_password = md5($confirm_password);

        $query = "SELECT password FROM admin WHERE id = $userID";
        $result = mysqli_query($connection, $query);
        $row = mysqli_fetch_row($result);
        $password = $row[0];
        
        if($confirm_password == $password)
        {
            if(isset($_POST["collegeSelectOption"]) && isset($_POST["changephone"]))
                $query = "UPDATE admin SET college_id=$college, phone = $changephone  WHERE id = $userID";
            else if(isset($_POST["collegeSelectOption"]))
                $query = "UPDATE admin SET college_id=$college  WHERE id = $userID";
            else if(isset($_POST["changephone"]))
                $query = "UPDATE admin SET phone = $changephone  WHERE id = $userID";
            $result = mysqli_query($connection, $query);
            $row = mysqli_fetch_row($result);

            // for alerting user about page changes
            $alert = "Details Updated Successfully";
            $success = 1;
        }
        else{
            $alert = "Password Incorrect";
            $success = 0;
        }
        unset($_POST);
    }
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
            td{
                word-wrap: break-word;
            }
        </style>
        <script type="text/javascript">
            function checkPhone()
            {
                var phoneNo = document.getElementById("changephone").value;
                var phoneMatch = /^\d{10}$/;  
                if(!phoneNo.match(phoneMatch))
                {
                    document.getElementById("formUpdateUserDetails").disabled = true;
                    $('#changephone').popover('show');
                    setTimeout(function(){$('#changephone').popover('hide');}, 2000)  
                }else
                {
                    document.getElementById("formUpdateUserDetails").disabled = false;
                    $('#changephone').popover('hide');
                }
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
    <div class="container">
        
<!--        for alerting user for profile updates-->
        <?php 
            if(isset($alert) && ($success == 1 || $success == 0)){ ?>
        <div class="alert col-xs-4 offset-xs-4 <?php if(isset($success) && $success == 1){ echo 'alert-success';}else {echo 'alert-danger';}?> alert-dismissible fade in" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <span><?php echo $alert; ?></span>
        </div>
        <?php } ?>
<!--        end of user alert -->
        
      <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 offset-xs-0 offset-sm-0 offset-md-3 offset-lg-3 toppad" >
              <div class="card mb-3">              
                  
                  <div class="card-header">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item"><a class="nav-link active text-primary" href="#profile" role="tab" data-toggle="tab"><?php echo ucwords($user_details["name"]);?></a></li>
                        <li class="nav-item"><a class="nav-link text-primary" href="#editprofile" role="tab" data-toggle="tab">Edit Profile</a></li>
                        <li class="nav-item"><a class="nav-link text-primary" href="#changepassword" role="tab" data-toggle="tab">Change Password</a></li>
                    </ul>
                  </div>
                  <!-- Tab panes -->
                    <div class="tab-content">
                          <div role="tabpanel" class="tab-pane fade in active py-1" id="profile">
                                <div class="card-block">
                                  <div class="row">
                                    <div class="col-md-3 col-lg-3 " align="center"> <img alt="User Pic"
                                    src="img\profile-icon.png" class=" img-fluid" style="border-radius: 50%;"> </div>
                                    <div class=" col-md-9 col-lg-9 "> 
                                      <table class="table table-user-information table-hover table-responsive">
                                        <tbody>
                                          <tr>
                                            <td>Email</td>
                                            <td><?php echo $user_details["email"];?></td>
                                          </tr>
                                          <tr>
                                            <tr>
                                                <td>College</td>
                                                <td><?php echo $user_details["college_name"];?></td>
                                            </tr>
                                            <tr>
                                                <td>Phone Number</td>
                                                <td><?php echo $user_details["phone"];?></td>
                                          </tr>

                                        </tbody>
                                      </table>
                                    </div>
                                  </div>
                            </div>
                          </div>
                        <?php
                        $userID = $_SESSION['login_user'];
                        $query = "SELECT * FROM admin WHERE id = $userID";
                        $result = mysqli_query($connection, $query);
                        $row = mysqli_fetch_row($result);
                        ?>
                          <div role="tabpanel" class="tab-pane fade py-1" id="editprofile" style="padding: 10px;">
                            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                                <div class="form-group">
                                    <label for="changephone">Change Phone No</label>
                                    <input type="number" class="form-control" id="changephone" placeholder="Change Phone No" name="changephone" onblur="checkPhone();" data-toggle="popover" data-trigger="manual" title="Error" data-content="Please enter a valid phone number, without any prefixes or service codes." data-placement="top" tabindex="0" value="<?php echo $user_details['phone']; ?>">
                                </div> 
                                <div class="form-group">
                                    <label for="collegeSelectOption">Change College</label>
                                    <div class="input-group">
                                        <select class="form-control custom-select" id="collegeSelectOption" name="collegeSelectOption">
                                            <?php
                                                printCollegeList();
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="confirmpassword">Confirm Password</label>
                                    <input type="password" class="form-control" id="confirmpassword" placeholder="Confirm Password" name="confirmpassword" required>
                                </div>    
                                <input type="submit" class="btn btn-primary" name="formUpdateUserDetails" value="Update" id="formUpdateUserDetails">
                            </form>
                          </div>
                            <div role="tabpanel" class="tab-pane fade py-1" id="changepassword" style="padding: 10px;">
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                                  <div class="form-group">
                                    <label for="oldpassword">Old Password</label>
                                    <input type="password" class="form-control" name="oldpassword" placeholder="Confirm Password">
                                  </div>
                                  <div class="form-group">
                                    <label for="newpassword">New Password</label>
                                    <input type="password" class="form-control" name="newpassword" placeholder="Confirm Password">
                                  </div>
                                  <div class="form-group">
                                    <label for="retypepassword">Retype New Password</label>
                                    <input type="password" class="form-control" name="retypepassword" placeholder="Confirm Password">
                                  </div>
                                  
                                  <button type="submit" class="btn btn-primary" name="formChangePassword">Update</button>
                                </form>
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