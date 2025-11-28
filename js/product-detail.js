function changeMainImage(src, element) {
  document.getElementById("mainImg").src = src;
  document
    .querySelectorAll(".thumb-wrapper")
    .forEach((t) => t.classList.remove("active"));
  element.classList.add("active");
}

$(document).on("click", ".favorite-btn", function () {
  $(this).find("i").toggleClass("changeColor");
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
