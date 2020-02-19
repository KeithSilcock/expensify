<?php

function user_is_logged_in() {
    $login_out_label = "Login";
    $login_out_path = "/";
    $result = array("allowed"=>false,
                    "warning"=>"",
                    "error"=>"",
                    );

    // Permissions & SESSION Handling 
	if(isset($_SESSION['authToken'])) {
        // check if session has expired by checking if the user has done something within the last 2 hours.
        if(isset($_SESSION['last_active']) && (time() - $_SESSION['last_active'] > 7200) ){
            // user login is over 2 hours old. Clearing session
            session_unset();
            session_destroy();
            // creating parameter catch for J.S. to look for to present session expiration warning.
            $result['warning'] = "<p class='warning'>Your session has expired. Please log in again.</p>";
            return $result;
        }
        // else, user can continue
        $result['allowed'] = true;
    }
    session_write_close();
	return $result;
}

?>