<?php
require("RateInformation.php");
use Rates\RateInformation;
$res = json_encode(RateInformation::getData($_POST['source']));
file_put_contents("request.txt", "Returning ".$res);
echo $res;