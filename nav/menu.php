<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
  
$(document).ready(function() {

$('.stand').click(function() {
    $('.open-menu').toggle();
});

$('.stand').keypress(function(event) {
  if ((event.keyCode == 13) || (event.keyCode == 32)) {
    $(this).click();
  }
});

});

</script>

<div class="menu-wrapper">
    <img class="stand" src="../images/menu.png" tabindex="0">
    <div class="open-menu fakebtncolor">
        <a class="menu-item btncolor" href="dashboard.php">
            Dashboard
        </a>
        <a class="menu-item btncolor" href="profile?id=<?php echo $_SESSION['user_id']; ?>">
            Profile
        </a>
        <a class="menu-item btncolor" href="tasks">
            Tasks
        </a>
        <a class="menu-item btncolor" href="calendar">
            Calendar
        </a>
        <a class="menu-item btncolor" href="library">
            Documents
        </a>
        <a class="menu-item btncolor" href="directory">
            Staff
        </a>
        <a class="menu-item btncolor" href="https://ethicallyzesty.com" style="margin-bottom: 0px;">
            Main Website
        </a>
    </div>
</div>