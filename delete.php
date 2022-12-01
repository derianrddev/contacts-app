<?php

require "database.php";

session_start();

if (!isset($_SESSION["user"])) {
  header("Location: login.php");
  return;
}

$id = $_GET["id"];

$statement = $conn->prepare("SELECT * FROM contacts WHERE id = :id LIMIT 1");
$statement->execute([":id" => $id]);

if ($statement->rowCount() == 0) {
  // http_response_code(404);
  $_SESSION["flash"] = ["message" => "There is no such contact.", "type" => "danger"];
  header("Location: home.php");
  return;
}

$contact = $statement->fetch(PDO::FETCH_ASSOC);

if ($contact["user_id"] !== $_SESSION["user"]["id"]) {
  // http_response_code(403);
  $_SESSION["flash"] = ["message" => "You do not have access to that contact.", "type" => "danger"];
  header("Location: home.php");
  return;
}

$conn->prepare("DELETE FROM contacts WHERE id = :id")->execute([":id" => $id]);

$_SESSION["flash"] = ["message" => "Contact {$contact['name']} deleted.", "type" => "success"];

header("Location: home.php");
