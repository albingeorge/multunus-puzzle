<?php

require_once('classes.php');
require_once('model.php');

// $tempJSON = '[{"id":15953279,"tweet_id":0,"number_of_occurrences":1,"profile_image":"http:\/\/pbs.twimg.com\/profile_images\/378800000263740868\/197ba48d01afaaed807d9d87c6aaefc3.jpeg","count":3514,"screen_name":"danzelikman"},{"id":781314,"tweet_id":0,"number_of_occurrences":1,"profile_image":"http:\/\/pbs.twimg.com\/profile_images\/3091343815\/56a19b7d8c584a11ec83ad894b3a381b.jpeg","count":3024,"screen_name":"maggit"},{"id":7792422,"tweet_id":0,"number_of_occurrences":1,"profile_image":"http:\/\/pbs.twimg.com\/profile_images\/3415136442\/ab0ef05015eb2115695f90c0545573d5.jpeg","count":1653,"screen_name":"morganwarstler"},{"id":14341495,"tweet_id":0,"number_of_occurrences":1,"profile_image":"http:\/\/pbs.twimg.com\/profile_images\/3321641645\/e4eaa88679f1fcb6cfecb7151015e717.jpeg","count":1608,"screen_name":"adolfont"},{"id":14425176,"tweet_id":0,"number_of_occurrences":1,"profile_image":"http:\/\/pbs.twimg.com\/profile_images\/1587847275\/london.jpeg","count":1026,"screen_name":"inadarei"},{"id":582759042,"tweet_id":0,"number_of_occurrences":1,"profile_image":"http:\/\/pbs.twimg.com\/profile_images\/378800000213468042\/75383a2d34b24a04d5ddb8180a64f534.jpeg","count":959,"screen_name":"Autosports_Diva"},{"id":17188903,"tweet_id":0,"number_of_occurrences":1,"profile_image":"http:\/\/pbs.twimg.com\/profile_images\/68660467\/3130026223_caab2d1e6c.jpg","count":875,"screen_name":"evanchooly"},{"id":9887102,"tweet_id":0,"number_of_occurrences":1,"profile_image":"http:\/\/pbs.twimg.com\/profile_images\/1037442518\/recentphoto_big.jpg","count":852,"screen_name":"metaskills"},{"id":164016525,"tweet_id":0,"number_of_occurrences":1,"profile_image":"http:\/\/pbs.twimg.com\/profile_images\/3268927580\/15eba16aa10b6e5e66074fda1e504a34.jpeg","count":756,"screen_name":"adgerrits"},{"id":760557266,"tweet_id":0,"number_of_occurrences":1,"profile_image":"http:\/\/pbs.twimg.com\/profile_images\/378800000535123905\/2014eaab28a947010233c042a0bdc725.png","count":746,"screen_name":"screenheroapp"}]';

$count = 10;

if(isset($_POST)){
  $username = $_POST['username'];
} else {
  $username = getUsername();
}


$db = new TwitterModel('redat675_multunus_puzzle', 'redat675_mult', 'redatomstudios', 'localhost', 'twitter');

// Getting the Twitter handle
$twitter = new TwitterAPIWrapper('7HtNfN5dYkUhZVzvKw3HNg', 'tv1XqGZRWNceanHBAneEUfz5KUuQbIIiXw7CUtZZkY');

// Creating a user object
$user = new User($username, $twitter);

 // Get the top 10 tweets by default
$user->getTweets($count);

 // Get retweeters for each tweet
foreach($user->tweets as &$tweet){
  $tweet->getRetweeters();
}

// Merge all the retweeters from all the tweets
$retweeters = $user->mergeRetweeters();

 // Split the retweeters into batches of 100 to make efficient api call
$split_ids = array_chunk(array_keys($retweeters), 100);

 // Get the retweeters details
$user_details = array();
foreach ($split_ids as $ids) {
  $csv = implode(',', $ids);
  $url = 'https://api.twitter.com/1.1/users/lookup.json';
  $getfield = "?user_id=$csv&include_entities=false";

  if($details = $twitter->performRequest($url, $getfield)){

    $user_details = array_merge($user_details, $details);
  } else {

    if($_POST){

      if(!$data['users'] = $db->getJSON($username)){
        echo "Limit exceeded and not available in db!! Try again after sometime.";
      } else {
        $data['userImage'] = $user->profile_image;
        echo view("TwitterWheel.php", $data);
        die();
      }
    } else {
      exit();
    }
  }
}


// Fill in the details for each retweeter object
foreach ($user_details as $user_detail) {
  $retweeters[$user_detail['id_str']]->count = $retweeters[$user_detail['id_str']]->number_of_occurrences * $user_detail['followers_count'];
  $retweeters[$user_detail['id_str']]->profile_image = str_replace('_normal.', '.', $user_detail['profile_image_url']);
  $retweeters[$user_detail['id_str']]->screen_name = $user_detail['screen_name'];
}

 // Sort the retweeters based on count
usort($retweeters, "sortByFollowersCount");
function sortByFollowersCount($a, $b) {
  return $b->count - $a->count;
}

$top10 = array_slice($retweeters, 0, 10);


$db->setJSON($handle, json_encode($top10));




if($_POST){
  $data['users'] = $top10;
  $data['userImage'] = $user->profile_image;

  echo view("TwitterWheel.php", $data);
}


function getUsername($file = 'mult_userid.txt') {

  $current = file_get_contents($file);
  $id = $current + 1;
  if($current == 10){
    $id = 1;
  }

  file_put_contents($file, $id);

  switch($current){
    case 1:
      $user = 'github';
      break;
    case 2:
      $user = 'timoreilly';
      break;
    case 3:
      $user = 'twitter';
      break;
    case 4:
      $user = 'martinfowler';
      break;
    case 5:
      $user = 'dhh';
      break;
    case 6:
      $user = 'gvanrossum';
      break;
    case 7:
      $user = 'BillGates';
      break;
    case 8:
      $user = 'spolsky';
      break;
    case 9:
      $user = 'firefox';
      break;
  }
  return $user;
}

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