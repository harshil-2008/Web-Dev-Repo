/* =========================================
   STUDENTHUB JAVASCRIPT
========================================= */

/* =========================================
   1. NOTIFICATION BANNER
========================================= */

const closeNotification = document.getElementById("closeNotification");
const notification = document.getElementById("notification");

if (closeNotification && notification) {
    closeNotification.addEventListener("click", function () {
        notification.style.display = "none";
    });
}


/* =========================================
   2. HAMBURGER MENU
========================================= */

const menuBtn = document.getElementById("menuBtn");
const navbar = document.querySelector("nav");

if (menuBtn && navbar) {
    menuBtn.addEventListener("click", function () {
        navbar.classList.toggle("active");
    });
}


/* =========================================
   3. LIGHT / DARK THEME
========================================= */

const themeBtn = document.getElementById("themeBtn");

if (themeBtn) {
    themeBtn.addEventListener("click", function () {
        document.body.classList.toggle("light-theme");

        if (document.body.classList.contains("light-theme")) {
            themeBtn.textContent = "🌙 Dark";
        } else {
            themeBtn.textContent = "☀️ Light";
        }
    });
}


/* =========================================
   4. MODAL POPUP
========================================= */

const modal = document.getElementById("modal");
const learnMoreBtn = document.getElementById("learnMoreBtn");
const closeModal = document.getElementById("closeModal");
const modalOk = document.getElementById("modalOk");

if (modal && learnMoreBtn) {
    learnMoreBtn.addEventListener("click", function () {
        modal.classList.add("active");
    });
}

if (modal && closeModal) {
    closeModal.addEventListener("click", function () {
        modal.classList.remove("active");
    });
}

if (modal && modalOk) {
    modalOk.addEventListener("click", function () {
        modal.classList.remove("active");
    });
}

if (modal) {
    modal.addEventListener("click", function (event) {
        if (event.target === modal) {
            modal.classList.remove("active");
        }
    });
}


/* =========================================
   5. CONTENT SLIDER
========================================= */

const slides = document.querySelectorAll(".slide");
const nextBtn = document.getElementById("nextBtn");
const prevBtn = document.getElementById("prevBtn");
let currentSlide = 0;

function showSlide(index) {
    if (slides.length === 0) {
        return;
    }

    slides.forEach(function (slide) {
        slide.classList.remove("active");
    });

    slides[index].classList.add("active");
}

if (nextBtn && slides.length > 0) {
    nextBtn.addEventListener("click", function () {
        currentSlide++;

        if (currentSlide >= slides.length) {
            currentSlide = 0;
        }

        showSlide(currentSlide);
    });
}

if (prevBtn && slides.length > 0) {
    prevBtn.addEventListener("click", function () {
        currentSlide--;

        if (currentSlide < 0) {
            currentSlide = slides.length - 1;
        }

        showSlide(currentSlide);
    });
}


/* =========================================
   6. COLLAPSIBLE FAQ
========================================= */

const questions = document.querySelectorAll(".faq-question");

questions.forEach(function (question) {
    question.addEventListener("click", function () {
        const answer = question.nextElementSibling;
        const symbol = question.querySelector("span");

        if (answer) {
            answer.classList.toggle("active");
        }

        if (symbol && answer) {
            if (answer.classList.contains("active")) {
                symbol.textContent = "−";
            } else {
                symbol.textContent = "+";
            }
        }
    });
});


/* =========================================
   7. EVENT REGISTER BUTTONS
========================================= */

const eventButtons = document.querySelectorAll(".event-register");

eventButtons.forEach(function (button) {
    button.addEventListener("click", function (event) {
        event.preventDefault();
        alert("Event registration selected. Thank you!");
    });
});


/* =========================================
   8. FORM VALIDATION (REGISTRATION FORM)
   Validates: Name, Email, Mobile, Password,
   Confirm Password, Course, Year, Gender, Terms.
   Uses Regular Expressions for pattern matching.
========================================= */
const registerForm = document.getElementById('registerForm');

