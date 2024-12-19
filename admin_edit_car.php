<?php
session_start();
require_once 'helpers/helpers.php';
require_once 'helpers/storage.php';

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
  header("Location: login.php");
  exit();
}

$carsJsonIO = new JsonIO("data/cars.json");
$carsStorage = new Storage($carsJsonIO);
$reservationsJsonIO = new JsonIO("data/reservations.json");
$reservationsStorage = new Storage($reservationsJsonIO);

$carId = $_GET['id'] ?? null;
if ($carId === null) {
  header("Location: profile.php");
  exit();
}
$car = $carsStorage->findOne(["id" => (int)$carId]);

$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['delete_reservation'])) {
  $updatedCar = [
    'id' => $car['id'],
    'brand' => $_POST['brand'],
    'model' => $_POST['model'],
    'year' => $_POST['year'],
    'transmission' => $_POST['transmission'],
    'fuel_type' => $_POST['fuel_type'],
    'passengers' => $_POST['passengers'],
    'daily_price_huf' => $_POST['daily_price_huf'],
    'image' => $_POST['image']
  ];

  $errors = validateNewCar($updatedCar);

  if (empty($errors)) {
    $carsStorage->update($car['id'], $updatedCar);
    header("Location: profile.php");
    exit();
  }
}

if (isset($_POST['delete_reservation'])) {
  $reservationId = $_POST['reservation_id'];
  $from = $_POST['from'];
  $to = $_POST['to'];
  $reservationsStorage->deleteMany(function($reservation) use ($reservationId, $from, $to) {
    return $reservation['car_id'] == $reservationId && $reservation['from'] == $from && $reservation['to'] == $to;
  });
  header("Location: admin_edit_car.php?id=$carId");
  exit();
}
?>
<!DOCTYPE html>
<html lang="hu">
  <head>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
      crossorigin="anonymous"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap"
      rel="stylesheet"
    />
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
      rel="stylesheet"
    />
    <link rel="icon" href="assets/icon.png" type="image/png" />
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>iKarRental - Autó szerkesztése</title>
    <link href="styles.css" rel="stylesheet" />
  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container-fluid">
        <a class="navbar-brand" href="index.php">iKarRental</a>
        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarNav"
          aria-controls="navbarNav"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
            <?php if (isLoggedIn()): ?>
              <li class="nav-item">
                <a class="nav-link" href="profile.php">Profil</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="logout.php">Kilépés</a>
              </li>
            <?php else: ?>
              <li class="nav-item">
                <a class="nav-link" href="login.php">Bejelentkezés</a>
              </li>
              <li class="nav-item">
                <a class="nav-link cta" href="register.php">Regisztráció</a>
              </li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </nav>
    <div class="container mt-5">
      <div class="row">
        <div class="col-md-6">
          <h1 class="mb-4 text-center">Autó szerkesztése</h1>
          <form action="admin_edit_car.php?id=<?php echo $car['id'] ?>" method="post" novalidate>
            <div class="mb-3">
              <label for="brand" class="form-label">Márka</label>
              <input type="text" class="form-control" id="brand" name="brand" value="<?php echo $_POST['brand'] ?? $car['brand'] ?>">
              <?php if(isset($errors['brand'])): ?>
                <div class="text-danger mt-2"><?php echo $errors['brand']?></div>
              <?php endif;?>
            </div>
            <div class="mb-3">
              <label for="model" class="form-label">Modell</label>
              <input type="text" class="form-control" id="model" name="model" value="<?php echo $_POST['model'] ?? $car['model'] ?>">
              <?php if(isset($errors['model'])): ?>
                <div class="text-danger mt-2"><?php echo $errors['model']?></div>
              <?php endif;?>
            </div>
            <div class="mb-3">
              <label for="year" class="form-label">Évjárat</label>
              <input type="number" class="form-control" id="year" name="year" value="<?php echo $_POST['year'] ?? $car['year'] ?>">
              <?php if(isset($errors['year'])): ?>
                <div class="text-danger mt-2"><?php echo $errors['year']?></div>
              <?php endif;?>
            </div>
            <div class="mb-3">
              <label for="transmission" class="form-label">Váltó típusa</label>
              <select class="form-control" id="transmission" name="transmission">
                <option value="Automata" <?php echo ($_POST['transmission'] ?? $car['transmission']) == 'Automata' ? 'selected' : '' ?>>Automata</option>
                <option value="Manuális" <?php echo ($_POST['transmission'] ?? $car['transmission']) == 'Manuális' ? 'selected' : '' ?>>Manuális</option>
              </select>
              <?php if(isset($errors['transmission'])): ?>
                <div class="text-danger mt-2"><?php echo $errors['transmission']?></div>
              <?php endif;?>
            </div>
            <div class="mb-3">
              <label for="fuel_type" class="form-label">Üzemanyag típusa</label>
              <input type="text" class="form-control" id="fuel_type" name="fuel_type" value="<?php echo $_POST['fuel_type'] ?? $car['fuel_type'] ?>">
              <?php if(isset($errors['fuel_type'])): ?>
                <div class="text-danger mt-2"><?php echo $errors['fuel_type']?></div>
              <?php endif;?>
            </div>
            <div class="mb-3">
              <label for="passengers" class="form-label">Utasok száma</label>
              <input type="number" class="form-control" id="passengers" name="passengers" value="<?php echo $_POST['passengers'] ?? $car['passengers'] ?>">
              <?php if(isset($errors['passengers'])): ?>
                <div class="text-danger mt-2"><?php echo $errors['passengers']?></div>
              <?php endif;?>
            </div>
            <div class="mb-3">
              <label for="daily_price_huf" class="form-label">Napi ár (HUF)</label>
              <input type="number" class="form-control" id="daily_price_huf" name="daily_price_huf" value="<?php echo $_POST['daily_price_huf'] ?? $car['daily_price_huf'] ?>">
              <?php if(isset($errors['daily_price_huf'])): ?>
                <div class="text-danger mt-2"><?php echo $errors['daily_price_huf']?></div>
              <?php endif;?>
            </div>
            <div class="mb-3">
              <label for="image" class="form-label">Kép URL</label>
              <input type="text" class="form-control" id="image" name="image" value="<?php echo $_POST['image'] ?? $car['image'] ?>">
              <?php if(isset($errors['image'])): ?>
                <div class="text-danger mt-2"><?php echo $errors['image']?></div>
              <?php endif;?>
            </div>
            <button type="submit" class="btn btn-primary w-100">Mentés</button>
          </form>
        </div>
        <div class="col-md-6">
          <h2 class="mt-5">Foglalások</h2>
          <ul class="list-group">
            <?php
            $reservations = $reservationsStorage->findAll(["car_id" => (int)$carId]);
            if (empty($reservations)) {
              echo '<li class="list-group-item">Nincs foglalás az adott autóra.</li>';
            } else {
              foreach ($reservations as $reservation) {
                echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
                echo 'Foglalás ID: ' . $reservation['car_id'] . ' - Dátum: ' . $reservation['from'] . ' - ' . $reservation['to'];
                echo '<form action="admin_edit_car.php?id=' . $carId . '" method="post" class="ms-3">';
                echo '<input type="hidden" name="reservation_id" value="' . $reservation['car_id'] . '">';
                echo '<input type="hidden" name="from" value="' . $reservation['from'] . '">';
                echo '<input type="hidden" name="to" value="' . $reservation['to'] . '">';
                echo '<button type="submit" name="delete_reservation" class="btn btn-danger btn-sm">Törlés</button>';
                echo '</form>';
                echo '</li>';
              }
            }
            ?>
          </ul>
        </div>
      </div>
    </div>
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
      crossorigin="anonymous"
    ></script>
  </body>
</html>
