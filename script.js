document.addEventListener("DOMContentLoaded", function () {

    /* =========================================================
       THEME TOGGLE
       ========================================================= */

    const themeBtn = document.getElementById("themeBtn");

    if (themeBtn) {

        const icon = themeBtn.querySelector("i");

        // Load saved theme
        const savedTheme = localStorage.getItem("theme");

        if (savedTheme === "dark") {

            document.body.classList.add("dark-mode");

            if (icon) {
                icon.classList.remove("fa-sun");
                icon.classList.add("fa-moon");
            }

        }


        // Toggle theme
        themeBtn.addEventListener("click", function () {

            document.body.classList.toggle("dark-mode");

            const isDark =
                document.body.classList.contains("dark-mode");


            if (isDark) {

                localStorage.setItem("theme", "dark");

                if (icon) {
                    icon.classList.remove("fa-sun");
                    icon.classList.add("fa-moon");
                }

            } else {

                localStorage.setItem("theme", "light");

                if (icon) {
                    icon.classList.remove("fa-moon");
                    icon.classList.add("fa-sun");
                }

            }

        });

    }


    /* =========================================================
       CONTACT FORM
       ========================================================= */

    const contactForm =
        document.getElementById("contactForm");


    if (contactForm) {

        contactForm.addEventListener("submit", function (event) {

            event.preventDefault();


            const name =
                document.getElementById("name").value.trim();

            const email =
                document.getElementById("email").value.trim();

            const subject =
                document.getElementById("subject").value.trim();

            const message =
                document.getElementById("message").value.trim();


            if (
                name === "" ||
                email === "" ||
                subject === "" ||
                message === ""
            ) {

                alert("Please complete all fields.");

                return;

            }


            alert(
                "Thank you, " +
                name +
                "! Your message has been received."
            );


            contactForm.reset();

        });

    }


    /* =========================================================
       ACTIVE NAVIGATION
       ========================================================= */

    const currentPage =
        window.location.pathname
            .split("/")
            .pop();


    const navLinks =
        document.querySelectorAll(".navbar a");


    navLinks.forEach(function (link) {

        const linkPage =
            link.getAttribute("href");


        if (linkPage === currentPage) {

            link.classList.add("active");

        }

    });


    /* =========================================================
       SIMPLE SCROLL ANIMATION
       ========================================================= */

    const animatedElements =
        document.querySelectorAll(
            ".project-card, .technology-card, .resume-section, .contact-item"
        );


    const observer =
        new IntersectionObserver(
            function (entries) {

                entries.forEach(function (entry) {

                    if (entry.isIntersecting) {

                        entry.target.classList.add(
                            "show-animation"
                        );

                    }

                });

            },
            {
                threshold: 0.15
            }
        );


    animatedElements.forEach(function (element) {

        observer.observe(element);

    });


    /* =========================================================
       PREVENT EMPTY SOCIAL LINKS FROM JUMPING
       ========================================================= */

    const emptyLinks =
        document.querySelectorAll(
            'a[href="#"]'
        );


    emptyLinks.forEach(function (link) {

        link.addEventListener("click", function (event) {

            event.preventDefault();

        });

    });

});