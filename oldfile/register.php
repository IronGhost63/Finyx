<?php
require_once('twitteroauth/twitteroauth.php');
$oauth = new TwitterOAuth('UFztEgT1eHNeqnXPAuoCqA','m51toTe44HPf6kmgeaC49VSGdN3iWsIhibfHBzPmWjg');

$request = $oauth->getRequestToken();
$requestToken = $request['oauth_token'];
$requestTokenSecret = $request['oauth_token_secret'];

// place the generated request token/secret into local files
file_put_contents('request_token', $requestToken);
file_put_contents('request_token_secret', $requestTokenSecret);

// display Twitter generated registration URL
$registerURL = $oauth->getAuthorizeURL($request);
echo '<a href="' . $registerURL . '">Register with Twitter</a>';
?>