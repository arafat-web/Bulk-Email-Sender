
// Modern Sidebar Toggle
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('show');

    // Add backdrop for mobile
    if (sidebar.classList.contains('show')) {
        const backdrop = document.createElement('div');
        backdrop.className = 'sidebar-backdrop';
        backdrop.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0,0,0,0.5);
                    z-index: 1040;
                    backdrop-filter: blur(4px);
                `;
        backdrop.onclick = () => {
            sidebar.classList.remove('show');
            backdrop.remove();
        };
        document.body.appendChild(backdrop);
    }
}

// Enhanced DataTables initialization
$(document).ready(function () {
    // Modern loading animation
    if ($('.data-table').length) {
        $('.data-table').DataTable({
            responsive: true,
            pageLength: 10,
            processing: true,
            language: {
                search: '<i class="bi bi-search me-2"></i>Search:',
                lengthMenu: 'Show _MENU_ entries',
                info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                infoEmpty: 'No entries available',
                infoFiltered: '(filtered from _MAX_ total entries)',
                paginate: {
                    first: '<i class="bi bi-chevron-double-left"></i>',
                    last: '<i class="bi bi-chevron-double-right"></i>',
                    next: '<i class="bi bi-chevron-right"></i>',
                    previous: '<i class="bi bi-chevron-left"></i>'
                },
                processing: '<div class="d-flex align-items-center"><div class="loading-spinner me-3"></div>Loading...</div>'
            },
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                '<"row"<"col-sm-12"tr>>' +
                '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        });
    }

    // Smooth scroll for anchor links
    $('a[href^="#"]').on('click', function (event) {
        var target = $(this.getAttribute('href'));
        if (target.length) {
            event.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 100
            }, 1000);
        }
    });

    // Auto-hide alerts after 5 seconds
    $('.alert:not(.alert-permanent)').delay(5000).fadeOut(300);

    // Add loading states to buttons
    $('form').on('submit', function () {
        const submitBtn = $(this).find('button[type="submit"]');
        if (submitBtn.length) {
            submitBtn.prop('disabled', true);
            const originalText = submitBtn.html();
            submitBtn.html('<div class="loading-spinner me-2" style="width: 16px; height: 16px;"></div>Processing...');

            setTimeout(() => {
                submitBtn.prop('disabled', false);
                submitBtn.html(originalText);
            }, 10000);
        }
    });
});

// Close sidebar when clicking outside on mobile
$(document).click(function (event) {
    if ($(window).width() < 992) {
        if (!$(event.target).closest('.sidebar, .btn, .sidebar-backdrop').length) {
            $('#sidebar').removeClass('show');
            $('.sidebar-backdrop').remove();
        }
    }
});

// Window resize handler
$(window).resize(function () {
    if ($(window).width() >= 992) {
        $('#sidebar').removeClass('show');
        $('.sidebar-backdrop').remove();
    }
});

// Page transition effect
window.addEventListener('beforeunload', function () {
    document.body.style.opacity = '0.5';
});

// Theme toggle (if needed in future)
function toggleTheme() {
    const html = document.documentElement;
    const currentTheme = html.getAttribute('data-bs-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-bs-theme', newTheme);
    localStorage.setItem('theme', newTheme);
}

// Initialize theme from localStorage
document.addEventListener('DOMContentLoaded', function () {
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-bs-theme', savedTheme);
});

