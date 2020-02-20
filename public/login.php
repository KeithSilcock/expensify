<?php
include_once './error.php';
include_once './redirect.php';
include_once './init.php';
include_once './ping_expensify.php';

$email = "";
$password = "";

if (isset($_POST['form'])) {
    $form = $_POST['form'];
    if (isset($form['email'])) {
        $email = $form['email'];
    }
    if (isset($form['password'])) {
        $password = $form['password'];
    }
    // expensify server should clean data, but just in case we'll break up anything funny
    $email = addslashes($email);
    $password = addslashes($password);

    if (!$email) {
        // User didn't enter an email...
        $errors = array(
            'error_message' => "No email provided.",
            'resolution' => "Please enter an email",
            'target' => "#error"
        );
        error($errors);
    }
    // perform reach out to expensify API to verify user login
    $result = authenticate_user($email, $password);

    // if no response
    if (is_null($result)) {
        $errors = array(
            // generic message due to unknown issue.
            'error_message' => "Something went wrong.",
            'resolution' => "Please try again. If the issue persists, please contact Keith Silcock at silcockk@gmail.com",
            'target' => "#error"
        );
        error($errors);
    }

    setcookie('accountID', $result['accountID'], 0);
    setcookie('email', $result['email'], 0);
    setcookie('authToken', $result['authToken'], 0);
    setcookie('current_time', time(), 0);

    $_SESSION['accountID'] = $result['accountID'];
    $_SESSION['email'] = $result['email'];
    $_SESSION['authToken'] = $result['authToken'];
    $_SESSION['last_active'] = time();

    // back to homepage to display table results
    redirect("/");
}

function authenticate_user($email, $password)
{
    $_PARTNER_NAME = 'applicant';
    $_PARTNER_PASSWORD = 'd7c3119c6cdab02d68d9';
    $url_params = '?command=Authenticate';
    $data = array(
        'partnerName' => $_PARTNER_NAME, 'partnerPassword' => $_PARTNER_PASSWORD,
        'partnerUserID' => $email, 'partnerUserSecret' => $password,
    );

    $result = expensify_post($data, $url_params);

    return ($result);
}
