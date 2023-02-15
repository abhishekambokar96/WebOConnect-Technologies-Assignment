<?php
add_action('init', 'connect_another_db');
function connect_another_db() {
    global $kgappdb;
    $kgappdb = new wpdb(DB_USER, DB_PASSWORD, APP_DB_NAME, DB_HOST);
}

add_action("wpcf7_before_send_mail", "wpcf7_do_something_else");
function wpcf7_do_something_else($cf7) {
	global $kgappdb;

    $wpcf = WPCF7_ContactForm::get_current();
    $submission = WPCF7_Submission::get_instance();

    $data = $submission->get_posted_data();

    $user_count = $kgappdb->get_var( "SELECT COUNT(*) FROM `mdl_kgeduworld_user` WHERE `username` ='".$data['your-email']."'" );
    if($user_count == 0)
    {
	    $kgappdb->insert(
			'mdl_kgeduworld_user',
			array(
				'username' => $data['your-email'],
				'password' => password_hash('India@123', PASSWORD_DEFAULT),
			),
			array(
				'%s',
				'%s',
			)
		);

		$user_id = $kgappdb->insert_id;

		$kgappdb->insert(
			'mdl_kgeduworld_personal_details',
			array(
				'user_id' => $user_id,
				'phone_no' => $data['tel-989'],
			),
			array(
				'%d',
				'%s',
			)
		);

		$personal_id =$kgappdb->insert_id;

		$kgappdb->insert(
			'mdl_kgeduworld_user_details_status',
			array(
				'user_id' => $user_id,
				'personal_details' => $personal_id,
			),
			array(
				'%d',
				'%d',
			)
		);
	}

	// print_r($data);
}