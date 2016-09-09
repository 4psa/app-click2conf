<?php
/**
 * 4PSA VoipNow App: Click2Conference
 *  
 * This script make the request to the VoipNow server
 * The logic for inviting the users to the conference is located here
 *
 * @version 2.0.0
 * @license released under GNU General Public License
 * @copyright (c) 2012 4PSA. (www.4psa.com). All rights reserved.
 * @link http://wiki.4psa.com
*/

require_once('config/config.php');
require_once('plib/cURLRequest.php');

// init session, we will keep tokens in the session
session_start();
@date_default_timezone_set(@date('e'));
/**
 * Sets a handle for uncaught exceptions.
 * @param Exception $exception
 */
function exception_handler($exception) {
	echo "Uncaught exception: " , $exception->getMessage(), "\n";
}

set_exception_handler('exception_handler');

/**
 * Generate a new token based on App ID and App secret
 *
 * @return string token
 * @return boolean FALSE when token could not be generated
 */
function generateToken() {
    global $config;

    $reqUrl = 'https://'.$config['VN_SERVER_IP'].'/oauth/token.php';

    $request = new cURLRequest();
    $request->setMethod(cURLRequest::METHOD_POST);

    $fields = array(
        'grant_type' => 'client_credentials',
        'redirect_uri' => $_SERVER['PHP_SELF'],
        'client_id' =>  urlencode($config['OAUTH_APP_ID']),
        'client_secret' => urlencode($config['OAUTH_APP_SECRET']),
        'state' => '0',

    );
    $request->setBody($fields);
    $response = $request->sendRequest($reqUrl);

    $respBody = $response->getBody();
    if ($response->getStatus() == Response::STATUS_OK && isset($respBody['access_token'])) {
        $_SESSION['Click2Conference']['token'] = 'Bearer '.$respBody['access_token'];
        return 'Bearer '.$respBody['access_token'];
    }
    return false;
}

/**
 * Get the token used for previous requests, or generate a new one if none exists
 *
 * @return string token
 */
function getToken() {
    if (isset($_SESSION['Click2Conference']['token']) && $_SESSION['Click2Conference']['token']) {
          $token = $_SESSION['Click2Conference']['token'];
    } else {
        /* generate token */
        $token = generateToken();
        /* store token for next requests */
        $_SESSION['Click2Conference']['token'] = $token;
    }
    return $token;
}


/**
 * Make the UnifiedAPI request: InviteToConference
 * The request will first try to authenticate using the Oauth protocol, 
 * using the credentials from the config.php file
 * If the authentication is ok then the UnifiedAPI request will be sent
 *
 * @param string $phoneNumber the phone number to add to conference
 * 
 * @return string the JSON response
 *
*/
function sendRequest($phoneNumber){
	global $config;

    $token = getToken();
    if (!$token) {
        return false;
    }

    $headers = array(
        'Content-type' => 'application/json',
        'Authorization' => $token
    );

	/* This is the URL accessed using the REST protocol */
	$reqUrl = 'https://'.$config['VN_SERVER_IP'].'/unifiedapi/phoneCalls/@me/conferenceInvite';

	$request = new cURLRequest();
	$request->setMethod(cURLRequest::METHOD_POST);
	$request->setHeaders($headers);
	
	$jsonData = array(
		'extension' => $config['VN_CONFERENCE_EXT'],
		'phoneCallView' => array(array('source' => array($config['VN_CONFERENCE_EXT']),
				'destination' => array($phoneNumber))));
	
	$request->setBody(json_encode($jsonData));
	
	$response = $request->sendRequest($reqUrl);

    if ($response->getStatus() == Response::STATUS_FORBIDDEN) {
        // try to regenerate token
        $headers['Authorization'] = generateToken();
        $request->setHeaders($headers);
        // retry request
        $response = $request->sendRequest($reqUrl);
    }
	
	return $response->getBody(true);
}

/**
 * Process group information
 * Verify first that the group is valid
 * If the group is valid, invite each member to a conference call
 * A request is made for each user that must be called
 * The script does not check if the user answered the call
*/

if(!empty($_GET['group'])) {
	if(empty($config['GROUPS'][$_GET['group']]) || !is_array($config['GROUPS'][$_GET['group']])) {
		exit(CONFERENCE_FAILED);
	}
	
	$groups = $config['GROUPS'][$_GET['group']];
	$failed = false;
	foreach ($groups as $key => $number) {
	
		$result = sendRequest($number);
		$json = json_decode($result, true);
		if(isset($json['error'])) {
			$failed = true;
		} 
	}
	if(!$failed) {
		exit(CONFERENCE_STARTED);
	}
}
exit(CONFERENCE_FAILED);
?>