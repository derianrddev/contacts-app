<?php

  require "database.php";

  session_start();

  if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    return;
  }

  $error = null;

  $name = (isset($_POST["name"])) ? $_POST["name"] : '';
  $phoneNumber = (isset($_POST["phone_number"])) ? $_POST["phone_number"] : '';

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["name"]) || empty($_POST["phone_number"])) {
      $error = "Please fill all the fields.";
    } else if (strlen($_POST["phone_number"]) < 8) {
      $error = "Phone number must be at least 8 characters.";
    } else {
      $user_id = $_SESSION['user']['id'];

      $statement = $conn->prepare("INSERT INTO contacts (user_id, name, phone_number) VALUES (:user_id, :name, :phone_number)");
      $statement->bindParam(":user_id", $user_id);
      $statement->bindParam(":name", $name);
      $statement->bindParam(":phone_number", $phoneNumber);
      $statement->execute();

      $_SESSION["flash"] = ["message" => "Contact {$_POST['name']} added.", "type" => "success"];

      header("Location: home.php");
      return;
    }
  }

?>

<?php require "partials/header.php" ?>

<div class="container pt-5">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">Add New Contact</div>
        <div class="card-body">
          <?php if ($error): ?>
            <p class="text-danger">
              <?= $error ?>
            </p>
          <?php endif ?>
          <form method="POST" action="add.php">
            <div class="mb-3 row">
              <label for="name" class="col-md-4 col-form-label text-md-end">Name</label>

              <div class="col-md-6">
                <input id="name" type="text" class="form-control" name="name" value="<?= $name; ?>" autocomplete="name" autofocus>
              </div>
            </div>

            <div class="mb-3 row">
              <label for="phone_number" class="col-md-4 col-form-label text-md-end">Phone Number</label>

              <div class="col-md-6">
                <input id="phone_number" type="tel" class="form-control" name="phone_number" value="<?= $phoneNumber; ?>" autocomplete="phone_number" autofocus>
              </div>
            </div>

            <div class="mb-3 row">
              <div class="col-md-6 offset-md-4">
                <button type="submit" class="btn btn-primary">Submit</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require "partials/footer.php" ?>
