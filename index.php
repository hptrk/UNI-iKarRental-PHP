<?php
session_start();
require_once 'helpers/storage.php';
require_once 'helpers/helpers.php';

$carsJsonIO = new JsonIO("data/cars.json");
$reservationsJsonIO = new JsonIO("data/reservations.json");

$carsStorage = new Storage($carsJsonIO);
$reservationsStorage = new Storage($reservationsJsonIO);

$cars = $carsStorage->findAll();
$reservations = $reservationsStorage->findAll();

# Filtering
$filters = [];
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  $filters = [
    'transmission' => $_GET['transmission'] ?? "",
    'passengers' => $_GET['passengers'] ?? "",
    'price_min' => $_GET['price_min'] ?? "",
    'price_max' => $_GET['price_max'] ?? "",
    'date_from' => $_GET['date_from'] ?? "",
    'date_to' => $_GET['date_to'] ?? ""
  ];

  $errors = validateFilters($filters);
  

  if (empty($errors) && !empty($filters)) {
    $cars = $carsStorage->findMany(function ($car) use ($filters, $reservationsStorage) {
      $dateFrom = $filters['date_from'] != "" ? new DateTime($filters['date_from']) : null;
      $dateTo = $filters['date_to'] != "" ? new DateTime($filters['date_to']) : null;
      return ($filters['transmission'] === "" || $car['transmission'] == $filters['transmission']) &&
             ($filters['passengers'] === "" || $car['passengers'] == $filters['passengers']) &&
             ($filters['price_min'] === "" || $car['daily_price_huf'] >= $filters['price_min']) &&
             ($filters['price_max'] === "" || $car['daily_price_huf'] <= $filters['price_max']) &&
             ($dateFrom === null || $dateTo === null || isCarAvailable($car, $dateFrom, $dateTo, $reservationsStorage));
    });
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
    <title>iKarRental - Főoldal</title>
    <link href="styles.css" rel="stylesheet" />
  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container-fluid">
        <a class="navbar-brand" href="#">iKarRental</a>
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
    <?php if(!isLoggedIn()):?>
    <div class="container mt-5 text-center">
      <h1 class="display-4">Kölcsönözz autókat könnyedén</h1>
      <p class="lead">
        Regisztrálj most és bérelj autót egyszerűen és gyorsan!
      </p>
      <a href="register.php" class="btn btn-primary btn-lg">Regisztráció</a>
    </div>
    <?php endif; ?>
    <div class="container mt-5">
      <h1 class="mb-4">Elérhető autók</h1>
      <div class="row g-5">
        <div class="col-md-3">
          <h5>Szűrők</h5>
            <form class="mb-4" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="get">
            <div class="form-group mb-3">
              <label for="transmission">Váltó típusa</label>
                <select class="form-control rounded-pill" id="transmission" name="transmission">
                <option value="" <?php echo $filters['transmission'] == "" ? "selected" : ""; ?>>Bármilyen</option>
                <option value="Automata" <?php echo $filters['transmission'] == "Automata" ? "selected" : ""; ?>>Automata</option>
                <option value="Manuális" <?php echo $filters['transmission'] == "Manuális" ? "selected" : ""; ?>>Manuális</option>
                </select>
                <?php if(isset($errors['transmission'])): ?>
                  <div class="text-danger mt-2"><?php echo $errors['transmission']?></div>
                <?php endif;?>
            </div>
            <div class="form-group mb-3">
              <label for="passengers">Férőhelyek száma</label>
              <div class="input-group">
                <button
                  class="btn btn-outline-secondary rounded-pill"
                  type="button"
                  id="decreasePassengers"
                >
                  <i class="fas fa-minus"></i>
                </button>
                <input
                  type="text"
                  class="form-control text-center rounded-pill mx-2"
                  id="passengers"
                  placeholder="Férőhelyek száma"
                  min="1"
                  name="passengers"
                  value="<?php echo htmlspecialchars($filters['passengers']); ?>"
                />
                <button
                  class="btn btn-outline-secondary rounded-pill"
                  type="button"
                  id="increasePassengers"
                >
                  <i class="fas fa-plus"></i>
                </button>
              </div>
              <?php if(isset($errors['passengers'])): ?>
                <div class="text-danger mt-2"><?php echo $errors['passengers']?></div>
              <?php endif;?>
            </div>
            <div class="form-group mb-3">
              <label for="price">Napidíj (HUF)</label>
              <div class="input-group">
                <input
                  type="number"
                  class="form-control rounded-pill"
                  id="priceMin"
                  placeholder="Alsó határ"
                  min="0"
                  name="price_min"
                  value="<?php echo htmlspecialchars($filters['price_min'])?>"
                />
                <span class="mx-2 my-auto">-</span>
                <input
                  type="number"
                  class="form-control rounded-pill"
                  id="priceMax"
                  placeholder="Felső határ"
                  min="0"
                  name="price_max"
                  value="<?php echo htmlspecialchars($filters['price_max'])?>"
                />
              </div>
              <?php if(isset($errors['price'])): ?>
                <div class="text-danger mt-2"><?php echo $errors['price']?></div>
              <?php endif;?>
            </div>
            <div class="form-group mb-3">
              <label for="dateFrom">Dátum</label>
              <div class="input-group">
                <input
                  type="date"
                  class="form-control rounded-pill"
                  id="dateFrom"
                  name="date_from"
                  value="<?php echo htmlspecialchars($filters['date_from'])?>"
                />
                <span class="mx-2 my-auto">-</span>
                <input
                  type="date"
                  class="form-control rounded-pill"
                  id="dateTo"
                  name="date_to"
                  value="<?php echo htmlspecialchars($filters['date_to'])?>"
                />
              </div>
              <?php if(isset($errors['date'])): ?>
                <div class="text-danger mt-2"><?php echo $errors['date']?></div>
              <?php endif;?>
            </div>
            <button type="submit" class="btn btn-primary">
              Szűrők alkalmazása
            </button>
          </form>
        </div>
        <div class="col-md-9">
          <div class="row">
            <!-- CARD -->
             <?php foreach($cars as $car): ?>
              <div class="col-md-4 mb-4">
              <div
                class="card shadow-sm index-card"
                onclick="window.location.href='car.php?id=<?php echo $car['id']?>'"
              >
                <img
                  src="<?php echo $car['image']?>"
                  class="card-img-top"
                  alt="<?php echo $car['brand'] . ' ' . $car['model'] ?>"
                />
                <div class="card-body">
                  <h5 class="card-title"><?php echo $car['brand'] . ' ' . $car['model'] ?></h5>
                  <div
                    class="d-flex justify-content-between align-items-center"
                  >
                    <div>
                      <p class="card-text mb-0">
                        <i class="fas fa-cogs"></i> <?php echo $car['transmission'] ?><br />
                        <i class="fas fa-users"></i> <?php echo $car['passengers'] ?> férőhely<br />
                        <i class="fas fa-money-bill-wave"></i>
                        <strong><?php echo $car['daily_price_huf'] ?></strong> HUF/nap
                      </p>
                    </div>
                    <a href="booking.php?id=<?php echo $car['id']?>" class="btn btn-primary">Foglalás</a>
                  </div>
                </div>
              </div>
            </div>
            <?php endforeach;?>
          </div>
        </div>
      </div>
    </div>
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
      crossorigin="anonymous"
    ></script>
    <script>
      document
        .getElementById('decreasePassengers')
        .addEventListener('click', function () {
          const passengersInput = document.getElementById('passengers');
          const currentValue = parseInt(passengersInput.value) || 1;
          if (currentValue > 1) {
            passengersInput.value = currentValue - 1;
          }
        });

      document
        .getElementById('increasePassengers')
        .addEventListener('click', function () {
          const passengersInput = document.getElementById('passengers');
          const currentValue = parseInt(passengersInput.value) || 1;
          passengersInput.value = currentValue + 1;
        });
    </script>
  </body>
</html>
