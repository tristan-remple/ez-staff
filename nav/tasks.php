<?php

include('conn.php');

$user = $_SESSION['user_id'];

$query = "SELECT * FROM `users` WHERE `user_id` = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $user);
$stmt->execute();
$result = $stmt->get_result();
$rowu = mysqli_fetch_array($result);

$pintaskarr = explode(", ", $rowu['pin_tasks']);

$color = $rowu['pref_color_ez'];

$filters = array('project', 'digital', 'comm', 'socmed', 'finance', 'pin');
$displays = array('due', 'title', 'most', 'least', 'ref');

if ((isset($_GET['d'])) && (in_array($_GET['d'], $displays))) {
  $d = $_GET['d'];
} else {
  $d = 'ref';
}

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

if (isset($_GET['id'])) {
  $id = (int)$_GET['id'];
  if ($id == 0) {
    unset($id);
  }
}

?><html>
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

$('.menu-icon').click(function() {
  var id = $(this).attr('id');
  $('.details#'+id).toggle();
});

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
        <div class="title"><?php echo $rowu['username']; ?>'s Tasks</div>
        <div class="rowbox">
          <div class="solobox textbox">
                <div class="padded center">
                  <?php
                  
                  $query3 = "SELECT * FROM `task_lists` WHERE `ref_id` = ?";
                  $stmt3 = $db->prepare($query3);
                  $stmt3->bind_param("i", $id);
                  $stmt3->execute();
                  $result3 = $stmt3->get_result();
                  if (mysqli_num_rows($result3) == 1) {
                    $row3 = mysqli_fetch_array($result3);
                    
                    $editarr = explode(', ', $row3['editors']);
                    
                    echo '<div class="subtitle">', $row3['title'], '</div>';
                    
                    $qtask = "SELECT * FROM `tasks` WHERE `ref_id` = ?";
                    $stmttask = $db->prepare($qtask);
                    
                    $tasks1 = explode(', ', $row3['tasks']);
                    $count = count($tasks1);
                    $complete = 0;
                    $duetasks = [];
                    $titletasks = [];
                    $mosttasks = [];
                    
                    foreach ($tasks1 as $num) {
                      $stmttask->bind_param("i", $num);
                      $stmttask->execute();
                      $resulttask = $stmttask->get_result();
                      $rtask = mysqli_fetch_array($resulttask);
                      if ($rtask['complete'] == 1) {
                        $complete++;
                      }
                      
                      $duetasks += [$num => $rtask['due']];
                      $titletasks += [$num => $rtask['title']];
                      
                      $userno = explode(', ', $rtask['viewers']);
                      $tcount = count($userno);
                      $mosttasks += [$num => $tcount];
                      
                    }
                    
                    arsort($mosttasks);
                    $leasttasks = $mosttasks;
                    asort($leasttasks);
                    asort($duetasks);
                    asort($titletasks);
                    ksort($tasks1);
                    
                    echo '<div class="boxrow center">', $complete, '/', $count, ' Complete</div>';
                              
                              $q = "SELECT username, icon, rank FROM `users` WHERE `user_id` = ?";
                              $stmt2 = $db->prepare($q);
                              $last = end($editarr);
                              
                              foreach ($editarr as $num) {
                                  $stmt2->bind_param("i", $num);
                                  $stmt2->execute();
                                  $result2 = $stmt2->get_result();
                                  $r1 = mysqli_fetch_array($result2);
                                  echo '<img class="menu-icon admin round spacer" src="../images/', $r1['icon'], '">';
                              }
                              echo '<br>';
                    echo '
                    </div><div class="padded stretch">
                    <hr class="line">
                    <form class="to-do check-align">
                    <div class="to-do">';

                    function printTask($num) {
                      global $db;
                      global $color;
                      global $user;
                      global $editarr;
                      
                      $qtask = "SELECT * FROM `tasks` WHERE `ref_id` = ?";
                      $stmttask = $db->prepare($qtask);
                      $stmttask->bind_param("i", $num);
                      $stmttask->execute();
                      $resulttask = $stmttask->get_result();
                      $rtsk = mysqli_fetch_array($resulttask);
                      $dvd = 'task_'.$rtsk['ref_id'];
                      echo '<div class="boxrow check-align"><div class="boxrow">
                      <img class="menu-icon" id="', $dvd, '" src="../images/', $color, '-lemon-small.png">
                      <div class="num">#', $rtsk['ref_id'], '</div>';
                      if ($rtsk['complete'] == 1) {
                              echo '<strike>';
                            }
                            echo '<label for="', $dvd, '" class="checklabel" id="', $dvd, '">',
                            $rtsk['title'],
                            '</label>';
                            if ($rtsk['complete'] == 1) {
                              echo '</strike>';
                            }
                            
                            if (in_array($user, $editarr)) {
                              echo '<input type="checkbox" name="', $dvd, '" id="', $dvd, '" class="secretbox';
                            if ($rtsk['complete'] == 1) {
                              echo ' is-check" value="true';
                            }
                            echo '"></div>
                            <div class="checkbox2 btncolor" id="', $dvd, '"><div class="checkmark" id="', $dvd, '"';
                            if ($rtsk['complete'] == 1) {
                              echo ' style="display: block;"';
                            }
                            echo '>✔</div></div>';
                            } else {
                              echo '</div>';
                            }
                            
                        echo '</div>
                        <div class="details" id="', $dvd, '">
                          <strong>Due:</strong> ', date("F j, g:ia", strtotime($rtsk['due'])), '<br>';
                              
                          $row = $rtsk;
                              include('details2.php');
                              
                            if (isset($rtsk['summary'])) {
                              echo '<br><div class="accent padded"><strong>Notes:</strong> ', $rtsk['summary'], '</div>';
                            }
                        echo '</div>
                        <hr class="line">';
                      
                    }
                    
                    if ($d == 'ref') {
                      foreach ($tasks1 as $num) {
                        printTask($num);
                      }
                    } elseif ($d == 'due') {
                      foreach ($duetasks as $num => $v) {
                        printTask($num);
                      }
                    } elseif ($d == 'title') {
                      foreach ($titletasks as $num => $v) {
                        printTask($num);
                      }
                    } elseif ($d == 'most') {
                      foreach ($mosttasks as $num => $v) {
                        printTask($num);
                      }
                    } elseif ($d == 'least') {
                      foreach ($leasttasks as $num => $v) {
                        printTask($num);
                      }
                    }
                    
                    echo '
                        
                        </div><div class="button btncolor center sub"><div class="padded">Submit Completed Tasks</div></div>
                    
                    </form></div>';
                    
                  } else {
                    echo '<div class="subtitle">No Task List Selected</div>';
                  }
                  
                  ?>
            </div>
          <div class="column textbox" id="optionbox">
            <img class="menu-icon view-arrow" id="options-clicker" tabindex="0" src="../images/brunch-lemon-small.png">
                <div class="padded center options">
                  <a class="button btncolor center padded boxrow" href="../create/tasks">Create Task List</a>
                  <br>
                  <div class="subtitle">Viewing Options</div>
                </div>
                <div class="padded options">
                    <div class="boxrow">Filter Lists</div>
                    <a class="button btncolor smallpad center<?php if ((isset($f)) && (($f == 'pin') || (in_array('pin', $fil)))) { echo ' active'; } ?>" href="tasks?f=pin<?php
                    if ( isset($f) ) {echo '-', $f; }
                    if ( isset($d) ) { echo '&d=', $d; }
                    if ( isset($id) ) { echo '&id=', $id; }
                    ?>">Pinned</a>
                    <a class="button btncolor smallpad center<?php if ((isset($f)) && (($f == 'project') || (in_array('project', $fil)))) { echo ' active'; } ?>" href="tasks?f=project<?php if ( isset($f) ) {echo '-',$f; } if ( isset($d) ) { echo '&d=', $d; } if ( isset($id) ) { echo '&id=', $id; } ?>">Projects</a>
                    <a class="button btncolor smallpad center<?php if ((isset($f)) && (($f == 'digital') || (in_array('digital', $fil)))) { echo ' active'; } ?>" href="tasks?f=digital<?php if ( isset($f) ) {echo '-',$f; } if ( isset($d) ) { echo '&d=', $d; } if ( isset($id) ) { echo '&id=', $id; } ?>">Digital Media</a>
                    <a class="button btncolor smallpad center<?php if ((isset($f)) && (($f == 'comm') || (in_array('comm', $fil)))) { echo ' active'; } ?>" href="tasks?f=comm<?php if ( isset($f) ) {echo '-',$f; } if ( isset($d) ) { echo '&d=', $d; } if ( isset($id) ) { echo '&id=', $id; } ?>">Community</a>
                    <a class="button btncolor smallpad center<?php if ((isset($f)) && (($f == 'socmed') || (in_array('socmed', $fil)))) { echo ' active'; } ?>" href="tasks?f=socmed<?php if ( isset($f) ) {echo '-',$f; } if ( isset($d) ) { echo '&d=', $d; } if ( isset($id) ) { echo '&id=', $id; } ?>">Social Media</a>
                    <a class="button btncolor smallpad center<?php if ((isset($f)) && (($f == 'finance') || (in_array('finance', $fil)))) { echo ' active'; } ?>" href="tasks?f=finance<?php if ( isset($f) ) {echo '-',$f; } if ( isset($d) ) { echo '&d=', $d; } if ( isset($id) ) { echo '&id=', $id; } ?>">Finance</a>
                    <a class="button btncolor smallpad center<?php if (!isset($f)) { echo ' active'; } ?>" href="tasks<?php if ( isset($d) ) { echo '?d=', $d; } if ( isset($id) ) { echo '&id=', $id; } ?>">Show All</a>
                    <br>
                    <div class="boxrow">Sort Tasks By</div>
                    <a class="button btncolor smallpad center<?php if ((isset($d)) && ($d == 'ref')) { echo ' active'; } ?>" href="tasks?d=ref<?php if ( isset($f) ) { echo '&f=', $f; } if ( isset($id) ) { echo '&id=', $id; } ?>">Reference ID</a>
                    <a class="button btncolor smallpad center<?php if ((isset($d)) && ($d == 'due')) { echo ' active'; } ?>" href="tasks?d=due<?php if ( isset($f) ) { echo '&f=', $f; } if ( isset($id) ) { echo '&id=', $id; } ?>">Due Soonest</a>
                    <a class="button btncolor smallpad center<?php if ((isset($d)) && ($d == 'title')) { echo ' active'; } ?>" href="tasks?d=title<?php if ( isset($f) ) { echo '&f=', $f; } if ( isset($id) ) { echo '&id=', $id; } ?>">Title</a>
                    <a class="button btncolor smallpad center<?php if ((isset($d)) && ($d == 'most')) { echo ' active'; } ?>" href="tasks?d=most<?php if ( isset($f) ) { echo '&f=', $f; } if ( isset($id) ) { echo '&id=', $id; } ?>">Most People Tagged</a>
                    <a class="button btncolor smallpad center<?php if ((isset($d)) && ($d == 'least')) { echo ' active'; } ?>" href="tasks?d=least<?php if ( isset($f) ) { echo '&f=', $f; } if ( isset($id) ) { echo '&id=', $id; } ?>">Least People Tagged</a>
                </div>
                <div class="padded center caps options">
                  <div class="subtitle">Task Lists</div>
                        <hr class="line">
                        
                        <?php
                        
                        $query = "SELECT * FROM `task_lists`";
                        $stmt = $db->prepare($query);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        
                        while ($r = mysqli_fetch_array($result)) {
                          $viewers = $r['viewers'];
                          $deptarr2 = array('project' => $r['dept_projects'], 'digital' => $r['dept_digital'], 'comm' => $r['dept_community'], 'socmed' => $r['dept_socmed'], 'finance' => $r['dept_finance']);
                          $deptarr2 = array_filter($deptarr2, 'checkval');
                          $viewarr = explode(", ", $viewers);
                          if (in_array($user, $viewarr)) {
                            if (isset($f)) {
                              foreach ($deptarr2 as $dept => $value) {
                                if (!in_array($dept, $fil)) {
                                  $fview = FALSE;
                                } else {
                                  $fview = TRUE;
                                  break;
                                }
                              }
                            } else {
                              $fview = TRUE;
                            }
                            if ((isset($f)) && (($f == 'pin') || (in_array('pin', $fil))) && ($fview == TRUE)) {
                              if (in_array($r['ref_id'], $pintaskarr)) {
                                $fview = TRUE;
                              } else {
                                $fview = FALSE;
                              }
                            }
                            if ($fview == TRUE) {
                              $divid = 'tasklist_'.$r['ref_id'];
                              echo '
                              <div class="accent padded">
                              <div class="boxrow check-align"><div class="boxrow">';
                              if (in_array($r['ref_id'], $pintaskarr)) {
                                echo '<img class="menu-icon" src="../images/', $color, '-lime-small.png">';
                              } else {
                                echo '<img class="menu-icon" src="../images/', $color, '-orange-small.png">';
                              }
                            echo '<img class="menu-icon" id="', $divid, '" src="../images/', $color, '-lemon-small.png"><a href="tasks?id=', $r['ref_id'];
                            if ( isset($f) ) {echo '&f=',$f; } if ( isset($d) ) { echo '&d=', $d; }
                            echo '"></div>', $r['title'], '</a>
                            </div>
                            <div class="details" id="', $divid, '">
                              <strong>Reference ID:</strong> ', $r['ref_id'], '<br>';
                              
                              echo '<strong>Complete:</strong> ';
                              
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
                              echo $complete, '/', $count;
                              
                              echo '<br>';
                              
                              
                              echo '<strong>Due:</strong> ', date("F j, g:ia", strtotime($r['due'])), '<br>
                              <strong>People Tagged:</strong> ';
                              
                              $row = $r;
                              include('details.php');
                              
                            echo '<strong>Notes:</strong> ', $row['summary'], '<br>
                            <div class="center">';
                            
                            foreach ($editarr as $num) {
                                  $stmt2->bind_param("i", $num);
                                  $stmt2->execute();
                                  $result2 = $stmt2->get_result();
                                  $r1 = mysqli_fetch_array($result2);
                                  echo '<img class="menu-icon admin round spacer" src="../images/', $r1['icon'], '">';
                              }
                            echo '</div></div></div>',
                            '<hr class="line">';
                            }
                          }
                        }
                        
                        ?>
                        
                </div>
          </div>
        
        <?php include("menu.php"); ?>
        
    </div>
    
</body>