if (registerForm) {
    // Helper function to display error for a specific field
    function showError(elementId, errorSpanId, message) {
        const errorSpan = document.getElementById(errorSpanId);
        const inputElement = document.getElementById(elementId);
        if (errorSpan) {
            errorSpan.textContent = message;
        }
        if (inputElement) {
            inputElement.style.borderColor = '#ff4444';
        }
    }

    // Helper function to clear error for a specific field
    function clearError(elementId, errorSpanId) {
        const errorSpan = document.getElementById(errorSpanId);
        const inputElement = document.getElementById(elementId);
        if (errorSpan) {
            errorSpan.textContent = '';
        }
        if (inputElement) {
            inputElement.style.borderColor = '';
        }
    }

    // Clear individual errors on input/change
    const fieldsToWatch = [
        { id: 'name', errorId: 'nameError' },
        { id: 'email', errorId: 'emailError' },
        { id: 'mobile', errorId: 'mobileError' },
        { id: 'course', errorId: 'courseError' },
        { id: 'year', errorId: 'yearError' },
        { id: 'password', errorId: 'passwordError' },
        { id: 'confirm_password', errorId: 'confirmPasswordError' }
    ];

    fieldsToWatch.forEach(field => {
        const input = document.getElementById(field.id);
        if (input) {
            input.addEventListener('input', () => clearError(field.id, field.errorId));
            input.addEventListener('change', () => clearError(field.id, field.errorId));
        }
    });

    const genderInputs = document.querySelectorAll('input[name="gender"]');
    genderInputs.forEach(radio => {
        radio.addEventListener('change', () => {
            const span = document.getElementById('genderError');
            if (span) span.textContent = '';
        });
    });

    const termsCheckbox = document.getElementById('terms');
    if (termsCheckbox) {
        termsCheckbox.addEventListener('change', () => {
            const span = document.getElementById('termsError');
            if (span) span.textContent = '';
        });
    }

    // Main Submit Validation
    registerForm.addEventListener('submit', function (event) {
        let isValid = true;
        const errorSummary = document.getElementById('errorSummary');
        const errorsList = [];

        // Clear all previous errors
        fieldsToWatch.forEach(field => clearError(field.id, field.errorId));
        const genderErrorSpan = document.getElementById('genderError');
        if (genderErrorSpan) genderErrorSpan.textContent = '';
        const termsErrorSpan = document.getElementById('termsError');
        if (termsErrorSpan) termsErrorSpan.textContent = '';

        // 1. Name Validation (Letters and spaces only, 3 to 50 characters)
        const nameInput = document.getElementById('name');
        const nameValue = nameInput ? nameInput.value.trim() : '';
        const nameRegex = /^[A-Za-z\s]{3,50}$/;
        if (nameValue === '') {
            showError('name', 'nameError', 'Full name is required.');
            errorsList.push('Full name is required.');
            isValid = false;
        } else if (!nameRegex.test(nameValue)) {
            showError('name', 'nameError', 'Name must contain only letters and be at least 3 characters.');
            errorsList.push('Name must contain only letters and spaces.');
            isValid = false;
        }

        // 2. Email Validation (Standard email pattern)
        const emailInput = document.getElementById('email');
        const emailValue = emailInput ? emailInput.value.trim() : '';
        const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        if (emailValue === '') {
            showError('email', 'emailError', 'Email address is required.');
            errorsList.push('Email address is required.');
            isValid = false;
        } else if (!emailRegex.test(emailValue)) {
            showError('email', 'emailError', 'Please enter a valid email address (e.g. user@domain.com).');
            errorsList.push('Please enter a valid email address.');
            isValid = false;
        }

        // 3. Mobile Number Validation (Exact 10 digits)
        const mobileInput = document.getElementById('mobile');
        const mobileValue = mobileInput ? mobileInput.value.trim() : '';
        const mobileRegex = /^[0-9]{10}$/;
        if (mobileValue === '') {
            showError('mobile', 'mobileError', 'Mobile number is required.');
            errorsList.push('Mobile number is required.');
            isValid = false;
        } else if (!mobileRegex.test(mobileValue)) {
            showError('mobile', 'mobileError', 'Mobile number must be exactly 10 digits.');
            errorsList.push('Mobile number must be exactly 10 digits.');
            isValid = false;
        }

        // 4. Course Validation (Must select an option)
        const courseInput = document.getElementById('course');
        const courseValue = courseInput ? courseInput.value.trim() : '';
        if (courseValue === '') {
            showError('course', 'courseError', 'Please select a course / department.');
            errorsList.push('Please select a course.');
            isValid = false;
        }

        // 5. Academic Year Validation (Digits 1 to 4)
        const yearInput = document.getElementById('year');
        const yearValue = yearInput ? yearInput.value.trim() : '';
        const yearRegex = /^[1-4]$/;
        if (yearValue === '') {
            showError('year', 'yearError', 'Academic year is required.');
            errorsList.push('Academic year is required.');
            isValid = false;
        } else if (!yearRegex.test(yearValue)) {
            showError('year', 'yearError', 'Year must be a number between 1 and 4.');
            errorsList.push('Year must be between 1 and 4.');
            isValid = false;
        }

        // 6. Gender Validation (Radio button checked)
        const selectedGender = document.querySelector('input[name="gender"]:checked');
        if (!selectedGender) {
            if (genderErrorSpan) genderErrorSpan.textContent = 'Please select your gender.';
            errorsList.push('Please select your gender.');
            isValid = false;
        }

        // 7. Password Validation (At least 8 chars, 1 letter, 1 number)
        const passwordInput = document.getElementById('password');
        const passwordValue = passwordInput ? passwordInput.value : '';
        const passwordRegex = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*#?&]{8,}$/;
        if (passwordValue === '') {
            showError('password', 'passwordError', 'Password is required.');
            errorsList.push('Password is required.');
            isValid = false;
        } else if (!passwordRegex.test(passwordValue)) {
            showError('password', 'passwordError', 'Password must be min 8 chars with at least 1 letter and 1 number.');
            errorsList.push('Password must be at least 8 characters with letters & numbers.');
            isValid = false;
        }

        // 8. Confirm Password Validation (Must match Password)
        const confirmPasswordInput = document.getElementById('confirm_password');
        const confirmPasswordValue = confirmPasswordInput ? confirmPasswordInput.value : '';
        if (confirmPasswordValue === '') {
            showError('confirm_password', 'confirmPasswordError', 'Please confirm your password.');
            errorsList.push('Confirm password is required.');
            isValid = false;
        } else if (passwordValue !== confirmPasswordValue) {
            showError('confirm_password', 'confirmPasswordError', 'Passwords do not match.');
            errorsList.push('Passwords do not match.');
            isValid = false;
        }

        // 9. Terms Acceptance Validation (Checkbox checked)
        if (termsCheckbox && !termsCheckbox.checked) {
            if (termsErrorSpan) termsErrorSpan.textContent = 'You must accept the terms and conditions.';
            errorsList.push('You must accept the terms & conditions.');
            isValid = false;
        }

        // If invalid, prevent submission and show summary
        if (!isValid) {
            event.preventDefault(); // Stop form submission to PHP
            if (errorSummary) {
                errorSummary.style.display = 'block';
                errorSummary.innerHTML = `<strong>⚠️ Please fix the following errors:</strong><ul>${errorsList.map(err => `<li>${err}</li>`).join('')}</ul>`;
                errorSummary.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        } else {
            // Validation passed! Form will naturally submit via POST to process_registration.php
            if (errorSummary) {
                errorSummary.style.display = 'none';
            }
        }
    });
}


/* =========================================
   9. FETCH API - STUDENT DIRECTORY
   Dynamic Rendering, Search, Course Filter,
   Sorting (A-Z / Z-A), and Pagination.
========================================= */
const studentList = document.getElementById('studentList');

if (studentList) {
    let allStudents = [];
    let currentPage = 1;
    const itemsPerPage = 3; // Show 3 cards per page for clear pagination
    let sortAsc = true;

    // Function to render student cards for current page
    function renderStudents(studentsToRender) {
        studentList.innerHTML = '';
        const pageInfo = document.getElementById('pageInfo');
        const prevPageBtn = document.getElementById('prevPageBtn');
        const nextPageBtn = document.getElementById('nextPageBtn');

        // Handle empty search/filter result
        if (studentsToRender.length === 0) {
            studentList.innerHTML = `
                <div style="width: 100%; padding: 30px; text-align: center; color: #ff9999;">
                    <p style="font-size: 18px;">🔍 No students found matching your search criteria.</p>
                </div>
            `;
            if (pageInfo) pageInfo.textContent = 'Page 0 of 0';
            if (prevPageBtn) prevPageBtn.disabled = true;
            if (nextPageBtn) nextPageBtn.disabled = true;
            return;
        }

        const totalPages = Math.ceil(studentsToRender.length / itemsPerPage);
        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        // Calculate slice indexes for pagination
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const currentSlice = studentsToRender.slice(startIndex, endIndex);

        // Render each student card
        currentSlice.forEach(student => {
            const card = document.createElement('div');
            card.className = 'card';
            card.innerHTML = `
                <h2>👤 ${student.name}</h2>
                <p><strong>Course:</strong> ${student.course}</p>
                <p><strong>Year:</strong> ${student.year}</p>
                <p><strong>Email:</strong> ${student.email}</p>
                <p><strong>Gender:</strong> ${student.gender || 'Not specified'}</p>
                <span class="badge">${student.course}</span>
            `;
            studentList.appendChild(card);
        });

        // Update pagination status
        if (pageInfo) {
            pageInfo.textContent = `Page ${currentPage} of ${totalPages} (${studentsToRender.length} Total)`;
        }
        if (prevPageBtn) prevPageBtn.disabled = (currentPage === 1);
        if (nextPageBtn) nextPageBtn.disabled = (currentPage === totalPages);
    }

    // Function to filter and sort students based on user controls
    function updateStudentView() {
        let filtered = [...allStudents];

        // 1. Search text filter
        const searchInput = document.getElementById('searchInput');
        if (searchInput && searchInput.value.trim() !== '') {
            const query = searchInput.value.toLowerCase().trim();
            filtered = filtered.filter(s =>
                s.name.toLowerCase().includes(query) ||
                s.email.toLowerCase().includes(query) ||
                s.course.toLowerCase().includes(query)
            );
        }

        // 2. Course dropdown filter
        const filterCourse = document.getElementById('filterCourse');
        if (filterCourse && filterCourse.value !== '') {
            const selectedCourse = filterCourse.value.toLowerCase();
            filtered = filtered.filter(s => s.course.toLowerCase().includes(selectedCourse));
        }

        // 3. Sort by Name (Ascending / Descending)
        filtered.sort((a, b) => {
            const nameA = a.name.toLowerCase();
            const nameB = b.name.toLowerCase();
            if (nameA < nameB) return sortAsc ? -1 : 1;
            if (nameA > nameB) return sortAsc ? 1 : -1;
            return 0;
        });

        renderStudents(filtered);
    }

    // Fetch student data from external JSON file using Fetch API
    fetch('data.json')
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to load student data. HTTP status: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            allStudents = data.students || [];
            updateStudentView();
        })
        .catch(error => {
            console.error('Fetch Error:', error);
            studentList.innerHTML = `<p style="color:red;">Error loading student directory: ${error.message}</p>`;
        });

    // Event Listeners for Directory Controls
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            currentPage = 1; // Reset to first page when search changes
            updateStudentView();
        });
    }

    const filterCourse = document.getElementById('filterCourse');
    if (filterCourse) {
        filterCourse.addEventListener('change', () => {
            currentPage = 1; // Reset to first page when filter changes
            updateStudentView();
        });
    }

    const sortBtn = document.getElementById('sortBtn');
    if (sortBtn) {
        sortBtn.addEventListener('click', () => {
            sortAsc = !sortAsc;
            sortBtn.textContent = sortAsc ? 'Sort: A to Z ↕' : 'Sort: Z to A ↕';
            updateStudentView();
        });
    }

    const prevPageBtn = document.getElementById('prevPageBtn');
    if (prevPageBtn) {
        prevPageBtn.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                updateStudentView();
            }
        });
    }

    const nextPageBtn = document.getElementById('nextPageBtn');
    if (nextPageBtn) {
        nextPageBtn.addEventListener('click', () => {
            currentPage++;
            updateStudentView();
        });
    }
}


