<?php

define('DB_NAME', 'redat675_multunus_puzzle');
define('DB_USER', 'redat675_mult');
define('DB_PASSWORD', 'redatomstudios');
define('DB_HOST', 'localhost');
define('CONSUMER_KEY', '7HtNfN5dYkUhZVzvKw3HNg');
define('CONSUMER_SECRET', 'tv1XqGZRWNceanHBAneEUfz5KUuQbIIiXw7CUtZZkY');



$file = 'mult_userid.txt';

$current = file_get_contents($file);
$id = $current+1;
if($current == 10){
  $id = 1;
}

file_put_contents($file, $id);

switch($current){
  case 1:
    $user = 'github';
    $count = 10;
    break;
  case 2:
    $user = 'timoreilly';
    $count = 10;
    break;
  case 3:
    $user = 'twitter';
    $count = 10;
    break;
  case 4:
    $user = 'martinfowler';
    $count = 9;
    break;
  case 5:
    $user = 'dhh';
    $count = 10;
    break;
  case 6:
    $user = 'gvanrossum';
    $count = 7;
    break;
  case 7:
    $user = 'BillGates';
    $count = 10;
    break;
  case 8:
    $user = 'spolsky';
    $count = 9;
    break;
  case 9:
    $user = 'firefox';
    $count = 2;
    break;
}

echo "User: $user";

$bearer_token = get_bearer_token();

$tweets = getTweets($user, $count, $bearer_token);

$ids = getRetweetersForEachTweet($tweets, $bearer_token);

$ids_split = splitAllIds(array_keys($ids));

$users = getUserDetails($ids_split, $ids, $bearer_token);

usort($users, "sortByFollowersCount");

$topUsers = array_slice($users, 0, 10);

$json = json_encode($topUsers);

// echo $json;

// Create connection
mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
mysql_select_db(DB_NAME);


$res = mysql_query("select * from twitter where handle = '$user'");
if(mysql_num_rows($res) == 0 && $user != NULL){
  $res = mysql_query("insert into twitter(`handle`,`json`) values ('$user', '". mysql_real_escape_string($json) ."')");
}
else{
  mysql_query("update twitter set json = '". mysql_real_escape_string($json) ."' where handle = '$user'");
}


function get_bearer_token(){
        // Step 1
        // step 1.1 - url encode the consumer_key and consumer_secret in accordance with RFC 1738
        $encoded_consumer_key = urlencode(CONSUMER_KEY);
        $encoded_consumer_secret = urlencode(CONSUMER_SECRET);
        // step 1.2 - concatinate encoded consumer, a colon character and the encoded consumer secret
        $bearer_token = $encoded_consumer_key.':'.$encoded_consumer_secret;
        // step 1.3 - base64-encode bearer token
        $base64_encoded_bearer_token = base64_encode($bearer_token);
        // step 2
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
                        // there was no bearer token
                }else{
                        $bearer_token = $line;
                }
        }
        $bearer_token = json_decode($bearer_token);
        return $bearer_token->access_token;
}


function performRequest($url, $getfield, $bearer_token){
  $headers = array( 
          "Host: api.twitter.com", 
          "User-Agent: Multunus Puzzle",
          "Authorization: Bearer ".$bearer_token."",
  );
  $ch = curl_init();  // setup a curl
  curl_setopt($ch, CURLOPT_URL,$url.$getfield);  // set url to send to
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // set custom headers
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return output
  $out = curl_exec ($ch); // execute the curl
  // echo $out;
  return $out;
}

// Get top tweets for the given user
function getTweets($screen_name, $number_of_tweets, $bearer_token){
  
  $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
  $getfield = "?screen_name=$screen_name&exclude_replies=true&count=25&trim_user=true";
  $bareString = performRequest($url, $getfield, $bearer_token);
  $tweetArray = json_decode($bareString, $assoc = true);

  $tweets = array();
  for($i=0; $i<sizeof($tweetArray)&&$i<$number_of_tweets; $i++){
    $tweets[] = array('id_str' => $tweetArray[$i]['id_str'],
                      'text' => $tweetArray[$i]['text']
                      );
  }
  return $tweets;
}


// Get the top 20 retweeters
function getRetweetersForEachTweet(&$tweets, $bearer_token) {
  $all_ids = array();
  foreach ($tweets as &$tweet) {

    $url = 'https://api.twitter.com/1.1/statuses/retweeters/ids.json';
    $getfield = '?id=' . $tweet['id_str'] . '&count=20';
    $bareString = performRequest($url, $getfield, $bearer_token);
    $ids = json_decode($bareString, $assoc = true);

    $tweet['user_ids'] = $ids['ids'];
    foreach ($ids['ids'] as $id) {
      if(array_key_exists($id, $all_ids)){
        $all_ids[$id]++;
      }else{
        $all_ids[$id] = 1;
      }
    }
  }
  return $all_ids;
}


// Split all the ids in batches of 100(limit for users_lookup twitter api call)
function splitAllIds($all_ids){
  $ids_array = array();
  $tempArray = array();
  for ($i=0; $i < sizeof($all_ids); $i++) {
    if($i % 100 == 0 && $i != 0) {
      $ids_array[] = implode(',', $tempArray);
    $tempArray = array();
    }
    $tempArray[] = $all_ids[$i];
  }
  $ids_array[] = implode(',', $tempArray);
  return $ids_array;
}


// Get batches of 100 users' details
function getUserDetails($ids_split, $ids_orig, $bearer_token){
  $users = array();
  foreach ($ids_split as $ids) {

    $url = 'https://api.twitter.com/1.1/users/lookup.json';
    $getfield = "?user_id=$ids&include_entities=false";
    $bareString = performRequest($url, $getfield, $bearer_token);
    $usersArrays =  json_decode($bareString, true);

    foreach ($usersArrays as $user) {
      $tempUsr['followers_count'] = $user['followers_count']*$ids_orig[$user['id_str']];
      $tempUsr['name'] = $user['name'];
      $tempUsr['screen_name'] = $user['screen_name'];
      $tempUsr['profile_image_url'] = str_replace('_normal.', '.', $user['profile_image_url']);
      $users[$user['id_str']] = $tempUsr;
    }
  }
  return $users;
}

// Function used in usort
function sortByFollowersCount($a, $b) {
  return $b['followers_count'] - $a['followers_count'];
}