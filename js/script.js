let cart = [];
const cartCount = document.getElementById("cartCount");
const cartTotalPrice = document.getElementById("cartTotalPrice");
const cartItemsContainer = document.getElementById("cartItems");
const cartSidebar = document.getElementById("cartSidebar");
const cartOverlay = document.getElementById("cartOverlay");
const cartIcon = document.getElementById("cartIcon");

// Open/close cart
cartIcon.addEventListener("click", () => {
  cartSidebar.classList.toggle("open");
  cartOverlay.classList.toggle("active");
});

document.querySelector(".close-cart").addEventListener("click", () => {
  cartSidebar.classList.remove("open");
  cartOverlay.classList.remove("active");
});

cartOverlay.addEventListener("click", () => {
  cartSidebar.classList.remove("open");
  cartOverlay.classList.remove("active");
});

// Add to cart function
function addToCart(productName, price) {
  cart.push({ name: productName, price: price });
  updateCart();
  showAddAnimation();
}

function updateCart() {
  cartCount.textContent = cart.length;
  cartTotalPrice.textContent =
    "$" + cart.reduce((sum, item) => sum + item.price, 0).toFixed(2);

  if (cart.length === 0) {
    cartItemsContainer.innerHTML =
      '<p class="empty-cart">Your cart is empty</p>';
    return;
  }

  cartItemsContainer.innerHTML = cart
    .map(
      (item, index) => `
        <div class="cart-item">
            <div style="width:60px;height:60px;background:#e5e7eb;border-radius:8px;display:grid;place-items:center;color:#999;font-size:0.9rem;">
                ${item.name.split(" ")[0]}
            </div>
            <div class="cart-item-info">
                <div class="cart-item-title">${item.name}</div>
                <div class="cart-item-price">$${item.price.toFixed(2)}</div>
            </div>
            <button class="remove-item" onclick="removeFromCart(${index})">&times;</button>
        </div>
    `
    )
    .join("");
}

function removeFromCart(index) {
  cart.splice(index, 1);
  updateCart();
}

function showAddAnimation() {
  cartIcon.style.transform = "scale(1.4)";
  setTimeout(() => (cartIcon.style.transform = ""), 300);
}

// Attach to all Add to Cart buttons
document.querySelectorAll(".add-to-cart").forEach((button) => {
  button.addEventListener("click", function (e) {
    e.preventDefault();
    const title =
      this.closest(".tool-card").querySelector(".tool-title").textContent;
    const priceText =
      this.closest(".tool-card").querySelector(".price").textContent;
    const price = parseFloat(priceText.replace("$", ""));

    addToCart(title, price);

    // Button feedback
    const original = this.innerHTML;
    this.innerHTML = '<i class="fa-solid fa-check"></i> Added!';
    this.style.backgroundColor = "#16a34a";
    setTimeout(() => {
      this.innerHTML = original;
      this.style.backgroundColor = "";
    }, 1200);
  });
});

document
  .getElementById("mobileMenuToggle")
  .addEventListener("click", function () {
    const menu = document.getElementById("mobileDropdown");
    menu.classList.toggle("open");

    // Toggle icon: bars → x
    this.querySelector("i").classList.toggle("fa-bars");
    this.querySelector("i").classList.toggle("fa-xmark");
  });
