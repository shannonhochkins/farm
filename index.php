<?php
    if (isset($_GET['action']) && $_GET['action'] == 'checkStatus') {
        include('functions/checkJobStatus.php');
        $array = checkStatus();


        echo json_encode($array);
        exit();

    }

    if (isset($_GET['action']) && $_GET['action'] == 'checkMissingFrames') {        
        if (isset($_GET['job']) && isset($_GET['dir'])) {            
            include('functions/checkMissingFrames.php');
            $array = checkMissingFrames($_GET['job'], $_GET['dir']);
            echo json_encode($array);
            exit();
            //exit();
        }
        

    }    
    
    

?>


<!DOCTYPE html>
<!--[if IEMobile 7 ]> <html dir="ltr" lang="en-US"class="no-js iem7"> <![endif]-->
<!--[if lt IE 7 ]> <html dir="ltr" lang="en-US" class="no-js ie6 oldie"> <![endif]-->
<!--[if IE 7 ]>    <html dir="ltr" lang="en-US" class="no-js ie7 oldie"> <![endif]-->
<!--[if IE 8 ]>    <html dir="ltr" lang="en-US" class="no-js ie8 oldie"> <![endif]-->
<!--[if (gte IE 9)|(gt IEMobile 7)|!(IEMobile)|!(IE)]><!--><html dir="ltr" lang="en-US" class="no-js"><!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Limeworks Farm Manager</title>
        <meta name="description" content="">
        <meta name="keywords" content="">
        <meta name="author" content="" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="favicon.ico">
        <!-- CSS -->
        <link rel="stylesheet" href="css/dark-hive/jquery-ui-1.10.4.custom.css">
        <link rel="stylesheet" href="css/main/main.css">     

        <link rel="stylesheet" href="css/bootstrap-theme.min.css">        
        <link rel="stylesheet" href="css/bootstrap.min.css"> 
        
        <!-- JS -->
        <script type="text/javascript" src="js/vendor/modernizr-2.6.2.min.js"></script>
        <script type="text/javascript" src="js/vendor/jquery-1.9.1.js"></script>
        <script type="text/javascript" src="js/plugins.js"></script>
        <script type="text/javascript" src="js/bootstrap.min.js"></script> 
        <script type="text/javascript" src="js/jquery.tablesorter.js"></script>         
        <script type="text/javascript" src="js/jquery-ui-1.10.4.custom.js"></script>
        <script type="text/javascript" src="js/main.js"></script>


    </head>
    <body>      

        <?php include('functions/getUserIP.php'); ?>
        <?php include('functions/readNodeResponses.php'); ?>
  
        <section id="mainContent">
            <div class="inner gridContainer">
                <div class="grid16">
                    <div class="floatLeft">
                        <h3>Limeworks Farm Manager</h3>
                    </div>
                    <div class="floatRight">
                        <h3><?php echo $realIpAddress;?></h3>
                    </div>
                </div>
                <div class="grid16 noMargin">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#jobs" data-toggle="tab"  >Jobs</a></li>
                        <li><a href="#nodes" data-toggle="tab">Nodes</a></li>
                        <li><a href="#nodeGroups" data-toggle="tab">Node Groups</a></li>
                        <li><a href="#manager" data-toggle="tab">Manager</a></li>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane active" id="jobs">
                            <div class="grid16" id="jobsNavigation">
                                <p>Jobs</p>                                
                                <button type="button" class="btn btn-default  btn-sm" onclick="deleteSelectedJobsFromList()">Delete Selected</button>
                                <p>Frames</p>                                
                                <button type="button" class="btn btn-default  btn-sm" onclick="missingFramesWindow();">Check Missing</button>
                                
                                <p>Auto Refresh every</p>
                                <input type="number" value="60" min="1" id="refreshRate">
                                <p>Seconds</p>
                                <button type="button" class="btn btn-default  btn-sm" onclick="readStatus();">Refresh Now</button>
                            </div>
                            <table class="table table-striped table-bordered table-hover verticalMargin tablesorter" id="jobTable">
                                <thead>
                                    <tr>
                                        <th class="nosort"><input type="checkbox" id="selectAll" /></th>
                                        <th>Name</th>
                                        <th>Assigned to</th>                                        

                                        <th>% Done</th>

                                        <th>Current Frame</th>
                                        <th>Frame Range</th>
                                        <th>Frame Elapsed For</th>
                                        <!--<th>Type</th>
                                        <th>Description</th>-->
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane" id="nodes">
                            <div class="grid16" id="nodesNavigation">
                                <p>Servers</p>
                                <form action="javascript:void(0);" method="post" id="nodes"> 
                                    <button type="button" class="btn btn-default btn-sm" name="refreshAllNodesBtn" id="refreshAllNodesBtn">Refresh</button>
                                    <button type="button" class="btn btn-default btn-sm">Settings</button>
                                    <button type="button" class="btn btn-default btn-sm">Delete</button>
                                    <button type="button" class="btn btn-default btn-sm">Show Current Job</button>
                                </form>
                            </div>
                            <table class="table table-striped table-bordered table-hover tablesorter" id="listingOfNodes">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>IP Address</th>
                                        <th>Status</th>
                                        <th>Last Updated</th>
                                        <th>Group</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php echo $nodeTable;?>
                                </tbody>
                                
                            </table>
                        </div>
                        <div class="tab-pane" id="nodeGroups">
                            <div class="grid16" id="nodeGroupsNavigation">
                                <p>Groups</p>
                                <button type="button" class="btn btn-default btn-sm" id="refreshAllNodesBtn">Refresh</button>
                                <button type="button" class="btn btn-default btn-sm">Modify</button>
                                <button type="button" class="btn btn-default btn-sm">Create</button>
                                <button type="button" class="btn btn-default btn-sm">Delete</button>
                            </div>
                            <table class="table table-striped table-bordered table-hover" id="listingOfGroups">
                                <thead>
                                    <tr>
                                        <th>Group Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php echo $nodeGroupTable;?>
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane" id="manager">
                            <div class="clearfix"></div>
                            <div class="grid3">&nbsp;</div>
                            <div class="grid13">
                                <h3>Logging and Information</h3>
                            </div>
                            <div class="grid3">
                                <p>Logging Level</p>
                            </div>
                            <div class="grid13">
                                <select class="form-control" name="verbosity" id="">
                                    <option value="info">Info Messages</option>
                                    <option value="error">Error Messages</option>
                                    <option value="warning">Warning Messages</option>
                                    <option value="debug">Debug Messages</option>
                                    <option value="exDebug">Extended Debug Messages</option>
                                </select>
                            </div>
                            <div class="clearfix"></div>
                            <div class="grid3">Defaul Mail Server</div>
                            <div class="grid13">
                                <input type="text" class="form-control">
                            </div>
                            <div class="clearfix"></div>
                            <div class="grid3">&nbsp;</div>
                            <div class="grid13">
                                <h3>Server Assignment</h3>
                            </div>
                            <div class="grid3">Max Concurrent Jobs</div>
                            <div class="grid13">
                                <input type="number" min="1" value="10" class="floatLeft">
                                <p class="floatLeft">Maxium number of active jobs</p>
                            </div>
                            <div class="clearfix"></div>
                            <div class="grid3">&nbsp;</div>
                            <div class="grid13">
                                <h3>Task Failures</h3>
                            </div>
                            <div class="grid3">Retry Count</div>
                            <div class="grid13">
                                <input type="number" min="1" value="10" class="floatLeft">
                                <p class="floatLeft">Number of times to resend failed tasks to a node.</p>
                            </div>
                            <div class="grid3">Time Between Retries</div>
                            <div class="grid13">
                                <input type="number" min="1" value="30" class="floatLeft">
                                <p class="floatLeft">Cool off period (in seconds) of failed servers.</p>
                            </div>
                            <div class="clearfix"></div>
                            <div class="grid3">&nbsp;</div>
                            <div class="grid13">
                                <h3>Job Handling</h3>
                            </div>
                            <div class="grid3">On Job Completion</div>
                            <div class="grid13">
                                <input type="radio" name="jobCompletion" id="leave">
                                <label for="leave">Leave</leave>                                
                                <input type="radio" name="jobCompletion"  id="archive">
                                <label for="leave">Archive</leave>                                
                                <input type="radio" name="jobCompletion"  id="delete">
                                <label for="leave">Delete</leave>                                
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>           
        </section>
        <div id="dialog-confirm" title="Are you sure you?" style="display:none;">
            <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>These list items will be permanently deleted and wont be re-created until the next frame starts for each job.</p>
        </div>
        <div id="dialog-missing" title="Job Frame Information" style="display:none;">
            
        </div>
    </body>
</html>

<!-- http://bootswatch.com/slate/ -->
