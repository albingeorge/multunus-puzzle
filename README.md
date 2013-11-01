What the number represents:
  (Followers count of the specified user) * (Count of retweets(within the specified count) of whichever profile is selected)


Algorith for the puzzle solution:
- Get top 10(say) tweets of the person <=> get this count 
- Get the top 20 latest retweeters for the above 10 tweets
- Calculate the value for each user by multiplying their followers count with the number of retweets within the above 10 tweets
- Sort the users based on their value in descending order
- Get the top 10 users

Assumptions:
	Pass the username and the number of tweets to take in URL as GET variables 'user' and 'count' respectively

A Short Description:
 - I have split the core functionalities into functions so that it would be easier for me to debug
 - Also, while solving the puzzle I found the API limit REALLY irritating. So as a solution to that, I would json encode the result array that I get first and later instead of making the API call(for testing ONLY), I would decode the json string and thus bypass the rate limit(temporarily, ie before there is a change in the tweet status) => These are mentioned in the comments
 - This also works for all the handles given at http://puzzle.multunus.com/ parallelly



Limits:
 - I still havenot found a way to bypass twitter's API limit
 - This algorithm makes too many API calls, so it needs an interval of 15 minutes(approx) before each testing
 	- One solution to this is to store the json encoded string mentioned above in database/file and read the file, if the rate is exceeded. Then do a cronjob to cycle through all the handles. I guess this is what you have done in your puzzles page, which results in a small difference between the real-time solutions and the posted solutions.
 - While testing if you get 'Warning: Invalid argument supplied for foreach() in /home/redat675/public_html/multunus-twitter/TheNewTest.php on line 87', this just means that the API limit is exceeded and that the solution(if any) mentioned will be partial/incorrect. So, wait for maybe 10 minutes and try again.
 - **I have not implemented designed a UI for this, since I am basically a backend developer.**


Changes:
 - Reduced the rate limit by 5 times(by using application-only authentication)
 - Implemnented cron job to further reduce rate limit
 - Implemented a front-end file separately
 - Improvement in the view(Now it is similar to that in puzzle.multunus.com)
 - Removed dependency(TwitterAPIExchange.php)

Changes #2:
- Implemented OOPs
- Created model
- Created a separate landing page
- Added cross browser compatibility
- Validation added