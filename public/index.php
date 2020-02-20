<?php
include_once "./api.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Expensify Take-Home Challenge</title>
  <!-- add some favicon flavor from my porfolio -->
  <link rel="shortcut icon" type="image/png" href="./favicon.ico" />
  <link rel="stylesheet" type="text/css" href="styles.css" />
</head>

<body>
  <div id="modal" style="display:none;">
    <div class="modal-background">
    </div>
    <div class="modal-contents">
      <!-- If there were more options for modal inputs, I would use javascript or php to serve this form.
           Since there will only be 1 form in here, I'll just hard code it to save time-->
      <div class="close-modal"><i class="fas fa-times"></i></div>
      <div class="form-box">
        <div id="modal-warning"></div>
        <h4 class="form-label">Create a New Transaction</h4>
        <form id="create-transaction-form" method="POST">
          <label for="amount">Amount: </label>
          <input id="amount" type="text" name="amount" />
          <label for="merchant">Merchant: </label>
          <input id="merchant" type="text" name="merchant" placeholder="" />
          <label for="date">Date of Transaction: </label>
          <input id="date" type="date" name="date">
          <button type="button" class="submit-button" onclick="submit_transaction();">Submit</button>
        </form>
      </div>
    </div>
  </div>
  <div id="error"></div>
  <?php
  // check if user is logged in. If not, display log in info
  $login_status = user_is_logged_in();
  if (!$login_status['allowed']) : ?>
    <div id="login-content">
      <!-- Add your login form here -->
      <div id="login-container">
        <div id="login-error">
          <?= $login_status['warning'] ?></div>
        <h3>Login:</h3>
        <form id="login-form" action="login.php" method="POST">
          <label for="email">Email: </label>
          <input id="email" type="text" name="email" placeholder="example@friendly.com" />
          <label for="password">Password: </label>
          <input id="password" type="password" name="password" />
        </form>
        <button id="login-button" onclick="login();">Submit</button>
      </div>
    </div>
  <?php else : ?>
    <!-- if user logged in, display log out button -->
    <div class="logout-container">
      <a href="logout.php">
        Logout
      </a>
    </div>

  <?php endif; ?>


  <?php
  // if user logged in, show transaction table
  if ($login_status['allowed']) : ?>
    <div id="transaction-table-container">
      <div class="header-bar">
        <h2>Transactions:</h2>
        <button id="create-transaction-button" class="submit-button" onclick="create_transaction()">Create Transaction</button>
      </div>
      <div id="table-loader" style="display:none;">
        <i id="load-spinner" class="fas fa-spinner"></i><span id="loader-text"></span>
      </div>
      <table id='transaction-table'>
        <thead>
          <tr>
            <th class='col-0'>Transaction Date</th>
            <th class='col-1'>Merchant</th>
            <th class='col-2'>Amount</th>
          </tr>
        </thead>

        <tbody id="transaction-table-body">
          <!-- Add the transaction rows here -->
        </tbody>
      </table>
    </div>
  <?php endif; ?>

  <!-- Javascript Files, we've included JQuery here, feel free to use at your discretion. Add whatever else you may need here too. -->
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <script type="text/javascript" src="script.js"></script>
  <!-- font awesome for loader icon -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/js/all.min.js"></script>
  <?php
  // only if user is logged in do we pull data on page load. 
  if ($login_status['allowed']) : ?>
    <script>
      $(document).ready(() => {
        $("#modal").css('display', 'block');
        set_modal(false);
        console.log("User logged in. Pulling table results.");
        get_table_results();
      });
    </script>

  <?php endif; ?>
</body>

</html>