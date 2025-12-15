function validateField(fieldName, rules) {
  let input = $(`[name="${fieldName}"]`);
  if (!input.length) return true; // field not found → skip

  let value = input.val().trim();

  clearError(input);

  // Required
  if (rules.required && value === "") {
    showError(input, "This field is required");
    return false;
  }

  // Min length
  if (rules.min && value.length < rules.min) {
    showError(input, `Minimum ${rules.min} characters`);
    return false;
  }

  // Max length
  if (rules.max && value.length > rules.max) {
    showError(input, `Maximum ${rules.max} characters`);
    return false;
  }

  // Number
  if (rules.number && isNaN(value)) {
    showError(input, "Must be a number");
    return false;
  }

  // Decimal (positive only)
  if (rules.decimal && !/^\d+(\.\d+)?$/.test(value)) {
    showError(input, "Must be a positive decimal number");
    return false;
  }

  // Positive
  if (rules.positive && Number(value) < 0) {
    showError(input, "Must be a positive number");
    return false;
  }

  if (rules.Mimstock && value < 5) {
    showError(input, "Stock Must be more than 5");
    return false;
  }

  return true;
}

function showError(input, message) {
  let field = $(input);

  // Remove previous error
  field.next(".error-msg").remove();

  // Append new error
  field.after(
    `<small class="error-msg" style="color:#f87171;">${message}</small>`
  );

  // Add red border
  field.addClass("input-error");
}

function clearError(input) {
  let field = $(input);
  field.removeClass("input-error");
  field.next(".error-msg").remove();
}

function passwordFormat(password) {
  const passwordRegex = /^(?=.*[A-Za-z])(?=.*\d).{8,12}$/;
  return (
    password.length >= 8 &&
    password.length <= 12 &&
    passwordRegex.test(password)
  );
}

function userIDFormat(userID) {
  return (
    userID.length >= 4 &&
    userID.length <= 12 &&
    !userID.includes("@") &&
    !userID.includes(" ")
  );
}

function userNameFormat(name) {
  const nameRegex = /^[a-zA-Z\s]+$/;
  return name.length >= 4 && name.length <= 50 && nameRegex.test(name);
}

function emailFormat(email) {
  const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
  return email.length > 1 && email.length <= 50 && emailRegex.test(email);
}

function contactNumberFormat(contact_number) {
  const phoneRegex = /^\+\d{11,13}$/;
  return phoneRegex.test(contact_number);
}

function dobFormat(dob, minAge=17){
  if (!dob) return false;

    const birthDate = new Date(dob);
    const today = new Date();

    // Invalid date
    if (isNaN(birthDate.getTime())) return false;

    // Future date
    if (birthDate > today) return false;

    // Age check (optional)
    if (minAge > 0) {
        let age = today.getFullYear() - birthDate.getFullYear();
        const m = today.getMonth() - birthDate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        if (age < minAge) return false;
    }

    return true;
}
