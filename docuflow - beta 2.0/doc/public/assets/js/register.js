document.addEventListener("DOMContentLoaded", function () {
  App.returnYear();
  const form = document.getElementById("registerForm");
  const submitBtn = form.querySelector('button[type="submit"]');

  if (form) {
    form.addEventListener("submit", async function (e) {
      e.preventDefault();
      let valid = true;
      
      // Clear previous errors
      App.clearAllFieldErrors();
      
      // Show loading state
      const originalText = submitBtn.textContent;
      submitBtn.disabled = true;
      submitBtn.textContent = 'Creating...';
      
      this.querySelectorAll("input, select").forEach((input) => {
        if (input.value.trim() === "") {
          valid = false;
          const fieldName = input.name.charAt(0).toUpperCase() + input.name.slice(1);
          App.showFieldError(input, `${fieldName} field is required`);
          return;
        } else {
          passRgx =
            /^(?!.*[\s`|/\\'"]{1})(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,35}$/g;
          emRgx =
            /^(?!.*[._-]{2})[a-zA-Z](([a-zA-Z0-9._-]+)?[a-zA-Z0-9]+)?@(?=[a-zA-Z]+)([\w-._]+)?[a-zA-Z]+\.[a-zA-Z]+$/g;
         
          if (input.name === "name" && !App.validateFormInput(input, "name")) {
            valid = false;
          }
          if (
            input.name === "email" &&
            !App.validateFormInput(input, "email", emRgx)
          ) {
            valid = false;
          }
          if (
            input.name === "password" &&
            !App.validateFormInput(input, "password", passRgx)
          ) {
            valid = false;
          }
          if (input.name === "confirmPassword" ) {
             const password = document.getElementById('password');
             if(input.value === password.value) {
                App.clearFieldError(input);
                return
             }
             valid = false;
             App.showFieldError(input, 'Passwords do not match');
          }
        }
      });

      if (valid) {
        try {
          const formData = new FormData(form);
          const response = await fetch('/doc/api/register.php', {
            method: 'POST',
            body: formData
          });
          
          const result = await response.json();
          
          if (result.status === 'success') {
            notifications.success('User created successfully!');
            form.reset();
            // Redirect after 2 seconds
            setTimeout(() => {
              window.location.href = '/doc/public/pages/dashboard/dashboard.php';
            }, 2000);
          } else {
            notifications.error(result.message || 'Failed to create user');
          }
        } catch (error) {
          notifications.error('Network error. Please try again.');
          console.error('Registration error:', error);
        }
      }
      
      // Restore button state
      submitBtn.disabled = false;
      submitBtn.textContent = originalText;
    });
  }
});
