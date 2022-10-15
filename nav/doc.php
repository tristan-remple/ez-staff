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
    $doc_id = $_GET['id'];
    
    $query = "SELECT * FROM `docs` WHERE `ref_id` = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $doc_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if (mysqli_num_rows($result) !== 0) {
    
    $row = mysqli_fetch_array($result);
    
    $viewarr = explode(', ', $row['viewers']);
    
    if (in_array($user, $viewarr)) {
        

?>
<html>
<head>

<link rel="stylesheet" type="text/css" href="../css/<?php echo $color; ?>.css">
<link rel="stylesheet" type="text/css" href="../css/desktop.css">

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

</head>
<body>
    
    <div class="verti">
        <div class="title">Document</div>
            <div class="solobox textbox">
              <div class="padded caps">
                <?php
                
                $query = "SELECT username FROM `users` WHERE `user_id` = ?";
                $stmt = $db->prepare($query);
                
                echo 
                '<div class="subtitle"> ', $row['title'], '</div>
                <strong>Reference ID:</strong> ', $row['ref_id'], '<br>
                <strong>Date Created:</strong> ', date("F j, Y", strtotime($row['date'])), '<br>
                <strong>Creator:</strong> ';
                $stmt->bind_param("i", $row['creator']);
                $stmt->execute();
                $result = $stmt->get_result();
                $r = mysqli_fetch_array($result);
                echo $r['username'],
                '<br>';
                
                echo '<strong>Editors:</strong> ';
                
                $editarr = explode(', ', $row['can_edit']);
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
                
                include("details.php");
                
              echo '</div>
            </div>';
            if ($row['doc_type'] == 'text') {
                    
                    $filename = '../docs/'.$row['filename'].'.txt';
                    echo '<div class="solobox textbox">
                    <div class="padded">';
                    $doc = fopen($filename, "r") or die("The specified document could not be found.");
                    while (!feof($doc)) {
                      echo fgets($doc) . "<br>";
                      }
                    fclose($doc);
                    echo '</div></div>';
                } elseif ($row['doc_type'] == 'table') {
                    
                    $filename = '../docs/'.$row['filename'].'.csv';
                    echo '<table class="textbox bigdeal">';
                    $f = fopen($filename, "r");
                    while (($line = fgetcsv($f)) !== false) {
                            echo "<tr>";
                            foreach ($line as $cell) {
                                    echo "<td><div class='smalldeal'>" . htmlspecialchars($cell) . "</div></td>";
                            }
                            echo "</tr>\n";
                    }
                    fclose($f);
                    echo '</table>';
                }
                
                ?>
        <?php
        
        if (in_array($user, $editarr)) {
            
            echo '<div class="solobox textbox">
              <div class="padded caps">
              <div class="boxrow check-align">
              <a class="button btncolor padded center" href="">download</a>
              
              <form class="editdoc">
              <input type="file" id="newdoc" name="newdoc">
  <input type="submit" class="button btncolor center padded" value="Upload">
  </form>
  </div>
  </div>
  </div>
  </div>';
            
        }
    } else {
        echo '
    <head>

<link rel="stylesheet" type="text/css" href="../css/', $color, '.css">
<link rel="stylesheet" type="text/css" href="../css/desktop.css">

</head>
    <div class="verti">
    <div class="title">No Access</div>';
    }
} else {
    echo '
    <head>

<link rel="stylesheet" type="text/css" href="../css/', $color, '.css">
<link rel="stylesheet" type="text/css" href="../css/desktop.css">

</head>
    <div class="verti">
    <div class="title">Bad URL</div>';
}
} else {
    echo '
    <head>

<link rel="stylesheet" type="text/css" href="../css/', $color, '.css">
<link rel="stylesheet" type="text/css" href="../css/desktop.css">

</head>
    <div class="verti">
    <div class="title">Bad URL</div>';
}
        
        
        include("menu.php"); ?>
    </div>
</body>
</html>