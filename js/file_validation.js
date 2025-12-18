function validateFileInput(selector, options = {}) {
  var $input = $(selector);
  var files = $input[0].files; // FileList
  var isValid = true;

  // Remove previous error
  $input.next(".error-msg").remove();

  // Required validation
  if (options.required && files.length === 0) {
    $input.after(
      '<small class="error-msg" style="color:#f87171;">Please select at least one file.</small>'
    );
    return false;
  }

  // File type and size validation
  if (files.length > 0) {
    var allowedTypes = options.types || [];
    var maxSize = options.maxSize || Infinity;

    $.each(files, function (index, file) {
      if (
        allowedTypes.length > 0 &&
        $.inArray(file.type, allowedTypes) === -1
      ) {
        $input.after(
          '<small class="error-msg" style="color:#f87171;">File "' +
            file.name +
            '" must be one of: ' +
            allowedTypes.join(", ") +
            "</small>"
        );
        isValid = false;
        return false; // break $.each
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
        return false; // break $.each
      }
    });
  }
  
  return isValid;
}
