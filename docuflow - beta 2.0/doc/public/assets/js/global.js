window.App = {
  returnYear: function () {
    document.querySelector('span[class="copy"]').textContent = (new Date().getFullYear() + " DocuFlow. All rights reserved.");
  },

  clearAllFieldErrors: function() {
    document.querySelectorAll('.field-error').forEach(error => error.remove());
    document.querySelectorAll('.error').forEach(field => field.classList.remove('error'));
  },

  showFieldError: function (field, message) {
    this.clearFieldError(field);

    field.classList.add("error");
    const errorDiv = document.createElement("div");
    errorDiv.className = "field-error";
    errorDiv.textContent = message;
    errorDiv.style.color = "#dc2626";
    errorDiv.style.fontSize = "0.875rem";
    errorDiv.style.marginTop = "0.25rem";
    field.after(errorDiv);
  },

  clearFieldError: function (field) {
    if (field.classList.remove("error")) {
      field.classList.remove("error");
    }
    const errorDiv = field.parentNode.querySelector(".field-error");
    if (errorDiv) {
      errorDiv.remove();
    }
  },
  validateFormInput: function(field, type, rgx = null) {
  if (!field || !type) {
    console.log("place full all argument");
    return;
  }

  if (type === "name") {
    // validate Name
    if (field && field.value) {
      if (!/\p{L}{3,}/u.test(field.value)) {
        this.showFieldError(
          field,
          `${field.name} must be at least 3 letters long`,
        );
      } else if (/^\s/.test(field.value)) {
        this.showFieldError(field, `${field.name} cannot start with a space`);
      } else if (/\s{2,}/.test(field.value)) {
        this.showFieldError(
          field,
          `Only one space allowed between ${field.name}s`,
        );
      } else if (
        !/^(?!\s)(?!.*\s{2,})[\p{L}]{3,}(?:\s[\p{L}]{3,})*$/u.test(field.value)
      ) {
        this.showFieldError(
          field,
          "Please enter letters only (3+ letters per name)",
        );
      } else {
      this.clearFieldError(field);
       return true;
    }
    } 

  } else if (type === "email") {
    // validate email
    if (field.value === "" || !rgx.test(field.value)) {
      this.showFieldError(field, `${field.name} is not valid`);
      return true;
    } else {
      this.clearFieldError(field);
      return true;
    }
  } else if (type === "password") {
    // validate password
    if (field.value === "" || !rgx.test(field.value)) {
      this.showFieldError(
        field,
        `${field.name} contein A-Z Capitel a-z Small 0-9 and Spiacel Character between 6 and 35`,
      );
      return false;
    } else {
      this.clearFieldError(field);
      return true;
    }
  } else {
    console.log("place full all argument");
    return false;
  }
}
};
