<?php
  //Config steps
  //1. set $authorized_ip to your ip address
  //2. set the db credentials in the openConn function

  //Authorize
  $authorized_ip = "99.99.999.999";
  if($_SERVER["REMOTE_ADDR"] !== $authorized_ip) {
    header('HTTP/1.0 403 Forbidden');
    echo "<h1>403: Forbidden</h1>";
    echo "<p>You do not have permission to access this page</p>";
    exit;
  }

  //Process the request
  $conn = openConn();

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST["new_proj_name"])) {
      create($conn, sanitizeData($_POST["new_proj_name"]));
    } else if(isset($_POST["proj_to_increment"])) {
      update($conn, sanitizeData($_POST["proj_to_increment"]));
    } else if(isset($_POST["proj_to_delete"])) {
      destroy($conn, sanitizeData($_POST["proj_to_delete"]));
    }
  }

  //always get all projects to render out
  $all_projects = read($conn);
  $conn->close();

  //CRUD functions

  function create($conn, $proj_name) {
    //logged_hrs and updated_at have default values set, so we let the MySQL handle it.
    $sql = "INSERT INTO projects (project) VALUES ('$proj_name')";
    if(!$conn->query($sql)) {
      echo $conn->error;
      $conn->close();
      die();
    }
  }

  /**
  * read the whole table
  */
  function read($conn) {
    return $conn->query("SELECT * FROM projects");
  }

  /**
  * increment logged hours by 1 on specified project
  */
  function update($conn, $proj_name) {
    $sql = "UPDATE projects SET logged_hrs=logged_hrs + 1 WHERE project='$proj_name'";
    if(!$conn->query($sql)) {
      echo $conn->error;
      $conn->close();
      die();
    }
  }

  function destroy($conn, $proj_name) {
    $sql = "DELETE FROM projects WHERE project='$proj_name'";
    if(!$conn->query($sql)) {
      echo $conn->error;
      $conn->close();
      die();
    }
  }

  //utility functions

  function sanitizeData($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }

  function openConn() {
    $server = "localhost";
    $user = "your_username";
    $pass = "your_password";
    $db = "this_projects_db_schema";

    $result = new mysqli($server, $user, $pass, $db);

    //Check connection
    if($result->connect_error) {
      die("Connection failed: " . $result->connect_error);
    }

    return $result;
  }
?>

<html>
<head>
  <title>PHT</title>
  <style>
    #projects .project {
      display: flex;
      align-items: baseline;
    }

    .project * {
      margin-right: 10px;
    }
  </style>
</head>
<body>
  <h1>Project Hour Tracker</h1>
  <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>">
    <fieldset>
      <legend>Add new project</legend>
      <input type="text" name="new_proj_name" placeholder="Project name" required>
      <input type="submit" value="Add Project">
    </fieldset>
  </form>
  <div id="projects">
    <?php if($all_projects->num_rows > 0) {
      while($row = $all_projects->fetch_assoc()) { ?>
        <div class="project">
          <p><?php echo $row["project"] . ": <b>" . $row["logged_hrs"] . "</b> hours "; ?></p>
          <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>">
            <input type="hidden" name="proj_to_increment" value="<?php echo $row["project"] ?>">
            <input type="submit" value="+1">
          </form>
          <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>">
            <input type="hidden" name="proj_to_delete" value="<?php echo $row["project"] ?>">
            <input type="submit" value="Delete project">
          </form>
        </div>
      <?php } ?>
    <?php } else { ?>
      <p>No ongoing projects found.</p>
    <?php } ?>
  </div>

  <script>
    //Confirm delete actions.
    let delForms = document.querySelectorAll(".project form:last-of-type");
    for(let i = 0; i < delForms.length; i++) {
      let delForm = delForms[i];
      delForm.addEventListener("submit", function(event) {
        event.preventDefault();
        if(confirm("Really delete " + delForm.querySelector("input[name='proj_to_delete']").value + "?")) {
          delForm.submit();
        }
      });
    }
  </script>
</body>
</html>
