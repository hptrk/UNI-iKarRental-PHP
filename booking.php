<?php 
session_start();
require_once 'helpers/helpers.php';
require_once 'helpers/storage.php';
if (!isLoggedIn()){
  header("Location: index.php");
  exit();
}

$carsJsonIO = new JsonIO("data/cars.json");
$reservationsJsonIO = new JsonIO("data/reservations.json");

$carsStorage = new Storage($carsJsonIO);
$reservationsStorage = new Storage($reservationsJsonIO);

$successMessage = "";
$errorMessage = "";

$carId = $_GET['id'] ?? "";
$car = $carsStorage->findOne(["id" => (int)$carId]);

$reservations = $reservationsStorage->findAll(["car_id" => (int)$carId]);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $dateFrom = new DateTime($_POST['date_from']);
  $dateTo = new DateTime($_POST['date_to']);
  $carId = $_POST['car_id'];

  if (isCarAvailable($car, $dateFrom, $dateTo, $reservationsStorage)) {
    $reservation = [
      'user_email' => $_SESSION['user_email'],
      'car_id' => (int)$carId,
      'from' => $dateFrom->format('Y-m-d'),
      'to' => $dateTo->format('Y-m-d')
    ];
    $reservationsStorage->add($reservation);
    $successMessage = "Sikeres foglalás!";
  } else {
    $errorMessage = "Sikertelen foglalás, az adott időintervallum foglalt.";
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
    <title>iKarRental - Foglalás</title>
    <link href="styles.css" rel="stylesheet" />
    <!-- FullCalendar CSS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />
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
      <?php if ($car): ?>
        <h1 class="mb-4"><?php echo $car['brand'] . ' ' . $car['model'] ?></h1>
        <div class="row">
          <div class="col-md-6">
            <img src="<?php echo $car['image'] ?>" class="img-fluid" alt="<?php echo $car['brand'] . ' ' . $car['model'] ?>">
          </div>
          <div class="col-md-6">
            <form action="booking.php?id=<?php echo $car['id'] ?>" method="post" novalidate>
              <input type="hidden" name="car_id" value="<?php echo $car['id'] ?>">
              <div class="mb-3">
                <label for="date_from" class="form-label">Bérlés kezdete</label>
                <input type="date" class="form-control" id="date_from" name="date_from">
              </div>
              <div class="mb-3">
                <label for="date_to" class="form-label">Bérlés vége</label>
                <input type="date" class="form-control" id="date_to" name="date_to">
              </div>
              <button type="submit" class="btn btn-primary">Foglalás</button>
            </form>
            <?php if ($successMessage): ?>
              <div class="alert alert-success mt-3"><?php echo $successMessage ?></div>
            <?php endif; ?>
            <?php if ($errorMessage): ?>
              <div class="alert alert-danger mt-3"><?php echo $errorMessage ?></div>
            <?php endif; ?>
          </div>
        </div>
      <?php else: ?>
        <div class="alert alert-danger">Az autó nem található.</div>
      <?php endif; ?>
    </div>
    <!-- FullCalendar JS -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js'></script>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.createElement('div');
        calendarEl.id = 'calendar';
        document.querySelector('.container').appendChild(calendarEl);

        var calendar = new FullCalendar.Calendar(calendarEl, {
          initialView: 'dayGridMonth',
          events: [
            <?php foreach ($reservations as $reservation): ?>
              {
                title: 'Foglalás',
                start: '<?php echo $reservation['from']; ?>',
                end: '<?php echo $reservation['to']; ?>',
                display: 'background',
                color: '#ff9f89'
              },
            <?php endforeach; ?>
          ],
          selectable: true,
          select: function(info) {
            document.getElementById('date_from').value = info.startStr;
            document.getElementById('date_to').value = info.endStr;
          }
        });

        calendar.render();
      });
    </script>
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
      crossorigin="anonymous"
    ></script>
  </body>
</html>