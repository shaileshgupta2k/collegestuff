<script>
    function logout() {
        location.href = 'logout.php';
    }
    <?php
    if( isset($_GET["success"]))
    {
        echo "$(document).ready(function(){" . "$('#myModal').modal();});";
    }
    ?>
    
</script>
<nav class="navbar mb-2 bg-faded text-white" style="background: #11758e; border-radius:0;">
    <div class="container">
        <ul class="nav navbar-nav">
            <a class="navbar-brand text-white" href="index.php">CollegeStuff</a>
            <?php if(isset($_SESSION["login_user"])){ ?>
            <li class="nav-item dropdown float-xs-right">
                <a class="nav-link dropdown-toggle text-white" href="profile.php" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo ucwords($user_details['name']); ?></a>
                <div class="dropdown-menu" aria-labelledby="supportedContentDropdown" style="right: 0; left: auto;">
                    <a class="dropdown-item" href="wishlist.php">Wishlist & My ads</a>
                    <a class="dropdown-item" href="profile.php">Dashboard</a>
                    <a class="dropdown-item" href="#" id="logout" onclick="logout();">Sign Out</a>
                </div>
            </li>
            <?php } ?>
        </ul>
        <?php if(!isset($_SESSION['login_user'])){?>
            <div class="float-xs-right">
                <a href="login_signup.php" class="btn btn-secondary">Login | Signup</a>
            </div>
        <?php } ?>
    </div>
</nav>