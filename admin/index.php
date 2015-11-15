<?php
include('../lib/db.php');
include('../lib/admindb.php');
init_db();

if(!isset($_COOKIE["adminsid"])) {
    header('Location: login.php');
    exit();
}

if(!validateAdminSession($_COOKIE["adminsid"])) {
    header('Location: login.php');
    exit();
}



$soldiers = getTimelyTripSoldiers();

$stats = getTimelyTripStats($soldiers);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>MARTA Army Admin</title>

    <link href="../jslib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../jslib/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <link href="../css/admin/sb-admin.css" rel="stylesheet">
    <link href="../css/admin/timelytrip.css" rel="stylesheet">
    <link id="theme-style" rel="stylesheet" href="../css/float-label.css">
    

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <div id="wrapper">
        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.html">MARTA Army</a>
            </div>
            <!-- Top Menu Items -->
            <ul class="nav navbar-right top-nav">
                <!-- <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-dashboard"></i> Operation TimelyTrip <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="#"><i class="fa fa-fw fa-power-off"></i> Operation TimelyTrip</a>
                        </li>
                    </ul>
                </li> -->

                <li><a href='#'><i class="fa fa-dashboard"></i> Operation TimelyTrip</a></li>
                <li>
                    <a href="logout.php"><i class="fa fa-key"></i> Logout</a>
                </li>
                
            </ul>
        </nav>

        <div id="page-wrapper">
            <div class="container-fluid">

                <!-- Page Heading -->
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">
                            Operation TimelyTrip <small>Operation Dashboard</small>
                        </h1>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-3">
                        <div class="panel panel-green">
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <i class="fa fa-users fa-5x"></i>
                                    </div>
                                    <div class="col-xs-9 text-right">
                                        <div class="huge"><?php echo $stats['num_soldiers']; ?></div>
                                        <div>Total soldiers</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-yellow">
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <i class="fa fa-bus fa-5x"></i>
                                    </div>
                                    <div class="col-xs-9 text-right">
                                        <div class="huge"><?php echo $stats['num_stops']; ?></div>
                                        <div>Stops adopted</div>
                                    </div>
                                </div>
                            </div>
                        </div> 

                        <div><a class='btn btn-primary' href='#' id='new-soldier-button'>Register new soldier</a></div>                      
                    </div>

                    <div class="col-sm-4">
                        <table class="table table-bordered table-hover table-striped">
                            <tr><td colspan=2>Stops without signs</td><td><?php echo $stats['num_nosign']; ?></td></tr>
                            <tr><td rowspan=3>Stops pending audit..</td><td>With no tasks</td><td>--</td></tr>
                            <tr><td>With pending tasks</td><td>--</td></tr>
                            <tr><td>With overdue tasks</td><td>--</td></tr>
                            <tr><td rowspan=3>Up-to-date Stops</td><td>With no tasks</td><td>--</td></tr>
                            <tr><td>With pending tasks</td><td>--</td></tr>
                            <tr><td>With overdue tasks</td><td>--</td></tr>
                        </table>
                    </div>
                </div>
                <!-- /.row -->

                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title"><i class="fa fa-user fa-fw"></i> Soldiers</h3>
                            </div>
                            <div class="panel-body">
                                <div class="text-left">
                                    <button id='get-emails' class='btn btn-primary'><i class='fa fa-envelope'></i> Get Emails</button>
                                    <p></p>
                                </div>
                                <div class="table-responsive">
                                    <table id='soldiers-table' class="table table-bordered table-hover table-striped">
                                        <thead>
                                            <tr>
                                                <th rowspan=2>Select <br/><a id='select-all-soldiers' href='#'>All</a> | <a id='select-none-soldiers' href='#'>None</a></th>
                                                <th rowspan=2>Join Date <br/>&amp; time</th>
                                                <th rowspan=2>Name</th>
                                                <th rowspan=2>Stops without signs<br/> <a id='select-nosign-all-soldiers' href='#'>All</a> | <a id='select-nosign-none-soldiers' href='#'>None</a></th>
                                                <th colspan=3>Stops with pending audit, having...</th>
                                                <th rowspan=2>Up-to-date stops</th>
                                                <th rowspan=2>Actions</th>
                                            </tr>
                                            <tr>
                                                <th>no tasks<br/> <a id='select-sign-notask-all-soldiers' href='#'>All</a> | <a id='select-sign-notask-none-soldiers' href='#'>None</a></th>
                                                <th>pending tasks</th>
                                                <th>overdue tasks</th>
                                            </tr>
                                        </thead>
                                        <tbody>
