<footer>
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <a href="#" class="logo" style="margin-bottom: 1rem; display: flex;">
                    <img src="<?= $rootDir ?>/images/logo.png" alt="Logo" style="width: 70px; height: 100px;" />
                    Fix&Go
                </a>
                <p>Your trusted partner for high-quality hardware tools and equipment.</p>
            </div>
            <div class="footer-col">
                <h4>Shop</h4>
                <ul>
                    <li><a href="#">All Products</a></li>
                    <li><a href="#">New Arrivals</a></li>
                    <li><a href="#">Best Sellers</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Company</h4>
                <ul>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Contact</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Newsletter</h4>
                <p>Subscribe for updates and exclusive offers.</p>
                <form class="newsletter-form" onsubmit="event.preventDefault(); alert('Subscribed!');">
                    <input type="email" placeholder="Enter email" required>
                    <button type="submit" class="btn btn-primary">OK</button>
                </form>
            </div>
        </div>
        <div class="copyright">
            &copy; 2025 Fix&Go. All rights reserved.
        </div>
    </div>
</footer>