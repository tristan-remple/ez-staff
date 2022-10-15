<?php

include('conn.php');

$user = $_SESSION['user_id'];

$query = "SELECT * FROM `users` WHERE `user_id` = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $user);
$stmt->execute();
$result = $stmt->get_result();
$row = mysqli_fetch_array($result);

if ((isset($_GET['display'])) && ($_GET['display'] == 'pinned')) {
  $display = 'pinned';
} else {
  $display = 'latest';
}

$pineventarr = explode(", ", $row['pin_events']);
$pindocarr = explode(", ", $row['pin_docs']);
$pintaskarr = explode(", ", $row['pin_tasks']);

$color = $row['pref_color_ez'];

?>
<html>
<head>

<link rel="stylesheet" type="text/css" href="../css/<?php echo $color; ?>.css">
<link rel="stylesheet" type="text/css" href="../css/desktop.css">

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
  
$(document).ready(function() {

$('.checkbox2').keypress(function(event) {
  if ((event.keyCode == 13) || (event.keyCode == 32)) {
    $(this).click();
  }
});

$('.checkbox2').click(function() {
  var id = $(this).attr('id');
  if ( !$('.secretbox#'+id).hasClass('is-check') ) {
    $('.secretbox#'+id).val(true);
    $('#'+id+'.checkmark').show();
    $('.checklabel#'+id).wrap("<strike>");
    $('.secretbox#'+id).addClass('is-check');
  } else {
    $('.secretbox#'+id).val(false);
    $('.secretbox#'+id).removeClass('is-check');
    $('#'+id+'.checkmark').hide();
    $('.checklabel#'+id).unwrap();
  }
});

});

</script>

