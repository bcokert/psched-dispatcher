<?php
class scheduledGrabber {
    private static $mysqli;

    public static function refresh() {
        if (!self::$mysqli) {
            self::$mysqli = new mysqli("localhost", "psched-poster", "psched4brandon", "psched");
        }

        if (self::$mysqli->connect_errno) {
            echo "Failed to collect feed. The internal error is: " . self::$mysqli->connect_errno . " :: " . self::$mysqli->connect_error;
        } else {
            $result = self::$mysqli->query("SELECT poster_name,message,scheduled_time FROM scheduled ORDER BY scheduled_time ASC");
            while($row = $result->fetch_assoc()) {
                echo "<div class=\"post\">\n";
                echo "<span class=\"background-text\">Poster Name</span>\n";
                echo "<span class=\"user\">&nbsp;" . $row["poster_name"] . "</span>\n";
                echo "<span class=\"background-text\">&nbsp;at " . $row["scheduled_time"] . "</span>\n";
                echo "<p>" . $row["message"] . "</p>\n";
                echo "</div>\n";
            }
        }
    }
}
?>
