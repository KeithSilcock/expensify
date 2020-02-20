console.log("Hello Expensify Employees!");

// function to reach api server to get around CORS
function post_to_api_server(url, data, done_function, fail_function, always = null) {
  $.ajax({
    type: "POST",
    url: url,
    data: data,
    dataType: "JSON"
  })
    .done(success_data => {
      success_data = check_response_type(success_data);
      done_function(success_data);
    })
    .fail(error => {
      handle_error(error);
      fail_function();
    })
    .always(always_data => {
      if (typeof always === "function") {
        always();
      }
    });
}

// API server uses key-object convention to determine action.
// This function checks to see what was returned by our API server
// and then how to react.
function check_response_type(data) {
  data_response = Object.keys(data);
  for (response_type_idx in data_response) {
    response_type = data_response[response_type_idx];
    switch (response_type) {
      case "ERROR":
        create_error(data["ERROR"]);
        return false;
      case "MODAL":
        // create modal... un-used for now
        break;
      case "REDIRECT":
        redirect(data["REDIRECT"]);
        return false;
      case "WARNING":
        create_warning(data["WARNING"]);
        return false;
      default:
        // on success, just return data
        return data["SUCCESS"];
    }
  }
}

// Login function
function login() {
  console.log("Attempting login");
  let url = "/login.php";
  // Get all form data and put it into a nice array
  let form = form_decode($("#login-form").serializeArray());

  // since we're going to a specific url, we dont need an action. If we were pinging the api.php, we would need an action.
  let data = {
    form: form
  };

  post_to_api_server(
    url,
    data,
    data => {
      console.log("successfully logged in!");
    },
    error => {
      // error message creation is handled in post_to_api_server
      console.log("there was an error loggin user in");
    },
    always_data => {
      set_loader(false);
    }
  );
}

// call to query all table transactions
function get_table_results() {
  console.log("Pulling results");
  let url = "/api.php";
  let action = "get-table-data";

  // displays loading message over table
  set_loader(true, "Pulling data, Please wait...");

  // no form needed, just getting results
  let data = {
    action: action
  };

  post_to_api_server(
    url,
    data,
    data => {
      // trying to change message, but DOM freezes with large append.
      // not worth the time to fix, unfortunately.
      set_loader(true, "Now creating your table. Please wait...");
      build_table(data);
    },
    error => {
      // error message creation is handled in post_to_api_server
      // would hide these in production environment
      console.log("Did not successfully retrieved the get data...");
      console.log("ERROR: ", error);
    },
    always_data => {
      set_loader(false);
    }
  );
}

// constructs transaction table with all transactions.
function build_table(data) {
  let transaction_list = data["transactionList"];

  let table_body = $("#transaction-table-body");
  let table_row_array = [];

  for (transaction_idx in transaction_list) {
    let transaction = transaction_list[transaction_idx];
    let table_row = $(`<tr>`);

    // Each transaction has 36 fields.
    // Options here include either running through all 36 transaction fields... Excessive...
    // Or, we could just pull the data we want. That's what I'm going to do.

    // Otherwise, we would do this:
    // for (transaction in transaction_list[transaction_idx]) {
    //   console.log("With over 10,000 rows, at 36 iterations per, we're killing our client!");
    // }

    // Instead, we'll get a list of important fields and pull from that only.
    // Our table only has 3 headers: Transaction Date, Merchant, and Amount.
    // Assuming the "created" paramter isn't valid since a lot of the data says it is from the year 2999,
    // I'll be using the timestamp from "inserted".
    // We could also do all of this on the server side before sending the data over if we didn't want
    // to send the client all of the transaction data (for security purposes).

    // create an array of table datas (<td>)
    transaction_row_array = create_transaction_row(transaction);

    // HUGE overhead when appending to DOM with jQuery's append. Arrays are much quicker for storage purposes.
    // Above, we created a large array before pushing whole array to DOM.
    // If we were appending straight to DOM every loop, it has over 3 minute creation time.
    // But if we append the whole array at once, it only has around 5 seconds creation time. MUCH better.

    table_row.append(transaction_row_array);
    table_row_array.push(table_row);
  }
  table_body.append(table_row_array);
}

