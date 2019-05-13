<?php
ini_set('memory_limit', '-1');
include 'db_config.php';

// Read json content from request
$json = file_get_contents('php://input');
$json = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $json);
$data = json_decode($json, true);

// Parsing JSON data and kept in array
$bigArr   = array();
$cnt      = 0;
$tmpArr   = array();
$tmpArr_1 = array();
foreach ($data as $ind => $json_subarr) {
    $bigArr[$cnt] = array();
    $json_subarr = (array) $json_subarr;
    foreach ($json_subarr as $key => $value) {
          if ($key == "Desc") {
            $val    = json_decode($value, true);
            $tmpArr = explode("-newAttr-", $value);
            for ($i = 0; $i < sizeOf($tmpArr); $i++) {
                $tmpArr_1 = explode(":", $tmpArr[$i],2);
                if ($i == 0) {
                    $bigArr[$cnt]['ProcessStatus'] = $tmpArr_1[0];
                } else {
                    $atr = (string) $tmpArr_1[0];
                    $bigArr[$cnt][$atr] = $tmpArr_1[1];
                }
            }
        }
        if ($key == "Id") {
            $bigArr[$cnt]['EventId'] = $value;
        }
        if ($key == "MachineName") {
            $bigArr[$cnt]['MachineName'] = $value;
        }
        
        if ($key == "TimeCreated") {
            $bigArr[$cnt]['CreatedTime'] = $value;
        }
        if ($key == "EventRecordId") {
            $bigArr[$cnt]['EventRecordId'] = $value;
        }
    }
    $cnt++;
}

//Get fieldname from defined table
$columnArr = array();
$sql = "SHOW COLUMNS FROM Events_Logs";
$query = $conn->query($sql);
while ($row = $query->fetch_assoc()) {
    $result[] = $row;
}
// Array of all column names
$columnArr = array_column($result, 'Field');

// Prepare SQL Statements for execution
for ($i = 0; $i < sizeOf($bigArr); $i++) {
    $arr = $bigArr[$i];
    $commCond = false;
    $commStr  = "";
    $field    = "";
    $fieldVal = "";
    $otherVal = "";
    
    foreach ($arr as $key => $value) {
        if ($commCond == true) {
            $commStr = ",";
        }
        $commCond = true;
        if (in_array($key, $columnArr)) {
            $field .= $commStr . $key;
            if ($key == "ProcessId" || $key == "TerminalSessionId" || $key == "EventId" || $key == "EventRecordId") {
                $fieldVal .= $commStr . $value;
            } else {
                $fieldVal .= $commStr . "'" . $value . "'";
            }
        } else {
            $otherVal .= $key . ":" . $value . "-";
        }
    }
    $sql_insert = "INSERT INTO Events_Logs(" . $field . ",Others) values(" . $fieldVal . ",'" . $otherVal . "')";
    $query = $conn->query($sql_insert);
    echo "Server Script Finished : ".$query."\n"."Query :".$sql_insert."\n";
    }

?>
