<?php
	/**
	 *
	 * Simple Twitter Feed
	 * simpleTwitterFeed
	 *
	 * A utillity class for fetching twitter messages. It uses parts of the twitter api
	 * that doesn't require an api key and gives you a list of tweets from a specific
	 * user or hash tag).
	 *
	 */
	 class simpleTwitterFeed {
	 	 
		 private $query; /* The twitter search query */
		 private $userfeed = false; /* Special treatment is needed if the result is a singel users feed */
		 
		 /**
		  * $o - The options for the object
		  *
		  */
		 function __construct($o) {
		 	/* Start by checking for settings */
		 	
		 	if (isset($o['user'])) {
		 		/* The feed should be a specific users tweets */
		 		$this->query = 'http://api.twitter.com/statuses/user_timeline.json?screen_name=' . $o['user'];
		 		$this->userfeed = true;
		 		
		 	} elseif (isset($o['query'])) {
		 		/* The feed should be the result of a search query */
		 		$this->query = 'http://search.twitter.com/search.json?q=' . urlencode($o['query']);
		 	}
		 }
		 
		 /**
		  * Make the request for tweets
		  *
		  * Returns a JSON object
		  */
		 public function getTweets() {
		 	$response = $this->requestData();
		 	if ($this->userfeed === true) {
			 	$response = $this->parseUserData($response);
			 } else {
			 	$response = $this->parseSearchData($response);
			 }
		 	
		 	return $response;
		 }
		 
		 /**
		  * Request the tweets from the twitter api
		  */
		 private function requestData() {
		 	/* Set up cURL */
			$curl = curl_init($this->query); 
			curl_setopt($curl, CURLOPT_POST, false); 
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($curl);
			curl_close($curl);
			
			return $response;
		 }
		 
		 /**
		  * Some special treatment for the result if it is a users timeline
		  */
		 private function parseUserData($d) {
		 	$d = json_decode($d);
		 	$tweets = array();
			foreach($d as $r) {
				array_push($tweets, $r->text);
			}
			
			return json_encode($tweets);
		 }
		 
		 /**
		  * Parse the search result
		  */
		 private function parseSearchData($d) {
		 	$d = json_decode($d);
		 	
		 	$tweets = array();
		 	foreach ($d->results as $r) {
		 		array_push($tweets, $r->from_user . ': ' . $r->text);
		 	}
		 	
		 	return(json_encode($tweets));
		 }
	 
	 }
?>