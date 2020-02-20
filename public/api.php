<?php

include_once './check_login.php';
include_once './error.php';
include_once './redirect.php';
include_once './init.php';
include_once './ping_expensify.php';

$URL_BASE = "https://www.expensify.com/api";

if (isset($_POST['action'])  && !empty($_POST['action'])) {

  $action = $_POST['action'];

  switch ($action) {
    case "get-table-data":
      respond_success(get_table_data());
      break;

    case "create-transaction":
      if (isset($_POST['form'])) {
        respond_success(create_transaction($_POST['form']));
      } else {
        $errors = array(
          'error_message' => "Something went wrong",
          'resolution' => "Please try again. If the issue persists, please contact Keith Silcock at silcockk@gmail.com",
          'target' => "#modal-warning"
        );
        error($errors);
      }
      break;
    default:
      // log that a broken action has been recorded and someone is trying to reach our server NEFARIOUSLY! (maybe...)
      error_log("An unexpected action has been called: " . $action);
  }
}

function get_table_data()
{
  $url_params = '?command=Get';
  $data = array(
    'authToken' => $_SESSION['authToken'], 'returnValueList' => "transactionList",
    'startDate' => null, 'endDate' => null,
  );

  $table_data = expensify_post($data, $url_params);

  return ($table_data);
}

function create_transaction($form)
{
  $url_params = '?command=CreateTransaction';
  $data = array(
    'authToken' => $_SESSION['authToken'], 'created' => $form['date'],
    'amount' => $form['amount'], 'merchant' => $form['merchant'],
  );

  $table_data = expensify_post($data, $url_params);

  return ($table_data);
}

function respond_success($data)
{
  echo json_encode(array("SUCCESS" => $data));
}
