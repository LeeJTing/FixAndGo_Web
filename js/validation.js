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
