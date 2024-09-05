<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="navbar_all">
    <a href="index.php" class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
        <div class="logo"></div>
    </a>
    
    <div class="nav_links">
        <a href="index.php" class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
            <span><i class="fa-solid fa-house"></i></span> Home
        </a>
        <a href="tickets.php" class="<?php echo ($current_page == 'tickets.php') ? 'active' : ''; ?>">
            <span><i class="fa-solid fa-ticket"></i></span> Tickets
        </a>
        <a href="blog.php" class="<?php echo ($current_page == 'blog.php') ? 'active' : ''; ?>">
            <span><i class="fa-solid fa-newspaper"></i></span> Blog
        </a>
        <a href="login.php" class="<?php echo ($current_page == 'login.php') ? 'active' : ''; ?>">
            <span><i class="fa-regular fa-circle-user"></i></span> Login
        </a>
    </div>
</div>