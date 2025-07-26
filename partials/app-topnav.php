<div class="dashboard_topNav">
    <div class="topnav_left">
        <button class="sidebarToggle" id="sidebarToggle">
            <i class="fa fa-bars"></i>
        </button>
    </div>
    
    <div class="topnav_right">
        <div class="user_dropdown">
            <button class="user_dropdown_btn">
                <i class="fa fa-user"></i>
                <?= htmlspecialchars($user['first_name']) ?>
                <i class="fa fa-chevron-down"></i>
            </button>
            <div class="user_dropdown_content">
                <a href="logout.php">
                    <i class="fa fa-sign-out"></i>
                    Logout
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function(){
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.dashboard_sidebar');
        const mainContainer = document.getElementById('dashboardMainContainer');
        
        sidebarToggle.addEventListener('click', function(){
            sidebar.classList.toggle('collapsed');
            mainContainer.classList.toggle('sidebar-collapsed');
        });
    });
</script>
