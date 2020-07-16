<?php 
require_once __DIR__ . '/vendor/autoload.php'; // Autoload files using Composer autoload

$phpCricket = new PhpCricket\PhpCriclib('972838506d81e4fe544747bfe5ee9eba', '63a0dd622a5263b0751fdea6acc74389', 'Litzscore.suyatesting', 'developer');


/**
* NOTE: To access the Cricket API's data, you need Valid Match Keys.
*
* Here, you may try with some Free Match Keys.
*
*/

/* Get Match Details */
// $getMatch = $phpCricket->getMatch('dev_season_2014_q3', 'summary_card');
// echo json_encode($getMatch);


/* Get BallbyBall Details */
// $getBallbyBall = $phpCricket->getBallByBall('dev_season_2014_q3', 'b_1_10');
// echo json_encode($getBallbyBall);


/* Get Recent Match Details */
// $getRecentMatch = $phpCricket->getRecentMatch('dev_season_2014', 'micro_card');
// echo json_encode($getRecentMatch);


/* Get Recent Season Details */
// $getRecentSeason = $phpCricket->getRecentSeason();
// echo json_encode($getRecentSeason);


/* Get Schedule Details */
// $getSchedule = $phpCricket->getSchedule('2013-05');
// echo json_encode($getSchedule);


/* Get Season Schedule Details */
// $getSeasonSchedule = $phpCricket->getSeasonSchedule('dev_season_2014', '2013-05');
// echo json_encode($getSeasonSchedule);


/* Get Player Stats Details */
// $getPlayerStats = $phpCricket->getPlayerStats('ms_dhoni', 'icc');
// echo  json_encode($getPlayerStats);


/* Get Season Player Stats Details */
// $getSeasonPlayerStats = $phpCricket->getSeasonPlayerStats('asiacup_2016', 'ms_dhoni');
// echo json_encode($getSeasonPlayerStats);


/* Get Season Details */
// $getSeason = $phpCricket->getSeason('dev_season_2014', 'micro_card');
// echo json_encode($getSeason);


/* Get Season Stats Details */
// $getSeasonStats = $phpCricket->getSeasonStats('dev_season_2014');
// echo json_encode($getSeasonStats);


/* Get Season Points Details */
// $getSeasonPoints = $phpCricket->getSeasonPoints('dev_season_2014');
// echo json_encode($getSeasonPoints);


/* Get Season Team Details */
// $getSeasonTeam = $phpCricket->getSeasonTeam('dev_season_2014', 'dev_season_2014_teamx', 'icc');
// echo json_encode($getSeasonTeam);


/* Get Overs Summary Details */
// $getOverSummary = $phpCricket->getOversSummary('dev_season_2014_q6');
// echo json_encode($getOverSummary);


/* Get News Aggregation */
// $getNewsAgg = $phpCricket->getNewsAggregation();
// echo json_encode($getNewsAgg);


/* Get Coverage MG101*/
// $getCoverage = $phpCricket->getCoverage();
// echo json_encode($getCoverage);

/* Get Board Schedule MG101*/
// $getBoardSchedule = $phpCricket->getBoardSchedule('c.board.ecc.2f218','2020-07');
// echo json_encode($getBoardSchedule);

/* Get Season Schedule MG101 */
// $getSeasonScheduleMg101 = $phpCricket->getSeasonScheduleMg101('c.season.ecsk.8bf7c','c.board.ecc.2f218','2020-07');
// echo json_encode($getSeasonScheduleMg101);

/* Get Season recent MG101*/
// $getSeasonRecent = $phpCricket->getSeasonRecent('c.season.ecsk.8bf7c');
// echo json_encode($getSeasonRecent);

/* Get Season MG101 */
// $getSeasonMG101 = $phpCricket->getSeasonMG101('c.season.ecsk.8bf7c');
// echo json_encode($getSeasonMG101);

/* Get Season Team MG101 */
// $getSeasonteamMG101 = $phpCricket->getSeasonteamMG101('c.season.ecsg.1e82b','c.steam.ecsg_alm.6c487');
// echo json_encode($getSeasonteamMG101);

/* Get Match MG101 */
// $getMatchMG101 = $phpCricket->getMatchMG101('c.match.jkp_vs_wzc.7e4bd','metric_101');
// echo json_encode($getMatchMG101);

/* Get Season stats MG101 */
// $getSeasonStatsMg101 = $phpCricket->getSeasonStatsMg101('c.season.ecsg.1e82b');
// echo json_encode($getSeasonStatsMg101);

/* Get Season Player stats MG101 */
// $getSeasonPlayerStatsMg101 = $phpCricket->getSeasonPlayerStatsMg101('c.season.ecsg.1e82b','c.player.muhammad_tanveer.993a3');
// echo json_encode($getSeasonPlayerStatsMg101);

/* Get fantasy match credit MG101 */
// $getFantasyMatchCredit = $phpCricket->getFantasyMatchCredit('c.match.alm_vs_ssd.abb60','RZ-C-A101');
// echo json_encode($getFantasyMatchCredit);

/* Get fantasy match points MG101 */
// $getFantasyMatchPoints = $phpCricket->getFantasyMatchPoints('c.match.alm_vs_ssd.abb60','RZ-C-A101');
// echo json_encode($getFantasyMatchPoints);