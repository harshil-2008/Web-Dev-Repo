# Practical 5: Registration Form with Frontend Validation and User-Friendly Error Handling

**Name:** Harshil Prajapati | **ID:** 25DCE093 | **Program:** B.Tech (CE) Sem III

| Parameter | Details |
| :--- | :--- |
| **CO Mapping** | CO1, CO3 |
| **Bloom's Taxonomy** | L3 – Apply / L4 – Analyze |
| **Lab Duration** | 4 Hours |
| **Implementation Hours** | 4 Hours |
| **Testing / Evaluation Hours** | 2 Hours |

### Practical Aim
To construct an accessible student registration form featuring comprehensive client-side Regular Expression validation, inline error spans, and an interactive password strength meter.

### Problem Definition
Create a student registration form with HTML5 input types and JavaScript validation for name, email, mobile number, password, confirm password, course, year, gender, and terms acceptance. Use Regular Expression for validations.

### Key Questions / Analysis / Interpretation

#### Question 1: Are correct input types and attributes used?

Yes. The form utilizes specific HTML5 input types and constraint attributes:
• Full Name: <input type='text' required>
• Email: <input type='email' required>
• Mobile: <input type='tel' maxlength='10' required>
• Course: <select required> with predefined options
• Academic Year: <input type='number' min='1' max='4' required>
• Gender: Radio buttons grouped with name='gender'
• Password & Confirm Password: <input type='password' required>
• Terms Acceptance: <input type='checkbox' required>

#### Question 2: Are validation errors displayed near the relevant fields?

Yes. Every field possesses a dedicated <span class='error-msg' id='...Error'> immediately beneath the input box that populates with a specific error message upon validation failure. In addition, an #errorSummary box appears at the top summarizing all outstanding issues.

#### Question 3: Is password strength checked?

Yes. An input event listener evaluates password length, uppercase letters, digits, and special characters. The meter dynamically animates from Weak (red, 25%) to Medium (yellow, 50%) to Good (blue, 75%) to Strong (green, 100%).

#### Question 4: Is the form accessible using keyboard and screen-reader-friendly labels?

Yes. Every label uses the for attribute mapped to input IDs, aria-describedby links inputs to error messages, and tab navigation follows natural visual flow.

### Key Skills, Applications & Learning Outcomes
- **Key Skills:** HTML forms, JavaScript regex validation, regular expressions, password strength meter, UX error messaging.
- **Applications:** User onboarding workflows, data integrity enforcement, registration portals.
- **Learning Outcomes:** Students will design accessible forms with robust client-side validation and responsive UX feedback.
- **Tools / Technology Used:** HTML5, JavaScript ES6+, browser dev tools.

### Implementation / Source Code
```text
// Client-Side Regular Expression Validation from script.js
const nameRegex = /^[A-Za-z\s]{3,50}$/;
const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
const mobileRegex = /^[0-9]{10}$/;
const passwordRegex = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*#?&]{8,}$/;

registerForm.addEventListener('submit', function (event) {
    let isValid = true;
    if (!nameRegex.test(nameInput.value.trim())) {
        showError('name', 'nameError', 'Name must contain only letters (min 3 chars).');
        isValid = false;
    }
    if (!emailRegex.test(emailInput.value.trim())) {
        showError('email', 'emailError', 'Enter a valid institutional email.');
        isValid = false;
    }
    if (!mobileRegex.test(mobileInput.value.trim())) {
        showError('mobile', 'mobileError', 'Mobile number must be exactly 10 digits.');
        isValid = false;
    }
    if (passwordInput.value !== confirmPasswordInput.value) {
        showError('confirm_password', 'confirmPasswordError', 'Passwords do not match.');
        isValid = false;
    }
    if (!isValid) event.preventDefault();
});
```

### Output & Verification Screenshots
![Figure 5: practical_05_form_validation_errors.png](../screenshots/practical_05_form_validation_errors.png)

![Figure 5: practical_05_password_strength.png](../screenshots/practical_05_password_strength.png)

### Post Laboratory Work / Viva Questions
- **Viva Q1:** Explain the regex: /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*#?&]{8,}$/
  - **Answer:** This regular expression uses positive lookaheads: '(?=.*[A-Za-z])' ensures at least one letter exists, '(?=.*\d)' ensures at least one digit exists, and '{8,}' requires the total string length to be at least 8 characters containing allowed characters.
- **Viva Q2:** Why is client-side validation not sufficient on its own?
  - **Answer:** Client-side validation can be bypassed by disabling JavaScript or submitting directly via tools like Postman; therefore, server-side validation is required to ensure data integrity and security.

### Conclusion
*With this practical we can conclude that client-side form validation with Regular Expressions provides instantaneous feedback to users, reduces invalid server round-trips, and improves data entry accuracy.*

---

