<?php 
function validateFilters($filters) {
  $errors = [];

  if (!in_array($filters['transmission'], ["Automata", "Manuális", ""])) {
      $errors['transmission'] = "A váltó típusa csak (Automata, Manuális, Bármilyen) lehet!";
  }
  if (!empty($filters['passengers']) && $filters['passengers'] < 2) {
      $errors['passengers'] = "A férőhelyek száma legalább 2!";
  }
  if (isset($filters['passengers']) && $filters['passengers'] == 0) {
      $errors['passengers'] = "A férőhelyek száma legalább 2!";
  }
  if (!empty($filters['price_min']) && !empty($filters['price_max']) && $filters['price_min'] > $filters['price_max']) {
      $errors['price'] = "Megfelelő határokat adj meg!";
  }
  if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
      $dateFrom = DateTime::createFromFormat('Y-m-d', $filters['date_from']);
      $dateTo = DateTime::createFromFormat('Y-m-d', $filters['date_to']);
      if ($dateFrom && $dateTo && $dateFrom > $dateTo) {
          $errors['date'] = "Megfelelő dátumot adj meg!";
      }
  }

  return $errors;
}
function isOverlap(DateTime $start1, DateTime $end1, DateTime $start2, DateTime $end2){
  return $start1 <= $end2 && $end1 >= $start2;
}
function isLoggedIn(){
  return (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true);
}
function isCarAvailable($car,$dateFrom, $dateTo, $reservationsStorage){
  $id = $car['id'];
  $carReservations = $reservationsStorage->findAll(["car_id" => $id]);
  foreach($carReservations as $reservation){
    if (isset($reservation['from']) && isset($reservation['to'])) {
      $reservationFrom = new DateTime($reservation['from']);
      $reservationTo = new DateTime($reservation['to']);
      if (isOverlap($dateFrom, $dateTo, $reservationFrom, $reservationTo)) {
        return false;
      }
    }
  }
  return true;
}

function validateNewCar($car) {
  $errors = [];

  if (empty($car['brand'])) {
    $errors['brand'] = "A márka megadása kötelező!";
  }
  if (empty($car['model'])) {
    $errors['model'] = "A modell megadása kötelező!";
  }
  if (empty($car['year']) || !is_numeric($car['year']) || $car['year'] < 1886 || $car['year'] > date("Y") + 1) {
    $errors['year'] = "Az évjárat megadása kötelező és érvényes évszámnak kell lennie!";
  }
  if (!in_array($car['transmission'], ["Automata", "Manuális"])) {
    $errors['transmission'] = "A váltó típusa csak (Automata, Manuális) lehet!";
  }
  if (empty($car['fuel_type'])) {
    $errors['fuel_type'] = "Az üzemanyag típus megadása kötelező!";
  }
  if (empty($car['passengers']) || !is_numeric($car['passengers']) || $car['passengers'] < 2) {
    $errors['passengers'] = "A férőhelyek száma legalább 2!";
  }
  if (empty($car['daily_price_huf']) || !is_numeric($car['daily_price_huf']) || $car['daily_price_huf'] <= 0) {
    $errors['daily_price_huf'] = "A napi árnak pozitív számnak kell lennie!";
  }
  if (empty($car['image'])) {
    $errors['image'] = "A kép URL megadása kötelező!";
  }

  return $errors;
}
?>