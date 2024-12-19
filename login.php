<?php 
session_start();

require_once 'helpers/helpers.php';
if (isLoggedIn()){
  header("Location: index.php");
  exit();
}

if ($_SERVER['REQUEST_METHOD'] == "POST"){
  require_once 'helpers/storage.php';
  $usersJsonIO = new JsonIO("data/users.json");
  $usersStorage = new Storage($usersJsonIO);

  $email = $_POST['email'] ?? "";
  $password = $_POST['password'] ?? "";
  $user = $usersStorage->findOne(["email" => $email]);

  if (!$user || !password_verify($password, $user['password'])){
    $errorMessage = "Az email cím vagy a jelszó helytelen!";
  }else{
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['is_admin'] = $user['is_admin'];
    $_SESSION['loggedIn'] = true;
    
    header("Location: index.php");
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
    <link rel="icon" href="assets/icon.png" type="image/png" />
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>iKarRental - Bejelentkezés</title>
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
          <div class="p-4 rounded form-background">
            <h1 class="mb-4 text-center">Bejelentkezés</h1>
            <form method="POST">
              <div class="form-group mb-3">
                <label for="email">Email cím</label>
                <input
                  type="email"
                  class="form-control rounded-pill"
                  id="email"
                  name="email"
                  placeholder="Add meg az email címed"
                />
              </div>
              <div class="form-group mb-3">
                <label for="password">Jelszó</label>
                <input
                  type="password"
                  class="form-control rounded-pill"
                  id="password"
                  name="password"
                  placeholder="Add meg a jelszavad"
                />
              </div>
              <button type="submit" class="btn btn-primary w-100 rounded-pill">
                Bejelentkezés
              </button>
              <?php if(isset($errorMessage)): ?>
                  <div class="text-danger mt-2"><?php echo $errorMessage?></div>
                <?php endif;?>
            </form>
          </div>
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
