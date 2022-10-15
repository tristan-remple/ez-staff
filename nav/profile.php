<?php

include('conn.php');

$user = $_SESSION['user_id'];

$query = "SELECT * FROM `users` WHERE `user_id` = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $user);
$stmt->execute();
$resultu = $stmt->get_result();
$rowu = mysqli_fetch_array($resultu);

$color = $rowu['pref_color_ez'];

if ($rowu['rank'] == 'non-staff') {
  $perm = FALSE;
  $err = 'No access';
}

if ((isset($_GET)) && (isset($_GET['id']))) {
  $stmt->bind_param("i", $_GET['id']);
  $stmt->execute();
  $result = $stmt->get_result();
  if ((mysqli_num_rows($result)) !== 1) {
    $perm = FALSE;
    $err = 'Not found';
  } else {
  
  $row = mysqli_fetch_array($result);
  
  if ($user == $_GET['id']) {
    $viewer = 'self';
    $perm = TRUE;
  } elseif ($rowu['rank'] == 'manager') {
    $viewer = 'manager';
    $perm = TRUE;
  } elseif ($rowu['rank'] == 'admin') {
    $viewer = 'admin';
    $perm = TRUE;
  } elseif ($rowu['rank'] == 'staff') {
    if ($row['privacy'] == 'need-to-know') {
      $perm = FALSE;
      $err = 'Not allowed';
    } else {
      $viewer = 'staff';
      $perm = TRUE;
    }
  }
  }
} else {
  $perm = FALSE;
  $err = 'Unspecified URL';
}

