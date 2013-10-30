<?php

require_once('classes.php');
require_once('model.php');

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