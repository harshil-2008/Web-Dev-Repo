/* ============================================================
   StudentHub Portal — Shared JavaScript
   Navigation, breadcrumbs, mock auth, scroll animations
   ============================================================ */

document.addEventListener('DOMContentLoaded', () => {

    // ── Active Nav Link ──
    const currentPage = window.location.pathname.split('/').pop() || 'index.html';
    document.querySelectorAll('.nav-inner a').forEach(link => {
        const href = link.getAttribute('href');
        if (href === currentPage) {
            link.classList.add('active');
        }
    });

    // ── Mobile Menu Toggle ──
    const menuToggle = document.querySelector('.menu-toggle');
    const navInner = document.querySelector('.nav-inner');
    if (menuToggle && navInner) {
        menuToggle.addEventListener('click', () => {
            navInner.classList.toggle('open');
            menuToggle.textContent = navInner.classList.contains('open') ? '✕' : '☰';
        });
    }

    // ── Scroll Fade-In Animation ──
    const fadeElements = document.querySelectorAll('.fade-in');
    if (fadeElements.length > 0) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        fadeElements.forEach(el => observer.observe(el));
    }

    // ── Mock Login System ──
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();
            const alertBox = document.getElementById('login-alert');

            if (username && password) {
                // Mock authentication — accept any non-empty credentials
                localStorage.setItem('studenthub_user', JSON.stringify({
                    name: username,
                    loggedIn: true,
                    loginTime: new Date().toISOString()
                }));

                if (alertBox) {
                    alertBox.className = 'alert success';
                    alertBox.textContent = 'Login successful! Redirecting to dashboard…';
                }

                setTimeout(() => {
                    window.location.href = 'dashboard.html';
                }, 1000);
            } else {
                if (alertBox) {
                    alertBox.className = 'alert error';
                    alertBox.textContent = 'Please enter both username and password.';
                }
            }
        });
    }

    // ── Logout ──
    const logoutLinks = document.querySelectorAll('.logout-link');
    logoutLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            localStorage.removeItem('studenthub_user');
            window.location.href = 'index.html';
        });
    });

    // ── Dashboard: Show student name ──
    const welcomeName = document.getElementById('welcome-name');
    if (welcomeName) {
        const user = JSON.parse(localStorage.getItem('studenthub_user') || '{}');
        if (user.name) {
            welcomeName.textContent = user.name;
        }
    }

    // ── Protected pages: redirect if not logged in ──
    const protectedPages = ['dashboard.html', 'attendance.html', 'assignments.html', 'materials.html', 'grades.html', 'profile.html'];
    if (protectedPages.includes(currentPage)) {
        const user = JSON.parse(localStorage.getItem('studenthub_user') || '{}');
        if (!user.loggedIn) {
            window.location.href = 'login.html';
        }
    }

    // ── Contact Form ──
    const contactForm = document.getElementById('contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const alertBox = document.getElementById('contact-alert');
            if (alertBox) {
                alertBox.className = 'alert success';
                alertBox.textContent = 'Thank you! Your message has been sent successfully.';
            }
            contactForm.reset();
        });
    }

    // ── Animate progress bars ──
    const progressFills = document.querySelectorAll('.progress-bar .fill');
    if (progressFills.length > 0) {
        const progressObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const target = entry.target;
                    const width = target.getAttribute('data-width');
                    if (width) {
                        target.style.width = width;
                    }
                    progressObserver.unobserve(target);
                }
            });
        }, { threshold: 0.3 });

        progressFills.forEach(fill => {
            fill.style.width = '0%';
            progressObserver.observe(fill);
        });
    }

    // ── Animate bar chart ──
    const barChartBars = document.querySelectorAll('.bar-chart .bar');
    if (barChartBars.length > 0) {
        const barObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const target = entry.target;
                    const height = target.getAttribute('data-height');
                    if (height) {
                        target.style.height = height;
                    }
                    barObserver.unobserve(target);
                }
            });
        }, { threshold: 0.2 });

        barChartBars.forEach(bar => {
            bar.style.height = '0%';
            barObserver.observe(bar);
        });
    }

});
