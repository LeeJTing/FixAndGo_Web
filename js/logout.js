
    $('.logout-btn').on('click', function (e) {
        e.preventDefault();

        logout = confirm("Click OK to confirm to exit!");
        if (logout) {
            $.post(ROOT_DIR + "/_logout.php", {
                logout: true
            }).done(function () {
                window.location.href = ROOT_DIR + "/guest/login.php";
                console.log("posted");
            }
            ).fail(function () {
                console.log("failed");
            })
        }
    })

