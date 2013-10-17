<?php
require_once('TwitterAPIExchange.php');
$settings = array(
          'oauth_access_token' => "521839152-NIaozKJslU9qrV1xKKhs2dQqSWKjKTRJes4cdLfm",
          'oauth_access_token_secret' => "cxeyfcidIjQ9BE3Qs1TSnPPjaOF82isGK4FwevVO5MI",
          'consumer_key' => "7HtNfN5dYkUhZVzvKw3HNg",
          'consumer_secret' => "tv1XqGZRWNceanHBAneEUfz5KUuQbIIiXw7CUtZZkY"
      );

$username = $_GET['user'];
$countOfTweets = $_GET['count'];

// For Testing - Decode the json string for bypassing the API limit during testing
// $tweets = json_decode('[{"id_str":"387963908134146049","text":"Our partnership with @Comcast and @NBCUniversal lets users tune in to a TV show directly from a Tweet: https:\/\/t.co\/vOWzAK43Rs","user_ids":[719306807,1967246054,1967049510,473540901,1951979216,1966325191,1932953018,1635750763,1903967041,34674286,297375737,1265290279,148997973,1933252680,305749856,1966117435,1120144578,487391677,194841598,718517849]},{"id_str":"385873373353361408","text":"Our S-1 will be filed publicly with the SEC momentarily. This Tweet does not constitute an offer of any securities for sale.","user_ids":[1648230990,473540901,1966959822,305749856,487391677,162929585,1965735313,1391977879,1925103990,1962533713,1873793558,881060016,1957815434,1127498562,396353497,710267271,1965189481,245606360,1228815517,1960335546]},{"id_str":"385778998506033154","text":"We tested live-tweeting with @MLB to see if teams could boost follower engagement and their Twitter audience: https:\/\/t.co\/XPhHmVDNxJ","user_ids":[1680103746,473540901,1943938404,1366126898,1925103990,1962533713,1873793558,881060016,713784651,1383814230,1127498562,710267271,1965189481,386168676,1081725529,1671544250,1949717106,1460026340,36815980]},{"id_str":"383304965432672257","text":"Putting the photo front and center, now in embedded Tweets: https:\/\/t.co\/k0bAqOlBpe","user_ids":[1648230990,473540901,1900493960,1925103990,1873793558,881060016,710267271,1602220788,1081725529,1339320985,1671544250,1665639434,1460026340,1398295698,774381823,1618306964,1962607567,715888947]},{"id_str":"383233339181637632","text":"RT @nfl: Are you ready for some real-time football? We\'re thrilled to announce a partnership to amplify @nfl on @twitter! http:\/\/t.co\/IW5kI\u2026","user_ids":[]},{"id_str":"382945792513675264","text":"RT @TwitterEng: \"Java and Scala let Twitter readily share and modify its enormous codebase across a team of hundreds of developers.\" http:\/\u2026","user_ids":[]},{"id_str":"382921074146439168","text":"RT @gov: \"#TwitterAlerts provide an opportunity to get information directly from trusted sources,\" says @CraigatFEMA. https:\/\/t.co\/y47CYZfR\u2026","user_ids":[]},{"id_str":"382916695897034752","text":"Twitter Alerts: A new way to get accurate, important information when you need it most. Learn more: https:\/\/t.co\/ygFxyE04AO","user_ids":[473540901,1962533713,1925103990,1873793558,710267271,1602220788,133912207,1047298500,1671544250,1382028679,1962607567,715888947,633276411,709467742,1960794012,1961595428,1880375132,1699649388,1409030454]},{"id_str":"382908804418920448","text":"RT @twittertv: How Twitter users got backstage at the #Emmys, the most tweeted moments & much more: a @twittermedia special report https:\/\/\u2026","user_ids":[]},{"id_str":"382620312514220032","text":"We\'re rolling out a new recommendations feature that helps you stay in the know: https:\/\/t.co\/Lm1nX2vd4n","user_ids":[1873793558,881060016,710267271,1602220788,85680408,1081725529,1671544250,1618306964,1962607567,1961595428,1880375132,1699649388,1409030454,1651460672,1927691616,355935813,1837340749,1955999946]}]', true);
// $ids = json_decode('{"719306807":1,"1967246054":1,"1967049510":1,"473540901":5,"1951979216":1,"1966325191":1,"1932953018":1,"1635750763":1,"1903967041":1,"34674286":1,"297375737":1,"1265290279":1,"148997973":1,"1933252680":1,"305749856":2,"1966117435":1,"1120144578":1,"487391677":2,"194841598":1,"718517849":1,"1648230990":2,"1966959822":1,"162929585":1,"1965735313":1,"1391977879":1,"1925103990":4,"1962533713":3,"1873793558":5,"881060016":4,"1957815434":1,"1127498562":2,"396353497":1,"710267271":5,"1965189481":2,"245606360":1,"1228815517":1,"1960335546":1,"1680103746":1,"1943938404":1,"1366126898":1,"713784651":1,"1383814230":1,"386168676":1,"1081725529":3,"1671544250":4,"1949717106":1,"1460026340":2,"36815980":1,"1900493960":1,"1602220788":3,"1339320985":1,"1665639434":1,"1398295698":1,"774381823":1,"1618306964":2,"1962607567":3,"715888947":2,"133912207":1,"1047298500":1,"1382028679":1,"633276411":1,"709467742":1,"1960794012":1,"1961595428":2,"1880375132":2,"1699649388":2,"1409030454":2,"85680408":1,"1651460672":1,"1927691616":1,"355935813":1,"1837340749":1,"1955999946":1}', true);

