console.log("Hello World!");

function post_to_api_server(url, data, done_function, fail_function, always = null) {
  $.ajax({
    type: "POST",
    url: url,
    data: data,
    dataType: "JSON"
  })
    .done(success_data => {
      success_data = check_response_type(success_data);
      if (success_data) {
        done_function(success_data);
      }
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

function check_response_type(data) {
  data_response = Object.keys(data);
  for (response_type_idx in data_response) {
    response_type = data_response[response_type_idx];
    switch (response_type) {
      case "ERROR":
        create_error(data["ERROR"]);
        return false;
      case "MODAL":
        // create modal
        break;
      case "REDIRECT":
        redirect(data["REDIRECT"]);
        return false;
      case "WARNING":
        create_warning(data["WARNING"]);
        return false;
      default:
        // successful
        return data["SUCCESS"];
    }
  }
}

function login() {
  console.log("Attempting login");
  let url = "/login.php";
  // Get all form data and put it into a nice array
  let form = form_decode($("#login-form").serializeArray());

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
      console.log("there was an error loggin user in");
    },
    always_data => {
      set_loader(false);
    }
  );
}

function get_table_results() {
  console.log("Pulling results");
  let url = "/api.php";
  let action = "get-table-data";

  set_loader(true);

  let data = {
    action: action
  };

  post_to_api_server(
    url,
    data,
    data => {
      build_table(data);
    },
    error => {
      // would hide these in production environment
      console.log("Did not successfully retrieved the get data...");
      console.log("ERROR: ", data);
    },
    always_data => {
      set_loader(false);
    }
  );
}

function build_table(data) {
  let transaction_list = data["transactionList"];
  console.log(("transaction_list", transaction_list));

  let table_body = $("#transaction-table-body");
  let table_row_array = [];

  for (transaction_idx in transaction_list) {
    let transaction = transaction_list[transaction_idx];
    let table_row = $(`<tr>`);

    // Options here include either running through all 36 transaction fields... Excessive...
    // Or, we could just pull the data we want. That's what I'm going to do.

    // Otherwise, we would do this:
    // for (transaction in transaction_list[transaction_idx]) {
    //   console.log("With over 10,000 rows, at 36 iterations per, we're killing our client!");
    // }

    // Instead, we'll get a list of important fields and pull from that only.
    // Our table only has 3 headers: Transaction Date, Merchant, and Amount.
    // Assuming the "created" paramter isn't valid since it is from the year 2999,
    // I'll be using the timestamp from "inserted".
    // We could also do this on the server side if we didn't want to send the client all of the transaction data (for security purposes).

    transaction_row_array = create_transaction_row(transaction);

    // HUGE overhead when appending to DOM with jQuery's append. Arrays are much quicker for storage purposes.
    // Here we create large array before pushing whole set to DOM.
    // Appending straight to DOM every time: over 1 minute loading time
    // Appending from array, instead of individually: around 5 seconds load time. MUCH better.

    table_row.append(transaction_row_array);
    table_row_array.push(table_row);
  }
  table_body.append(table_row_array);
}

function create_transaction_row(transaction) {
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
  // if there were more potential forms, I would code out a form creator to insert into the modal.
  // since it's just the transaction form, I've hard coded it in the index.php
  set_modal(true);
}
function submit_transaction() {
  console.log("submitting transaction");
  let url = "/api.php";
  let action = "create-transaction";
  // Get all form data and put it into a nice array
  let form = form_decode($("#create-transaction-form").serializeArray());

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
      // success, insert row
      insert_row(success_data["transactionList"], $("#transaction-table-body"));
      console.log("successfully logged in!");
    },
    error => {
      console.log("there was an error loggin user in");
    },
    always_data => {
      set_modal(false);
    }
  );
}

function insert_row(data, table) {
  let table_row = $(`<tr>`).append(create_transaction_row(data[0]));
  table.prepend(table_row);
}

///////////////////////
/// Document Ready: ///
///////////////////////
$(document).ready(() => {
  $(".modal-background").on("click", () => {
    set_modal(false);
  });
  $(".close-modal").on("click", () => {
    set_modal(false);
  });
});

///////////////////////
// helper functions: //
///////////////////////
function set_modal(active) {
  active ? $("#modal").show() : $("#modal").hide();
}
function handle_error(error) {
  console.log("Error from server", error);
}
function create_error(error) {
  // Server creates static error messages, no need to worry about cleaning html
  defaults = {
    error_code: "",
    error_message: "",
    resolution: "",
    target: ""
  };
  // combining objects.
  error = { ...defaults, ...error };
  let error_message = `<p class='error'>${error.error_message}</p><p class='error'>${error.resolution}</p>`;
  $(error.target).html(error_message);
}
function create_warning(warning) {
  defaults = {
    warning_message: "",
    resolution: "",
    target: ""
  };
  // combining objects.
  warning = { ...defaults, ...warning };

  let warning_message = `<p class='warning'>${warning.warning_message}</p><p class='warning'>${warning.resolution}</p>`;
  $(warning.target).html(warning_message);
}

function set_loader(active) {
  active ? $("#table-loader").show() : $("#table-loader").hide();
  $("#create-transaction-button").attr("disabled", active);
}

function redirect(location) {
  // for log in only!
  window.location.replace(location);
}

function form_decode(form) {
  // Decoding jquery serialized array to personal preference
  let decoded_form = {};
  for (input_index in form) {
    let input_vals = form[input_index];
    decoded_form[input_vals["name"]] = input_vals["value"];
  }
  return decoded_form;
}
