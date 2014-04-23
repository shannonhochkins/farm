<?php
    
    
    
    if ($_POST['state'] == "reccurssive"){
        ## This state is invoked when an ajax call, runs the refresh function ever 7 seconds.
        $nTable = exploreResponseFiles('nodes');     
        $gTable = exploreResponseFiles('groups');
        $ngTable = writeGroupsTable($gTable);
        ## Add a dividor between the groups and the nodes, so that we can split the string for each table.
        echo($nTable . "=====" . $ngTable);
    }
    
    $nodeGroupTable = "";
    $nodeTable = "";
    $nodeGroups = array();
    $response = array();

    ## This function will return a string of a table row, 
    ## with the information for the current node passed to it
    ## -----------------------------------------------


    function writeNodesTable($hostName, $ip, $time, $status, $group) {

        $out = "";
        $statusClass = ($status == 0 ? "Dead" : "Ready");
        $statusText = ($status == 0 ? "Offline" : "Online");

        $td = (time() - $time);
        $timeDiff = getElapsedTime($td);

        $out .= "<tr data-refreshString=".$ip . "," .  $hostName . "," . $group .">";
        $out .= "<td>". $hostName ."</td>";
        $out .= "<td>192.168.". $ip . "</td>";
        $out .= "<td class=". $statusClass . ">". $statusText . "</td>";
        $out .= "<td>". $timeDiff ."</td>";
        $out .= "<td>". $group ."</td>";
        $out .= "</tr>";
        return $out;
    }

    function exploreResponseFiles($return) {

        $nodeTable = "";
        $directory = "/mnt/raid/farm_script/nodes_response/";
        if ( ! is_dir($directory)) {
            exit('Invalid diretory path');
        }
        echo $nodeTable;
        foreach (scandir($directory) as $file) {

            if ('.' === $file) continue;
            if ('..' === $file) continue;

            $fileContents = file_get_contents("/mnt/raid/farm_script/nodes_response/" . $file, true);

            $fileExploded = explode(",", $fileContents);

            $response[$file] = array(
                "time" => $fileExploded[0],
                "host" => $fileExploded[1],
                "status" => $fileExploded[2],
                "hostName" => $fileExploded[3],
                "group" => $fileExploded[4],
            );
        }

        ## Loop over the values and build our table into a variable.
        ## -----------------------------------------------
        
        $values = array_values($response);
        for($i =0; $i < count($response); ++$i) {
            $currentValues = array_values($values[$i]);        
            $nodeTable .= writeNodesTable($currentValues[3], $currentValues[1], $currentValues[0], $currentValues[2], $currentValues[4]);            
            $nodeGroups[] = $currentValues[4];
        }

        switch($return) {
            case 'groups':
                return $nodeGroups;
                break;
            case 'nodes':
                return $nodeTable;
                break;
        }
    }


    

    ## This function will return a string of a table row, 
    ## With the farm groups, as well as each node belonging to each group.
    ## -----------------------------------------------

    
    function writeGroupsTable($array) {   
        $out = "";     
        $array = array_unique($array);
        foreach($array as $item) {
            $out .= "<tr>";
            $out .= "<td>Limeworks ". $item ."</td>";
            $out .= "</tr>";
        }
        return $out;
    }




    ## This function will get the time elapsed from two unix timestamps
    ## -----------------------------------------------

    function getElapsedTime($secs){
        $bit = array(
            ' Years' => $secs / 31556926 % 12,
            ' Weeks' => $secs / 604800 % 52,
            ' Days' => $secs / 86400 % 7,
            ' Hours' => $secs / 3600 % 24,
            ' Minute' => $secs / 60 % 60,
            ' Seconds' => $secs % 60
            );
            
        foreach($bit as $k => $v)
            if($v > 0)$ret[] = $v . $k;
            
        return join(' ', $ret);
    }


    ## Refresh the state of the nodes
    ## -----------------------------------------------

    function refreshNodes($node){
        if (empty($_GET['node'])) {
            $node = "";
            echo "test";
        }
        system("sh /mnt/raid/farm_script/_apps/_scripts/farm_ping.sh" . $node);
    }

    ## Store the tables in a variable for later use.

    $nodeTable = exploreResponseFiles("nodes");
    $nodeGroups = exploreResponseFiles('groups');
    $nodeGroupTable = writeGroupsTable($nodeGroups);

    

?>