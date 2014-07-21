<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Psched Homepage</title>
    <link rel="stylesheet" type="text/css" href="static/css/style.css">
</head>
<body>
<div class="content">
    <div class="feed">
        <h3 class="compose-header">Scheduled Messages</h3>
<?php
require 'components/scheduledGrabber.php';
scheduledGrabber::refresh();
?>
    </div>
</div>
</body>
</html>
