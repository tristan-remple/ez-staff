<?php

include('conn.php');

$user = $_SESSION['user_id'];

$query = "SELECT * FROM `users` WHERE `user_id` = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $user);
$stmt->execute();
$result = $stmt->get_result();
$rowu = mysqli_fetch_array($result);

$pineventarr = explode(", ", $rowu['pin_events']);

$color = $rowu['pref_color_ez'];

if (isset($_GET['id'])) {
    $event_id = $_GET['id'];
    
    $query = "SELECT * FROM `events` WHERE `ref_id` = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if (mysqli_num_rows($result) !== 0) {
    
    $row = mysqli_fetch_array($result);
    
    $viewarr = explode(', ', $row['invited']);
    
    if (in_array($user, $viewarr)) {
      $viewevent = TRUE;
    } else {
      $err = 'Not Allowed';
    }
    } else {
      $err = 'Not Found';
    }
} else {
  $err = 'Bad URL';
}

if ((isset($viewevent)) && ($viewevent == TRUE)) {
?>
<html>
<head>

<link rel="stylesheet" type="text/css" href="../css/<?php echo $color; ?>.css">
<link rel="stylesheet" type="text/css" href="../css/desktop.css">

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
  
$(document).ready(function() {

$('#options-clicker').click(function() {
    $('.options').toggle();
    if ( $('#optionbox').hasClass('small-col') ) {
        $('#optionbox').removeClass('small-col');
    } else {
        $('#optionbox').addClass('small-col');
    }
});

$('#options-clicker').keypress(function(event) {
  if ((event.keyCode == 13) || (event.keyCode == 32)) {
    $(this).click();
  }
});

});

</script>

</head>
<body>
    
    <div class="verti">
        <div class="title">Event</div>
        <div class="rowbox">
          <div class="solobox">
            <div class="textbox kern">
              <div class="padded">
                <?php
                
                echo '<div class="subtitle">', $row['title'], '</div>
                <strong>Reference ID:</strong> ', $row['ref_id'], '<br>
                <strong>Date:</strong> ', date("F j, Y", strtotime($row['date'])), '<br>
                <strong>Time:</strong> ', date('g:ia', strtotime($row['date'])), '<br>';
                $q = "SELECT username FROM `users` WHERE `user_id` = ?";
                $stmt3 = $db->prepare($q);
                $stmt3->bind_param("i", $row['creator']);
                $stmt3->execute();
                $result3 = $stmt3->get_result();
                $r3 = mysqli_fetch_array($result3);
                
                echo '<strong>Organizer:</strong> ', $r3['username'], '<br>',
                '<strong>Attending:</strong> ';
                $attendarr = explode(', ', $row['attending']);
                $last = end($attendarr);
                
                foreach ($attendarr as $num) {
                  $stmt3->bind_param("i", $num);
                  $stmt3->execute();
                  $result3 = $stmt3->get_result();
                  $r3 = mysqli_fetch_array($result3);
                  echo $r3['username'];
                  if ($num !== $last) {
                    echo ', ';
                  }
                }
                echo '<br>';
                include("details.php");
                
                echo '
                </div></div>
                <div class="textbox kern">
              <div class="padded">',
                $row['summary'],
              '</div>
            </div>
            </div>
            <div class="column textbox" id="optionbox">
            <img class="menu-icon view-arrow" id="options-clicker" tabindex="0" src="../images/brunch-lemon-small.png">
                <div class="padded center options attendance">
                  <form class="">
                    <input type="hidden" name="attend" value="going">
                    <input type="submit" class="boxrow button btncolor center padded" value="I\'m going">
                  </form>
                </div>
                <div class="padded center options">
                    <div class="subtitle">Relevant Documents</div>
                </div>
                <div class="padded options">
                  <hr class="line">';
                    
                    $query = "SELECT * FROM `docs` WHERE `ref_id` = ?";
                    $stmt = $db->prepare($query);
                    $docarr = explode(', ', $row['rel_docs']);
                    
                    foreach ($docarr as $num) {
                        $stmt->bind_param("i", $num);
                        $stmt->execute();
                        $resultd = $stmt->get_result();
                        $rd = mysqli_fetch_array($resultd);
                        echo '<div class="boxrow check-align">',
                        '<img class="menu-icon" src="../images/', $color, '-lime-small.png">',
                            $rd['title'],
                            '<a class="button btncolor center detail" href="doc?id=', $rd['ref_id'], '"><div class="padded">View</div></a>
                        </div>
                        Created: ', date("F j, Y", strtotime($rd['date'])),
                        '<hr class="line">';
                    }
                    
                echo '</div>
            </div>
            </div>
        </div>';
        
} else {
    echo '
    <head>

<link rel="stylesheet" type="text/css" href="../css/', $color, '.css">
<link rel="stylesheet" type="text/css" href="../css/desktop.css">

</head>
    <div class="verti">
    <div class="title">', $err, '</div>';
}
        
        include("menu.php"); ?>
    </div>
</body>