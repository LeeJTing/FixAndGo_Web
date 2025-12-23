function changeMainImage(src, element) {
  document.getElementById("mainImg").src = src;
  document
    .querySelectorAll(".thumb-wrapper")
    .forEach((t) => t.classList.remove("active"));
  element.classList.add("active");
}

$(document).on("click", ".favorite-btn", function (e) {
  e.preventDefault();
  e.stopPropagation();
  const $btn = $(this);
  const productId = parseInt($btn.data("product-id"), 10);
  if (!productId) return;

  $.post(
    "../../AJAX/toggle_wishlist.php",
    { toggle_wishlist: true, product_id: productId },
    function (res) {
      if (res && res.success) {
        $btn.find("i").toggleClass("changeColor", !!res.in_wishlist);
      } else if (res && res.error === "LOGIN_REQUIRED") {
        alert(res.message || "Please login to use Wishlist.");
        window.location.href = "../../pages/guest/login.php";
      } else {
        alert((res && res.error) || "Wishlist update failed.");
      }
    },
    "json"
  ).fail(function () {
    alert("Network error. Please try again.");
  });
});

function changeQty(change) {
  const input = document.getElementById("quantity");
  let current = parseInt(input.value);
  let max = parseInt(input.max);

  current += change;

  if (current < 1) current = 1;
  if (current > max) current = max;

  input.value = current;
}

// Optional: Update cart data when quantity changes
document.getElementById("quantity").addEventListener("change", function () {
  const btn = document.querySelector(".btn-add-cart");
  if (btn) {
    // You can later read this value when adding to cart
    console.log("Quantity:", this.value);
  }
});