</head>
<body>
    
    <div class="verti">
      <div class="rowbox center">
        <div class="title"><?php echo $row['username']; ?>'s Dashboard</div>
          <?php
            if ($display == 'pinned') {
              echo '<a class="button btncolor center padded limiter" href="dashboard">Show Soonest</a>';
            } else {
              echo '<a class="button btncolor center padded limiter" href="dashboard?display=pinned">Show Pinned</a>';
            }
          ?>
        </div>
        
        <div class="rowbox">
            <div class="column textbox check-align">
                <div class="padded center caps">
                    <div class="subtitle">Task Lists</div>
                      
                      <?php
                    echo '<hr class="line">';
                    if ($display == 'latest') {
                    $tasknum = 1;
                    $query = "SELECT * FROM `task_lists` ORDER BY `due` ASC";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    while ($r = mysqli_fetch_array($result)) {
                      $viewers = $r['viewers'];
                      $viewarr = explode(", ", $viewers);
                      
                      $qtask = "SELECT complete FROM `tasks` WHERE `ref_id` = ?";
                    $stmttask = $db->prepare($qtask);
                    
                    $tasks1 = explode(', ', $r['tasks']);
                    $count = count($tasks1);
                    $complete = 0;
                    
                    foreach ($tasks1 as $num) {
                      $stmttask->bind_param("i", $num);
                      $stmttask->execute();
                      $resulttask = $stmttask->get_result();
                      $rtask = mysqli_fetch_array($resulttask);
                      if ($rtask['complete'] == 1) {
                        $complete++;
                      }
                    }
                      
                      if ((in_array($user, $viewarr)) && ($complete !== $count)) {
                        
                        $divid = 'task_'.$r['ref_id'];
                        echo '<div class="boxrow check-align">';
                        if (in_array($r['ref_id'], $pintaskarr)) {
                          echo '<img class="menu-icon" src="../images/', $color, '-lime-small.png">';
                        } else {
                          echo '<img class="menu-icon" src="../images/', $color, '-orange-small.png">';
                        }
                        echo $r['title'], '
                        <a class="button btncolor center detail" href="tasks?id=', $r['ref_id'], '"><div class="padded">View</div></a></div>';
                        
                        echo $complete, '/', $count, ' tasks complete',
                        '<hr class="line">';
                        
                        $tasknum++;
                      }
                      if ($tasknum > 3) {
                        break;
                      }
                    }
                    } else {
                      foreach ($pintaskarr as $id) {
                      
                      $tasknum = 1;
                    $query = "SELECT * FROM `task_lists` WHERE `ref_id` = ?";
                    $stmt = $db->prepare($query);
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $r = mysqli_fetch_array($result);
                      $viewers = $r['viewers'];
                      $viewarr = explode(", ", $viewers);
                      
                      $qtask = "SELECT complete FROM `tasks` WHERE `ref_id` = ?";
                    $stmttask = $db->prepare($qtask);
                    
                    $tasks1 = explode(', ', $r['tasks']);
                    $count = count($tasks1);
                    $complete = 0;
                    
                    foreach ($tasks1 as $num) {
                      $stmttask->bind_param("i", $num);
                      $stmttask->execute();
                      $resulttask = $stmttask->get_result();
                      $rtask = mysqli_fetch_array($resulttask);
                      if ($rtask['complete'] == 1) {
                        $complete++;
                      }
                    }
                      
                      if ((in_array($user, $viewarr)) && ($complete !== $count)) {
                        
                        $divid = 'task_'.$r['ref_id'];
                        echo '<div class="boxrow check-align">';
                        if (in_array($r['ref_id'], $pintaskarr)) {
                          echo '<img class="menu-icon" src="../images/', $color, '-lime-small.png">';
                        } else {
                          echo '<img class="menu-icon" src="../images/', $color, '-orange-small.png">';
                        }
                        echo $r['title'], '
                        <a class="button btncolor center detail" href="tasks?id=', $r['ref_id'], '"><div class="padded">View</div></a></div>';
                        
                        echo $complete, '/', $count, ' tasks complete',
                        '<hr class="line">';
                        
                        $tasknum++;
                      }
                      if ($tasknum > 3) {
                        break;
                      }
                    }
                    }
                    
                    ?>
                </div>
                <div class="padded">
                    <a class="button btncolor center padded spacer" href="tasks">View All Task Lists</a>
                    <a class="button btncolor center padded spacer" href="../create/tasks">Create Task List</a>
                </div>
            </div>
            <div class="column textbox check-align">
                <div class="padded center caps">
                    <div class="subtitle">Events</div>
                    <hr class="line">
                    
                    <?php
                    
                    if ($display == 'latest') {
                    $eventnum = 1;
                    $query = "SELECT * FROM `events` ORDER BY `date` ASC";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    while ($r = mysqli_fetch_array($result)) {
                      $attend = $r['attending'];
                      $attendarr = explode(", ", $attend);
                      if (in_array($user, $attendarr)) {
                        echo '<div class="boxrow check-align">';
                        if (in_array($r['ref_id'], $pineventarr)) {
                          echo '<img class="menu-icon" src="../images/', $color, '-lime-small.png">';
                        } else {
                          echo '<img class="menu-icon" src="../images/', $color, '-orange-small.png">';
                        }
                        echo $r['title'],
                        '<a class="button btncolor center detail" href="event?id=', $r['ref_id'], '"><div class="padded">Details</div></a>
                        </div>
                        Date: ', date("F j", strtotime($r['date'])), ' | Time: ', date("g:ia", strtotime($r['date'])),
                        '<hr class="line">';
                        
                        $eventnum++;
                      }
                      if ($eventnum > 3) {
                        break;
                      }
                    }
                    } elseif ($display == 'pinned') {
                      
                      foreach ($pineventarr as $event) {
                        $query = "SELECT * FROM `events` WHERE `ref_id` = ?";
                        $stmt = $db->prepare($query);
                        $stmt->bind_param("i", $event);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $r = mysqli_fetch_array($result);
                        echo '<div class="boxrow check-align">
                        <img class="menu-icon" src="../images/', $color, '-lime-small.png">
                        ',
                        $r['title'],
                        '<a class="button btncolor center detail" href="event?id=', $r['ref_id'], '"><div class="padded">Details</div></a>
                        </div>
                        Date: ', date("F j", strtotime($r['date'])), ' | Time: ', date("g:ia", strtotime($r['date'])),
                        '<hr class="line">';
                      }
                      
                    }
                    
                    ?>
                </div>
                <div class="padded">
                    <a class="button btncolor center padded spacer" href="events">View All Events</a>
                    <a class="button btncolor center padded spacer" href="../create/event">Create Event</a>
                </div>
            </div>
            <div class="column textbox check-align">
                <div class="padded center caps">
                    <div class="subtitle">Documents</div>
                    <hr class="line">
                    
                    <?php
                    
                    if ($display == 'latest') {
                    $docnum = 1;
                    $query = "SELECT * FROM `docs` ORDER BY `date` DESC";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    while ($r = mysqli_fetch_array($result)) {
                      $viewers = $r['viewers'];
                      $viewarr = explode(", ", $viewers);
                      if (in_array($user, $viewarr)) {
                        echo '<div class="boxrow check-align">';
                        if (in_array($r['ref_id'], $pindocarr)) {
                          echo '<img class="menu-icon" src="../images/', $color, '-lime-small.png">';
                        } else {
                          echo '<img class="menu-icon" src="../images/', $color, '-orange-small.png">';
                        }
                        echo $r['title'],
                        '<a class="button btncolor center detail" href="doc?id=', $r['ref_id'], '"><div class="padded">View</div></a>
                        </div>
                        Created: ', date("F j, Y", strtotime($r['date'])),
                        '<hr class="line">';
                        $docnum++;
                      }
                      if ($docnum > 3) {
                        break;
                      }
                    }
                    } elseif ($display == 'pinned') {
                      
                      foreach ($pindocarr as $doc) {
                        $query = "SELECT * FROM `docs` WHERE `ref_id` = ?";
                        $stmt = $db->prepare($query);
                        $stmt->bind_param("i", $doc);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $r = mysqli_fetch_array($result);
                        echo '<div class="boxrow check-align">',
                        '<img class="menu-icon" src="../images/', $color, '-lime-small.png">',
                            $r['title'],
                            '<a class="button btncolor center detail" href="doc?id=', $r['ref_id'], '"><div class="padded">View</div></a>
                        </div>
                        Created: ', date("F j, Y", strtotime($r['date'])),
                        '<hr class="line">';
                      }
                      
                    }
                    
                    ?>
                </div>
                <div class="padded">
                    <a class="button btncolor center padded spacer" href="library">View All Documents</a>
                    <a class="button btncolor center padded spacer" href="../create/doc">Create Document</a>
                </div>
            </div>
        </div>
        
        <?php include("menu.php"); ?>
        
    </div>
    
</body>