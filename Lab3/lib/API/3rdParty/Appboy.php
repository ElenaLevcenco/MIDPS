<?php
namespace API;

use Slim\Slim;

class Appboy extends Slim
{
	protected  $app_group_id = 'Application Id Here';

	public function push_notification($users, $message, $messageTitle){
		// Determine the users you plan to message
		$external_user_ids = $users;

		// MESSAGING ENDPOINT VARIABLES ONLY
		$request_url = 'https://api.appboy.com/messages/send';

		// Establish the contents of your messages array
		$android_noti = array('alert' => $message,
		                      'title' => $messageTitle);
		$apple_noti = array('alert' => $message,
		                    'badge' => 999);
		// Instantiate the messages array
		$messages = array('android_push' => $android_noti,
		                  'apple_push' => $apple_noti);

		// Organize the data to send to the API as another map
		// comprised of your previously defined variables.
		$postData = array(
		  'app_group_id' => $this->app_group_id,
		  'external_user_ids' => $external_user_ids,
		  'messages' => $messages,
		);

		// CAMPAIGN TRIGGER ENDPOINT VARIABLES ONLY

		$request_url = 'https://api.appboy.com/campaigns/trigger/send';
		$campaign_id = 'Campaign Id Here';

		// Organize the data to send to the API as another map
		// comprised of your previously defined variables.
		$postData = array(
		  'app_group_id' => $this->app_group_id,
		  'campaign_id' => $campaign_id,
		  'external_user_ids' => $external_user_ids,
		);

		// END ENDPOINT-SPECIFIC VARIABLES

		// Create the context for the request
		$context = stream_context_create(array(
		    'http' => array(
		        'method' => 'POST',
		        'header' => "Content-Type: application/json\r\n",
		        'content' => json_encode($postData)
		    )
		));

		// Send the request
		$response = file_get_contents($request_url, FALSE, $context);

		// Print the response to ensure a successful request
		echo $response;
	}
}
