<?php

include('conn.php');

$user = $_SESSION['user_id'];

$query = "SELECT * FROM `users` WHERE `user_id` = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $user);
$stmt->execute();
$result = $stmt->get_result();
$rowu = mysqli_fetch_array($result);

$pindocarr = explode(", ", $rowu['pin_tasks']);

$color = $rowu['pref_color_ez'];

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


?><html>
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

$('.menu-icon').click(function() {
  var id = $(this).attr('id');
  $('.details#'+id).toggle();
});

});

</script>

</head>
<body>
    
    <div class="verti">
        <div class="title">Library</div>
        <div class="rowbox">
          <div class="solobox">
            <?php
            
            $qdoc = "SELECT * FROM `docs`";
            $stmtdoc = $db->prepare($qdoc);
            $stmtdoc->execute();
            $resultdoc = $stmtdoc->get_result();
            
            $count = mysqli_num_rows($resultdoc);
            $alphadocs = [];
            $refdocs = [];
            $newdocs = [];
            
            while ($d = mysqli_fetch_array($resultdoc)) {
              $alphadocs += [$d['ref_id'] => $d['title']];
              $refdocs += [$d['ref_id'] => $d['ref_id']];
              $newdocs += [$d['ref_id'] => $d['date']];
            }
            
            asort($alphadocs);
            asort($refdocs);
            arsort($newdocs);
            
            $id = 1;
            
            $q = "SELECT * FROM `docs` WHERE `ref_id` = ?";
            $stmtd = $db->prepare($q);
            
            function displayDoc($id) {
              
              global $stmtd;
              global $pindocarr;
              global $db;
              global $color;
              global $user;
              global $pindocarr;
              global $fil;
              global $f;
              
            $stmtd->bind_param("i", $id);
            $stmtd->execute();
            $resultd = $stmtd->get_result();
            $do = mysqli_fetch_array($resultd);
            
            $deptarr2 = array('project' => $do['dept_projects'], 'digital' => $do['dept_digital'], 'comm' => $do['dept_community'], 'socmed' => $do['dept_socmed'], 'finance' => $do['dept_finance']);
                          $deptarr2 = array_filter($deptarr2, 'checkval');
                          
                          if ((isset($fil)) && (((in_array('pin', $fil)) && (in_array($do['ref_id'], $pindocarr))) || (!in_array('pin', $fil)))) {
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
                          
                          $perm = $do['viewers'];
                          $permarr = explode(', ', $perm);
                          
                          if (($fview == TRUE) && (in_array($user, $permarr))) {
                            $fview = TRUE;
                          } else {
                            $fview = FALSE;
                          }
                          
                          if ($fview == TRUE) {
            
            $divid = 'doc_'.$do['ref_id'];
            echo '<div class="textbox kern">
            <div class="padded kern">
            <div class="boxrow check-align">';
            if (in_array($do['ref_id'], $pindocarr)) {
              echo '<img class="menu-icon" src="../images/', $color, '-lime-small.png">';
            } else {
              echo '<img class="menu-icon" src="../images/', $color, '-orange-small.png">';
            }
            echo '<img class="menu-icon" id="', $divid, '" src="../images/', $color, '-lemon-small.png">
            ', $do['title'],
            '<a class="button btncolor padded center detail" href="doc?id=', $do['ref_id'], '">View</a>
            </div>';
            
            $query = "SELECT username FROM `users` WHERE `user_id` = ?";
            $stmt = $db->prepare($query);
            
            echo'<div class="details" id="', $divid, '">',
            '<strong>Reference ID: ', $do['ref_id'], '</strong><br>',
            '<strong>Date Created:</strong> ', date("F j, Y", strtotime($do['date'])), '<br>
                <strong>Creator:</strong> ';
                $stmt->bind_param("i", $do['creator']);
                $stmt->execute();
                $result = $stmt->get_result();
                $r = mysqli_fetch_array($result);
                echo $r['username'],
                '<br>';
                
                echo '<strong>Editors:</strong> ';
                
                $editarr = explode(', ', $do['can_edit']);
                $last = end($editarr);
                
                foreach ($editarr as $num) {
                    $stmt->bind_param("i", $num);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $r = mysqli_fetch_array($result);
                    echo $r['username'];
                    if ($num !== $last) {
                        echo ', ';
                    }
                }
                
                echo '<br>';
                
                echo '<strong>Viewers:</strong> ';
                
                $viewers = $do['viewers'];
                $viewarr = explode(', ', $viewers);
                
                $last = end($viewarr);
                
                foreach ($viewarr as $num) {
                    $stmt->bind_param("i", $num);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $r = mysqli_fetch_array($result);
                    echo $r['username'];
                    if ($num !== $last) {
                        echo ', ';
                    }
                }
                
                echo '<br>';
                
                $row = $do;
                include("details.php");
            
            echo '<strong>Notes:</strong> ', $do['summary'];
            
            echo '</div></div></div>';
            }
            }
            
            $displays = array('new', 'title', 'ref');
            asort($alphadocs);
            asort($refdocs);
            arsort($newdocs);
            
            $displays = array('new', 'title', 'ref');
            
            if ((isset($_GET['d'])) && (in_array($_GET['d'], $displays))) {
              $d = $_GET['d'];
            } else {
              $d = 'ref';
            }
            
            if ($d == 'new') {
              foreach ($newdocs as $id => $value) {
                displayDoc($id);
              }
            } elseif ($d == 'title') {
              foreach ($alphadocs as $id => $value) {
                displayDoc($id);
              }
            } elseif ($d == 'ref') {
              foreach ($refdocs as $id => $value) {
                displayDoc($id);
              }
            }
            
            ?>
          </div>
          <div class="column textbox" id="optionbox">
            <img class="menu-icon view-arrow" id="options-clicker" tabindex="0" src="../images/brunch-lemon-small.png">
                <div class="padded center options">
                  <a class="button btncolor center padded boxrow" href="../create/doc">Create Document</a>
                  <br>
                    <div class="subtitle">Viewing Options</div>
                </div>
                <div class="padded options">
                    <div class="boxrow">Filter Lists</div>
                    <a class="button btncolor smallpad center<?php if ((isset($f)) && (($f == 'pin') || (in_array('pin', $fil)))) { echo ' active'; } ?>" href="library?f=pin<?php if ( isset($f) ) {echo '-', $f; } if ( isset($d) ) { echo '&d=', $d; } ?>">Pinned</a>
                    <a class="button btncolor smallpad center<?php if ((isset($f)) && (($f == 'project') || (in_array('project', $fil)))) { echo ' active'; } ?>" href="library?f=project<?php if ( isset($f) ) {echo '-',$f; } if ( isset($d) ) { echo '&d=', $d; } ?>">Projects</a>
                    <a class="button btncolor smallpad center<?php if ((isset($f)) && (($f == 'digital') || (in_array('digital', $fil)))) { echo ' active'; } ?>" href="library?f=digital<?php if ( isset($f) ) {echo '-',$f; } if ( isset($d) ) { echo '&d=', $d; } ?>">Digital Media</a>
                    <a class="button btncolor smallpad center<?php if ((isset($f)) && (($f == 'comm') || (in_array('comm', $fil)))) { echo ' active'; } ?>" href="library?f=comm<?php if ( isset($f) ) {echo '-',$f; } if ( isset($d) ) { echo '&d=', $d; } ?>">Community</a>
                    <a class="button btncolor smallpad center<?php if ((isset($f)) && (($f == 'socmed') || (in_array('socmed', $fil)))) { echo ' active'; } ?>" href="library?f=socmed<?php if ( isset($f) ) {echo '-',$f; } if ( isset($d) ) { echo '&d=', $d; } ?>">Social Media</a>
                    <a class="button btncolor smallpad center<?php if ((isset($f)) && (($f == 'finance') || (in_array('finance', $fil)))) { echo ' active'; } ?>" href="library?f=finance<?php if ( isset($f) ) {echo '-',$f; } if ( isset($d) ) { echo '&d=', $d; } ?>">Finance</a>
                    <a class="button btncolor smallpad center<?php if (!isset($f)) { echo ' active'; } ?>" href="library<?php if ( isset($d) ) { echo '?d=', $d; } ?>">Show All</a>
                    <br>
                    <div class="boxrow">Sort Documents By</div>
                    <a class="button btncolor smallpad center<?php if ((isset($d)) && ($d == 'ref')) { echo ' active'; } ?>" href="library?d=ref<?php if ( isset($f) ) { echo '&f=', $f; } ?>">Reference ID</a>
                    <a class="button btncolor smallpad center<?php if ((isset($d)) && ($d == 'title')) { echo ' active'; } ?>" href="library?d=title<?php if ( isset($f) ) { echo '&f=', $f; } ?>">Title</a>
                    <a class="button btncolor smallpad center<?php if ((isset($d)) && ($d == 'new')) { echo ' active'; } ?>" href="library?d=new<?php if ( isset($f) ) { echo '&f=', $f; } ?>">Newest</a>
                </div>
          </div>
        
        <?php include("menu.php"); ?>
        
    </div>
    
</body>