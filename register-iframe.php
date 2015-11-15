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
    
    <!-- <link rel="shortcut icon" href="favicon.ico">  -->

    <!-- <link href='http://fonts.googleapis.com/css?family=Lato:300,400,300italic,400italic' rel='stylesheet' type='text/css'> -->
    <link href='https://fonts.googleapis.com/css?family=Raleway:400,300,100,200' rel='stylesheet' type='text/css'>    

    <!-- <link rel="stylesheet" href="jslib/bootstrap/css/bootstrap.min.css">   
    <link rel="stylesheet" href="jslib/font-awesome/css/font-awesome.css">
    <link rel="stylesheet" href="jslib/ionicons/css/ionicons.css">     -->
    
    <!-- Theme CSS -->  
    <style type="text/css">
    label, p {
        font-family: Garamond,Baskerville,Baskerville Old Face,Hoefler Text,Times New Roman,serif; 
        font-size: 18px;
        color: rgba(26,26,26,0.7);
        display: block;
    }
    input {
        width: 90%;
        display: block;
        font-family: sans-serif;
        font-size: 12px;
        padding: 12px;
        margin: 6px 0 28px 0;
        border: 1px solid #ccc;
        background-color: #fafafa;
    }
    input:focus { outline: 0;}

    button {
        text-transform: uppercase;
        color: #fff;
        background-color: #1a1a1a;
        border: none;
        font-family: raleway;
        padding: 10px 15px;
        font-size: 14px;
        letter-spacing: 2px;
    }
    #success-message {
        display: none;
    }
    #error-message {
        display: none;
        color: #f77;
    }
    </style>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    
</head> 

<body>

    <p id='error-message'></p>

    <form id='signup-form'>
        <div class='form-group float-label'>
            <label for='name'>Name *</label>
            <input type='text' id='name' name='name' class='form-control'/>
        </div>
        <div class='form-group float-label'>
            <label for='email'>Email address *</label>
            <input type='text' id='email' name='email' class='form-control'/>
        </div>
        <div class='form-group float-label'>
            <label for='stoptoadopt'>Address of bus stop(s) you want to adopt *</label>
            <input type='text' id='stoptoadopt' name='stoptoadopt' class='form-control'/>
        </div>
        <div class='form-group float-label'>
            <label for='comment'>Comment (Anything - why you want to adopt a stop, why you like transit, etc) *</label>
            <input type='text' id='comment' name='comment' class='form-control'/>
        </div>
        
        <button type="submit" class="btn btn-success" >Sign Up</button>
    </form>
    <p id='success-message'>Thank you! We'll get in touch with you asap.</p>

    
    <!-- Javascript -->          
    <script type="text/javascript" src="jslib/jquery-2.1.4.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/json2/20150503/json2.min.js"></script>
    <script type="text/javascript" src="js/register-iframe.js"></script>
    
</body>
</html> 

