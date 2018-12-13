<?php
echo "Are you sure you want to do this?  Type 'yes' to continue: ";
$handle = fopen ("php://stdin","r");
$line = fgets($handle);
if(trim($line) != 'yes'){
    echo "ABORTING!\n";
    exit;
}
echo "\n";
echo "Thank you, continuing...\n";

echo "Enter your name: ";
$handle = fopen ("php://stdin","r");
$line = fgets($handle);
if(trim($line) == 'sas'){
    echo "You the boss!\n";
    exit;
}
echo "\n";
echo "You are no king!\n";