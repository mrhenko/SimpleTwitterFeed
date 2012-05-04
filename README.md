Simple Twitter Feed
===================

By: [MrHenko][1]  
Licence: GPL

Usage
-----
### Example
	$twitter = new simpleTwitterFeed(array(
		'query' => '#daladevelop',
		'cacheTime' => '3600'
	));
	
	$tweets = $twitter->getTweets();

(Return format is json.)

When initializing an object you *must* specify either a **query** or a **user**. If you use a query, the request will be made against the [Twitter Search Api][2]. If you specify a user name then that particular users tweets will be fetched from the [Statuses API][3].

The **cacheTime** setting is optional and specifies the maximum age of a cache file in seconds. Defaults to 10 minutes.

[1]: https://github.com/mrhenko/
[2]: https://dev.twitter.com/docs/api/1/get/search
[3]: https://dev.twitter.com/docs/api/1/get/statuses/user_timeline

Version History
---------------

### 0.2.2.1
* Replace DateTime('now') with Time()
* Corrected incorrect example in README file

### 0.2.2
* Changed the default cacheTime to 10 minutes.
* Added the **Usage** section of this README file.

### 0.2.1
* Added the ability for the user to define the cache time.

### 0.2
* Added support for cache.

### 0.1.1
* Added this readme file and version numbering.
