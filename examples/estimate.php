#!/usr/bin/php
<?php
/**
 * @author Jonathan Oxer <jon.oxer@ivt.com.au>
 * @version 2011-08-08
 *
 * This example is designed to be run from the command line using
 * PHP-CLI, and is a minimal example of using the php-liquidplanner
 * library to set the remaining estimated time for an existing task in
 * your workspace. You must configure it to use your own workspace ID,
 * email address, and Liquid Planner password, and set a task_id value
 * of one of your existing tasks.
 */
require_once("../liquidplanner.php");

$taskID           = 123456;
$taskData['low']  = "4h";
$taskData['high'] = "8h";

/* Create an instance of the Liquid Planner object */
$lp = new LiquidPlanner("12345", "you@example.com", "yourLPpassword");

/* Update the low and high estimated remaining time values */
$response = $lp->estimate($taskID, $taskData);

exit;
