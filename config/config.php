<?php
/**
 * 4PSA VoipNow App: Click2Conference
 *  
 * This file stores all the configuration parameters like: the IP, 
 * OAuth autorization paramters, group to invite to the conference
 *
 * @version 2.0.0
 * @license released under GNU General Public License
 * @copyright (c) 2012 4PSA. (www.4psa.com). All rights reserved.
 * @link http://wiki.4psa.com
*/

/**
 * Flags to signal failure to iniate the conference
*/
define('CONFERENCE_FAILED', '1');

/**
 * Flags to signal the start of the conference
*/
define('CONFERENCE_STARTED', '0');

/**
 * The IP/Hostname of the VoipNow Professional server
 * @global string
*/
$config['VN_SERVER_IP'] = 'CHANGEME';

/**
 * The number, in extended format, of the extension of type conference that will
 * connect the users in a call 
 * @global string
*/
$config['VN_CONFERENCE_EXT'] = 'CHANGEME';

/**
 * APP ID for 3-legged OAuth
 * Must be fetched from VoipNow interface
 * @global string
 */
$config['OAUTH_APP_ID'] = 'CHANGEME';

/**
 * APP Secret for 3-legged OAuth
 * Must be fetched from VoipNow interface
 * @global string
 */
$config['OAUTH_APP_SECRET'] = 'CHANGEME';

/**
 * Array containing the numbers that will be invited to a conference
 * You can add as many groups as you like, the Default group is used only as demo
 * @global array
 * @example $config['GROUPS']['Default'] = array('0004*003', '777777777')
 *			$config['GROUPS']['CustomGroup'] = array('0004*023', '888888888')
*/
$config['GROUPS']['Default'] = array();
?>