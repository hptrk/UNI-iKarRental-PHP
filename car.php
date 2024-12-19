<?php 
session_start();
require_once 'helpers/helpers.php';
require_once 'helpers/storage.php';

$carsJsonIO = new JsonIO("data/cars.json");
$carsStorage = new Storage($carsJsonIO);

$carId = $_GET['id'] ?? null;
if ($carId === null) {
  header("Location: index.php");
  exit();
}
$car = $carsStorage->findOne(["id" => (int)$carId]);
if (empty($car)){
  header("Location: index.php");
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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>iKarRental - Autó adatok</title>
    <link href="styles.css" rel="stylesheet" />
    <link rel="icon" href="assets/icon.png" type="image/png" />
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
          <img
            src="<?php echo $car['image']?>"
            class="img-fluid rounded"
            alt="<?php echo $car['brand'] . " " . $car['model']?>"
          />
        </div>
        <div class="col-md-6">
          <h1 class="mb-4"><?php echo $car['brand'] . " " . $car['model']?></h1>
          <ul class="list-group list-group-flush mb-4">
            <li class="list-group-item bg-dark text-light">Évjárat: <?php echo $car['year']?></li>
            <li class="list-group-item bg-dark text-light">
              Váltó típusa: <?php echo $car['transmission']?>
            </li>
            <li class="list-group-item bg-dark text-light">
              Üzemanyag: <?php echo $car['fuel_type']?>
            </li>
            <li class="list-group-item bg-dark text-light">Férőhelyek: <?php echo $car['passengers']?></li>
            <li class="list-group-item bg-dark text-light">
              Napidíj: <?php echo $car['daily_price_huf']?> HUF
            </li>
          </ul>
          <a href="booking.php?id=<?php echo $car['id']?>" class="btn btn-primary w-100 rounded-pill"
            >Foglalás</a
          >
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
