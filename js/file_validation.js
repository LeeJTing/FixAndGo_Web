// Your original function should work if you fix the type checking:
function validateFileInput(selector, options = {}) {
  var $input = $(selector);
  var files = $input[0].files;

  // Defaults
  options.required = options.required !== undefined ? options.required : true;
  // Accept both MIME types and extensions
  options.allowedTypes = options.types || [
    "image/jpeg",
    "image/png",
    "image/gif",
    "image/webp",
  ];
  options.maxSize = options.maxSize || 2 * 1024 * 1024; // 2MB

  // Clear previous errors
  $input.siblings(".error-msg").remove();

  // Required check
  if (options.required && (!files || files.length === 0)) {
    $input.after(
      '<small class="error-msg" style="color:#f87171;">Please select an image.</small>'
    );
    return false;
  }

  // If no file and not required, that's OK
  if (!files || files.length === 0) {
    return true;
  }

  var file = files[0];

  // Check file type using MIME type
  if (!options.allowedTypes.includes(file.type.toLowerCase())) {
    // Also check by extension as fallback
    var ext = file.name.split(".").pop().toLowerCase();
    var allowedExtensions = options.allowedTypes.map((type) => {
      return type.split("/")[1] || type.replace("image/", "");
    });

    if (!allowedExtensions.includes(ext)) {
      $input.after(
        '<small class="error-msg" style="color:#f87171;">Only JPG, PNG, GIF, or WebP images are allowed.</small>'
      );
      return false;
    }
  }

  // Check file size
  if (file.size > options.maxSize) {
    $input.after(
      '<small class="error-msg" style="color:#f87171;">Image must be less than 2MB.</small>'
    );
    return false;
  }

  return true;
}
