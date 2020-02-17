console.log("Hello World!");

function get_function() {
  console.log("working");
  let url = "/api.php";
  let data = { action: "test" };

  $.ajax({
    type: "POST",
    url: url,
    data: data,
    dataType: "JSON"
  })
    .done(get_success_function)
    .fail(get_failure_function);
}

function post_to_server(url, data, done, fail) {
  $.ajax({
    type: "POST",
    url: url,
    data: data,
    dataType: "JSON"
  })
    .done(done)
    .fail(fail);
}
function check_for_error(data) {
  console.log("CHECKING FOR ERROR IN ", data);
}

function get_success_function(data) {
  console.log("successfully retrieved get data!");
  console.log("DATA: ", data);
}

function get_failure_function(error) {
  console.log("Did not successfully retrieved the get data...");
  console.log("ERROR: ", error);
}

function login() {
  console.log("Attempting login");
  let url = "/login.php";
  // Get all form data and put it into an array
  let form = $("#login-form").serializeArray();

  let data = {
    form: form
  };

  post_to_server(url, data, get_success_function, get_failure_function);
}
