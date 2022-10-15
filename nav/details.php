<?php

echo '<strong>Departments:</strong> ';

$deptarr = array('projects' => $row['dept_projects'], 'digital media' => $row['dept_digital'], 'community' => $row['dept_community'], 'social media' => $row['dept_socmed'], 'finance' => $row['dept_finance']);
                
                $deptarr = array_filter($deptarr, 'checkval');
                
                $last = array_key_last($deptarr);
                foreach ($deptarr as $key => $value) {
                    echo $key;
                    if ($key !== $last) {
                        echo ', ';
                }
                }
                
                echo '<br>';
                
                if ($row['rel_docs'] !== NULL) {
                
                echo '<strong>Related Documents:</strong> ';
                
                    $query = "SELECT title FROM `docs` WHERE `ref_id` = ?";
                    $stmt = $db->prepare($query);
                    
                    $docarr = explode(', ', $row['rel_docs']);
                    $last = end($docarr);
                    
                    foreach ($docarr as $num) {
                        $stmt->bind_param("i", $num);
                        $stmt->execute();
                        $resultd = $stmt->get_result();
                        $rd = mysqli_fetch_array($resultd);
                        echo '<a href="doc?id=', $num, '">', $rd['title'], '</a>';
                        if ($num !== $last) {
                            echo ', ';
                        }
                    }
                    
                    echo '<br>';
                }
                
                if ($row['rel_events'] !== NULL) {
                
                echo '<strong>Related Events:</strong> ';
                
                $query = "SELECT title FROM `events` WHERE `ref_id` = ?";
                    $stmt = $db->prepare($query);
                    
                    $eventarr = explode(', ', $row['rel_events']);
                    $last = end($eventarr);
                    
                    foreach ($eventarr as $num) {
                        $stmt->bind_param("i", $num);
                        $stmt->execute();
                        $resulte = $stmt->get_result();
                        $re = mysqli_fetch_array($resulte);
                        echo '<a href="event?id=', $num, '">', $re['title'], '</a>';
                        if ($num !== $last) {
                            echo ', ';
                        }
                    }
                
                echo '<br>';
                }
                
                if ($row['rel_tasks'] !== NULL) {
                
                echo '<strong>Related Task Lists:</strong> ';
                
                $query = "SELECT title FROM `task_lists` WHERE `ref_id` = ?";
                    $stmt = $db->prepare($query);
                    
                    $taskarr = explode(', ', $row['rel_tasks']);
                    $last = end($taskarr);
                    
                    foreach ($taskarr as $num) {
                        $stmt->bind_param("i", $num);
                        $stmt->execute();
                        $resultt = $stmt->get_result();
                        $rt = mysqli_fetch_array($resultt);
                        echo '<a href="tasks?id=', $num, '">', $rt['title'], '</a>';
                        if ($num !== $last) {
                            echo ', ';
                        }
                    }
                
                echo '<br>';
                }
                
                if ($row['tags'] !== NULL) {                
                    echo '<strong>Tags:</strong> ', $row['tags'], '<br>';
                }
                
                ?>