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

  <div class="loader" style="display:none;">Currently loading your results. Please wait...</div>

    <?php 
    $login_status = user_is_logged_in();
    if(!$login_status['allowed']) : ?>
    <div id="loginContent">
      <!-- Add your login form here -->
      PLEASE REMOVE DEFAULT VALUES KEITH QQQQQQQQQQQQQQ
      <div id="login-container">
        <div id="login-error">
          <?=$login_status['warning']?></div>
        <h3>Login:</h3>
        <form id="login-form" action="login.php" method="POST">
          <input type="text" name="email" placeholder="example@friendly.com" value="expensifytest@mailinator.com" />
          <input type="password" name="password" value="hire_me" />
        </form>
        <button onclick="login();">Submit</button>
      </div>
    </div>
    <?php else : ?>
      <div>
        <a href="logout.php">
          Logout
        </a> 
      </div>

    <?php endif; ?>
    
    
    <?php
      if($login_status['allowed']) : ?>

    <div id="transactionTable">
      <h1>Transactions:</h1>
      <table>
        <thead>
          <tr>
            <th>Transaction Date</th>
            <th>Merchant</th>
            <th>Amount</th>
          </tr>
        </thead>

        <tbody id="transactionTableBody">
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
    <?php
      if($login_status['allowed']) : ?>
    <script>
      $(document).ready(()=>{
        console.log("User logged in. Pulling table results.")
        get_table_results();
      })
    </script>
    <?php endif; ?> 
  </body>
</html>
