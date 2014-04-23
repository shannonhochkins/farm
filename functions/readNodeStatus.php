<?php
    
        
    $response = array();



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

    function cb_quote($v) {
        return '"'.trim($v[1]).'"';
    }  

    function exploreStatusFiles() {

        
        $directory = "/mnt/raid/farm_script/nodes_status/";
        if ( ! is_dir($directory)) {
            exit('Invalid diretory path');
        }
        
        foreach (scandir($directory) as $file) {

            if ('.' === $file) continue;
            if ('..' === $file) continue;

            $fileContents = file_get_contents("/mnt/raid/farm_script/nodes_status/" . $file, true);


            $json = trim(file_get_contents("/mnt/raid/farm_script/nodes_status/" . $file));

            $json = str_replace(array('{','}',':',','),array('{" ',' }','":',',"'),$json);

            $newJSON = preg_replace_callback("~\"(.*?)\"~","cb_quote", $json);
            $arr = json_decode($newJSON, true);
            if (isset($arr['startedCurrentFrameAt'])) {


                $t = (int) $arr['startedCurrentFrameAt'];

                $d = (time() - $t);
                $timeDiff = getElapsedTime($d);
            } else {
                $timeDiff = "N/A";
            }

            if (isset($arr['renderStartTime']) && isset($arr['renderFinishTime'])) {


                $startTime = (int) $arr['renderStartTime'];
                $endTime = (int) $arr['renderFinishTime'];

                $d = ($endTime - $t);
                $finishTime = getElapsedTime($d);

                $timeDiff = "Completed";
            } else {
                $finishTime = "N/A";
            }            

            $response[$file] = array(
                'contents' => $fileContents,
                'timeSinceStart' => $timeDiff,
                'completionTime' => $finishTime
            );
        }

        return json_encode($response);
    }








    echo exploreStatusFiles();
    

?>