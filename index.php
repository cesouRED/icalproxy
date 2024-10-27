<?php
$original_ical = file_get_contents("https://ade2-web.insa-lyon.fr/jsp/custom/modules/plannings/direct_cal.jsp?data=005c21a185b3c71a47cd30d6a648a0982c781c38a53ef555ae81fbf34d8a4108064b740280775c410e92b2ba3c5da9ed00f5a25742b085ce74ccb471b1462f7e20b1f19d2da6fcec1221db246bfbe864df5709ce29f2a455de974aac3fc5d60987124696a9413bdccb8d1329a91052add0993217863340f030e1f1734eb0c4cc,1");
$original_ical = str_replace("\r\n", "\n", $original_ical);
$original_ical = str_replace("\n ", "", $original_ical);

$pos_start = strpos($original_ical, "BEGIN:VEVENT");
$pos_end = strrpos($original_ical, "END:VEVENT");

$ical_before = substr($original_ical, 0, $pos_start-1);
$ical_after = substr($original_ical, $pos_end+11);
$ical_events = substr($original_ical, $pos_start+13, $pos_end-$pos_start-14);

$split_events = array();
foreach (explode("\nEND:VEVENT\nBEGIN:VEVENT\n", $ical_events) as $event_txt) {
    $event_data = array();
    foreach (explode("\n", $event_txt) as $property) {
        $delim_pos = strpos($property, ":");
        $key = substr($property, 0, $delim_pos);
        $data = substr($property, $delim_pos+1);
        $event_data[$key] = $data;
    }

    // $event_data["COLOR"] = array_rand(array("red" => 0,"blue" => 0));
    // $event_data["LOCATION"] = $event_data["LOCATION"] . " is cool.";

    // Enleve Créneau SOU/EPS/LV
    if (!str_contains($event_data["DESCRIPTION"], "Créneau")){
        $split_events[] = $event_data;
    }
}

$new_ical = $ical_before . "\nBEGIN:VEVENT\n";
foreach ($split_events as $event_data) {
    foreach ($event_data as $key => $data){
        $new_ical .= $key .":". $data ."\n";
    }
    $new_ical .= "END:VEVENT\nBEGIN:VEVENT\n";
}
$new_ical = substr($new_ical, 0, -13) . $ical_after;
$new_ical = str_replace("\n", "\r\n", $new_ical);

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ical display</title>
    <style>
        html {
            color-scheme: light dark;
        }
        .text {
            font-family: monospace;
            white-space: pre;
            overflow: auto;
            outline: 1px solid rgba(255, 255, 255, .5);
        }
    </style>
</head>
<body>
    <div class="text"><?php print_r($split_events); ?></div>
    <!-- <div class="text"><?php echo str_replace("\r\n", "↵\n", $new_ical) ?></div> -->
</body>
</html>
