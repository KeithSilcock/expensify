<?php
include_once "./api.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Expensify Take-Home Challenge</title>
  <link rel="stylesheet" type="text/css" href="styles.css" />
</head>

<body>
  <div id="modal" style="display:none;">
    <div class="modal-background">
    </div>
    <div class="modal-contents">
      <!-- If there were more options for modal inputs, I would use javascript to create this form.
           Since there will only be 1 form in here, I'll just code it with HTML -->
      <div class="close-modal"><i class="fas fa-times"></i></div>
      <div class="form-box">
        <h4 class="form-label">Create a New Transaction</h4>
        <form id="create-transaction-form" method="POST">
          <label for="amount">Amount: </label>
          <input type="text" name="amount" />
          <label for="merchant">Merchant: </label>
          <input type="text" name="merchant" placeholder="" />
          <button type="button" class="submit-button" onclick="submit_transaction();">Submit</button>

        </form>
      </div>
    </div>
  </div>
  <div id="error"></div>
  <?php
  $login_status = user_is_logged_in();
  if (!$login_status['allowed']) : ?>
    <div id="loginContent">
      <!-- Add your login form here -->
      <div id="login-container">
        <div id="login-error">
          <?= $login_status['warning'] ?></div>
        <h3>Login:</h3>
        <form id="login-form" action="login.php" method="POST">
          <input type="text" name="email" placeholder="example@friendly.com" />
          <input type="password" name="password" />
        </form>
        <button onclick="login();">Submit</button>
      </div>
    </div>
  <?php else : ?>
    <div class='logout-container'>
      <a href="logout.php">
        Logout
      </a>
    </div>

  <?php endif; ?>


  <?php
  if ($login_status['allowed']) : ?>

    <div id="transaction-table-container">
      <div class="header-bar">
        <h2>Transactions:</h2>
        <button id="create-transaction-button" class="submit-button" onclick="create_transaction()">Create Transaction</button>
      </div>
      <div id="table-loader" style="display:none;">
        <i id="load-spinner" class="fas fa-spinner"></i> Please wait...
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

    <div id="transactionForm">
      <!-- Add your create transaction form here -->
    </div>
  <?php endif; ?>

  <!-- Javascript Files, we've included JQuery here, feel free to use at your discretion. Add whatever else you may need here too. -->
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <script type="text/javascript" src="script.js"></script>
  <!-- font awesome for loader icon -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/js/all.min.js"></script>
  <?php
  if ($login_status['allowed']) : ?>
    <script>
      $(document).ready(() => {
        $("#modal").css('display', 'block');
        set_modal(false);
        console.log("User logged in. Pulling table results.");
        get_table_results();
      })
    </script>

  <?php endif; ?>
</body>

</html>