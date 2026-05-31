<?php
// We expect $active_page to be set by the including file, e.g. $active_page = 'dashboard';
if (!isset($active_page)) {
    $active_page = '';
}
?>
    <!-- Mobile Toggle -->
    <div class="admin-mobile-nav d-lg-none bg-white p-3 border-bottom sticky-top d-flex justify-content-between align-items-center">
        <div class="fw-bold fw-bold" style="font-family: 'Outfit';">IAC <span class="text-primary">Covenant</span></div>
        <button class="btn btn-light border" onclick="toggleSidebar()"><i class="fa-solid fa-bars"></i></button>
    </div>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <div class="admin-sidebar" id="adminSidebar">
        <div class="admin-sidebar-logo d-flex align-items-center gap-2 mb-4">
            <img src="../assets/images/dnsclogo.png" alt="DNSC" style="height: 35px;">
            <img src="../assets/images/iclogo.png" alt="IC" style="height: 35px;">
            <div class="ms-1">
                <span style="color: #2e1065;">IAC</span> <span style="color: var(--accent-core);">Covenant</span>
            </div>
        </div>
        <ul class="nav nav-admin flex-column">
            <li class="nav-item"><a href="dashboard" class="nav-link <?php echo $active_page == 'dashboard' ? 'active' : ''; ?>"><i class="fa-solid fa-chart-pie"></i> Dashboard</a></li>
            <li class="nav-item"><a href="analytics" class="nav-link <?php echo $active_page == 'analytics' ? 'active' : ''; ?>"><i class="fa-solid fa-chart-line"></i> Analytics</a></li>
            <li class="nav-item"><a href="events" class="nav-link <?php echo $active_page == 'events' ? 'active' : ''; ?>"><i class="fa-solid fa-calendar-alt"></i> Events</a></li>
            <li class="nav-item"><a href="attendance" class="nav-link <?php echo $active_page == 'attendance' ? 'active' : ''; ?>"><i class="fa-solid fa-user-check"></i> Attendance</a></li>
            <li class="nav-item"><a href="submissions" class="nav-link <?php echo $active_page == 'submissions' ? 'active' : ''; ?>"><i class="fa-solid fa-file-signature"></i> Submissions</a></li>
            <li class="nav-item"><a href="activity_logs" class="nav-link <?php echo $active_page == 'activity_logs' ? 'active' : ''; ?>"><i class="fa-solid fa-list-ul"></i> Activity Logs</a></li>
            <li class="nav-item"><a href="edit_profile" class="nav-link <?php echo $active_page == 'edit_profile' ? 'active' : ''; ?>"><i class="fa-solid fa-user-cog"></i> Profile</a></li>
            <li class="nav-item"><a href="../covenant" class="nav-link" target="_blank"><i class="fa-solid fa-file-contract"></i> Signing Form</a></li>
            <li class="nav-item"><a href="../signature-wall" class="nav-link" target="_blank"><i class="fa-solid fa-external-link"></i> Live Wall</a></li>
        </ul>
        <div style="margin-top: auto; padding-top: 2rem; border-top: 1px solid #f1f5f9;">
            <a href="../logout" class="nav-link text-danger fw-bold"><i class="fa-solid fa-power-off"></i> Logout</a>
        </div>
    </div>
