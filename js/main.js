$(document).ready(function() {

    var updateTablesEvery = 5000;
    var updateStatusEvery = parseInt($('#refreshRate').val()) * 1000;


    $('body').on('change', '#selectAll', function() {

        var checked = $(this).is(":checked");
        console.log(checked)
        $('#jobTable [id^="checkbox_"]').prop('checked', checked);

    });



    function refreshNodes(state) {


        var hr = new XMLHttpRequest();
        var url = "functions/refreshNodes.php";
        var vars = "state=" + state;

        if (state == "reccurssive") {
            url = "functions/readNodeResponses.php";
        }

        hr.open("POST", url, true);
        hr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        // Access the onreadystatechange event for the XMLHttpRequest object
        hr.onreadystatechange = function() {
            if (hr.readyState == 4 && hr.status == 200) {

                var returnData = hr.responseText;
                if (state == "reccurssive") {
                    // We currently are refreshing the data in the table with a timeout set at the top ^^
                    $('#listingOfNodes tbody').html(returnData.split("=====")[0]);
                    $('#listingOfGroups tbody').html(returnData.split("=====")[1]);
                } else {
                    $('#listingOfNodes tbody').html(returnData);
                }
            }
        }
        // Send the data to PHP now... and wait for response to update the status div
        hr.send(vars); // Actually execute the request

        if (state == "ALL") {
            $("#listingOfNodes tbody").html("processing...");
        }

    }




    $('#refreshAllNodesBtn').on('click', function() {
        refreshNodes("ALL");
    });



    setInterval(function() {
        refreshNodes("reccurssive");
    }, updateTablesEvery);

    setInterval(function() {
        readStatus();
        updateStatusEvery = parseInt($('#refreshRate').val()) * 1000;
    }, updateStatusEvery);
    readStatus();





});

function emptyList(s) {

    $("#dialog-confirm").dialog({
        resizable: false,
        height: 200,
        width: 500,
        modal: true,
        buttons: {
            "Delete": function() {
                deleteNodeFrameInfo(s);
            },
            Cancel: function() {
                $(this).dialog("close");
            }
        }
    });
}

function deleteSelectedJobsFromList() {
    var totalCheckboxes = $('#jobTable [id^="checkbox_"]');
    var selectedRows = $('#jobTable [id^="checkbox_"]:checked');

    if (totalCheckboxes.length == selectedRows.length) {
        emptyList('ALL');
    } else {
        selectedRows.each(function() {
            var baseIP = $(this).closest('tr').attr('data-base-ip');
            var file = "192.168." + baseIP + ".txt";
            if (typeof(baseIP) != 'undefined') {
                deleteNodeFrameInfo(file);
            }

        });
    }
}

function deleteNodeFrameInfo(s) {
    var d = "state=" + s;

    $.ajax({
        type: 'post',
        url: 'functions/refreshStatus.php',
        data: d,
        success: function(data, textStatus, jqXHR) {
            readStatus();
        },
        error: function(jqXHR, textStatus, errorThrown) {

        }
    });
}

function missingFramesWindow() {
    var dialog = $("#dialog-missing");
    var jobTableRows = $('#jobTable tbody tr');
    var differentJobs = [];
    for (var i = 0; i < jobTableRows.length; i++) {
        var jobRow = jobTableRows.eq(i);

        var currentJob = jobRow.attr("data-job-name");
        var currentPath = jobRow.attr("title").replace("Render Directory: ", '');
        if (currentJob != "N/A" || currentPath != "N/A") {

            if (!isKeyInArray(differentJobs, currentJob, currentPath)) {
                differentJobs.push({
                    'job': currentJob,
                    'path': currentPath
                });
            }
        }

    }


    var out = '<table class="table table-striped table-bordered table-hover verticalMargin tablesorter" id="missingFramesTable"><thead><tr><th>Job</th><th>Frames</th></tr></thead><tbody>';
    $.each(differentJobs, function(k, v) {
        out += '<tr title="' + v['path'] + '">';
        out += '<td>';
        out += v['job'];
        out += '</td>';
        out += '<td>';
        out += checkMissingFrames(v['job'], v['path']);
        out += '</td>';
        out += '</tr>';
    });
    out += '</tbody></table>';

    dialog.html(out);

    setupTablesorter($("#missingFramesTable"));
    dialog.dialog({
        resizable: false,
        width: 1000,
        modal: true,
        buttons: {
            Cancel: function() {
                $(this).dialog("close");
            }
        }
    });
}

var isKeyInArray = function(obj, job, path) {
    var returnKey = false;

    $.each(obj, function(key, info) {
        if (info['job'] == job) {
            if (info['path'] == path) {
                returnKey = true;
            } else {
                returnKey = false;
            }
        };

    });
    return returnKey;

}




var frames = [];


