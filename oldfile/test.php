<?php

// Read in our saved access token/secret
$accessToken = file_get_contents("access_token");
$accessTokenSecret = file_get_contents("access_token_secret");

// Create our twitter API object
require_once("twitteroauth/twitteroauth.php");
$oauth = new TwitterOAuth('UFztEgT1eHNeqnXPAuoCqA', 'm51toTe44HPf6kmgeaC49VSGdN3iWsIhibfHBzPmWjg', $accessToken, $accessTokenSecret);

// Send an API request to verify credentials
$credentials = $oauth->get("account/verify_credentials");
echo "Connected as @" . $credentials->screen_name;

// Post our new "hello world" status
$oauth->post('statuses/update', array('status' => "เอ่ม ส่งมาเป็นออพเจ็กท์ = ="));
?>