<?php

//
// loads all the requested scripts and merges them 
//


$scripts = (isset($_GET["s"])) ? $_GET["s"] : "";
$contentType = (isset($_GET["t"])) ? $_GET["t"] : "";
$fList = explode("|", $scripts);  
$relPath = "";

if ($scripts == "") {
    exit;
}

// PENDING this is probably not the best way to go about this
$scriptDirRoot = preg_replace("/\/code$/", "", dirname($_SERVER["SCRIPT_FILENAME"]));

// output as a custom content type
if ($contentType != "") {
    header("Content-Type: {$contentType}");
}

// loop over what we were asked for
foreach ($fList as $f) {
    // no trying to sneak around
    if (strpos($f, "..") == false) {
        // url?
        if (preg_match("/^http/", $f)) {
            echo file_get_contents($f);
        } else {
            // does it exist? load it
            $fFullPath = "{$scriptDirRoot}/{$f}";
            if (is_file($fFullPath)) {
                // and output it
                echo file_get_contents($fFullPath);
            } else {
                // oops
                echo "404: " . $fFullPath . "<br />";
            }
        }
    } else {
        // another oops
        echo "SKIP: " . $f . "<br />";
    }
    echo "\n";
}

?>
