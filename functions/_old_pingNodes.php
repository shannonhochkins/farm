<?php

    $nodes = array(
        "Node 1" => "192.168.1.110",
        "Node 2" => "192.168.1.111",
        "Node 3" => "192.168.1.112",
        "Node 4" => "192.168.1.113",
        "Node 5" => "192.168.1.114",
        "Node 6" => "192.168.1.115",
        "Node 7" => "192.168.1.116",
        "Node 8" => "192.168.1.117",
        "Node 9" => "192.168.1.118",
        "Node 10" => "192.168.1.119",
        "Node 11" => "192.168.1.120",
        "Node 12" => "192.168.1.121",
        "Node 13" => "192.168.1.122",
        "Node 14" => "192.168.1.123",
        "Node 15" => "192.168.1.124",
        "Node 16" => "192.168.1.125",
        "Node 17" => "192.168.1.126",
        );
    

    function pingAddress($ip, $pos, $key) {
        $responds = "";
        $pingresult = exec("ping -c2 $ip", $outcome, $status);


        if (0 == $status) {
            $status = "Ready";
        } else {
            $status = "Dead";
        }

        $responds .= "<tr>";
        $responds .= "<td>". $key ."</td>";
        $responds .= "<td>". $ip . "</td>";
        $responds .= "<td>". $status . "</td>";
        $responds .= "<td></td>";
        $responds .= "<td></td>";
        $responds .= "</tr>";
        return $responds;
    }
    
    $nodeTable = "";
    for($i =0; $i < count($nodes); ++$i) {

        $keys = array_keys($nodes);
        $values = array_values($nodes);

        $nodeTable .= pingAddress($values[$i], $i, $keys[$i]);
    }
?>