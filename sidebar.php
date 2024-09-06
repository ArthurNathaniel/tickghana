<div class="sidebar_all">
    <div class="logo"></div>
    <button id="toggleButton"><i class="fa-solid fa-bars-staggered"></i></button>
<div id="sidebarLink" class="sidebar_link" style="display: none;">
<a href="dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard</a>
    <a href="add_events.php"><i class="fa-solid fa-calendar-plus"></i> Add Event</a>
    <a href="view_events.php"><i class="fa-solid fa-calendar-check"></i> Manage Event</a>
    <a href="onboarding.php"><i class="fa-solid fa-clipboard-list"></i> Onboarding</a>
    <a href="view_purchases.php"><i class="fa-solid fa-ticket"></i> Manage Ticket</a>
    <a href=""><i class="fa-solid fa-pen"></i> Add Blog</a>
    <a href=""><i class="fa-solid fa-file-alt"></i> Manage Blog</a>
    <a href="update_profile.php"><i class="fa-solid fa-user"></i> Profile </a>
    <a href="change_password.php"><i class="fa-solid fa-cog"></i> Change Password</a>
    <a href="logout.php"><i class="fa-solid fa-sign-out-alt"></i> Logout</a>
</div>
</div>


<script>
    document.getElementById('toggleButton').addEventListener('click', function() {
        var sidebar = document.getElementById('sidebarLink');
        if (sidebar.style.display === 'none') {
            sidebar.style.display = 'flex';
        } else {
            sidebar.style.display = 'none';
        }
    });
</script>