<?php
// POST message=... username=... time=...

$json = array();
foreach ($_POST as $key=>$value) {
    $json["received"][$key] = urldecode($value);
}

if ($json["received"]["username"] and strlen($json["received"]["username"]) >= 1
    and $json["received"]["message"] and strlen($json["received"]["message"]) > 1
    and $json["received"]["time"] and strlen($json["received"]["time"]) > 1) {

    $mysqli = new mysqli("localhost", "psched-scheduler", "psched4brandon", "psched");
    if (mysqli_connect_errno()) {
        $json["failure"] = "Failed to connect to database: " . mysqli_connect_error();
        $json["result"] = "Failure";
    } else {
        if (!($update = $mysqli->prepare("INSERT INTO scheduled (poster_name, message, scheduled_time) values (?, ?, ?)"))) {
            $json["failure"] = "Failed to create SQL statement";
            $json["result"] = "Failure";
        } else {
            $update->bind_param("sss", $json["received"]["username"], $json["received"]["message"], $json["received"]["time"]);
            if (!$update->execute()) {
                $json["failure"] = "Failed to post message. Error: " . mysqli_error($mysqli);
                $json["result"] = "Failure";
            } else {
                $json["result"] = "Success";
            }
        }
    }
} else {
    $json["result"] = "Failure";
    $json["failure"] = "Invalid Post. Expected three non-zero strings (username, message, time).";
}

echo json_encode($json);
?>

