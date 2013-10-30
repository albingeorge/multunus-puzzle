<?php

class TwitterAPIWrapper
{
  private $consumer_key;
  private $consumer_secret;
  private $bearer_token;

  function __construct($consumer_key, $consumer_secret) {
    $this->consumer_key = $consumer_key;
    $this->consumer_secret = $consumer_secret;

    $encoded_consumer_key = urlencode($consumer_key);
    $encoded_consumer_secret = urlencode($consumer_secret);
    $bearer_token = $encoded_consumer_key.':'.$encoded_consumer_secret;
    $base64_encoded_bearer_token = base64_encode($bearer_token);
    $url = "https://api.twitter.com/oauth2/token"; // url to send data to for authentication
    $headers = array( 
            "POST /oauth2/token HTTP/1.1", 
            "Host: api.twitter.com", 
            "User-Agent: jonhurlock Twitter Application-only OAuth App v.1",
            "Authorization: Basic ".$base64_encoded_bearer_token."",
            "Content-Type: application/x-www-form-urlencoded;charset=UTF-8", 
            "Content-Length: 29"
    ); 

    $ch = curl_init();  // setup a curl
    curl_setopt($ch, CURLOPT_URL,$url);  // set url to send to
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // set custom headers
    curl_setopt($ch, CURLOPT_POST, 1); // send as post
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return output
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials"); // post body/fields to be sent
    $header = curl_setopt($ch, CURLOPT_HEADER, 1); // send custom headers
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $retrievedhtml = curl_exec ($ch); // execute the curl
    curl_close($ch); // close the curl
    $output = explode("\n", $retrievedhtml);
    $bearer_token = '';
    foreach($output as $line)
    {
            if($line === false)
            {
                    $bearer_token = '';
            }else{
                    $bearer_token = $line;
            }
    }
    $bearer_token = json_decode($bearer_token);
    $this->bearer_token = $bearer_token->access_token;
  }

  function performRequest($url, $get_fields) {
    
    if($this->bearer_token == '') return false;

    $headers = array( 
          "Host: api.twitter.com", 
          "User-Agent: Multunus Puzzle",
          "Authorization: Bearer ".$this->bearer_token."",
    );
    $ch = curl_init();  // setup a curl
    curl_setopt($ch, CURLOPT_URL, $url.$get_fields);  // set url to send to
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // set custom headers
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return output
    $out = curl_exec($ch); // execute the curl
    $info = curl_getinfo($ch);
    curl_close($ch);
    if($info['http_code'] == 429) return false;
    return json_decode($out, $assoc=true);
  }
}


class User
{
  private $id;
  public $screen_name;
  public $name;
  public $tweets;
  public $profile_image;
  private $twitter;

  function __construct($screen_name, $twitter) {
    $this->twitter = $twitter;
    $details = $twitter->performRequest('https://api.twitter.com/1.1/users/show.json', "?screen_name=$screen_name");
    $this->id = $details['id_str'];
    $this->screen_name = $screen_name;
    $this->name = $details['name'];
    $this->tweets = array();
    $this->profile_image = str_replace('_normal.', '.', $details['profile_image_url']);
  }

  public function getTweets($count) {
    $tweetArray = $this->twitter->performRequest('https://api.twitter.com/1.1/statuses/user_timeline.json',
     '?screen_name=' . $this->screen_name . '&exclude_replies=true&count=25&trim_user=true');
    $tweets = array();
    for($i=0; $i<sizeof($tweetArray)&&$i<$count; $i++){
      $tweets[] = new Tweet($this->twitter, $tweetArray[$i]['id_str'], $tweetArray[$i]['text']);
    }
    $this->tweets = $tweets;
  }

  public function mergeRetweeters() {
    $merged = array();
    foreach ($this->tweets as $tweet) {
      foreach ($tweet->retweeters as $id=>$retweeter) {
        if(array_key_exists($id, $merged)) {
          $merged[$id]->number_of_occurrences += $retweeter->number_of_occurrences;
        } else {
          $merged[$id] = $retweeter;
        }
      }
    }
    return $merged;
  }

}


class Tweet
{  
  private $id;
  public $text;
  public $retweeters;
  private $twitter;

  function __construct($twitter, $id, $text) {
    $this->id = $id;
    $this->text = $text;
    $this->retweeters = array();
    $this->twitter = $twitter;
  }

  public function getRetweeters() {
    $all_ids = array();

    $url = 'https://api.twitter.com/1.1/statuses/retweeters/ids.json';
    $getfield = '?id=' . $this->id . '&count=20';
    if(!$ids = $this->twitter->performRequest($url, $getfield)){
      return false;
    }else{
      foreach ($ids['ids'] as $id) {
        if(array_key_exists($id, $this->retweeters)) {
          $this->retweeters[$id]->number_of_occurrences += 1;
        } else {
          $rtweeter = new Retweeter($id, $this->id);
          $this->retweeters[$id] = $rtweeter;
        }
      }
    }
  }
}


class Retweeter
{
  public $id;
  public $tweet_id;
  public $number_of_occurrences;
  public $profile_image;
  public $count;

  function __construct($id, $tweet_id) {
    $this->id = $id;
    $this->tweet_id = 0;
    $this->number_of_occurrences = 1;
    $this->profile_image = '';
    $this->count = 0;
  }
}