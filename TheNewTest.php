<?php

define('DB_NAME', 'redat675_multunus_puzzle');
define('DB_USER', 'redat675_mult');
define('DB_PASSWORD', 'redatomstudios');
define('DB_HOST', 'localhost');
define('CONSUMER_KEY', '7HtNfN5dYkUhZVzvKw3HNg');
define('CONSUMER_SECRET', 'tv1XqGZRWNceanHBAneEUfz5KUuQbIIiXw7CUtZZkY');


$user = $_GET['user'];
$countOfTweets = $_GET['count'];

// For Testing - Decode the json string for bypassing the API limit during testing
// $tweets = json_decode('[{"id_str":"387963908134146049","text":"Our partnership with @Comcast and @NBCUniversal lets users tune in to a TV show directly from a Tweet: https:\/\/t.co\/vOWzAK43Rs","user_ids":[719306807,1967246054,1967049510,473540901,1951979216,1966325191,1932953018,1635750763,1903967041,34674286,297375737,1265290279,148997973,1933252680,305749856,1966117435,1120144578,487391677,194841598,718517849]},{"id_str":"385873373353361408","text":"Our S-1 will be filed publicly with the SEC momentarily. This Tweet does not constitute an offer of any securities for sale.","user_ids":[1648230990,473540901,1966959822,305749856,487391677,162929585,1965735313,1391977879,1925103990,1962533713,1873793558,881060016,1957815434,1127498562,396353497,710267271,1965189481,245606360,1228815517,1960335546]},{"id_str":"385778998506033154","text":"We tested live-tweeting with @MLB to see if teams could boost follower engagement and their Twitter audience: https:\/\/t.co\/XPhHmVDNxJ","user_ids":[1680103746,473540901,1943938404,1366126898,1925103990,1962533713,1873793558,881060016,713784651,1383814230,1127498562,710267271,1965189481,386168676,1081725529,1671544250,1949717106,1460026340,36815980]},{"id_str":"383304965432672257","text":"Putting the photo front and center, now in embedded Tweets: https:\/\/t.co\/k0bAqOlBpe","user_ids":[1648230990,473540901,1900493960,1925103990,1873793558,881060016,710267271,1602220788,1081725529,1339320985,1671544250,1665639434,1460026340,1398295698,774381823,1618306964,1962607567,715888947]},{"id_str":"383233339181637632","text":"RT @nfl: Are you ready for some real-time football? We\'re thrilled to announce a partnership to amplify @nfl on @twitter! http:\/\/t.co\/IW5kI\u2026","user_ids":[]},{"id_str":"382945792513675264","text":"RT @TwitterEng: \"Java and Scala let Twitter readily share and modify its enormous codebase across a team of hundreds of developers.\" http:\/\u2026","user_ids":[]},{"id_str":"382921074146439168","text":"RT @gov: \"#TwitterAlerts provide an opportunity to get information directly from trusted sources,\" says @CraigatFEMA. https:\/\/t.co\/y47CYZfR\u2026","user_ids":[]},{"id_str":"382916695897034752","text":"Twitter Alerts: A new way to get accurate, important information when you need it most. Learn more: https:\/\/t.co\/ygFxyE04AO","user_ids":[473540901,1962533713,1925103990,1873793558,710267271,1602220788,133912207,1047298500,1671544250,1382028679,1962607567,715888947,633276411,709467742,1960794012,1961595428,1880375132,1699649388,1409030454]},{"id_str":"382908804418920448","text":"RT @twittertv: How Twitter users got backstage at the #Emmys, the most tweeted moments & much more: a @twittermedia special report https:\/\/\u2026","user_ids":[]},{"id_str":"382620312514220032","text":"We\'re rolling out a new recommendations feature that helps you stay in the know: https:\/\/t.co\/Lm1nX2vd4n","user_ids":[1873793558,881060016,710267271,1602220788,85680408,1081725529,1671544250,1618306964,1962607567,1961595428,1880375132,1699649388,1409030454,1651460672,1927691616,355935813,1837340749,1955999946]}]', true);
// $ids = json_decode('{"719306807":1,"1967246054":1,"1967049510":1,"473540901":5,"1951979216":1,"1966325191":1,"1932953018":1,"1635750763":1,"1903967041":1,"34674286":1,"297375737":1,"1265290279":1,"148997973":1,"1933252680":1,"305749856":2,"1966117435":1,"1120144578":1,"487391677":2,"194841598":1,"718517849":1,"1648230990":2,"1966959822":1,"162929585":1,"1965735313":1,"1391977879":1,"1925103990":4,"1962533713":3,"1873793558":5,"881060016":4,"1957815434":1,"1127498562":2,"396353497":1,"710267271":5,"1965189481":2,"245606360":1,"1228815517":1,"1960335546":1,"1680103746":1,"1943938404":1,"1366126898":1,"713784651":1,"1383814230":1,"386168676":1,"1081725529":3,"1671544250":4,"1949717106":1,"1460026340":2,"36815980":1,"1900493960":1,"1602220788":3,"1339320985":1,"1665639434":1,"1398295698":1,"774381823":1,"1618306964":2,"1962607567":3,"715888947":2,"133912207":1,"1047298500":1,"1382028679":1,"633276411":1,"709467742":1,"1960794012":1,"1961595428":2,"1880375132":2,"1699649388":2,"1409030454":2,"85680408":1,"1651460672":1,"1927691616":1,"355935813":1,"1837340749":1,"1955999946":1}', true);

mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
mysql_select_db(DB_NAME);


if($res = mysql_query("select * from twitter where handle = '$user'")){
  mysql_data_seek($res, 0);
  $row = mysql_fetch_assoc($res);
  $bearer_token = get_bearer_token();
  if(time()-strtotime($row['mydate']) > 600){


    $tweets = getTweets($user, $countOfTweets, $bearer_token);

    if(!$ids = getRetweetersForEachTweet($tweets, $bearer_token)){
      $data['users'] = json_decode($row['json']);
      break;
    }

    $ids_split = splitAllIds(array_keys($ids));

    $users = getUserDetails($ids_split, $ids, $bearer_token);

    usort($users, "sortByFollowersCount");

    $topUsers = array_slice($users, 0, 10);

    $json = json_encode($topUsers);

    mysql_query("update twitter set json = '". mysql_real_escape_string($json) ."' where handle = '$user'");
    
    $data['users'] = $topUsers;

  } else {
    $data['users'] = json_decode($row['json']);
  }
  $data['userImage'] = getCurrentUserImage($user, $bearer_token);
  echo view("TwitterWheel.php", $data);
}
else{
  echo "Error";
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
  $out = curl_exec($ch); // execute the curl
  $info = curl_getinfo($ch);
  curl_close($ch);
  if($info['http_code'] == 429) return false;
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
    if(!$bareString = performRequest($url, $getfield, $bearer_token)){
      return false;
    }else{
      $ids = json_decode($bareString, $assoc = true);
       $bareString . "<br/><br/><br/>";
      $tweet['user_ids'] = $ids['ids'];
      foreach ($ids['ids'] as $id) {
        if(array_key_exists($id, $all_ids)){
          $all_ids[$id]++;
        }else{
          $all_ids[$id] = 1;
        }
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

function getCurrentUserImage($user, $bearer_token){
    $url = 'https://api.twitter.com/1.1/users/show.json';
    $getfield = "?screen_name=$user";
    $bareString = performRequest($url, $getfield, $bearer_token);
    $userDetails =  json_decode($bareString, true);
    return str_replace('_normal.', '.', $userDetails['profile_image_url']);
}


// Load the view file
function view($file, $data) {

  extract($data);

  // Require the file
  ob_start();
  require($file);

  // Return the string
  $strView = ob_get_contents();
  ob_end_clean();
  return $strView;
}