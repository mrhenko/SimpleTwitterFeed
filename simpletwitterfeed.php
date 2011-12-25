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
		private $tweets = array(); /* The tweets */
		private $cache = array();
		private $cacheTime = '360';
		
		const VERSION = '0.1.1';
		private $cache_file = '.cache/simpletwitterfeed.cachefile';
		
		 
		/**
		 * $o - The options for the object
		 *
		 */
		function __construct($o) {
			/* Start by checking for settings */
			if (isset($o['cacheTime'])) {
				$this->cacheTime = $o['cacheTime'];
			}
			
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
			
			/* Reset the tweets and the name of the cache file */
			$this->tweets = array();
			$this->cache_file = '.cache/simpletwitterfeed' . md5($this->query) . '.cachefile';
		}
		 
		/**
		 * Make the request for tweets
		 *
		 * Returns a JSON object
		 */
		public function getTweets() {
			if ($this->checkCache()) {
				/* If we have a cache */
				$this->tweets = $this->cache->tweets;
			} else {
				$response = $this->requestData();
				$this->parseData($response);
				$this->cache();
			}
		
			return json_encode($this->tweets);
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
	 			array_push($this->tweets, $tweet);
	 		}
		}
		 
		/**
		 * Check if a cache exists
		 *
		 **/
		private function cacheExists() {
			if (file_exists($this->cache_file)) {
				/* File exists */
				return true;
			} else {
				return false;
			}
		}
		
		/**
		 * Is the cache valid
		 *
		 **/
		private function checkCache() {
			if (file_exists($this->cache_file)) {
				/* Open the file for reading and re-writing */
				$handle = fopen($this->cache_file, 'r+');
				$cache = json_decode(fread($handle, filesize($this->cache_file)));
				
				$time = new DateTime('now');
				if ($time->getTimestamp() > $cache->cachetime + $this->cacheTime) {
		 			/* If the cache has expired */
		 			return false;
		 		} else {
		 			/*
		 				If the cache has not expired, we must check if it
		 				contains any tweets
		 			*/
		 			if (count($cache->tweets) > 0) {
		 				/* Tweets exists, store them in memory */
		 				$this->cache = $cache;
		 				return true;
		 			} else {
		 				return false;
		 			}
		 		}
			} else {
				return false;
			}
		}
		 
	 	/**
	 	 * Write to the cache
	 	 *
	 	 */
	 	private function cache() {
	 		$time = new DateTime('now');
	 		
	 		/* Write to the cache property */
	 		$this->cache = array(
	 			'cachetime' => $time->getTimestamp(),
	 			'tweets' => $this->tweets
	 		);
	 		
	 		/* Write the property to the file */
	 		$handle = fopen($this->cache_file, 'w');
	 		fwrite($handle, json_encode($this->cache));
	 	}
	 
	 }
?>