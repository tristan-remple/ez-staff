<?php

include("conn.php");

$user = $_SESSION['user_id'];

$query = "SELECT * FROM `users` WHERE `user_id` = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $user);
$stmt->execute();
$result = $stmt->get_result();
$rowu = mysqli_fetch_array($result);

$color = $rowu['pref_color_ez'];
$pineventarr = explode(", ", $rowu['pin_events']);

$getm = '/^[0-9]{4}-[0-9]{2}$/';

if ((isset($_GET['month'])) && (preg_match($getm, $_GET['month']))) {
  $month = $_GET['month'];
} else {
  $month = date("Y-m");
}

$nextmonth = date("Y-m", strtotime($month." + 1 months"));
$lastmonth = date("Y-m", strtotime($month." - 1 months"));

$d_month = date("F Y", strtotime($month));

$firstday = strtotime($month.'-01');

$filters = array('project', 'digital', 'comm', 'socmed', 'finance', 'pin');

if (isset($_GET['f'])) {
  $f1 = $_GET['f'];
  if (in_array($f1, $filters)) {
    $f = $f1;
  } else {
    $fil2 = explode('-', $f1);
    foreach ($fil2 as $value) {
      if (in_array($value, $filters)) {
        $goodfil = TRUE;
      } else {
        $goodfil = FALSE;
        break;
      }
    }
    if ($goodfil == TRUE) {
      $f = $f1;
    }
}
}

if (isset($f)) {
  $fil = explode('-', $f);
}

$query2 = "SELECT * FROM `events`";
$stmt2 = $db->prepare($query2);
$stmt2->execute();
$result2 = $stmt2->get_result();

$events = [];
$eventdays = [];

