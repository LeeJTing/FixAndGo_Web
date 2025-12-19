function validateFileInput(selector, options = {}) {
  var $input = $(selector);
  var files = $input[0].files;
  var isValid = true;

  options.required = options.required !== undefined ? options.required : true;
  var allowedExtensions = options.types || [];
  var maxSize = options.maxSize || Infinity;

  // Clear previous error
  $input.next(".error-msg").remove();

  // Required validation
  if (options.required && (!files || files.length === 0)) {
    $input.after(
      '<small class="error-msg" style="color:#f87171;">Please select an image.</small>'
    );
    return false;
  }

  // File type & size validation
  if (files && files.length > 0) {
    $.each(files, function (i, file) {
      var ext = file.name.split(".").pop().toLowerCase();
      if (
        allowedExtensions.length > 0 &&
        allowedExtensions.indexOf(ext) === -1
      ) {
        $input.after(
          '<small class="error-msg" style="color:#f87171;">File "' +
            file.name +
            '" must be one of: ' +
            allowedExtensions.join(", ") +
            "</small>"
        );
        isValid = false;
        return false; // break
      }
      if (file.size > maxSize) {
        $input.after(
          '<small class="error-msg" style="color:#f87171;">File "' +
            file.name +
            '" exceeds maximum size of ' +
            maxSize / 1024 / 1024 +
            " MB.</small>"
        );
        isValid = false;
        return false; // break
      }
    });
  }

  return isValid;
}
