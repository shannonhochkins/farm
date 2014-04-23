<?php
	


	
	function checkMissingFrames($job, $path) {
			$state = 'ALL';
			$files = array();
			$tempFiles = array();
			$newPath = str_replace("Volumes", "mnt", $path);			
			switch ($state) {
			    case 'ALL':
			    	if ( ! is_dir($newPath)) {
				        return array('status' => 'error', 'message' => 'invalidDirectory', 'directory' => $newPath);
				    } else {
				    	exec('ls ' . escapeshellarg($newPath) . ' | grep ' . $job, $files);
				    	$filesInPath = glob($newPath . "*");

				    	foreach($filesInPath as $file){ // iterate files
				    		
						  	if(is_file($file)) {
						  		if (filesize($file) == 128) {
						  			array_push($tempFiles, str_replace($newPath, "", $file));
						  		}
						  		
							}
						}
				    }
			        break;
			}
			return array('status' => 'success', 'filenames' => $files, 'temporaryFiles' => $tempFiles);
	}	
?>