<?php
session_start();
require_once 'helpers/helpers.php';
require_once 'helpers/storage.php';

if (!isLoggedIn() || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
  header("Location: login.php");
  exit();
}

$carsJsonIO = new JsonIO("data/cars.json");
$carsStorage = new Storage($carsJsonIO);

$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $newCar = [
    'id' => $carsStorage->getNextId(),
    'brand' => $_POST['brand'],
    'model' => $_POST['model'],
    'year' => $_POST['year'],
    'transmission' => $_POST['transmission'],
    'fuel_type' => $_POST['fuel_type'],
    'passengers' => $_POST['passengers'],
    'daily_price_huf' => $_POST['daily_price_huf'],
    'image' => "assets/car-images/" . $_POST['image']
  ];

  $errors = validateNewCar($newCar);

  if (empty($errors)) {
    $carsStorage->add($newCar);
    header("Location: profile.php");
    exit();
  }
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
    <title>iKarRental - Új autó hozzáadása</title>
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
      <div class="row justify-content-center">
        <div class="col-md-6">
          <h1 class="mb-4 text-center">Új autó hozzáadása</h1>
          <form action="admin_add_car.php" method="post" novalidate>
            <div class="mb-3">
              <label for="brand" class="form-label">Márka</label>
              <input type="text" class="form-control" id="brand" name="brand" value="<?php echo $_POST['brand'] ?? '' ?>">
              <?php if(isset($errors['brand'])): ?>
                <div class="text-danger mt-2"><?php echo $errors['brand']?></div>
              <?php endif;?>
            </div>
            <div class="mb-3">
              <label for="model" class="form-label">Modell</label>
              <input type="text" class="form-control" id="model" name="model" value="<?php echo $_POST['model'] ?? '' ?>">
              <?php if(isset($errors['model'])): ?>
                <div class="text-danger mt-2"><?php echo $errors['model']?></div>
              <?php endif;?>
            </div>
            <div class="mb-3">
              <label for="year" class="form-label">Évjárat</label>
              <input type="number" class="form-control" id="year" name="year" value="<?php echo $_POST['year'] ?? '' ?>">
              <?php if(isset($errors['year'])): ?>
                <div class="text-danger mt-2"><?php echo $errors['year']?></div>
              <?php endif;?>
            </div>
            <div class="mb-3">
              <label for="transmission" class="form-label">Váltó típusa</label>
              <select class="form-control" id="transmission" name="transmission">
                <option value="Automata" <?php echo ($_POST['transmission'] ?? '') == 'Automata' ? 'selected' : '' ?>>Automata</option>
                <option value="Manuális" <?php echo ($_POST['transmission'] ?? '') == 'Manuális' ? 'selected' : '' ?>>Manuális</option>
              </select>
              <?php if(isset($errors['transmission'])): ?>
                <div class="text-danger mt-2"><?php echo $errors['transmission']?></div>
              <?php endif;?>
            </div>
            <div class="mb-3">
              <label for="fuel_type" class="form-label">Üzemanyag</label>
              <input type="text" class="form-control" id="fuel_type" name="fuel_type" value="<?php echo $_POST['fuel_type'] ?? '' ?>">
              <?php if(isset($errors['fuel_type'])): ?>
                <div class="text-danger mt-2"><?php echo $errors['fuel_type']?></div>
              <?php endif;?>
            </div>
            <div class="mb-3">
              <label for="passengers" class="form-label">Férőhelyek száma</label>
              <input type="number" class="form-control" id="passengers" name="passengers" value="<?php echo $_POST['passengers'] ?? '' ?>">
              <?php if(isset($errors['passengers'])): ?>
                <div class="text-danger mt-2"><?php echo $errors['passengers']?></div>
              <?php endif;?>
            </div>
            <div class="mb-3">
              <label for="daily_price_huf" class="form-label">Napidíj (HUF)</label>
              <input type="number" class="form-control" id="daily_price_huf" name="daily_price_huf" value="<?php echo $_POST['daily_price_huf'] ?? '' ?>">
              <?php if(isset($errors['daily_price_huf'])): ?>
                <div class="text-danger mt-2"><?php echo $errors['daily_price_huf']?></div>
              <?php endif;?>
            </div>
            <div class="mb-3">
              <label for="image" class="form-label">Kép fájl neve</label>
              <input type="text" class="form-control" id="image" name="image" value="<?php echo $_POST['image'] ?? '' ?>">
              <?php if(isset($errors['image'])): ?>
                <div class="text-danger mt-2"><?php echo $errors['image']?></div>
              <?php endif;?>
            </div>
            <button type="submit" class="btn btn-primary w-100">Hozzáadás</button>
          </form>
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
