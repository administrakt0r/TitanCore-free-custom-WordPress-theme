document.addEventListener("DOMContentLoaded", () => {
    const root = document.documentElement;
    const themeToggle = document.getElementById("theme-toggle");
    const menuButton = document.getElementById("mobile-menu-btn");
    const mobileMenu = document.getElementById("mobile-menu");
    const backdrop = document.getElementById("mobile-menu-backdrop");
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

    const initTocHighlighting = () => {
        const tocLists = Array.from(document.querySelectorAll("[data-toc]"));
        if (tocLists.length === 0 || !("IntersectionObserver" in window)) return;

        const links = new Map();
        tocLists.forEach((toc) => {
            toc.querySelectorAll('a[href^="#"]').forEach((link) => {
                const rawId = link.getAttribute("href").slice(1);
                let heading = rawId ? document.getElementById(rawId) : null;
                if (!heading) {
                    try {
                        heading = document.getElementById(decodeURIComponent(rawId));
                    } catch (error) {
                        heading = null;
                    }
                }
                if (!heading) return;
                if (!links.has(heading.id)) links.set(heading.id, []);
                links.get(heading.id).push(link);
            });
        });

        if (links.size === 0) return;

        const headings = Array.from(links.keys())
            .map((id) => document.getElementById(id))
            .filter(Boolean)
            .sort((a, b) => (a.compareDocumentPosition(b) & Node.DOCUMENT_POSITION_FOLLOWING ? -1 : 1));
        if (headings.length === 0) return;

        let activeId = null;
        const visibleIds = new Set();

        const setActive = (id) => {
            if (activeId === id) return;
            activeId = id;
            links.forEach((anchorList, key) => {
                const isActive = key === id;
                anchorList.forEach((link) => {
                    link.classList.toggle("is-active", isActive);
                    if (isActive) {
                        link.setAttribute("aria-current", "true");
                    } else {
                        link.removeAttribute("aria-current");
                    }
                });
            });
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    visibleIds.add(entry.target.id);
                } else {
                    visibleIds.delete(entry.target.id);
                }
            });
            if (visibleIds.size === 0) return;
            const current = headings.find((heading) => visibleIds.has(heading.id));
            if (current) setActive(current.id);
        }, { rootMargin: "-80px 0px -70% 0px", threshold: [0, 1] });

        headings.forEach((heading) => observer.observe(heading));
    };

    initTocHighlighting();

    if (!hasMobileMenu) {
        return;
    }

    const menuIcons = menuButton.querySelectorAll("svg");
    const getMenuFocusableElements = () => (
        Array.from(mobileMenu.querySelectorAll(menuFocusableSelector)).filter((element) => (
            !element.hasAttribute("hidden") && element.getAttribute("aria-hidden") !== "true"
        ))
    );

    const updateMenuButtonIcons = (isOpen) => {
        if (menuIcons.length >= 2) {
            menuIcons[0].classList.toggle("hidden", isOpen);
            menuIcons[1].classList.toggle("hidden", !isOpen);
        }
    };

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
        if (backdrop) {
            backdrop.classList.toggle("hidden", !isOpen);
            backdrop.setAttribute("aria-hidden", isOpen ? "false" : "true");
        }
        // Scroll lock + compensate scrollbar to avoid layout shift
        if (isOpen) {
            const scrollbarW = window.innerWidth - document.documentElement.clientWidth;
            document.body.style.overflow = "hidden";
            if (scrollbarW > 0) document.body.style.paddingRight = scrollbarW + "px";
        } else {
            document.body.style.overflow = "";
            document.body.style.paddingRight = "";
        }
        updateMenuButtonLabel(isOpen);
        updateMenuButtonIcons(isOpen);

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
        if (!isMenuOpen()) return;
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
        if (event.target.closest("a")) closeMenu();
    });

    if (backdrop) {
        backdrop.addEventListener("click", () => closeMenu());
    }

    document.addEventListener("click", (event) => {
        if (!isMenuOpen()) return;
        const clickedInsideMenu = event.target.closest("#mobile-menu");
        const clickedToggle = event.target.closest("#mobile-menu-btn");
        if (!clickedInsideMenu && !clickedToggle) closeMenu();
    });

    document.addEventListener("focusin", (event) => {
        if (!isMenuOpen()) return;
        if (!mobileMenu.contains(event.target) && !menuButton.contains(event.target)) {
            closeMenu();
        }
    });

    // Focus trap: Tab loops inside menu
    mobileMenu.addEventListener("keydown", (event) => {
        if (event.key !== "Tab" || !isMenuOpen()) return;
        const els = getMenuFocusableElements();
        if (els.length === 0) return;
        const first = els[0];
        const last = els[els.length - 1];
        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    });

    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape" && isMenuOpen()) {
            event.preventDefault();
            closeMenu({ returnFocus: true });
        }
    });

    window.addEventListener("resize", () => {
        if (window.innerWidth >= 768) closeMenu();
    }, { passive: true });
});