/* =========================================
   10. FETCH API - EVENTS LIST
   Dynamic Rendering, Search, Category Filter,
   and Sorting for events.html.
========================================= */
const eventsContainer = document.getElementById('eventsContainer');

if (eventsContainer) {
    let allEvents = [];
    let eventSortAsc = true;

    function renderEvents(eventsToRender) {
        eventsContainer.innerHTML = '';

        if (eventsToRender.length === 0) {
            eventsContainer.innerHTML = `
                <div style="width: 100%; padding: 30px; text-align: center; color: #ff9999;">
                    <p style="font-size: 18px;">🔍 No events found matching your criteria.</p>
                </div>
            `;
            return;
        }

        eventsToRender.forEach(ev => {
            const card = document.createElement('div');
            card.className = 'card';
            card.innerHTML = `
                <h2>${ev.title}</h2>
                <p><strong>Category:</strong> <span class="status-badge">${ev.category || 'Event'}</span></p>
                <p><strong>Date:</strong> ${ev.date}</p>
                <p><strong>Venue:</strong> ${ev.venue}</p>
                <p>${ev.description}</p>
                <a href="register.html" class="btn event-register" style="margin-top: 10px;">Register</a>
            `;
            eventsContainer.appendChild(card);
        });
    }

    function updateEventsView() {
        let filtered = [...allEvents];

        const searchInput = document.getElementById('eventSearchInput');
        if (searchInput && searchInput.value.trim() !== '') {
            const query = searchInput.value.toLowerCase().trim();
            filtered = filtered.filter(e =>
                e.title.toLowerCase().includes(query) ||
                (e.venue && e.venue.toLowerCase().includes(query)) ||
                (e.description && e.description.toLowerCase().includes(query))
            );
        }

        const filterCategory = document.getElementById('eventFilterCategory');
        if (filterCategory && filterCategory.value !== '') {
            const cat = filterCategory.value.toLowerCase();
            filtered = filtered.filter(e => e.category && e.category.toLowerCase() === cat);
        }

        filtered.sort((a, b) => {
            const titleA = a.title.toLowerCase();
            const titleB = b.title.toLowerCase();
            if (titleA < titleB) return eventSortAsc ? -1 : 1;
            if (titleA > titleB) return eventSortAsc ? 1 : -1;
            return 0;
        });

        renderEvents(filtered);
    }

    // Fetch events from data.json or events.json
    fetch('events.json')
        .then(res => res.json())
        .then(data => {
            allEvents = data;
            updateEventsView();
        })
        .catch(() => {
            // Fallback to data.json if events.json not loaded
            fetch('data.json')
                .then(res => res.json())
                .then(data => {
                    allEvents = data.events || [];
                    updateEventsView();
                })
                .catch(err => {
                    eventsContainer.innerHTML = `<p style="color:red;">Error loading events: ${err.message}</p>`;
                });
        });

    const searchInput = document.getElementById('eventSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', updateEventsView);
    }

    const filterCategory = document.getElementById('eventFilterCategory');
    if (filterCategory) {
        filterCategory.addEventListener('change', updateEventsView);
    }

    const sortBtn = document.getElementById('eventSortBtn');
    if (sortBtn) {
        sortBtn.addEventListener('click', () => {
            eventSortAsc = !eventSortAsc;
            sortBtn.textContent = eventSortAsc ? 'Sort: A to Z ↕' : 'Sort: Z to A ↕';
            updateEventsView();
        });
    }
}

