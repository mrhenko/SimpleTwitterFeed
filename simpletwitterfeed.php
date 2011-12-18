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
		 	$this->setQuery($o);
		 }
		 
		 /**
		  * Change query settings
		  */
		 public function changeQuery($o) {
		 	$this->setQuery($o);
		 }
		 
		 
		 /**
		  * The method that does the actual query setting
		  */
		 private function setQuery($o) {
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
		 	$response = $this->parseData($response);
			
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
		  * Parse the search result
		  */
		 private function parseData($d) {
		 	$d = json_decode($d);
		 	
		 	$tweets = array();
		 	
		 	/* Special stuff */
		 	if ($this->userfeed === true) {
		 		$data = $d;
		 	} else {
		 		$data = $d->results;
		 	}
		 	
	 		foreach ($data as $r) {
	 			$tweet = array(
	 				'text' => $r->text
	 			);
	 			if (isset($r->from_user)) {
	 				$tweet['from_user'] = $r->from_user;
	 			}
	 			array_push($tweets, $tweet);
	 		}
		 	
		 	return(json_encode($tweets));
		 }
	 
	 }
?>