function create_transaction_row(transaction) {
  // 3 useful parameters from the 36 transaction parameters we want.
  let columns_to_display = ["inserted", "merchant", "amount"];
  transaction_row_array = [];
  for (column_idx in columns_to_display) {
    let column = columns_to_display[column_idx]; //for readability only, separated into variables.
    let value = transaction[column];

    // create special case for dollar amount column
    // will make negative values red, but keep positive black, not color them green (tacky :P).
    let extra_class = "";
    if (column_idx == 2) {
      if (value < 0) {
        extra_class = " red";
      }
      value = `$${(value / 100).toFixed(2)}`;
    }

    transaction_row_array.push($(`<td class="col-${column_idx}${extra_class}">${value}</td>`));
  }
  return transaction_row_array;
}

function create_transaction() {
  // if there were more potential forms, I would code out a form creator here to insert into the modal.
  // since it's just the transaction form, I've hard coded it in the index.php
  set_modal(true);
}
// submitting the new transaction to our API server.
function submit_transaction() {
  console.log("submitting transaction");
  let url = "/api.php";
  let action = "create-transaction";
  // Get all form data and put it into a nice array
  let form = form_decode($("#create-transaction-form").serializeArray());

  // form validation: check for all required values.
  if (!form["amount"] || !form["merchant"] || !form["date"]) {
    let error = {
      error_message: "All values are required",
      resolution: "Please fill out the form.",
      target: "#modal-warning"
    };
    return create_error(error);
  }

  // check if user entered a number
  if (!parseFloat(form["amount"])) {
    let error = {
      error_message: "Amount must be number only.",
      resolution: "Please enter an amount.",
      target: "#modal-warning"
    };
    return create_error(error);
  }
  // format amount to cents...
  form["amount"] = form["amount"] * 100;

  let data = {
    action: action,
    form: form
  };

  post_to_api_server(
    url,
    data,
    success_data => {
      // success, insert the row
      insert_row(success_data["transactionList"], $("#transaction-table-body"));
      console.log("successfully created a transaction!");
      set_modal(false);
    },
    error => {
      console.log("there was an error creating the transaction.");
      set_modal(false);
    },
    always_data => {}
  );
}

// prepends the data to the top of the table.
function insert_row(data, table) {
  let table_row = $(`<tr>`).append(create_transaction_row(data[0]));
  table.prepend(table_row);
}

///////////////////////
/// Document Ready: ///
///////////////////////
$(document).ready(() => {
  // quick close for modal
  $(".modal-background").on("click", () => {
    set_modal(false);
  });
  $(".close-modal").on("click", () => {
    set_modal(false);
  });

  // ease of use for login
  // if user presses enter while in the login boxes, will submit instead of HAVING to press submit.
  $("#email").keypress(function(event) {
    var keycode = event.keyCode ? event.keyCode : event.which;
    if (keycode == "13") {
      login();
    }
  });
  $("#password").keypress(function(event) {
    var keycode = event.keyCode ? event.keyCode : event.which;
    if (keycode == "13") {
      login();
    }
  });
});

///////////////////////
// helper functions: //
///////////////////////
function clear_form() {
  $("#modal-warning").empty();
  $("#amount").val("");
  $("#merchant").val("");
  $("#date").val("");
}
// basically toggle modal.
function set_modal(active) {
  active ? $("#modal").show() : $("#modal").hide();
  clear_form();
}
// quick function for weird errors that haven't been handled yet.
function handle_error(error) {
  console.log("Error from server", error);
}
function create_error(error) {
  // Our API server creates static error messages, no need to worry about cleaning html
  defaults = {
    error_code: null,
    error_message: null,
    resolution: null,
    target: null
  };
  // combining objects.
  error = { ...defaults, ...error };
  let error_message = `<p class='error'>${error.error_message}</p><p class='error'>${error.resolution}</p>`;
  $(error.target).html(error_message);
  return false;
}
function create_warning(warning) {
  defaults = {
    warning_message: null,
    resolution: null,
    target: null
  };
  // combining objects.
  warning = { ...defaults, ...warning };

  let warning_message = `<p class='warning'>${warning.warning_message}</p><p class='warning'>${warning.resolution}</p>`;
  $(warning.target).html(warning_message);
}

// set table loader and text to display
function set_loader(active, text = "") {
  active ? $("#table-loader").show() : $("#table-loader").hide();
  // disables "create transaction" button while table is loading. Doesnt work great because button still looks clickable. Would fix with more time.
  $("#create-transaction-button").attr("disabled", active);
  $("#loader-text").text(text);
}

function redirect(location) {
  // for log in only!
  window.location.replace(location);
}

// Decoding jquery serialized array to personal preference
function form_decode(form) {
  let decoded_form = {};
  for (input_index in form) {
    let input_vals = form[input_index];
    decoded_form[input_vals["name"]] = input_vals["value"];
  }
  return decoded_form;
}
