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

function get_success_function(data) {
  console.log("successfully retrieved get data!");
  console.log("DATA: ", data);
}

function get_failure_function(error) {
  console.log("Did not successfully retrieved the get data...");
  console.log("ERROR: ", error);
}
