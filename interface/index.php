<?php
/**
 * 4PSA VoipNow App: Click2Conference
 *  
 * Application displays a button and a drop-down list.
 * You can choose from the list a group of numbers that you can invite to a conference
 * To add more groups just add new option for variable GROUPS located in the
 * config.php file
 *
 * @version 2.0.0
 * @license released under GNU General Public License
 * @copyright (c) 2012 4PSA. (www.4psa.com). All rights reserved.
 * @link http://wiki.4psa.com
*/

require_once('config/config.php');
require_once('language/en.php');

/* Generate the select containing the groups that will be invited */
$htmlGroups = '<select id="groups" class="select">';
foreach($config['GROUPS'] as $name => $user_numbers) {
	$htmlGroups .= '<option value="'.$name.'">'.$name.'</option>';
}
$htmlGroups .= '</select>';

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=9" />
		<title>Click2Conference</title>
		<link rel="stylesheet" type="text/css" href="skin/main.css" />
		<script type="text/javascript" src="js/jquery.min.js"></script>
		<script type="text/javascript">
			$().ready(function() {
				$('#click2conference').click(function() {
					$.ajax({
						url: "request.php",
						data: "group="+$('#groups').val(),
						success: function(data, textStatus, jqXHR){
							var message = '';
							if (data == 1) {
								message = 'Failed to invite group.';
							}
							else {
								message = 'Successfully invited group';
							}
							
							$('#info_msg').html('<span class="warning-icon"></span>'+message).css("display", "block");
							}
						});
				});
			});
		</script>
	</head>
	<body>
		<div class="button">
			<div class="header">
				Click2Conference
			</div>
			<div id="info_msg" class="info"></div>
			<div class="description">
				Select a group to invite to a conference call.
			</div>
			<div class="container">
				<?php echo $htmlGroups; ?>
				<button id="click2conference" type="button" title="<?php echo $msgArr['invite']; ?>"><?php echo $msgArr['invite']; ?></button>
			</div>
		</div>
	</body>
</html>