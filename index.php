<?php
require '_base.php';
$_title = 'Fix & Go';
include '_head.php';
include 'pages/homepage.php'
?>

<script>
    document
        .getElementById("mobileMenuToggle")
        .addEventListener("click", function() {
            const menu = document.getElementById("mobileDropdown");
            menu.classList.toggle("open");

            // Toggle icon: bars → x
            this.querySelector("i").classList.toggle("fa-bars");
            this.querySelector("i").classList.toggle("fa-xmark");
        });

    $(document).on("click", "#profileIcon", function(e) {
        e.stopPropagation();
        const $dropdown = $("#profileDropdown");
        const $arrow = $(this).find(".dropdown-arrow");

        $dropdown.toggleClass("active");
        $arrow.toggleClass("rotated");
    });

    // Close when clicking outside
    $(document).on("click", function(e) {
        if (!$(e.target).closest(".profile-wrapper").length) {
            $("#profileDropdown").removeClass("active");
            $(".dropdown-arrow").removeClass("rotated");
        }
    });
</script>
<?php
include '_foot.php';
