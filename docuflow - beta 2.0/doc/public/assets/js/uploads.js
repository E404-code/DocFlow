document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("customerForm");
  const submitBtn = form.querySelector(".submit-btn");

  form.addEventListener("submit", async function (e) {
    e.preventDefault();

    if (!validateForm()) {
      return;
    }

    // Show loading state
    submitBtn.disabled = true;
    submitBtn.textContent = "Creating...";

    if (submitBtn.dataset.i18n === "update") {
      update("/doc/api/uploads_document.php", form, submitBtn);
      submitBtn.textContent = "Updateting...";
      return false;
    }

    try {
      const formData = new FormData(form);

      const response = await fetch("/doc/api/uploads_document.php", {
        method: "POST",
        body: formData,
      });

      const result = await response.json();

      if (result.status === "success") {
        showNotification("Document uploaded successfully!", "success");
        form.reset();
        setTimeout(() => {
          window.location.reload();
          showNotification("you can upload another document now", "info");
        }, 4000);
      } else {
        showNotification(
          result.message || "Failed to create document",
          "error",
        );
      }
    } catch (error) {
      console.error("Error:", error);
      showNotification("An error occurred while creating document", "error");
    } finally {
      // Reset button state
      submitBtn.disabled = false;
      submitBtn.textContent = translations[currentLang]?.submit || "Submit";
    }
  });

  // File upload preview
  const passportInput = document.getElementById("passportImage");
  const nnInput = document.getElementById("nnImage");

  if (passportInput) {
    passportInput.addEventListener("change", function (e) {
      previewImage(e.target, "passport-preview");
    });
  }

  if (nnInput) {
    nnInput.addEventListener("change", function (e) {
      previewImage(e.target, "nn-preview");
    });
  }
});

function validateForm() {
  const form = document.getElementById("customerForm");
  const requiredFields = form.querySelectorAll("[Required]");
  let isValid = true;

  requiredFields.forEach((field) => {
    if (!field.value.trim()) {
      showFieldError(field, "This field is required");
      isValid = false;
    } else {
      clearFieldError(field);
    }
  });
  // validate customer Name
  const customerNameField = document.getElementById("customerName");
  if (customerNameField && customerNameField.value) {
    if (!/\p{L}{3,}/u.test(customerNameField.value)) {
      showFieldError(
        customerNameField,
        " Frist Name must be at least 3 letters long",
      );
      isValid = false;
    } else if (/^\s/.test(customerNameField.value)) {
      showFieldError(customerNameField, "Name cannot start with a space");
      isValid = false;
    } else if (/\s{2,}/.test(customerNameField.value)) {
      showFieldError(customerNameField, "Only one space allowed between names");
      isValid = false;
    } else if (
      !/^(?!\s)(?!.*\s{2,})[\p{L}]{3,}(?:\s[\p{L}]{3,})*$/u.test(
        customerNameField.value,
      )
    ) {
      showFieldError(
        customerNameField,
        "Please enter letters only (3+ letters per name)",
      );
      isValid = false;
    }
  }
  // validate national Number
  const nationalNumber = document.getElementById("nationalNumber");
  if (
    nationalNumber &&
    nationalNumber.value &&
    !/^\d{12}$/.test(nationalNumber.value)
  ) {
    showFieldError(nationalNumber, "National Number must be at 12 dgit long");
    isValid = false;
  }
  // Validate phone number
  const phoneField = document.getElementById("phone");
  if (phoneField && phoneField.value && !/^09\d{8}$/.test(phoneField.value)) {
    showFieldError(phoneField, "Please enter a valid phone number");
    isValid = false;
  }

  // Validate file sizes
  const passportFile = document.getElementById("passportImage")?.files[0];
  const nnFile = document.getElementById("nnImage")?.files[0];

  if (passportFile && passportFile.size > 5 * 1024 * 1024) {
    showFieldError(
      document.getElementById("passportImage"),
      "File size must be less than 5MB",
    );
    isValid = false;
  }

  if (nnFile && nnFile.size > 5 * 1024 * 1024) {
    showFieldError(
      document.getElementById("nnImage"),
      "File size must be less than 5MB",
    );
    isValid = false;
  }

  return isValid;
}

