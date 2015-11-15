<?php
include('lib/db.php');

init_db();

if(isset($_COOKIE["sid"])) {
    $sid = $_COOKIE["sid"];
    $user = getSessionUser($sid);

    if(!is_null($user)) {
        header('Location: home.php');
        exit();
    } else {
        setcookie('session', '', time() - 3600);
    }
}

?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->  
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->  
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->  
<head>
    <title>The MARTA Army Barracks</title>
    <!-- Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="shortcut icon" href="favicon.ico"> 

    <link href='http://fonts.googleapis.com/css?family=Lato:300,400,300italic,400italic' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'> 
    
    <link rel="stylesheet" href="jslib/bootstrap/css/bootstrap.min.css">   
    <link rel="stylesheet" href="jslib/font-awesome/css/font-awesome.css">
    <link rel="stylesheet" href="jslib/ionicons/css/ionicons.css">    
    
    <!-- Theme CSS -->  
    <link id="theme-style" rel="stylesheet" href="css/main.css">
    <link id="theme-style" rel="stylesheet" href="css/float-label.css">
    <link id="theme-style" rel="stylesheet" href="css/login.css">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    
</head> 

<body>

    <div class="container-fluid login-page-container">
        <div class='shader'></div>
        <div class="row">
            <div class="col-xs-12 col-md-8 col-md-offset-2 login-page-content">
                <img class="big-logo-image" src="images/marta-army-white.png" alt="The MARTA Army"/>
                <h1 class="title">Barracks</h1>
            </div>
        </div>

        <div class="row" id='login-page-content'>
            <div class='col col-xs-12 col-md-offset-2 col-md-4'>
                <h2>Login</h2>
                <form id='login-form' action='ajax/login.php' method='POST'>
                    <div class='form-group float-label'>
                        <label for='email'>Email address</label>
                        <span class="error-message"></span>
                        <input type='text' id='email' name='email' class='form-control'/>
                    </div>
                    <div class='form-group float-label'>
                        <label id='password-label' for='password'>Password </label>
                        <span class="error-message"></span>
                        <input type='password' id='password' name='password' class='form-control'/>
                    </div>
                    <button type="submit" id='submit-button' class="btn btn-success">Login</button>
                    <!-- todo: forgot password, register -->
                    <!-- todo make separate register page, embed in marta army website -->
                    <!-- todo make registration form for on-spot sign up tomorrow -->
                </form>
            </div>

            <div class='col col-xs-12 col-md-4'>
                <h2>Sign Up</h2>
                <p>
                By joining the army, you can help make public transit better in your own neighborhoods. You can read all about it on our <a href='http://martaarmy.org/' target='_blank'>website</a>.<br/><br/>
                Not a soldier in the MARTA Army yet? <br/><br/>
                <a href='#' class='btn btn-success' id='join-now-btn'>Join the army now!</a><br/><br/>
                </p>
                <form id='signup-form' action='ajax/event-signup.php' method='POST'>
                    <div class='form-group float-label'>
                        <label for='name'>Name</label>
                        <span class="error-message"></span>
                        <input type='text' id='reg-name' name='name' class='form-control'/>
                    </div>
                    <div class='form-group float-label'>
                        <label for='email'>Email address</label>
                        <span class="error-message"></span>
                        <input type='text' id='reg-email' name='email' class='form-control'/>
                    </div>
                    <div class='form-group float-label'>
                        <label for='password'>Create a password</label>
                        <span class="error-message"></span>
                        <input type='password' id='reg-password' name='password' class='form-control'/>
                    </div>
                    
                    <button type="submit" class="btn btn-success">Sign Up</button>
                </form>

                <p id='msg'></p>
            </div>
        </div>
    </div>
    
    <?php
    $scripts = array('login.js', 'register.js');
    include('common/footer.php');
    ?>
 