function checkMissingFrames(job, imageDirectory) {

    var fileNames = [];
    var tempFiles = [];
    var fileString = '';
    var missingFiles = '';
    $.ajax({
        type: "GET",
        url: '?action=checkMissingFrames&job=' + job + '&dir=' + imageDirectory,
        async: false,
        success: function(data) {
            var obj = eval('(' + data + ')');
            console.log(obj);
            if (obj.status == 'success') {



                fileNames = obj['filenames'];
                tempFiles = obj['temporaryFiles'];


                fileString = formatFileArrayString(fileNames, 'missing');
                missingFiles = formatFileArrayString(tempFiles, 'dead');
                //console.log(missingFiles);
            } else if (obj.status == 'error') {
                fileString = obj.message + ' : ';
                missingFiles = obj.directory;
            }

        }

    });

    return fileString + missingFiles;


}

function sortNumber(a, b) {
    return a - b;
}

function formatFileArrayString(array, type) {
    if (array.length > 0) {
        // Sort the array to prevent any incorrect numbering
        array.sort();
        var paddingArray = [];
        // Regex to match the padding number at the end before an extension
        // Matches all numbers (\d+) followed by a dot (\.) which is followed by letters or an extension ([a-zA-Z]+)
        var regex = /\d+\.[a-zA-Z]+/;
        var arrayLength = array.length;
        var lastFrame = array[arrayLength - 1];
        var firstFrame = array[0];
        // Match our frame padding
        lastFrame = parseInt(lastFrame.match(regex)[0].split('.')[0]);
        firstFrame = parseInt(firstFrame.match(regex)[0].split('.')[0]);
        // Calculate our start and end difference
        var loopLength = lastFrame - firstFrame;

        // Create the new array based on the padding of the 
        for (var i = 0; i < arrayLength; i++) {
            paddingArray[i] = parseInt(array[i].match(regex)[0].split('.')[0]);
        }
        // Sort the array to prevent any incorrect numbering
        paddingArray.sort(sortNumber);

        // Loop over our new array, check if we have any missing frames, store them in an array;
        if (type == "missing") {
            return formatIntArrayString(loopLength, firstFrame, lastFrame, paddingArray);
        }
        // Spit out the dead or rendering files found
        if (type == "dead") {
            return "<br /> Presumed Rendering or Dead files: " + paddingArray.toString();
        }
    } else {
        return '';
    }



}


function formatIntArrayString(loop, first, last, arr) {


    var missingArrayIndex = 0;
    var missingFrames = new Array();
    var missingFramesString = "";
    for (var i = 0; i <= loop; i++) {

        currentFrame = first + i;

        if (!isInArray(currentFrame, arr)) {

            missingFrames[missingArrayIndex] = currentFrame;

            // if the currentFrame is not the last missingFrame+1, then it is new sequence

            if (currentFrame !== missingFrames[missingArrayIndex - 1] + 1) {
                missingFramesString = missingFramesString + currentFrame;

            }

            missingArrayIndex++;

            // if not a missing frame and the currentFrame-1 was a missingFrame then it is last of sequence
        } else if (currentFrame == missingFrames[missingArrayIndex - 1] + 1) {
            if (missingFramesString.indexOf(missingFrames[missingArrayIndex - 1]) >= 0) missingFramesString = missingFramesString + ",";
            else missingFramesString = missingFramesString + "-" + missingFrames[missingArrayIndex - 1] + ",";
        }
    }
    // remove last comma
    missingFramesString = missingFramesString.replace(/,\s*$/, "");

    if (missingFramesString == "") {
        return first + "-" + last;
    } else {
        return first + "-" + last + " Missing: (" + missingFramesString + ")";
    }
}



function isInArray(value, array) {
    return array.indexOf(value) > -1;
}





// Hate it, but had to make a global variable to check the job status and access it everywhere.
var stats = [];
// Check the current status of each job in the directory, return it's output if matched.
function checkJobStatus() {

    $.ajax({
        type: "GET",
        url: '?action=checkStatus',
        async: false,
        success: function(data) {
            var obj = eval('(' + data + ')');
            var temp = [];
            $.each(obj, function(k, v) {

                if (typeof(k) != 'undefined' && typeof(v) != 'undefined') {
                    temp.push(k);

                    var item = $('[data-base-ip^="' + k.replace('192.168.', '') + '"]');
                    if (item.length > 0 && typeof(item) != 'undefined' && typeof(temp[k]) != 'undefined') {
                        switch (v) {
                            case 'fatalError':
                                temp[k]['status'] = 'Fatal Error';
                                break;
                            case 'fileNotFound':
                                temp[k]['status'] = 'File Not Found';
                                break;
                            case 'cameraNotFound':
                                temp[k]['status'] = 'Camera Not Found';
                                break;
                            case 'failedBeforeStart':
                                temp[k]['status'] = 'Failed Before Start';
                                break;
                            case 'renderCompleted':
                                temp[k]['status'] = 'Render Completed.';
                                break;
                            case 'invalidFlag':
                                temp[k]['status'] = 'Invalid Flag Passed';
                                break;
                        }
                        temp[k]['item'] = item;

                    }
                }

            });
            stats = temp;
        },
        error: function(data) {
            console.log(data);
        }

    });

    return stats;

}




