// ========================================
// PEGAWAI INDEX JAVASCRIPT
// ========================================

// Initialize pegawai functionality
document.addEventListener('DOMContentLoaded', function() {
    // Pegawai JavaScript loaded

    // Initialize header functions
    initializeHeaderFunctions();
});

// Initialize header-specific functions
function initializeHeaderFunctions() {
    // Toggle sidebar function
    window.toggleSidebar = function() {
        const sidebar = document.querySelector('.sidebar');
        const mainContent = document.getElementById('main-content');

        if (sidebar && mainContent) {
            sidebar.classList.toggle('collapsed');

            if (sidebar.classList.contains('collapsed')) {
                mainContent.classList.remove('ml-64');
                mainContent.classList.add('ml-16');
            } else {
                mainContent.classList.remove('ml-16');
                mainContent.classList.add('ml-64');
            }

            // Sidebar toggled
        } else {
            // Sidebar or main content not found
        }
    };

    // Toggle role dropdown function
    window.toggleRoleDropdown = function() {
        const dropdown = document.getElementById('role-dropdown-menu');
        if (dropdown) {
            dropdown.classList.toggle('hidden');

            // Close other dropdowns
            const profileDropdown = document.getElementById('profile-dropdown-menu');
            if (profileDropdown) {
                profileDropdown.classList.add('hidden');
            }
        }
    };

    // Toggle profile dropdown function
    window.toggleProfileDropdown = function() {
        const dropdown = document.getElementById('profile-dropdown-menu');
        if (dropdown) {
            dropdown.classList.toggle('hidden');

            // Close other dropdowns
            const roleDropdown = document.getElementById('role-dropdown-menu');
            if (roleDropdown) {
                roleDropdown.classList.add('hidden');
            }
        }
    };

    // Open password modal function
    window.openPasswordModal = function() {
        const modal = document.getElementById('passwordModal');
        if (modal) {
            modal.classList.remove('hidden');

            // Close dropdowns
            const profileDropdown = document.getElementById('profile-dropdown-menu');
            if (profileDropdown) {
                profileDropdown.classList.add('hidden');
            }
        }
    };

    // Close password modal function
    window.closePasswordModal = function() {
        const modal = document.getElementById('passwordModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    };

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        const roleDropdown = document.getElementById('role-dropdown-menu');
        const profileDropdown = document.getElementById('profile-dropdown-menu');

        // Check if click is outside dropdowns
        if (roleDropdown && !roleDropdown.contains(e.target) && !e.target.closest('[onclick*="toggleRoleDropdown"]')) {
            roleDropdown.classList.add('hidden');
        }

        if (profileDropdown && !profileDropdown.contains(e.target) && !e.target.closest('[onclick*="toggleProfileDropdown"]')) {
            profileDropdown.classList.add('hidden');
        }
    });

    // Close password modal when clicking outside
    document.addEventListener('click', function(e) {
        const modal = document.getElementById('passwordModal');
        if (modal && !modal.contains(e.target) && !e.target.closest('[onclick*="openPasswordModal"]')) {
            modal.classList.add('hidden');
        }
    });

    // Close dropdowns when pressing Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const roleDropdown = document.getElementById('role-dropdown-menu');
            const profileDropdown = document.getElementById('profile-dropdown-menu');
            const passwordModal = document.getElementById('passwordModal');

            if (roleDropdown) {
                roleDropdown.classList.add('hidden');
            }
            if (profileDropdown) {
                profileDropdown.classList.add('hidden');
            }
            if (passwordModal) {
                passwordModal.classList.add('hidden');
            }
        }
    });

    // Header functions initialized
}
