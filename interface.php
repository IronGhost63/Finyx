<?php
//header ("Content-Type:text/javascript");
require_once('twitteroauth/twitteroauth.php');
$mode = $_REQUEST['do'];
date_default_timezone_set('Europe/London');

if($mode == "register"){
	$oauth = new TwitterOAuth('UFztEgT1eHNeqnXPAuoCqA','m51toTe44HPf6kmgeaC49VSGdN3iWsIhibfHBzPmWjg');
	$request = $oauth->getRequestToken();
	$requestToken = $request['oauth_token'];
	$requestTokenSecret = $request['oauth_token_secret'];

	$registerURL = $oauth->getAuthorizeURL($request);
	
	$return = array("request_token"=>$requestToken,"request_token_secret"=>$requestTokenSecret,"registerURL"=>$registerURL);
	echo json_encode($return);
	
}elseif($mode == "validate"){
	$requestToken = $_GET['rt'];
	$requestTokenSecret = $_GET['rts'];
	$PINAuth = $_GET['pin'];
	
	if(!$requestToken || !$requestTokenSecret || !$PINAuth){
		$return = array("status"=>"0","code"=>"001","message"=>"No requestToken or requestTokenSecret or PIN");
		die(json_encode($return));
	}

	$oauth = new TwitterOAuth('UFztEgT1eHNeqnXPAuoCqA', 'm51toTe44HPf6kmgeaC49VSGdN3iWsIhibfHBzPmWjg', $requestToken, $requestTokenSecret);

	$request = $oauth->getAccessToken(NULL, $PINAuth);
	$accessToken = $request['oauth_token'];
	$accessTokenSecret = $request['oauth_token_secret'];

	$return = array("status"=>"1","access_token"=>$accessToken,"access_token_secret"=>$accessTokenSecret);
	echo json_encode($return);
	
}else{
	$accessToken = $_GET['at'];
	$accessTokenSecret = $_GET['ats'];
	
	$oauth = new TwitterOAuth('UFztEgT1eHNeqnXPAuoCqA', 'm51toTe44HPf6kmgeaC49VSGdN3iWsIhibfHBzPmWjg', $accessToken, $accessTokenSecret);
	//$oauth->decode_json = false;
	
	if($mode == "do_tweet"){
		$tweetStatus = $_GET['status'];
		$tweetReplyTo = $_GET['reply_to'];
		
		if($tweetStatus){
			
			if($tweetReplyTo){
				$updateResponse = $oauth->post('statuses/update', array('status' => stripslashes($tweetStatus),"in_reply_to_status_id" => $tweetReplyTo));
			}else{
				$updateResponse = $oauth->post('statuses/update', array('status' => stripslashes($tweetStatus)));
			}
			$return = array("id"=>$updateResponse->id, "reply_to_id"=>$updateResponse->in_reply_to_status_id, "source"=>$updateResponse->source, "time"=>$updateResponse->created_at,"message"=>$updateResponse->text);
			echo json_encode($return);
			
		}else{
			$return = array("status"=>"0","code"=>"003","message"=>"No status");
			echo json_encode($return);
		}
		
	}elseif($mode == "get_my_credential"){
		$credentialsResponse = $oauth->get("account/verify_credentials");
		$return = array("id"=>$credentialsResponse->id_str, "screen_name"=>$credentialsResponse->screen_name, "name"=>$credentialsResponse->name, "bio"=>$credentialsResponse->description, "photo"=>$credentialsResponse->profile_image_url, "utc_offset"=>$credentialsResponse->utc_offset);
		echo json_encode($return);
		
	}elseif($mode == "fetch_timeline"){
		$fetchPoint = $_GET['fetch_point'];
		$timeOffset = $_GET['offset'];
		$oauth->decode_json = false;
		
		if($fetchPoint != ""){
			$fetchResponse = $oauth->get("statuses/home_timeline", array("count"=>50,"since_id"=>$fetchPoint));
		}else{
			$fetchResponse = $oauth->get("statuses/home_timeline", array("count"=>50));
		}
		
		/*
		$return = json_decode($fetchResponse,true);
		$return2 = json_encode($return[0]);
		*/
		
		$decodeResponse = json_decode($fetchResponse, true);
		$allItems = count($decodeResponse);
		
		$i = 0;
		while($i < $allItems){
			
			$return[$i]['id'] = $decodeResponse[$i]['id_str'];
			$return[$i]['in_reply_to'] = $decodeResponse[$i]['in_reply_to_status_id_str'];
			$return[$i]['in_reply_to_user'] = $decodeResponse[$i]['in_reply_to_screen_name'];
			$return[$i]['source'] = $decodeResponse[$i]['source'];
			$return[$i]['created'] = date("D M d H:i Y", strtotime($decodeResponse[$i]['created_at'])+$timeOffset);
			$return[$i]['user']['id'] = $decodeResponse[$i]['user']['id_str'];
			$return[$i]['user']['screen_name'] = $decodeResponse[$i]['user']['screen_name'];
			$return[$i]['user']['avatar'] = $decodeResponse[$i]['user']['profile_image_url'];
			$return[$i]['user']['protected'] = $decodeResponse[$i]['user']['protected'];
			
			// retweet item
			
			if(is_array($decodeResponse[$i]['retweeted_status'])){
				$return[$i]['is_retweet'] = true;
				$return[$i]['retweet_status']['id'] = $decodeResponse[$i]['retweeted_status']['id'];
				$return[$i]['retweet_status']['user']['screen_name'] = $decodeResponse[$i]['retweeted_status']['user']['screen_name'];
				$return[$i]['retweet_status']['user']['avatar'] = $decodeResponse[$i]['retweeted_status']['user']['profile_image_url'];
				$return[$i]['message'] = $decodeResponse[$i]['retweeted_status']['text'];
			}else{
				$return[$i]['is_retweet'] = false;
				$return[$i]['message'] = $decodeResponse[$i]['text'];
			}
			
			$i++;
		}
		
		echo json_encode($return);
		//echo json_encode($decodeResponse);
		
	}elseif($mode == "fetch_mention"){
		$fetchPoint = $_GET['fetch_point'];
		$timeOffset = $_GET['offset'];
		$oauth->decode_json = false;
		
		if($fetchPoint != ""){
			$fetchResponse = $oauth->get("statuses/mentions", array("count"=>50,"since_id"=>$fetchPoint));
		}else{
			$fetchResponse = $oauth->get("statuses/mentions", array("count"=>50));
		}
		
		/*
		$return = json_decode($fetchResponse,true);
		$return2 = json_encode($return[0]);
		*/
		
		$decodeResponse = json_decode($fetchResponse, true);
		$allItems = count($decodeResponse);
		
		$i = 0;
		while($i < $allItems){
			
			$return[$i]['id'] = $decodeResponse[$i]['id_str'];
			$return[$i]['in_reply_to'] = $decodeResponse[$i]['in_reply_to_status_id_str'];
			$return[$i]['in_reply_to_user'] = $decodeResponse[$i]['in_reply_to_screen_name'];
			$return[$i]['source'] = $decodeResponse[$i]['source'];
			$return[$i]['created'] = date("D M d H:i Y", strtotime($decodeResponse[$i]['created_at'])+$timeOffset);
			$return[$i]['user']['id'] = $decodeResponse[$i]['user']['id_str'];
			$return[$i]['user']['screen_name'] = $decodeResponse[$i]['user']['screen_name'];
			$return[$i]['user']['avatar'] = $decodeResponse[$i]['user']['profile_image_url'];
			$return[$i]['user']['protected'] = $decodeResponse[$i]['user']['protected'];
			$return[$i]['is_retweet'] = false;
			$return[$i]['message'] = $decodeResponse[$i]['text'];
			
			$i++;
		}
		
		echo json_encode($return);
		//echo json_encode($decodeResponse);
		
	}elseif($mode == "delete"){
		$twId = $_GET['twId'];
		//$oauth->decode_json = false;
		
		$deleteResponse = $oauth->post('statuses/destroy', array('id' => $twId, 'trim_user' => true));
		
		if(!$deleteResponse->error){
			$return = array("status"=>"1", "code"=>"001", "message"=>"Tweet has been deleted");
		}else{
			$return = array("status"=>"0", "code"=>"004", "message"=>"Unable to delete");
		}
		
		echo json_encode($return);
	}elseif($mode == "retweet"){
		$twId = $_GET['twId'];
		//$oauth->decode_json = false;
		$retweetResponse = $oauth->post('statuses/retweet/'.$twId, array('id' => $twId, 'trim_user' => true));
		
		//echo $retweetResponse;
		
		if(!$retweetResponse->error){
			$return = array("status"=>"1", "code"=>"002", "message"=>"Tweet has been retweeted");
		}else{
			$return = array("status"=>"0", "code"=>"005", "message"=>"Unable to retweet");
		}
		
		echo json_encode($return);
	}else{
		$return = array("status"=>"0","code"=>"002","message"=>"Unknow Operation");
		echo json_encode($return);
		
	}
}
?>