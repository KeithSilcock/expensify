console.log("Hello World!");

// function get_function() {
//   console.log("working");
//   let url = "/api.php";
//   let data = { action: "test" };

//   $.ajax({
//     type: "POST",
//     url: url,
//     data: data,
//     dataType: "JSON"
//   })
//     .done(get_success_function)
//     .fail(get_failure_function);
// }

function post_to_api_server(url, data, done_function, fail_function) {
  $.ajax({
    type: "POST",
    url: url,
    data: data,
    dataType: "JSON"
  })
    .done(response => {
      response = check_response_type(response);
      done_function(response);
    })
    .fail(error => {
      handle_error(error);
      fail_function();
    });
}

function check_response_type(data) {
  data_response = Object.keys(data);
  for (response_type_idx in data_response) {
    response_type = data_response[response_type_idx];
    switch (response_type) {
      case "ERROR":
        create_error(data["ERROR"]);
        return null;
      case "MODAL":
        // create modal
        break;
      case "REDIRECT":
        redirect(data["REDIRECT"]);
        return null;
      case "WARNING":
        create_warning(data["WARNING"]);
        return null;
      default:
        // successful
        return data["SUCCESS"];
    }
  }
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
function handle_error(error) {
  console.log("Error from server", error);
}

function get_success_function(data) {
  set_loader(false);
  console.log("successfully retrieved get data!");
  console.log("DATA: ", data);
}

function get_failure_function(error) {
  set_loader(false);
  console.log("Did not successfully retrieved the get data...");
  console.log("ERROR: ", error);
}

function login() {
  console.log("Attempting login");
  let url = "/login.php";
  // Get all form data and put it into a nice array
  let form = form_decode($("#login-form").serializeArray());

  let data = {
    form: form
  };

  post_to_api_server(url, data, get_success_function, get_failure_function);
}

function get_table_results() {
  console.log("Pulling results");
  let url = "/api.php";
  let action = "get-table-data";

  set_loader(true);

  let data = {
    action: action
  };

  post_to_api_server(url, data, get_success_function, get_failure_function);
}

function set_loader(active) {
  let display = active ? "block" : "none";
  $(".loader").css("display", display);
}

function redirect(location) {
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
