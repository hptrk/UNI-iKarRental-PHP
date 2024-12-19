<?php 
function validateRegisterForm($fullName, $email, $password){
  require_once 'storage.php';
  $usersJsonIO = new JsonIO("data/users.json");
  $usersStorage = new Storage($usersJsonIO);
  $userEmails = array_column($usersStorage->findAll(), "email");

  $errors = [];
  if(empty($fullName)){
    $errors['fullName'] = "A teljes név megadása kötelező!";
  }elseif(count(explode(" " ,$fullName)) < 2){
    $errors['fullName'] = "A teljes névnek legalább két részből kell állnia!";
  }

  if(empty($email)){
    $errors['email'] = "Az email cím megadása kötelező!";
  }elseif(!filter_var($email, FILTER_VALIDATE_EMAIL))
  {
    $errors['email'] = "Érvénytelen email cím!";
  }elseif(in_array($email, $userEmails)){
    $errors['email'] = "Ez az email cím már foglalt!";
  }

  if(empty($password)){
    $errors['password'] = "A jelszó megadása kötelező!";
  } elseif (strlen($password) < 6){
    $errors['password'] = "A jelszónak legalább 6 karakter hosszúnak kell lennie!";
  }
  return $errors;
}
?>