<?php
include "../SQL.php";
echo json_encode(getSchedule($_REQUEST["name"], $_REQUEST["routes"]));