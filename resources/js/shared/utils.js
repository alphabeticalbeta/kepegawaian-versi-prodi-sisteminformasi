// ========================================
// SHARED UTILITIES JAVASCRIPT
// ========================================

class SharedUtils {
    constructor() {
        this.init();
    }

    init() {
        this.setupGlobalFunctions();
        this.initializeCommonFeatures();
    }

    // Setup global utility functions
    setupGlobalFunctions() {
        // CSRF Token Setup for AJAX requests
        this.setupCsrfToken();

        // Common utility functions
        this.setupCommonFunctions();
    }

    // CSRF Token Setup
    setupCsrfToken() {
        const token = document.querySelector('meta[name="csrf-token"]');
        if (token) {
            window.csrfToken = token.getAttribute('content');

            // Setup for fetch API
            window.fetchWithCsrf = function(url, options = {}) {
                options.headers = options.headers || {};
                options.headers['X-CSRF-TOKEN'] = window.csrfToken;
                options.headers['X-Requested-With'] = 'XMLHttpRequest';
                return fetch(url, options);
            };
        }
    }

    // Common utility functions
    setupCommonFunctions() {
        // Format currency
        window.formatCurrency = function(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            }).format(amount);
        };

        // Format date
        window.formatDate = function(date) {
            return new Intl.DateTimeFormat('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            }).format(new Date(date));
        };

        // Format datetime
        window.formatDateTime = function(date) {
            return new Intl.DateTimeFormat('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            }).format(new Date(date));
        };

        // Show loading spinner
        window.showLoading = function() {
            const spinner = document.createElement('div');
            spinner.id = 'loading-spinner';
            spinner.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
            spinner.innerHTML = `
                <div class="bg-white p-4 rounded-lg flex items-center space-x-3">
                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                    <span class="text-gray-700">Loading...</span>
                </div>
            `;
            document.body.appendChild(spinner);
        };

        // Hide loading spinner
        window.hideLoading = function() {
            const spinner = document.getElementById('loading-spinner');
            if (spinner) {
                spinner.remove();
            }
        };

        // Show success message
        window.showSuccess = function(message) {
            this.showAlert(message, 'success');
        };

        // Show error message
        window.showError = function(message) {
            this.showAlert(message, 'error');
        };

        // Show warning message
        window.showWarning = function(message) {
            this.showAlert(message, 'warning');
        };

        // Show info message
        window.showInfo = function(message) {
            this.showAlert(message, 'info');
        };

        // Generic alert function
        window.showAlert = function(message, type = 'info') {
            const alertDiv = document.createElement('div');
            alertDiv.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 max-w-sm ${this.getAlertClasses(type)}`;
            alertDiv.innerHTML = `
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        ${this.getAlertIcon(type)}
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium">${message}</p>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="flex-shrink-0">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            `;
            document.body.appendChild(alertDiv);

            // Auto remove after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentElement) {
                    alertDiv.remove();
                }
            }, 5000);
        };

        // Get alert classes based on type
        window.getAlertClasses = function(type) {
            const classes = {
                success: 'bg-green-50 border border-green-200 text-green-800',
                error: 'bg-red-50 border border-red-200 text-red-800',
                warning: 'bg-yellow-50 border border-yellow-200 text-yellow-800',
                info: 'bg-blue-50 border border-blue-200 text-blue-800'
            };
            return classes[type] || classes.info;
        };

        // Get alert icon based on type
        window.getAlertIcon = function(type) {
            const icons = {
                success: '<svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>',
                error: '<svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>',
                warning: '<svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>',
                info: '<svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>'
            };
            return icons[type] || icons.info;
        };

        // Confirm dialog
        window.confirmDialog = function(message, callback) {
            if (confirm(message)) {
                if (typeof callback === 'function') {
                    callback();
                }
            }
        };

        // Debounce function
        window.debounce = function(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        };

        // Throttle function
        window.throttle = function(func, limit) {
            let inThrottle;
            return function() {
                const args = arguments;
                const context = this;
                if (!inThrottle) {
                    func.apply(context, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            };
        };
    }

    // Initialize common features
    initializeCommonFeatures() {
        // Initialize Lucide icons
        this.initializeIcons();

        // Initialize common event listeners
        this.initializeCommonEventListeners();
    }

    // Initialize Lucide icons
    initializeIcons() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    // Initialize common event listeners
    initializeCommonEventListeners() {
        // Auto-hide alerts
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                if (alert.classList.contains('alert-success') || alert.classList.contains('alert-error')) {
                    alert.style.transition = 'opacity 0.5s ease-out';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }
            });
        }, 5000);

        // Handle form submissions
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (form.classList.contains('loading-form')) {
                e.preventDefault();
                window.showLoading();
                setTimeout(() => {
                    form.submit();
                }, 100);
            }
        });

        // Initialize header functions
        this.initializeHeaderFunctions();
    }

    // Initialize header-specific functions
    initializeHeaderFunctions() {
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

                console.log('Sidebar toggled:', sidebar.classList.contains('collapsed') ? 'collapsed' : 'expanded');
            } else {
                console.error('Sidebar or main content not found');
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
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new SharedUtils();
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = SharedUtils;
}
