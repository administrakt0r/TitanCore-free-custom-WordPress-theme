document.addEventListener("DOMContentLoaded", () => {
    const root = document.documentElement;
    const themeToggle = document.getElementById("theme-toggle");
    const menuButton = document.getElementById("mobile-menu-btn");
    const mobileMenu = document.getElementById("mobile-menu");
    const hasMobileMenu = Boolean(menuButton && mobileMenu);
    const menuFocusableSelector = [
        "a[href]",
        "button:not([disabled])",
        "input:not([disabled])",
        "select:not([disabled])",
        "textarea:not([disabled])",
        "[tabindex]:not([tabindex='-1'])",
    ].join(",");

    if (themeToggle) {
        themeToggle.addEventListener("click", () => {
            const isDark = root.classList.toggle("dark");
            try {
                localStorage.theme = isDark ? "dark" : "light";
            } catch (error) {
                // Ignore localStorage errors in private mode or blocked contexts.
            }
        });
    }

    if (!hasMobileMenu) {
        return;
    }

    const getMenuFocusableElements = () => (
        Array.from(mobileMenu.querySelectorAll(menuFocusableSelector)).filter((element) => (
            !element.hasAttribute("hidden") && element.getAttribute("aria-hidden") !== "true"
        ))
    );

    const updateMenuButtonLabel = (isOpen) => {
        const openLabel = menuButton.getAttribute("data-open-label");
        const closeLabel = menuButton.getAttribute("data-close-label");
        const nextLabel = isOpen ? closeLabel : openLabel;

        if (nextLabel) {
            menuButton.setAttribute("aria-label", nextLabel);
        }
    };

    const setMenuState = (isOpen, options = {}) => {
        const { moveFocus = false, returnFocus = false } = options;

        menuButton.setAttribute("aria-expanded", isOpen ? "true" : "false");
        mobileMenu.setAttribute("aria-hidden", isOpen ? "false" : "true");
        mobileMenu.classList.toggle("hidden", !isOpen);
        mobileMenu.classList.toggle("is-open", isOpen);
        updateMenuButtonLabel(isOpen);

        if (isOpen && moveFocus) {
            const firstFocusable = getMenuFocusableElements()[0];
            (firstFocusable || mobileMenu).focus();
        }

        if (!isOpen && returnFocus) {
            menuButton.focus();
        }
    };

    const isMenuOpen = () => menuButton.getAttribute("aria-expanded") === "true";
    const closeMenu = (options = {}) => {
        if (!isMenuOpen()) {
            return;
        }

        setMenuState(false, options);
    };

    setMenuState(false);

    menuButton.addEventListener("click", (event) => {
        const shouldOpen = !isMenuOpen();
        setMenuState(shouldOpen, {
            moveFocus: shouldOpen && event.detail === 0,
        });
    });

    mobileMenu.addEventListener("click", (event) => {
        if (event.target.closest("a")) {
            closeMenu();
        }
    });

    document.addEventListener("click", (event) => {
        if (!isMenuOpen()) {
            return;
        }

        const clickedInsideMenu = event.target.closest("#mobile-menu");
        const clickedToggle = event.target.closest("#mobile-menu-btn");
        if (!clickedInsideMenu && !clickedToggle) {
            closeMenu();
        }
    });

    document.addEventListener("focusin", (event) => {
        if (!isMenuOpen()) {
            return;
        }

        if (!mobileMenu.contains(event.target) && !menuButton.contains(event.target)) {
            closeMenu();
        }
    });

    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape" && isMenuOpen()) {
            event.preventDefault();
            closeMenu({ returnFocus: true });
        }
    });

    window.addEventListener("resize", () => {
        if (window.innerWidth >= 768) {
            closeMenu();
        }
    }, { passive: true });
});