if ($perm == TRUE) {

?>
<html>
<head>

<link rel="stylesheet" type="text/css" href="../css/<?php echo $color; ?>.css">
<link rel="stylesheet" type="text/css" href="../css/desktop.css">

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script src="https://twemoji.maxcdn.com/v/latest/twemoji.min.js" crossorigin="anonymous"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
  
$(document).ready(function() {
  
var emo = document.getElementsByClassName('emo')[0];
twemoji.parse(emo);

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
        <div class="title"><?php echo $row['username']; ?>'s Profile</div>
        </div>
        
        <div class="rowbox">
            <div class="column textbox check-align">
                <div class="padded emo">
                    <div class="boxrow subtitle check-align">General Info
                    <img class="icon" src="../images/<?php echo $row['icon']; ?>">
                    </div>
                    <?php
                    
                    echo '<strong>User ID:</strong> ', $row['user_id'], '<br>',
                    '<strong>Username:</strong> ', $row['username'], '<br>',
                    '<strong>Date Joined:</strong> ', date("Y-m-d", strtotime($row['date'])), '<br>',
                    '<strong>Pronouns:</strong> ', $row['pronouns'], '<br>',
                    '<strong>Triggers/Squicks:</strong> ', $row['triggers'], '<br>',
                    '<br><strong>Bio:</strong> ', $row['bio'];
                    
                    ?>
                </div>
                <div class="padded">
                  <div class="subtitle">Contact Info</div>
                  <?php
                  
                  if (($row['display_contact'] == 'yes') || ($viewer == 'manager') || ($viewer == 'admin') || ($viewer == 'self')) {
                    echo '<strong>Discord Handle:</strong> ', $row['discord'];
                    if ($row['preferred_contact'] == 'discord') {
                      echo ' (preferred)';
                    }
                    if (isset($row['email'])) {
                      echo '<br><strong>Email:</strong> ', $row['email'];
                      if ($row['preferred_contact'] == 'email') {
                        echo ' (preferred)';
                      }
                    }
                    if (isset($row['twitter'])) {
                      echo '<br><strong>Twitter handle:</strong> ', $row['twitter'];
                      if ($row['preferred_contact'] == 'twitter') {
                        echo ' (preferred)';
                      }
                    }
                  } elseif ($row['display_contact'] == 'preferred') {
                    if ($row['preferred_contact'] == 'discord') {
                      echo '<strong>Discord Handle:</strong> ', $row['discord'];
                    } elseif ($row['preferred_contact'] == 'email') {
                      echo '<strong>Email:</strong> ', $row['email'];
                    } elseif ($row['preferred_contact'] == 'twitter') {
                      echo '<strong>Twitter Handle:</strong> ', $row['twitter'];
                    }
                  }
                  
                  ?>
                </div>
                <div class="padded">
                    <a class="button btncolor center padded spacer" href="../portal/settings?id=<?php echo $row['user_id']; ?>">Change Settings</a>
                </div>
            </div>
            <div class="column textbox check-align">
                <div class="padded caps">
                    <div class="subtitle">Staff Info</div>
                  <?php
                  
                  echo '<strong>Rank:</strong> ', $row['rank'], '<br>';
                  
                  $deptarr = array('projects' => $row['dept_projects'], 'digital media' => $row['dept_digital'], 'community' => $row['dept_community'], 'social media' => $row['dept_socmed'], 'finance' => $row['dept_finance']);
                  
                  $deptarr = array_filter($deptarr, 'checkval');
                  
                  if (count($deptarr) == 1) {
                    echo '<strong>Department:</strong> ';
                  } else {
                    echo '<strong>Departments:</strong> ';
                  }
                  
                  $last = array_key_last($deptarr);
                  foreach ($deptarr as $key => $value) {
                    echo $key;
                    if ($key !== $last) {
                        echo ', ';
                    } else {
                      echo '<br>';
                    }
                  }
                  
                  if (strpos($row['position'], ', ')) {
                    echo '<strong>Positions:</strong> ', $row['position'], '<br>';
                  } else {
                    echo '<strong>Position:</strong> ', $row['position'], '<br>';
                  }
                  
                  echo '<strong>Term of Commitment:</strong> ';
                  if ($row['commitment'] == 'long-term') {
                    echo 'Foreseeable future';
                  } elseif ($row['commitment'] == 'couple-years') {
                    echo 'A couple years';
                  } elseif ($row['commitment'] == 'current-project') {
                    echo 'Current project only';
                  } elseif ($row['commitment'] == 'short-term') {
                    echo 'Uncertain';
                  }
                  echo '<br>';
                  
                  if (isset($row['project'])) {
                    if (strpos($row['project'], ', ')) {
                      echo '<strong>Projects:</strong> ';
                    } else {
                      echo '<strong>Project:</strong> ';
                    }
                    echo $row['project'], '<br>';
                  }
                  
                  ?>
                </div>
                <div class="padded">
                  <div class="subtitle">Other Participation</div>
                  <?php
                  
                  function displaySub($id) {
                    global $db;
                    global $thing;
                    $query = "SELECT title, anonymous FROM `subs` WHERE `ref_id` = ?";
                    $stmt = $db->prepare($query);
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $res = $stmt->get_result();
                    $s = mysqli_fetch_array($res);
                    
                    if ($s['anonymous'] == 0) {
                      $thing = '<a href="sub?id='.$id.'">'.$s['title'].'</a>';
                    } else {
                      $thing = NULL;
                    }
                  }
                  
                  function displayAd($id) {
                    global $db;
                    $query = "SELECT title FROM `docs` WHERE `ref_id` = ?";
                    $stmt = $db->prepare($query);
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $res = $stmt->get_result();
                    $a = mysqli_fetch_array($res);
                    
                    echo '<a href="ad?id=', $id, '">', $a['title'], '</a>';
                  }
                  
                  $subs = [];
                  
                  if ($row['submissions'] !== NULL) {
                    if (strpos($row['submissions'], ', ')) {
                      echo '<strong>Submissions:</strong> ';
                      $subarr = explode(', ', $row['submissions']);
                      foreach ($subarr as $id) {
                        displaySub($id);
                        if ($thing !== NULL) {
                          $subs += [$thing];
                        }
                      }
                    } else {
                      echo '<strong>Submission:</strong> ';
                      displaySub($row['submissions']);
                      if ($thing !== NULL) {
                          $subs += [$thing];
                        }
                    }
                  }
                  
                  if (empty($subs)) {
                    echo 'none';
                  } else {
                    $last = end($subs);
                    foreach ($subs as $th) {
                      echo $th;
                      if ($th !== $last) {
                        echo ', ';
                      }
                    }
                  }
                  
                  echo '<br>';
                  
                  if ($row['ads'] !== NULL) {
                    if (strpos($row['ads'], ', ')) {
                      echo '<strong>Ads:</strong> ';
                      $adarr = explode(', ', $row['ads']);
                      $last = end($adarr);
                      foreach ($adarr as $id) {
                        displayAd($id);
                        if ($id !== $last) {
                          echo ', ';
                        }
                      }
                    } else {
                      echo '<strong>Ad:</strong> ';
                      displayAd($row['ads']);
                    }
                  }
                  
                  
                  ?>
                </div>
                <div class="padded">
                    <a class="button btncolor center padded spacer" href="../portal/settings?id=<?php echo $row['user_id']; ?>">Change Settings</a>
                </div>
            </div>
            <div class="column textbox check-align">
                <div class="padded center caps">
                    <div class="subtitle">Activity</div>
                    <hr class="line">
                    <?php
                    
                    $recent = [];
                    
                    $query = "SELECT * FROM `docs` WHERE `creator` = ?";
                    $stmt = $db->prepare($query);
                    $stmt->bind_param("i", $row['user_id']);
                    $stmt->execute();
                    $rd = $stmt->get_result();
                    while ($d = mysqli_fetch_array($rd)) {
                      $recent += [$d['date'] => $d];
                    }
                    
                    $query = "SELECT * FROM `events` WHERE `creator` = ?";
                    $stmt = $db->prepare($query);
                    $stmt->bind_param("i", $row['user_id']);
                    $stmt->execute();
                    $re = $stmt->get_result();
                    while ($e = mysqli_fetch_array($re)) {
                      $recent += [$e['date'] => $e];
                    }
                    
                    $query = "SELECT * FROM `task_lists` WHERE `creator` = ?";
                    $stmt = $db->prepare($query);
                    $stmt->bind_param("i", $row['user_id']);
                    $stmt->execute();
                    $rt = $stmt->get_result();
                    while ($t = mysqli_fetch_array($rt)) {
                      $recent += [$t['date'] => $t];
                    }
                    
                    krsort($recent);
                    
                    $item = 1;
                    foreach ($recent as $r) {
                      if (isset($r['doc_type'])) {
                        echo '<div class="boxrow check-align">',
                        $r['title'],
                        '<a class="button btncolor padded center detail" href="doc?id=', $r['ref_id'], '">View</a>
                        </div>
                        Document created on ', date("F j", strtotime($r['date'])),
                        '<hr class="line">';
                      } elseif (isset($r['attending'])) {
                        echo '<div class="boxrow check-align">',
                        $r['title'],
                        '<a class="button btncolor padded center detail" href="event?id=', $r['ref_id'], '">View</a>
                        </div>
                        Event on ', date("F j", strtotime($r['date'])),
                        '<hr class="line">';
                      } elseif (isset($r['tasks'])) {
                        echo '<div class="boxrow check-align">',
                        $r['title'],
                        '<a class="button btncolor padded center detail" href="tasks?id=', $r['ref_id'], '">View</a>
                        </div>
                        Task list created on ', date("F j", strtotime($r['date'])),
                        '<hr class="line">';
                      }
                      $item++;
                      if ($item > 3) {
                        break;
                      }
                    }
                    
                    ?>
                </div>
                <div class="padded">
                    <a class="button btncolor center padded spacer" href="../portal/settings?id=<?php echo $row['user_id']; ?>">Change Settings</a>
                </div>
            </div>
        </div>
        
        <?php
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