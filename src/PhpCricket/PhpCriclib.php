<?php

/**
 * @package 	Cricket-API 
 * @author 		CricketAPI Developers
 * @version 	1.0.0
 * 
 * Description: This is a php library to get live cricket score, recent matches and 
 * schedules using Cricket API.
 * It also provides live cricket score for ICC, IPL, CL and CPL.
 * 
 * @link https://www.cricketapi.com/
 */

namespace PhpCricket;

/**
 * Configuration details to access Cricket API Data.
 *
 * @param RCA_url			Url of Cricket API which gives response by sending request.
 *
 */
define('RCA_url', 'https://rest.cricketapi.com/rest/v2/', true);


class PhpCriclib
{

	private $access_token;
	private $credentials;

	/**
	 * getValidToken
	 *
	 * This function will get the provide valid token.
	 * This access token will be automatically passed to getData() to get the response.
	 *
	 */

	function getValidToken()
	{

		$deviceCheck = $this->credentials['device_id'];

		$fields_string = '';

		foreach ($this->credentials as $key => $value) {
			$fields_string .= $key . '=' . $value . '&';
		}
		$fields_string = rtrim($fields_string, '&');

		$infotxt = file_get_contents('accesstoken.txt');
		$info = json_decode($infotxt, TRUE);

		/**
		 * Check whether the token is expired or empty
		 */
		if (time() >= ($info[$deviceCheck]['expires'])) {
			// CuRL init
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, RCA_url . 'auth/');
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			$response = curl_exec($ch);
			$response = json_decode($response, true);
			curl_close($ch);

			// Store access token & expire time to a file.
			$token_data = array($this->credentials['device_id'] =>
			array(
				'token' => $response['auth']['access_token'],
				'expires' => $response['auth']['expires'],
				'deviceId' => $this->credentials['device_id']
			));

			$fp = fopen('accesstoken.txt', 'w');
			$stringData = json_encode($token_data);
			fwrite($fp, $stringData);
			// fwrite($fp,serialize($token_data));
			return $response['auth']['access_token'];
		} else {
			return $info[$deviceCheck]['token'];
		}
	}

	/**
	 * Authentication
	 *
	 * auth function
	 *
	 * This function provides you an access token by validating your request which allows you to call remaining functions.
	 * Call this auth function whenever your access token is expired.
	 *
	 * For more info, follow the documentation in the below URL.
	 * @link https://www.cricketapi.com/docs/auth_api/
	 */
	function __construct($accessKey, $secretKey, $appId, $deviceId)
	{
		if (strpos($appId, '.') !== false) {
			define('RCA_url', 'https://rest.cricketapi.com/rest/v4/');
		}
		$this->credentials = array(
			'access_key' => $accessKey, // Your Valid Access Key
			'secret_key' => $secretKey, // Your Valid Secret Key
			'app_id' => $appId,         // Your Valid App Id
			'device_id' => $deviceId 	// Your Unique Device Id
		);

		$this->access_token = $this->getValidToken();
	}

	/**
	 * getData function
	 *
	 * This function will build the query url for calling API.
	 *
	 * @param $req_url		Pass the desired API url value.
	 * @param $fields        Pass the parameters for appending to API.
	 *
	 */
	function getData($req_url, $fields)
	{
		$url = RCA_url . $req_url . '/?access_token=' . $this->access_token . '&' . http_build_query($fields);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_ENCODING, '');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($ch);
		$response = json_decode($response, true);
		curl_close($ch);
		return $response['data'];
	}



	/**
	 * Match:
	 *
	 * getMatch function
	 * 
	 * This function provides full details of a match.
	 *
	 * @param $match_key 			Key of match to show the Match details.
	 * @param $card_type (optional) 	There are three card types. 
	 *                       			i)   full_card (Default).
	 *                       			ii)  summary_card.
	 *									iii) micro_card.
	 *
	 * For more info, follow the documentation in the below URL.
	 * @link https://www.cricketapi.com/docs/match_api/
	 */
	function getMatch($match_key, $card_type)
	{
		$fields = array(
			'card_type' => $card_type
		);

		$url = 'match/' . $match_key;
		$response = $this->getData($url, $fields);
		return $response;
	}



	/**
	 * Ball By Ball:
	 * 
	 * getBallByBall function
	 *
	 * This function provides all details about balls of requested over. 
	 * If the over key is not provided with request, response will have 1st over of 1st innings.
	 *
	 * @param $match_key			Key of match to show ball-by-ball details.
	 * @param $over_key			Over Key is a combination of {TEAM_KEY}_{INNINGS_KEY}_{OVER_NUMBER}
	 *
	 * TEAM_KEY			Match team key, possible values are 'a' and 'b'.
	 * INNINGS_KEY 		Key of the innings, possible values are 1,2 and superover.
	 * OVER_NUMBER 		Over number starts from 1.
	 *
	 * For more info, follow the documentation in the below URL.
	 * @link https://www.cricketapi.com/docs/ball_by_ball_api/
	 */
	function getBallByBall($match_key, $over_key)
	{
		$fields = array();

		if ($over_key != '') {
			/**
			 * This url will results in particular over of that match.
			 */
			$url = 'match/' . $match_key . '/balls/' . $over_key;
		}

		if ($over_key == '') {
			/**
			 * This url will results in first over of that match.
			 */
			$url = 'match/' . $match_key . '/balls';
		}
		$response = $this->getData($url, $fields);
		return $response;
	}



	/**
	 * Recent Matches:
	 * 
	 * getRecentMatch function 
	 *
	 * This function provides the recent matches for specific season.
	 * Usually it will give 3 completed matches, 3 upcoming matches and all live matches.
	 *
	 * @param $season_key (optional)	Key of season to show the seasons recent matches data.
	 * @param $card_type (optional) 	There are three card types. 
	 *                       			i)   micro_card (Default).
	 *                       			ii)  summary_card.
	 *									iii) full_card (Not Supported).
	 *
	 * For more info, follow the documentation in the below URL.
	 * @link https://www.cricketapi.com/docs/recent_match_api/
	 */
	function getRecentMatch($season_key, $card_type)
	{
		## TODO Split the Recent match into 2 functions.
		$fields = array(
			'card_type' => $card_type
		);

		/**
		 * If Season Key is passed,
		 * It will get the details of recent matches in a particular season.
		 * 
		 * Otherwise, it will get the default recent matches information.
		 */
		if ($season_key != '') {
			$url = 'season/' . $season_key . '/recent_matches';
		} else {
			$url = 'recent_matches';
		}

		$response = $this->getData($url, $fields);
		return $response;
	}



	/**
	 * Recent Seasons:
	 *
	 * getRecentSeason function
	 * 
	 * This function provides list of all recent seasons. 
	 * It includes seasons from last two months, current month and next month.
	 *
	 * For more info, follow the documentation in the below URL.
	 * @link https://www.cricketapi.com/docs/recent_season_api/
	 */
	function getRecentSeason()
	{
		$fields = array();
		$url = 'recent_seasons';
		$response = $this->getData($url, $fields);
		return $response;
	}



	/**
	 * Schedule:
	 *
	 * getSchedule function
	 * 
	 * This function provides schedule for the given month.
	 * By default it provides current month schedule.
	 * If the date is provided with the request, you will get the schedule for that particular day.
	 *
	 * @param $date (optional) 			Format: YYYY-MM / YYYY-MM-DD
	 *
	 * For more info, follow the documentation in the below URL.
	 * @link https://www.cricketapi.com/docs/schedule_api/		
	 */
	function getSchedule($date)
	{
		$fields = array(
			'date' => $date
		);
		$url = 'schedule';
		$response = $this->getData($url, $fields);
		return $response;
	}



	/**
	 * Schedule(Season Based):
	 * 
	 * getSeasonSchedule function
	 * 
	 * This function provides schedule for the given season.
	 * By default it provides the schedule of the whole season. 
	 * If the date is provided with the request, you will get the schedule for that particular day.
	 *
	 * @param $season_key			Key of season to show the particular season schedule.
	 * @param $formate 				Format: YYYY-MM / YYYY-MM-DD		
	 *
	 * For more info, follow the documentation in the below URL.
	 * @link https://www.cricketapi.com/docs/schedule_api/		
	 */
	function getSeasonSchedule($season_key, $formate)
	{
		$fields = array(
			'formate' => $formate
		);
		$url = 'season/' . $season_key . '/schedule';
		$response = $this->getData($url, $fields);
		return $response;
	}



	/**
	 * Player Stats
	 * 
	 * getPlayerStats function
	 *
	 * This function provides stats about a player for the specified league or board.
	 *
	 * @param $player_key 		Player Key
	 * @param $league_key		League or Board Key
	 *
	 * For more info, follow the documentation in the below URL.
	 * @link https://www.cricketapi.com/docs/player_stats_api/
	 */
	function getPlayerStats($player_key, $league_key)
	{
		$fields = array();
		$url = 'player/' . $player_key . '/league/' . $league_key . '/stats';
		$response = $this->getData($url, $fields);
		return $response;
	}



	/**
	 * Season Player Stats:
	 * 
	 * getSeasonPlayerStats function
	 *
	 * This function gives stats about a player for the specified season. It includes the following information.
	 * 	a) Fielding: Number of catches.
	 * 	b) Batting: Best batting and Total Number of fours, sixes and runs scored in the season.
	 * 	c) Bowling: Best bowling and Total numbers boundries, runs given and total number of wickets taken.
	 *
	 * @param $season_key 		Key of season match to show the season player stats.
	 * @param $player_key		Key of player to show particular player stats.
	 *
	 * For more info, follow the documentation in the below URL.
	 * @link https://www.cricketapi.com/docs/season_points_api/
	 */
	function getSeasonPlayerStats($season_key, $player_key)
	{
		$fields = array();

		$url = 'season/' . $season_key . '/player/' . $player_key . '/stats';
		$response = $this->getData($url, $fields);
		return $response;
	}



	/**
	 * Season:
	 * 
	 * getSeason function 
	 *
	 * This function provides all information about a season such matches, teams, players, rounds and groups.
	 *
	 * @param $season_key 			Key of season match to show the data.
	 * @param $card_type (optional) 	There are three card types. 
	 *                       			i)   micro_card (Default).
	 *                       			ii)  summary_card.
	 *									iii) full_card (Not supported).
	 *
	 * For more info, follow the documentation in the below URL.
	 * @link https://www.cricketapi.com/docs/season_api/
	 */
	function getSeason($season_key, $card_type)
	{
		$fields = array(
			'card_type' => $card_type
		);
		$url = 'season/' . $season_key;
		$response = $this->getData($url, $fields);
		return $response;
	}



	/**
	 * Season Stats:
	 * 
	 * getSeasonStats function
	 *
	 * This function provides stats for the given season (series). It includes the following information.
	 * 	a) Total Number of Fours, Sixes and Runs scored in the season.
	 * 	b) Fielding: Most number of catches
	 * 	c) Batting: Best battings, Most fours, Most sixes, Most dots, Most boundries and Best battings.
	 *   d) Bowling: Most wickets, Most runs, Most fours, Most sixes, Most boundries and Best bowling.
	 *
	 * @param $season_key 		Key of season match to show the season stats.
	 * 
	 * For more info, follow the documentation in the below URL.
	 * @link https://www.cricketapi.com/docs/season_stats_api/
	 */
	function getSeasonStats($season_key)
	{
		$fields = array();
		$url = 'season/' . $season_key . '/stats';
		$response = $this->getData($url, $fields);
		return $response;
	}



	/**
	 * Season Points:
	 * 
	 * getSeasonPoints function
	 *
	 * This function gives points table for the given season.
	 *
	 * @param $season_key 		Key of season match to show the season points.
	 * 
	 * For more info, follow the documentation in the below URL.
	 * @link https://www.cricketapi.com/docs/season_points_api/
	 */
	function getSeasonPoints($season_key)
	{
		$fields = array();
		$url = 'season/' . $season_key . '/points';
		$response = $this->getData($url, $fields);
		return $response;
	}



	/**
	 * Season Team:
	 *
	 * getSeasonTeam function
	 *
	 * This function provides information about team and players in the season team.
	 * By default, the response doesn't contains any stats data.
	 *
	 * To get the response along with stats data, you need to pass the parameter 'stats_type'.
	 *
	 * @param $season_key 				Key of Season
	 * @param $season_team_key 			Key of the season team
	 * @param $stats_type (Optional)		Possible values - null, season, icc, ipl
	 *
	 * For more info, follow the documentation in the below URL.
	 * @link https://www.cricketapi.com/docs/season_team_api/
	 */
	function getSeasonTeam($season_key, $season_team_key, $stats_type)
	{
		$fields = array(
			'stats_type' => $stats_type
		);
		$url = 'season/' . $season_key . '/team/' . $season_team_key;
		$response = $this->getData($url, $fields);
		return $response;
	}



	/**
	 * OversSummary:
	 * 
	 * getOversSummary function
	 *
	 * This function will provides summary of each overs in a match. 
	 * Its usefull for showing over comparison, score worm and other charts.
	 *
	 * @param $match_key 		Key of match to show the Overs Summary.
	 *
	 * For more info, follow the documentation in the below URL.
	 * @link https://www.cricketapi.com/docs/over_summary_api/
	 */
	function getOversSummary($match_key)
	{
		$fields = array();
		$url = 'match/' . $match_key . '/overs_summary';
		$response = $this->getData($url, $fields);
		return $response;
	}



	/**
	 * News Aggregation:
	 * 
	 * getNewsAggregation function
	 *
	 * This function provides news feed from the popular rss.
	 * You need to enable the News Feed option in your app details page.
	 *
	 * For more info, follow the documentation in the below URL.
	 * @link https://www.cricketapi.com/docs/news_aggregation_api/
	 */
	function getNewsAggregation()
	{
		$fields = array();
		$url = 'news_aggregation';
		$response = $this->getData($url, $fields);
		return $response;
	}

	/**
	 * Coverage:
	 * 
	 * getCoverage function
	 *
	 * The Coverage API gives the list of Board & Competition details. 
	 * To get the cricket fixtures(schedule) you require the board and competition details for Metric Group 101. 
	 *
	 * For more info, follow the documentation in the below URL.
	 * @link https://www.cricketapi.com/docs/Core-API/Coverage-API/
	 */

	function getCoverage()
	{
		$fields = array();
		$url = 'coverage';
		$response = $this->getData($url, $fields);

		return $response;
	}

	/**
	 * Board Schedule:
	 * 
	 * getBoardSchedule function
	 *
	 * The BoardSchedule gives the list of schedule by the board. 
	 * Month is a optional parameter to get the board schedule by month.
	 *  @param $formate 				Format: YYYY-MM.
	 *
	 * For more info, follow the documentation in the below URL.
	 * @link https://www.cricketapi.com/docs/Core-API/Coverage-API/
	 */
	function getBoardSchedule($board_key, $month = '')
	{
		$fields = array();
		if (!empty($month)) {
			$fields = array(
				'month' => $month
			);
		}
		$url = 'board/' . $board_key . '/schedule';
		$response = $this->getData($url, $fields);
		return $response;
	}

	/**
	 * Schedule(Season Based):
	 * 
	 * getSeasonScheduleMg101 function
	 * 
	 * This function provides schedule for the given season.
	 * By default it provides the schedule of the whole season. 
	 * If the date is provided with the request, you will get the schedule for that particular day.
	 *
	 * @param $season_key			Key of season to show the particular season schedule.
	 * @param $formate 				Format: YYYY-MM		
	 *
	 * For more info, follow the documentation in the below URL.
	 * @link https://www.cricketapi.com/docs/Core-API/Schedule-API/		
	 */
	function getSeasonScheduleMg101($season_key, $month = '')
	{
		$fields = array();
		if (!empty($month)) {
			$fields = array(
				'month' => $month
			);
		}
		$url = $url = 'season/' . $season_key . '/schedule';
		$response = $this->getData($url, $fields);
		return $response;
	}

	/**
	 * Recent matches(Season based):
	 * 
	 * getSeasonRecent function
	 * 
	 *The Season Recent Matches API gives the upcoming three matches,
	 * all the live matches, and the completed three matches for the given season.
	 *
	 * @param $season_key			Key of season to show the particular season schedule.		
	 *
	 * For more info, follow the documentation in the below URL.
	 * @link https://www.cricketapi.com/docs/Core-API/Recent-Season-Matches-API/		
	 */
	function getSeasonRecent($season_key)
	{
		$fields = array();
		$url = $url = 'season/' . $season_key . '/recent_matches';
		$response = $this->getData($url, $fields);
		return $response;
	}


	/**
	 * Season Stats:
	 * 
	 * getSeasonStatsMg101 function
	 * 
	 *Best batsmens, Best bowlers, Fielders, Most dots, Fewest dots, Boundary details, 
	 *the player who hit fours & six, the player who scored high runs, 
	 *and the total runs for the given tournament(season).
	 *
	 * @param $season_key			Key of season to show the particular season schedule.		
	 * For more info, follow the documentation in the below URL.
	 * @link https://www.cricketapi.com/docs/Core-API/Season-Stats-API/		
	 */
	function getSeasonStatsMg101($season_key)
	{
		$fields = array();
		$url = $url = 'season/' . $season_key . '/stats';
		$response = $this->getData($url, $fields);
		return $response;
	}

	/**
	 * Season player Stats:
	 * 
	 * getSeasonPlayerStatsMg101 function
	 * 
	 *Season Player Stats API gives stats for the given player in the season.
	 *
	 * @param $season_key			Key of season to show the particular season schedule.
	 * @param $player_key					
	 * For more info, follow the documentation in the below URL.
	 * @link https://www.cricketapi.com/docs/Core-API/Season-Player-Stats-API/	
	 */
	function getSeasonPlayerStatsMg101($season_key, $player_key)
	{
		$fields = array();
		$url = $url = 'season/' . $season_key . '/player/' . $player_key.'/stats';
		$response = $this->getData($url, $fields);
		return $response;
	}

	/**
	 * Fantasy Match points:
	 * 
	 * getFantasyMatchPoints function
	 * 
	 *The Fantasy Match points API gives the player match points for every match. 
	 *The pre-configured rules caluclated the player match points.
	 *
	 * @param $match_key			Key of match to show the particular match
	 * @param $model					
	 * For more info, follow the documentation in the below URL.
	 * @link https://www.cricketapi.com/docs/Fantasy/Fantasy-Match-Points-API/	
	 */
	function getFantasyMatchPoints($match_key, $model)
	{
		$fields = array(
			'model' => $model
		);
		$url = $url = 'fantasy-match-points/' . $match_key;
		$response = $this->getData($url, $fields);
		return $response;
	}

	/**
	 * Fantasy Match credit:
	 * 
	 * getFantasyMatchCredit function
	 * 
	 *The Fantasy Match Credit Point API gives the player credit value. 
	 *The player credit point API calculates the credit points based on the recent performance stats and historical stats of the player. 
	 *Roanuz has dedicated AI to control the credit points. 
	 *
	 * @param $match_key			Key of match to show the particular match
	 * @param $model					
	 * For more info, follow the documentation in the below URL.
	 * @link https://www.cricketapi.com/docs/Fantasy/Fantasy-Match-Credit-API/	
	 */
	function getFantasyMatchCredit($match_key, $model)
	{
		$fields = array(
			'model' => $model
		);
		$url = $url = 'fantasy-match-credits/' . $match_key;
		$response = $this->getData($url, $fields);
		return $response;
	}
}