<?php
    foreach($soldiers as $s) {
        $userid = $s['id'];
        $name = $s['name'];
        $email = $s['email'];
        $phone = $s['phone'];
        $notes = $s['notes'];
        $joindate = $s['joindate']->format('j-M-Y') . "<br/>" . $s['joindate']->format('g:iA');

        $notesclass = '';
        if(!is_null($notes)) {
            $notes = htmlentities($notes);
            $notesclass = 'hasnotes';
        }

        $notgivenhtml = '';
        foreach($s['stops_notgiven'] as $st) {
            $notgivenhtml .= getStopHtml($st, 'not-given');
        }

        $notaskshtml = '';
        foreach($s['stops_notasks'] as $st) {
            $notaskshtml .= getStopHtml($st, 'no-tasks');
        }

        $pendingtaskshtml = '';
        foreach($s['stops_pendingtasks'] as $st) {
            $pendingtaskshtml .= "<span data-index='0' class='stop'>Ponce.. @ Peach..</span>";
        }

        $overduetaskshtml = '';
        foreach($s['stops_overduetasks'] as $st) {
            $overduetaskshtml .= "<span data-index='0' class='stop'>Ponce.. @ Peach..</span>";
        }

        $uptodatehtml = '';
        foreach($s['stops_uptodate'] as $st) {
            $uptodatehtml .= "<span data-index='0' class='stop'>Ponce.. @ Peach..</span>";
        }

        echo <<<SOLDIER_ROW
                                            <tr data-userid='$userid'>
                                                <td class='selection'><input type='checkbox'/></td>
                                                <td class='join-date'>$joindate</td>
                                                <td class='user-data $notesclass'>
                                                    <a class='soldier-name' href='#'>$name</a>
                                                    <span class='email'>$email</span>
                                                    <span class='phone'>$phone</span>
                                                    <span class='notes'>$notes</span>
                                                </td>
                                                <td class='notgiven-td'>$notgivenhtml</td>
                                                <td class='notask-td'>$notaskshtml</td>
                                                <td>$pendingtaskshtml</td>
                                                <td>$overduetaskshtml</td>
                                                <td>$uptodatehtml</td>
                                                <td><a href='#' class='addstoplink'>Add Stop</a></td>
                                            </tr>
SOLDIER_ROW;
    }

    function getStopHtml($st, $extraclass) {
        $stopname = trim($st['name']);
        if(empty($stopname)) {
            $stopname = '(no name)';
            $extraclass .= ' noname ';
        }

        $id = $st['id'];
        $stopid = $st['stopid'];
        $agency = $st['agency'];
        $stopgiven = $st['given'] ? 'true' : 'false';
        $nameonsign = $st['given'] ? $st['nameonsign'] : '';
        $abandoned = $st['abandoned'];
        
        if(is_null($stopid)) {
            $extraclass .= " nostopid ";
            $stopid = '';
        }

        if($abandoned) {
            $extraclass .= " abandoned ";
        }

        return <<<STOP
        <span class='stop $extraclass'>
            <span class='id'>$id</span>
            <span class='name'>$stopname</span>
            <span class='stopid'>$stopid</span>
            <span class='agency'>$agency</span>
            <span class='given'>$stopgiven</span>
            <span class='nameonsign'>$nameonsign</span>
        </span>
STOP;
    }
