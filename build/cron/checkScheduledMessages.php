<?php
// POST message=... username=... time=...

$json = array();

$mysqli = new mysqli("localhost", "psched-poster", "psched4brandon", "psched");
if ($mysqli->connect_errno) {
    $json["failure"] = "Failed to connect to database: " . mysqli_connect_error();
    $json["result"] = "Failure";
} else {
    $result = $mysqli->query("SELECT post_id,poster_name,message FROM scheduled WHERE scheduled_time <= NOW()");
    $json["externalResult"] = array();

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $json["scheduledMessagesPosted"][$row["post_id"]] = array("poster_name"=>$row["poster_name"], "message"=>$row["message"]);

            /* Prepare post message */
            $forward_url = "192.168.0.109/postMessage.php";
            $post_data = array(
                "username" => urlencode($row["poster_name"]),
                "message" => urlencode($row["message"])
            );

            /* Prepare cURL, which will post our message */
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $forward_url);
            curl_setopt($ch, CURLOPT_POST, count($post_data));
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            /* Execute Post */
            $curl_result = curl_exec($ch);
            curl_close($ch);

            /* Remove posted message from Database */
            if ($curl_result) {
                $json["externalResult"][$row["post_id"]] = json_decode($curl_result);

                if (!($update = $mysqli->prepare("DELETE FROM scheduled WHERE post_id=?"))) {
                    $json["failure"] = "Failed to create SQL statement";
                    $json["result"] = "Failure";
                } else {
                    $update->bind_param("s", $row["post_id"]);
                    if (!$update->execute()) {
                        $json["failure"] = "Failed to remove scheduled message after posting it. Error: " . mysqli_error($mysqli);
                        $json["result"] = "Failure";
                    } else {
                        $json["result"] = "Success";
                    }
                }
            } else {
                $json["result"] = "Failure";
                $json["failure"] = "No response from Main Server";
            }
        }
    } else {
        $json["result"] = "Success";
        $json["resultDetails"] = "There were no scheduled posts ready to be posted";
    }
}

echo json_encode($json);
?>

