<?php
   session_start();
   date_default_timezone_set('Europe/Paris');

   require '../helpers.php';
   require '../model/messageModel.php';
   require '../model/logModel.php';

   $logged = (
      isset($_SESSION["logged"]) &&
      $_SESSION["logged"]
   );
   $admin = (
      isset($_SESSION["admin"]) &&
      $_SESSION["admin"]
   );
   $readMessageCondition = ($logged && $_GET["type"] === "readMessage");
   $createMessageCondition = (
      $logged &&      
      $_GET["type"] === "createMessage" &&
      isset($_POST['message']) 
   );


   if ($createMessageCondition) {   
      if ($admin && isset($_GET["target"])) {
         createMessage($_GET["target"], $_SESSION["admin"]);
      } else {
         openConversation($_SESSION["pseudo"]);
         createMessage($_SESSION["pseudo"]);
      }

      echo json_encode(["success" => true]);
   } 
   else if ($readMessageCondition) {
      if ($admin) {
         $response = readMessage($_SESSION["pseudo"], true);    
      } else {
         $response = readMessage($_SESSION["pseudo"]);
      } 
      $messageHistory = formatConversations($response->fetchAll(PDO::FETCH_ASSOC));
      echo json_encode($messageHistory);
   } else {
      errorLog("Message controler was unable to parse the request !");
      echo json_encode(["success" => false]);
   }


   function formatConversations($messageHistory) {
      $arr = array();

      foreach ($messageHistory as $message){
         if ($_SESSION["admin"] === false) {
            $currentPseudo = "Admin - Vazn";
         } else {      
            $currentPseudo = $message["pseudo_user"];
         }

         // Crée une clé par pseudo, et push tout les messages correspondant au pseudo correspondant.         
         $arr[$currentPseudo][] = $message;
      }

      return $arr;
   }
?>