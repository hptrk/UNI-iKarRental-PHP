<?php 
session_start();
require_once 'helpers/helpers.php';
require_once 'helpers/storage.php';
if (!isLoggedIn()){
  header("Location: login.php");
  exit();
}
$carsJsonIO = new JsonIO("data/cars.json");
$reservationsJsonIO = new JsonIO("data/reservations.json");

$carsStorage = new Storage($carsJsonIO);
$reservationsStorage = new Storage($reservationsJsonIO);

$reservations = $reservationsStorage->findAll(["user_email" => $_SESSION['user_email']]);
$cars = $carsStorage->findAll();
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
    <title>iKarRental - Profil</title>
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
      <h1 class="mb-4">Üdv, <strong><?php echo $_SESSION['user_name']?></strong></h1>
      <?php if ($_SESSION['is_admin'] === true): ?>
        <h2>Autók kezelése</h2>
        <a href="admin_add_car.php" class="btn btn-primary mb-3">Új autó hozzáadása</a>
        <div class="row mt-3">
          <?php foreach($cars as $car): ?>
            <div class="col-md-4 mb-4">
              <div class="card shadow-sm">
                <img
                  src="<?php echo $car['image']?>"
                  class="card-img-top"
                  alt="<?php echo $car['brand'] . ' ' . $car['model'] ?>"
                />
                <div class="card-body">
                  <h5 class="card-title"><?php echo $car['brand'] . ' ' . $car['model'] ?></h5>
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <p class="card-text mb-0">
                        <i class="fas fa-cogs"></i> <?php echo $car['transmission'] ?><br />
                        <i class="fas fa-users"></i> <?php echo $car['passengers'] ?> férőhely<br />
                        <i class="fas fa-money-bill-wave"></i>
                        <strong><?php echo $car['daily_price_huf'] ?></strong> HUF/nap
                      </p>
                    </div>
                  </div>
                  <a href="admin_edit_car.php?id=<?php echo $car['id']?>" class="btn btn-warning mt-2">Szerkesztés</a>
                  <a href="admin_delete_car.php?id=<?php echo $car['id']?>" class="btn btn-danger mt-2">Törlés</a>
                </div>
              </div>
            </div>
          <?php endforeach;?>
        </div>
      <?php else: ?>
        <h2>Foglalásaid</h2>
        <div class="row mt-3">
          <?php if (empty($reservations)): ?>
            <p>Még nincsenek foglalásaid.</p>
          <?php else: ?>
            <!-- CARD -->
            <?php foreach($reservations as $reservation): ?>
              <?php 
                $car = $carsStorage->findOne(["id"=>$reservation['car_id']])
              ?>
              <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                  <img
                    src="<?php echo $car['image']?>"
                    class="card-img-top"
                    alt="<?php echo $car['brand'] . ' ' . $car['model'] ?>"
                  />
                  <div class="card-body">
                    <h5 class="card-title"><?php echo $car['brand'] . ' ' . $car['model'] ?></h5>
                    <div class="d-flex justify-content-between align-items-center">
                      <div>
                        <p class="card-text mb-0">
                          <i class="fas fa-cogs"></i> <?php echo $car['transmission'] ?><br />
                          <i class="fas fa-users"></i> <?php echo $car['passengers'] ?> férőhely<br />
                          <i class="fas fa-money-bill-wave"></i>
                          <strong><?php echo $car['daily_price_huf'] ?></strong> HUF/nap<br />
                          <i class="fas fa-calendar-alt"></i> <?php echo $reservation['from'] ?> - <?php echo $reservation['to'] ?>
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach;?>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
      crossorigin="anonymous"
    ></script>
  </body>
</html>