function showFieldError(field, message) {
  clearFieldError(field);

  field.classList.add("error");
  const errorDiv = document.createElement("div");
  errorDiv.className = "field-error";
  errorDiv.textContent = message;
  errorDiv.style.color = "#dc2626";
  errorDiv.style.fontSize = "0.875rem";
  errorDiv.style.marginTop = "0.25rem";
  field.after(errorDiv);
}

function clearFieldError(field) {
  field.classList.remove("error");
  const errorDiv = field.parentNode.querySelector(".field-error");
  if (errorDiv) {
    errorDiv.remove();
  }
}

function previewImage(input, previewId) {
  const file = input.files[0];

  if (file) {
    const reader = new FileReader();
    reader.onload = function (e) {
      // Remove existing preview
      const existingPreview = document.getElementById(previewId);
      if (existingPreview) {
        existingPreview.remove();
      }

      // Create new preview
      const img = document.createElement("img");
      img.id = previewId;
      img.className = "image-preview";
      img.style.cssText = `
                max-width: 200px;
                max-height: 200px;
                margin-top: 10px;
                border-radius: 8px;
                border: 1px solid #e5e7eb;
            `;
      img.src = e.target.result;
      input.parentNode.appendChild(img);
    };
    reader.readAsDataURL(file);
  }
}

function showNotification(message, type = "info") {
  // Remove existing notifications
  const existingNotification = document.querySelector(".notification");
  if (existingNotification) {
    existingNotification.remove();
  }

  // Create notification element
  const notification = document.createElement("div");
  notification.className = `notification notification-${type}`;
  notification.textContent = message;
  notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        z-index: 1000;
        transform: translateX(100%);
        transition: transform 0.3s ease;
    `;

  // Set background color based on type
  if (type === "success") {
    notification.style.backgroundColor = "#16a34a";
  } else if (type === "error") {
    notification.style.backgroundColor = "#dc2626";
  } else {
    notification.style.backgroundColor = "#2563eb";
  }

  document.body.appendChild(notification);

  // Animate in
  setTimeout(() => {
    notification.style.transform = "translateX(0)";
  }, 100);

  // Remove after 3 seconds
  setTimeout(() => {
    notification.style.transform = "translateX(100%)";
    setTimeout(() => {
      notification.remove();
    }, 300);
  }, 3000);
}

let downBtn = document?.querySelectorAll(".download-btn");

if (downBtn) {
  downBtn.forEach((btn) => {
    btn.addEventListener("click", function (e) {
      e.preventDefault();
      const url = this.parentNode.querySelector("img").src;
      fetch(url)
        .then((response) => response.blob())
        .then((blob) => {
          const url = window.URL.createObjectURL(blob);
          const a = document.createElement("a");
          a.href = url;
          a.download = url.split("/").pop();
          a.click();
          window.URL.revokeObjectURL(url);
        });
    });
  });
}

async function update(url, form, submitBtn) {
  const formData = new FormData(form);
  try {
    const response = await fetch(url, {
      method: "POST",
      body: formData,
    });
    const result = await response.json();
    if (result.status === "success") {
      showNotification(result.message, "success");
      setTimeout(() => {
        location.reload();
      }, 1000);
    }
  } catch (e) {
    showNotification(e.message, "error");
  } finally {
    submitBtn.disabled = false;
    submitBtn.textContent = translations[currentLang]?.update || "update";
  }
}

/**
 * Toggle between Edit and View modes
 * - Switches between edit and view modes without page reload
 * - Updates URL parameters
 * - Reloads page with new mode
 */
function toggleEditMode() {
  const url = new URL(window.location);
  const params = url.searchParams;
  const id = params.get("view") ?? params.get("edit");

  if (params.has("view")) {
    params.delete("view");
    params.set("edit", id);
  } else if (params.has("edit")) {
    params.delete("edit");
    params.set("view", id);
  }

  // // Reload page with new mode
  window.location.href = url.toString();
}
