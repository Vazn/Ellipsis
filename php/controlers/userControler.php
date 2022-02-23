<?php
   session_start();
   date_default_timezone_set('Europe/Paris');

   require '../helpers.php';
   require '../model/userModel.php';
   require '../model/logModel.php';

   $logged = (
      isset($_SESSION["logged"]) &&
      $_SESSION["logged"]
   );
   $regCondition = (
      $_GET["type"] === "registerUser" &&
      isset($_POST['pseudo']) && 
      isset($_POST['password']) &&
      isset($_POST['email'])
   );   
   $loginCondition = (
      $_GET["type"] === "loginUser" &&
      isset($_POST['email']) && 
      isset($_POST['password'])
   ); 
   $logoutCondition = ($_GET["type"] === "logoutUser" && $logged);
   $updateCondition = ($_GET["type"] === "updateUser" && $logged);
   $deleteCondition = ($_GET["type"] === "deleteUser" && $logged);


   if ($regCondition) {
      $test = availableCheck();

      if ($test["success"]) {
         createUser();
         createLog("Registration", $_POST["pseudo"]);
      }
      echo json_encode($test);
   }
   else if ($loginCondition) {   
      $clientData = connectUser();
      echo json_encode($clientData);
   } 
   else if ($logoutCondition) {
      createLog("Logout", $_SESSION["pseudo"]);
      session_destroy();

      echo json_encode(["success" => true]);
   }
   else if ($updateCondition) {}  //== TODO 
   else if ($deleteCondition) {
      $success = deleteUser($_SESSION["pseudo"]);

      if ($success) {
         session_destroy();
         echo json_encode(["success" => true]);
      } else {
         echo json_encode(["success" => false]);
      }
   }
   else {
      errorLog("User controler was unable to parse the request !");
      json_encode(["success" => false]);
   }

   function connectUser() {
      $query = readUser($_POST["email"]);
      $result = $query->fetch();    // Retourn tableau si ok      
      $data = [];
  
      if (is_array($result)) {

         $result[2] = !($result[2] === "0");        // BDD retourn une chaine pour le booleen
         if (password_verify($_POST["password"], $result[0])) {
            $data["pseudo"] = $result[1];
            $data["admin"] = $result[2];
            $data["logged"] = True;
            
            $_SESSION["pseudo"] = $result[1];
            $_SESSION["admin"] = $result[2];
            $_SESSION["logged"] = True;
            
            createLog("Login", $_SESSION["pseudo"]);
         } else {
            $data["logged"] = False;
         }
      } else {
         $data["logged"] = False;
      }
      return $data;
   }
   function availableCheck() {
      $arr = [];

      try {
         $arr["pseudo"] = checkPseudo();
         $arr["email"] = checkMail();  
      } catch (PDOException $e) {
         errorLog("USER DUPLICATE CHECK", $e);
      }

      if ($arr["pseudo"] && $arr["email"]) $arr["success"] = True;
      else $arr["success"] = False;

      return $arr;
   }
?>