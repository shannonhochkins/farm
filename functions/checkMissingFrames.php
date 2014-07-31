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
				    	$totalFiles = (int) sizeof($filesInPath);
				    	$average = 0;
				    	$t = 0;
				    	foreach($filesInPath as $file){ // iterate files
				    		
						  	if(is_file($file)) {
						  		$average = filesize($file);
						  		if (filesize($file) < 200) {
						  			// Could write file size == 128, however it's possible that the file size can start writing and fail and write a bit more
						  			// Safe to say that all rendered images should be over 200kb...
						  			array_push($tempFiles, str_replace($newPath, "", $file));
						  		}
						  		
							}
						}
						
						$average /= $totalFiles;
				    }
			        break;
			}
			return array('status' => 'success', 'filenames' => $files, 'temporaryFiles' => $tempFiles, 'test' => $average);
	}	
?>