$twitter = new TwitterAPIExchange($settings);

$tweets = getTweets($username, $countOfTweets);

$ids = getRetweetersForEachTweet($tweets);

$ids_split = splitAllIds(array_keys($ids));

$users = getUserDetails($ids_split, $ids);

usort($users, "sortByFollowersCount");

$topUsers = array_slice($users, 0, 10);

// Json Encode the array for testing without having to struggle with twitter api
// echo json_encode($tweets);
// echo "<br/><br/><br/>";
// echo json_encode($ids);
// echo "<br/><br/><br/>";



// For debugging
// echo "<h2>Tweets:</h2><br/><pre>";
// print_r($tweets);
// echo "</pre><br/><br/>";

// echo "<h2>Top Users:</h2><pre>";
// print_r($topUsers);
// echo "</pre>";

foreach ($topUsers as $user) {
echo "Name: " . $user['name'] . "<br/>";
echo "Value: " . $user['followers_count'] . "<br/>";
echo "<img src=\"" . $user['profile_image_url'] . "\" /> <br/><br/><br/>";
}




// Get top tweets for the given user
function getTweets($screen_name, $number_of_tweets=5){
  global $twitter;

  $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
  $getfield = "?screen_name=$screen_name&exclude_replies=true&count=25&trim_user=true";
  $requestMethod = 'GET';
  $bareString = $twitter->setGetfield($getfield)
               ->buildOauth($url, $requestMethod)
               ->performRequest();
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
function getRetweetersForEachTweet(&$tweets){
  global $twitter;
  $all_ids = array();
  foreach ($tweets as &$tweet) {

    $url = 'https://api.twitter.com/1.1/statuses/retweeters/ids.json';
    $getfield = '?id=' . $tweet['id_str'] . '&count=20';
    $requestMethod = 'GET';
    $bareString = $twitter->setGetfield($getfield)
                  ->buildOauth($url, $requestMethod)
                  ->performRequest();
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
function getUserDetails($ids_split, $ids_orig){
  global $twitter;
  $users = array();
  foreach ($ids_split as $ids) {

    $url = 'https://api.twitter.com/1.1/users/lookup.json';
    $getfield = "?user_id=$ids&include_entities=false";
    $requestMethod = 'GET';
    $bareString = $twitter->setGetfield($getfield)
                 ->buildOauth($url, $requestMethod)
                 ->performRequest();
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
function sortByFollowersCount($a, $b){
  return $b['followers_count'] - $a['followers_count'];
}