$salt = 1;
while ($e = mysqli_fetch_array($result2)) {
  $edate = date("Y-m", strtotime($e['date']));
  if ($edate == $month) {
    $impday = date("j", strtotime($e['date']));
    $theday = $salt.'-'.$impday;
    $events += [$theday => $e];
    $eventdays[] = $impday;
    $eday = date("Y-m-d", strtotime($e['date']));
    $end = date("Y-m-d", strtotime($e['end']));
    if ($end !== $eday) {
      do {
        $eday = date("Y-m-d", strtotime($eday.' + 1 days'));
        $newdate = date("j", strtotime($eday));
        $eventdays[] = $newdate;
        $theday = $salt.'-'.$newdate;
        $events += [$theday => $e];
        $salt++;
      } while ($end !== $eday);
    } else {
      $salt++;
    }
  }
}

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
        <div class="title"><?php echo $rowu['username']; ?>'s Calendar</div>
        <div class="rowbox">
                <table class="month textbox">
                    <tr class="subtitle monthname">
                      <?php
                      
                      echo '<td><a href="calendar?month=', $lastmonth, '" class="day-event"><<</a></td>
                        <td colspan="5">', $d_month, '</td>
                        <td><a href="calendar?month=', $nextmonth, '" class="day-event">>></a></td>
                    </tr>
                    <tr class="weekdays">';
                    if ($rowu['week_start'] == 'sunday') {
                      echo '<td>Sunday</td>';
                      $firstdw1 = date("w", $firstday);
                      $firstdw = $firstdw1 + 1;
                    }
                        echo '<td>Monday</td>
                        <td>Tuesday</td>
                        <td>Wednesday</td>
                        <td>Thursday</td>
                        <td>Friday</td>
                        <td>Saturday</td>';
                        if ($rowu['week_start'] == 'monday') {
                          echo '<td>Sunday</td>';
                          $firstdw1 = date("w", $firstday);
                          $firstdw = (int)$firstdw1;
                        }
                        
                        $blankdays = $firstdw - 1;
                        
                        echo '</tr>
                        <tr class="week">
                        <td class="day" colspan="', $blankdays, '"></td>';
                        
                        $day = $firstday;
                        $dayplace = $blankdays + 1;
                        $emerg = 1;
                        
                        do {
                          
                        $curday = date("j", $day);
                        $readday = date("Y-m-j", $day);
                        
                        if (in_array($curday, $eventdays)) {
                          $eventnum = 0;
                          $dayevents = [];
                          foreach ($events as $key => $value) {
                            $dayarr = explode('-', $key);
                            if ($dayarr[1] == $curday) {
                              $eventnum++;
                              $dayevents[] = $key;
                            }
                          }
                          if ($eventnum == 1) {
                          $eventcode = $dayevents[0];
                          
                          $erow = $events[$eventcode];
                          $deptarr2 = array('project' => $erow['dept_projects'], 'digital' => $erow['dept_digital'], 'comm' => $erow['dept_community'], 'socmed' => $erow['dept_socmed'], 'finance' => $erow['dept_finance']);
                          $deptarr2 = array_filter($deptarr2, 'checkval');
                          
                          if ((isset($fil)) && (((in_array('pin', $fil)) && (in_array($erow['ref_id'], $pineventarr))) || (!in_array('pin', $fil)))) {
                            $fview = TRUE;
                          } elseif (isset($fil)) {
                            $fview = FALSE;
                          } else {
                            $fview = TRUE;
                          }
                          
                          if ((isset($fil)) && ($fview == TRUE) && ($f !== 'pin'))  {
                          foreach ($deptarr2 as $dept => $value) {
                                if (!in_array($dept, $fil)) {
                                  $fview = FALSE;
                                } else {
                                  $fview = TRUE;
                                  break;
                                }
                              }
                          }
                          
                          $perm = $erow['invited'];
                          $permarr = explode(', ', $perm);
                          
                          if (($fview == TRUE) && (in_array($user, $permarr))) {
                            $fview = TRUE;
                          } else {
                            $fview = FALSE;
                          }
                          
                          if ($fview == TRUE) {
                            
                            $eday = date("j", strtotime($erow['date']));
                            if ($curday == $eday) {
                            
                          echo '<td class="day ', $erow['event_type'], '">
                            <div class="day-height">
                            <div class="day2">
                                <div class="day-number">', $curday, '</div>
                                <a class="day-event" href="event?id=', $erow['ref_id'], '">',
                                  $erow['title'],
                                '</a>
                            </div>
                            <div class="day3">
                                <br>
                                <strong>Time:</strong> ', date('g:ia', strtotime($erow['date'])), '<br>
                                <strong>Organizer:</strong> ';
                                $q = "SELECT username FROM `users` WHERE `user_id` = ?";
                                $stmt3 = $db->prepare($q);

                                    $stmt3->bind_param("i", $erow['creator']);
                                    $stmt3->execute();
                                    $result3 = $stmt3->get_result();
                                    $r3 = mysqli_fetch_array($result3);
                                    echo $r3['username'];
                                echo '<br>
                                <strong>Tags:</strong> ', $erow['tags'], '<br>
                                <strong>Notes:</strong> ', $erow['summary'], '
                            </div>
                            </div>
                        </td>';
                          } else {
                            echo '<td class="day ', $erow['event_type'], '">
                        <div class="day2">
                        <div class="day-number">', $curday, '</div>
                        </div></td>';
                          }
                          } else {
                            
                            echo '<td class="day">
                        <div class="day2">
                        <div class="day-number">', $curday, '</div>
                        </div></td>';
                          }
                          } else {
                              echo '<td class="day">
                        <div class="day2">
                        <div class="day-number">', $curday, '</div>
                        </div>';
                        foreach ($dayevents as $value) {
                          $erow = $events[$value];
                          echo '<div class="smallpad day2 ', $erow['event_type'], '">
                          <a href="event?id=', $erow['ref_id'], '">',
                          $erow['title'],
                          '</a></div>';
                        }
                        echo '</td>';
                          }
                          } else {
                        echo '<td class="day">
                        <div class="day2">
                        <div class="day-number">', $curday, '</div>
                        </div></td>';
                        }
                        
                        $day = strtotime($readday.' + 1 days');
                        $curmonth = date("Y-m", $day);
                        $dayplace++;
                        
                        $emerg++;
                        if ($emerg > 35) {
                          break;
                        }
                        
                        if ($dayplace > 7) {
                          echo '</tr><tr>';
                          $dayplace = 1;
                        }
                        
                        } while ($curmonth == $month)
                ?>
                    </tr></table>
                
            <div class="column textbox" id="optionbox">
                <div class="padded">
                    <img class="menu-icon view-arrow" id="options-clicker" tabindex="0" src="../images/brunch-lemon-small.png">
                </div>
                <div class="padded center options">
                  <a class="button btncolor center padded boxrow" href="../create/event">Create Event</a>
                  <br>
                    <div class="boxrow">Filter Events</div>
                    <a class="button btncolor smallpad center<?php if ((isset($f)) && (($f == 'pin') || (in_array('pin', $fil)))) { echo ' active'; } ?>" href="calendar?f=pin<?php
                    if ( isset($f) ) {echo '-', $f; }
                    if (isset($month)) { echo '&month=', $month; }
                    ?>">Pinned</a>
                    <a class="button btncolor smallpad center<?php if ((isset($f)) && (($f == 'project') || (in_array('project', $fil)))) { echo ' active'; } ?>" href="calendar?f=project<?php if ( isset($f) ) {echo '-',$f; } if (isset($month)) { echo '&month=', $month; } ?>">Projects</a>
                    <a class="button btncolor smallpad center<?php if ((isset($f)) && (($f == 'digital') || (in_array('digital', $fil)))) { echo ' active'; } ?>" href="calendar?f=digital<?php if ( isset($f) ) {echo '-',$f; } if (isset($month)) { echo '&month=', $month; } ?>">Digital Media</a>
                    <a class="button btncolor smallpad center<?php if ((isset($f)) && (($f == 'comm') || (in_array('comm', $fil)))) { echo ' active'; } ?>" href="calendar?f=comm<?php if ( isset($f) ) {echo '-',$f; } if (isset($month)) { echo '&month=', $month; } ?>">Community</a>
                    <a class="button btncolor smallpad center<?php if ((isset($f)) && (($f == 'socmed') || (in_array('socmed', $fil)))) { echo ' active'; } ?>" href="calendar?f=socmed<?php if ( isset($f) ) {echo '-',$f; } if (isset($month)) { echo '&month=', $month; } ?>">Social Media</a>
                    <a class="button btncolor smallpad center<?php if ((isset($f)) && (($f == 'finance') || (in_array('finance', $fil)))) { echo ' active'; } ?>" href="calendar?f=finance<?php if ( isset($f) ) {echo '-',$f; } if (isset($month)) { echo '&month=', $month; } ?>">Finance</a>
                    <a class="button btncolor smallpad center<?php if (!isset($f)) { echo ' active'; } ?>" href="calendar<?php if (isset($month)) { echo '?month=', $month; } ?>">Show All</a>
                    <br>
                </div>
            </div>
        </div>
        <?php include("menu.php"); ?>
    </div>
</body>