function readStatus() {

    $.post('functions/readNodeStatus.php', function(data) {
        if (data != 'null') {
            var arr = eval("(" + data + ")");


            var out = '';
            //console.log($.parseJSON(data));
            var jobStatus = checkJobStatus();
            $.each(arr, function(k, v) {

                var strippedIP = k.replace('.txt', '');
                var obj = eval("(" + v.contents + ")");


                var name = (obj.scenePath != undefined ? (obj.scenePath.substring(obj.scenePath.lastIndexOf("/") + 1, obj.scenePath.length).replace('.mb', '').replace('.ma', '')) : 'N/A');
                var imagePath = (obj.imagePath != undefined ? obj.imagePath : 'N/A');
                //var type = (obj.scenePath != undefined ? (obj.scenePath.substr(obj.scenePath.length - 3)) : 'N/A');
                var currentFrame = 'N/A';
                var frameRange = "N/A";
                var percentage = "0";

                if (obj.currentFrame != undefined && obj.endFrame != undefined && obj.startFrame != undefined) {
                    frameRange = obj.startFrame + " - " + obj.endFrame;


                    currentFrame = (obj.currentFrame < obj.startFrame ? 'wrong' : obj.currentFrame);



                    if (obj.endFrame - obj.startFrame > 0) {
                        percentage = ((currentFrame - obj.startFrame) / (obj.endFrame - obj.startFrame + 1) * 100).toFixed(2);
                    }

                }
                if (obj.status != undefined && obj.status == 'completed') {
                    percentage = 100;
                }

                var status = (obj.status != undefined ? obj.status : 'N/A');
                var finishTime = (v.completionTime != undefined ? v.completionTime : '');
                var toolTipStatus = 'N/A';
                switch (obj.status) {
                    case 'completed':
                        toolTipStatus = 'Completed: ' + finishTime;
                        break;
                    case 'starting':
                        toolTipStatus = 'Starting...';
                        break;
                    case 'rendering':
                        toolTipStatus = 'Rendering...';
                        break;
                }

                var baseIP = strippedIP.replace('192.168.', '');
                var node = $('[data-refreshstring^="' + baseIP + '"]');
                var nodeName = node.attr('data-refreshstring').split(',')[1];
                var dead = node.find('.Dead').length;
                var nodeStatus = (dead == 0 ? 'ready' : 'dead');
                if (dead != 0) {
                    percentage = 0;
                    v.timeSinceStart = 0;
                    toolTipStatus = "Node Offline";
                }

                if (jobStatus[strippedIP] != undefined && jobStatus[strippedIP].status != undefined && jobStatus[strippedIP].status != 'noError' && obj.status != 'completed') {
                    toolTipStatus = jobStatus[strippedIP].status;
                    nodeStatus = 'dead';
                }
                if (jobStatus[strippedIP] != undefined && jobStatus[strippedIP].status != undefined && jobStatus[strippedIP].status == 'Render Completed.' && obj.status != 'completed') {
                    toolTipStatus = jobStatus[strippedIP].status;
                    nodeStatus = 'completed';
                }


                out += '<tr title="Render Directory: ' + imagePath + '"  class="' + status + ' ' + nodeStatus + '" data-base-ip="' + baseIP + '" data-job-name="' + name + '">';
                //out += '<td>' + contents.scenePath + '</td>';
                out += '<td><input type="checkbox" id="checkbox_' + baseIP + '" /></td>';
                out += '<td>' + name + '</td>';
                out += '<td>' + baseIP + ' <small class="text-primary">(' + nodeName + ')</small></td>';
                //out += '<td>' + status + '</td>';
                out += '<td>';
                out += '<div class="progress"><div class="progressText">' + percentage + '%</div>';
                out += '<progress max="100" value="' + percentage + '" class="jquery"></progress></div>';
                out += '</td>';
                out += '<td>' + currentFrame + '</td>';
                out += '<td>' + frameRange + '</td>';
                out += '<td data-total-time="' + toolTipStatus + '" data-status="' + status + '">' + v.timeSinceStart + '</td>';
                //out += '<td>' + type + '</td>';
                //out += '<td>' + description + '</td>';

                out += '</tr>';




            });
            $('#jobTable tbody').html(out);

            setupTablesorter($('#listingOfNodes, #jobTable'));


        }
    });

}


function setupTablesorter(t) {
    t.each(function(i, e) {
        var myHeaders = {}
        $(this).find('th.nosort').each(function(i, e) {
            myHeaders[$(this).index()] = {
                sorter: false
            };
        });

        $(this).tablesorter({
            widgets: ['zebra'],
            headers: myHeaders
        });
    });
}