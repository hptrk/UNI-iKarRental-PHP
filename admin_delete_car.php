<?php
session_start();
require_once 'helpers/helpers.php';
require_once 'helpers/storage.php';

if (!isLoggedIn() || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
  header("Location: login.php");
  exit();
}

$carsJsonIO = new JsonIO("data/cars.json");
$reservationsJsonIO = new JsonIO("data/reservations.json");

$carsStorage = new Storage($carsJsonIO);
$reservationsStorage = new Storage($reservationsJsonIO);

$carId = $_GET['id'] ?? null;
if ($carId === null) {
  header("Location: profile.php");
  exit();
}
$carsStorage->delete("id", $carId);
$reservationsStorage->deleteMany(function($reservation) use ($carId) {
  return $reservation['car_id'] == (int)$carId;
});

header("Location: profile.php");
exit();
?>