?>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.row -->

            </div>
            <!-- /.container-fluid -->

        </div> <!-- /#page-wrapper -->
        

    </div> <!-- /#wrapper -->

    <div class='modal fade' id='stopdetail-modal'>
        <div class="modal-dialog">
            <div class='modal-content'>
                <div class='modal-header'>
                    <h4 class='modal-title operation-title'>Update Stop Details</h4>
                </div>
                <div class='modal-body'>
                    
                    <form>
                        <div class='form-group float-label stopname'>
                            <label>Stop Name</label>
                            <span class="error-message"></span>
                            <input type='text' class='form-control'/>
                        </div>
                        <div class='form-group float-label stopid'>
                            <label>Stop Id</label>
                            <span class="error-message"></span>
                            <input type='text' class='form-control'/>
                        </div>
                        <div class='form-group agency'>
                            <label>Agency</label>
                            <select>
                                <option value=''>Not Set</option>
                                <option value='MARTA'>MARTA</option>
                                <option value='CCT'>CCT</option>
                                <option value='GRTA'>GRTA</option>
                            </select>
                        </div>
                        <div class='form-group given'>
                            <span class="error-message"></span>
                            <input type='checkbox' class='form-control'/>
                            <label>Sign Given</label>
                        </div>
                        <div class='form-group float-label nameonsign'>
                            <label>Name on sign</label>
                            <span class="error-message"></span>
                            <input type='text' class='form-control'/>
                        </div>
                        <div class='form-group abandoned'>
                            <span class="error-message"></span>
                            <input type='checkbox' class='form-control'/>
                            <label>Stop abandoned</label>
                        </div>

                    </form>
                </div>
                <div class='modal-footer'>
                    <button type='button' class='btn btn-default get-sign'>Get Sign</a>
                    <button type='button' class='btn btn-default' data-dismiss='modal'>Cancel</a>
                    <button type='button' class='btn btn-primary stopdetail-submit'>Update</button>
                </div>
            </div>
        </div>
    </div>

    <div class='modal fade' id='newsoldier-modal'>
        <div class="modal-dialog">
            <div class='modal-content'>
                <div class='modal-header'>
                    <h4 class='modal-title operation-title'>Register New Soldier</h4>
                </div>
                <div class='modal-body'>

                    <form>
                        <div class='form-group float-label' id='soldiername'>
                            <label>Name</label>
                            <span class="error-message"></span>
                            <input type='text' class='form-control'/>
                        </div>
                        <div class='form-group float-label' id='soldieremail'>
                            <label>Email</label>
                            <span class="error-message"></span>
                            <input type='text' class='form-control'/>
                        </div>
                        <div class='form-group float-label' id='soldierphone'>
                            <label>Phone (optional)</label>
                            <span class="error-message"></span>
                            <input type='text' class='form-control'/>
                        </div>
                        <div class='form-group float-label' id='soldierbusstop'>
                            <label>Stop to adopt</label>
                            <span class="error-message"></span>
                            <input type='text' class='form-control'/>
                        </div>
                        <div class='form-group' id='soldieragency'>
                            <label>Agency</label>
                            <select><option value='MARTA'>MARTA</option><option value='CCT'>CCT</option><option value='GRTA'>GRTA</option></select>
                        </div>
                        <div class='form-group float-label' id='soldiernotes'>
                            <label>Notes</label>
                            <span class="error-message"></span>
                            <input type='text' class='form-control'/>
                        </div>

                    </form>
                </div>
                <div class='modal-footer'>
                    <button type='button' class='btn btn-default' data-dismiss='modal'>Cancel</a>
                    <button type='button' class='btn btn-primary' id='newsoldier-submit'>Register</button>
                </div>
            </div>
        </div>
    </div>

    <div class='modal fade' id='addstop-modal'>
        <div class="modal-dialog">
            <div class='modal-content'>
                <div class='modal-header'>
                    <h4 class='modal-title operation-title'>Add Adopted Stop</h4>
                </div>
                <div class='modal-body'>

                    <form>
                        <div class='form-group float-label stopname'>
                            <label>Stop Name</label>
                            <span class="error-message"></span>
                            <input type='text' class='form-control'/>
                        </div>
                        <div class='form-group float-label stopid'>
                            <label>Stop Id</label>
                            <span class="error-message"></span>
                            <input type='text' class='form-control'/>
                        </div>
                        <div class='form-group agency'>
                            <label>Agency</label>
                            <select><option value='MARTA'>MARTA</option><option value='CCT'>CCT</option><option value='GRTA'>GRTA</option></select>
                        </div>

                    </form>
                </div>
                <div class='modal-footer'>
                    <button type='button' class='btn btn-default' data-dismiss='modal'>Cancel</a>
                    <button type='button' class='btn btn-primary addstop-submit'>Add Stop</button>
                </div>
            </div>
        </div>
    </div>

    <div class='modal fade' id='email-list-modal'>
        <div class="modal-dialog">
            <div class='modal-content'>
                <div class='modal-header'>
                    <h4 class='modal-title operation-title'>Selected Emails</h4>
                    <p>Select these emails and copy them</p>
                </div>
                <div class='modal-body'>
                    
                </div>
                <div class='modal-footer'>
                    <button type='button' class='btn btn-default' data-dismiss='modal'>Okay</a>
                </div>
            </div>
        </div>
    </div>

    <div class='modal fade' id='soldier-details-modal'>
        <div class="modal-dialog">
            <div class='modal-content'>
                <div class='modal-header'>
                    <h4 class='modal-title operation-title'>Soldier details</h4>
                </div>
                <div class='modal-body'>
                    <form>
                        <div class='form-group float-label soldiername'>
                            <label>Name</label>
                            <span class="error-message"></span>
                            <input type='text' class='form-control'/>
                        </div>
                        <div class='form-group float-label soldieremail'>
                            <label>Email</label>
                            <span class="error-message"></span>
                            <input type='text' class='form-control'/>
                        </div>
                        <div class='form-group float-label soldierphone'>
                            <label>Phone</label>
                            <span class="error-message"></span>
                            <input type='text' class='form-control'/>
                        </div>
                        <div class='form-group float-label soldiernotes'>
                            <label>Notes</label>
                            <span class="error-message"></span>
                            <textarea class='form-control'></textarea>
                        </div>
                        <div class='form-group float-label soldierjoindate'>
                            <label>Join Date (yyyy-mm-dd) </label>
                            <span class="error-message"></span>
                            <input type='text' class='form-control'/>
                        </div>
                    </form>
                </div>
                <div class='modal-footer'>
                    <button type='button' class='btn btn-default' data-dismiss='modal'>Okay</a>
                    <button type='button' class='btn btn-primary update-soldierdetails'>Update</a>
                </div>
            </div>
        </div>
    </div>

    
    <script type="text/javascript" src="../jslib/jquery-2.1.4.min.js"></script>
    <script type="text/javascript" src="../jslib/bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../js/admin/timelytrip.js"></script>
    <script type="text/javascript" src="../js/float-label.js"></script>
</body>

</html>

<?php
function getTimelyTripStats($soldiers) {
    
    function getStopsFromSoldier($s) { 
        return array_merge(
            $s['stops_notgiven'], $s['stops_notasks'], $s['stops_pendingtasks'], 
            $s['stops_overduetasks'], $s['stops_uptodate']
        );
    }

    $allstops_2d = array_map("getStopsFromSoldier", $soldiers);
    $allstops = array();

    $nosign_count = 0;

    foreach($allstops_2d as $stopsarr) {
        foreach($stopsarr as $stop) {
            if(!$stop['given']) $nosign_count++;

            $allstops[] = $stop;
        }
    }

    return array('num_soldiers'=>count($soldiers), 'num_stops'=>count($allstops), 'num_nosign'=>$nosign_count);
}

?>
