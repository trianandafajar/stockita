import './bootstrap';
import { gsap } from "gsap";
import Alpine from 'alpinejs';
import Swal from 'sweetalert2';

window.Swal = Swal;
window.Alpine = Alpine;
Alpine.start();

document.addEventListener("DOMContentLoaded", () => {
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("overlay");
    const main = document.getElementById("mainContent");
    const header = document.getElementById("headerDashboard");

    const toggleSidebar = document.getElementById("toggleSidebar");
    const closeSidebar = document.getElementById("closeSidebar");
    const toggleCollapse = document.getElementById("toggleCollapse");

    const titles = document.querySelectorAll(".sidebar-title");
    const texts = document.querySelectorAll(".sidebar-text");
    const groups = document.querySelectorAll(".group-item");

    const iconMenu = document.getElementById("iconMenu");
    const iconArrow = document.getElementById("iconArrow");

    const body = document.body;

    let collapsed = false;
    let hoverTimeout;


    gsap.set(sidebar, { x: "-100%" });
    gsap.set(overlay, { opacity: 0, display: "none" });

    // mobile
    function openSidebar() {
        gsap.to(sidebar, {
            x: 0,
            duration: 0.1,
            ease: "power3.out"
        });

        gsap.to(overlay, {
            opacity: 1,
            display: "block",
            duration: 0.2
        });

        gsap.fromTo(texts,
            { opacity: 0, x: -10 },
            {
                opacity: 1,
                x: 0,
                duration: 0.2,
                delay: 0.1,
                stagger: 0.05
            }
        );

        body.classList.add("overflow-hidden");
    }

    function closeSidebarFn() {
        gsap.to(sidebar, {
            x: "-100%",
            duration: 0.1,
            ease: "power3.inOut"
        });

        gsap.to(overlay, {
            opacity: 0,
            duration: 0.2,
            onComplete: () => {
                overlay.style.display = "none";
            }
        });

        gsap.to(texts, {
            opacity: 0,
            x: -10,
            duration: 0.05,
            stagger: 0.01
        });

        body.classList.remove("overflow-hidden");
    }

    let mm = gsap.matchMedia();

    mm.add("(max-width: 767px)", () => {
        toggleSidebar?.addEventListener("click", openSidebar);
        closeSidebar?.addEventListener("click", closeSidebarFn);
        overlay?.addEventListener("click", closeSidebarFn);

        return () => {
            toggleSidebar?.removeEventListener("click", openSidebar);
            closeSidebar?.removeEventListener("click", closeSidebarFn);
            overlay?.removeEventListener("click", closeSidebarFn);
        };
    });

    // dekstop
    let collapseHandler;

    mm.add("(min-width: 1024px)", () => {

        gsap.set(sidebar, { x: 0 });
        gsap.set(main, { paddingLeft: 256 });
        gsap.set(header, { left: 256 });
        gsap.to(iconMenu, { opacity: 1, scale: 1, duration: 0.2 });

        collapsed = false;

        collapseHandler = () => {
            collapsed = !collapsed;
            console.log('diklik')
            if (collapsed) {
                collapseSidebar();
                gsap.to(iconMenu, { opacity: 0, scale: 0.6, duration: 0.2 });
                gsap.to(iconArrow, { opacity: 1, scale: 1, duration: 0.2, zIndex: 100 });
            } else {
                expandSidebar();
                gsap.to(iconArrow, { opacity: 0, scale: 0.6, duration: 0.2, zIndex: 100 });
                gsap.to(iconMenu, { opacity: 1, scale: 1, duration: 0.2 });
            }
        };

        toggleCollapse?.addEventListener("click", collapseHandler);

        function collapseSidebar() {
            gsap.to(texts, {
                opacity: 0,
                x: -10,
                duration: 0.15,
                stagger: 0.02
            });

            gsap.to(sidebar, {
                width: 80,
                duration: 0.2,
                ease: "power3.inOut"
            });

            gsap.to(main, {
                paddingLeft: 80,
                duration: 0.2,
                ease: "power3.inOut"
            });

            gsap.to(header, {
                left: 80,
                duration: 0.2
            });

            gsap.to(groups, {
                marginTop: 0,
                duration: 0.2,
                ease: "power2.out"
            });

            gsap.to(titles, {
                opacity: 0,
                height: 0,
                marginTop: 0,
                duration: 0.15,
                stagger: 0.02
            });
        }

        function expandSidebar() {
            gsap.to(sidebar, {
                width: 256,
                duration: 0.2,
                ease: "power3.inOut"
            });

            gsap.to(main, {
                paddingLeft: 256,
                duration: 0.2,
                ease: "power3.inOut"
            });

            gsap.to(header, {
                left: 256,
                duration: 0.2
            });

            gsap.fromTo(texts,
                { opacity: 0, x: -10 },
                {
                    opacity: 1,
                    x: 0,
                    duration: 0.2,
                    delay: 0.1,
                    stagger: 0.03
                }
            );

            gsap.to(groups, {
                marginTop: 12,
                duration: 0.2,
                ease: "power2.out"
            });

            gsap.fromTo(titles,
                { opacity: 0, height: 0 },
                {
                    opacity: 1,
                    height: "auto",
                    duration: 0.2,
                    stagger: 0.03
                }
            );
        }

        // hover
        const handleMouseEnter = () => {
            if (!collapsed) return;

            clearTimeout(hoverTimeout);

            gsap.to(iconArrow, { opacity: 0, scale: 0.6, duration: 0.2 });
            gsap.to(iconMenu, { opacity: 1, scale: 1, duration: 0.2 });

            hoverTimeout = setTimeout(() => {
                expandSidebar();
            }, 100);
        };

        const handleMouseLeave = () => {
            if (!collapsed) return;

            gsap.to(iconMenu, { opacity: 0, scale: 0.6, duration: 0.2 });
            gsap.to(iconArrow, { opacity: 1, scale: 1, duration: 0.2 });

            hoverTimeout = setTimeout(() => {
                collapseSidebar();
            }, 150);
        };

        sidebar.addEventListener("mouseenter", handleMouseEnter);
        sidebar.addEventListener("mouseleave", handleMouseLeave);

        return () => {
            toggleCollapse?.removeEventListener("click", collapseHandler);
            sidebar.removeEventListener("mouseenter", handleMouseEnter);
            sidebar.removeEventListener("mouseleave", handleMouseLeave);
        };
    });

    // tablet
    mm.add("(min-width: 768px) and (max-width: 1023px)", () => {
        gsap.set(sidebar, { x: 0 });

        gsap.set(sidebar, { width: 80 });
        gsap.set(main, { paddingLeft: 80 });
        gsap.set(header, { left: 80 });

        gsap.set(texts, { opacity: 0, x: -10 });
        gsap.set(titles, { opacity: 0, height: 0, marginTop: 0 });
        gsap.set(groups, { marginTop: 0 });
        gsap.to(iconArrow, { opacity: 1, scale: 1, duration: 0.2, zIndex: 100 });

        collapsed = true;

        function expandSidebar() {
            gsap.to(sidebar, {
                x: 0,
                width: 256,
                duration: 0.3,
                ease: "power3.out"
            });

            gsap.to(main, {
                duration: 0.2,
                ease: "power3.inOut"
            });

            gsap.to(texts, {
                opacity: 1,
                x: 0,
                duration: 0.2,
                stagger: 0.03
            });

            gsap.to(overlay, {
                opacity: 1,
                display: "block",
                duration: 0.2
            });

            gsap.to(titles, {
                opacity: 1,
                height: "auto",
                duration: 0.2,
                stagger: 0.03
            });

            gsap.to(groups, {
                marginTop: 12,
                duration: 0.2
            });

            sidebar.style.zIndex = 200;

            collapsed = false;
        }

        function collapseSidebar() {
            gsap.to(texts, {
                opacity: 0,
                x: -10,
                duration: 0.1
            });

            gsap.to(sidebar, {
                width: 80,
                duration: 0.2,
                ease: "power3.inOut"
            });

            gsap.to(main, {
                duration: 0.2,
                ease: "power3.inOut"
            });

            gsap.to(overlay, {
                opacity: 0,
                duration: 0.2,
                onComplete: () => {
                    overlay.style.display = "none";
                }
            });

            gsap.to(titles, {
                opacity: 0,
                height: 0,
                marginTop: 0,
                duration: 0.15
            });

            gsap.to(groups, {
                marginTop: 0,
                duration: 0.2
            });

            sidebar.style.zIndex = 50;

            collapsed = true;
        }

        // hover
        const handleMouseEnter = () => {
            if (!collapsed) return;

            clearTimeout(hoverTimeout);

            hoverTimeout = setTimeout(() => {
                expandSidebar();
            }, 100);
        };

        const handleMouseLeave = () => {
            if (!collapsed) return;

            hoverTimeout = setTimeout(() => {
                collapseSidebar();
            }, 150);
        };

        sidebar.addEventListener("mouseenter", handleMouseEnter);
        sidebar.addEventListener("mouseleave", handleMouseLeave);

        // toggle
        closeSidebar?.addEventListener("click", () => collapseSidebar());
        overlay?.addEventListener("click", collapseSidebar);
        toggleCollapse?.addEventListener("click", () => {
            collapsed = !collapsed;

            if (collapsed) {
                collapseSidebar();
            } else {
                expandSidebar();
            }
        });

        return () => {
            sidebar.removeEventListener("mouseenter", handleMouseEnter);
            sidebar.removeEventListener("mouseleave", handleMouseLeave);
        };
    });
});

// landingpage
const toggle = document.getElementById('menuToggle');
const menu = document.getElementById('mobileMenu');
const menuLinks = document.querySelectorAll('#mobileMenu a');
const menuIcon = document.getElementById("menuIcon");
const closeIcon = document.getElementById("closeIcon");

let isOpen = false;

function closeMobileMenu() {
    if (!isOpen) return;

    gsap.to(menu, {
        height: 0,
        opacity: 0,
        duration: 0.3,
        ease: "power2.in",
        onComplete: () => {
            menu.classList.add('hidden');
        }
    });

    gsap.to(menuIcon, { opacity: 1, scale: 1, duration: 0.2 });
    gsap.to(closeIcon, { opacity: 0, scale: 0.6, duration: 0.2 });

    isOpen = false;
}

menuLinks.forEach(link => {
    link.addEventListener('click', () => {
        closeMobileMenu();
    });
});

toggle.addEventListener('click', () => {
    if (!isOpen) {
        menu.classList.remove('hidden');

        gsap.fromTo(menu, {
            height: 0,
            opacity: 0
        }, {
            height: "auto",
            opacity: 1,
            duration: 0.4,
            ease: "power2.out"
        });

        gsap.to(menuIcon, { opacity: 0, scale: 0.6, duration: 0.2 });
        gsap.to(closeIcon, { opacity: 1, scale: 1, duration: 0.2 });

    } else {
        gsap.to(menu, {
            height: 0,
            opacity: 0,
            duration: 0.3,
            ease: "power2.in",
            onComplete: () => {
                menu.classList.add('hidden');
            }
        });

        gsap.to(menuIcon, { opacity: 1, scale: 1, duration: 0.2 });
        gsap.to(closeIcon, { opacity: 0, scale: 0.6, duration: 0.2 });
    }

    isOpen = !isOpen;
});

// search
const inputs = document.querySelectorAll('#searchInput');
const clearBtns = document.querySelectorAll('#clearSearch');
const resultsBoxes = document.querySelectorAll('#searchResults');

inputs.forEach((input, i) => {
    const clearBtn = clearBtns[i];
    const resultsBox = resultsBoxes[i];

    let currentResults = [];
    let activeIndex = -1;
    let debounceTimer;

    input.addEventListener('input', function () {
        clearTimeout(debounceTimer);

        debounceTimer = setTimeout(() => {
            handleSearch(this.value);
        }, 200);
    });

    async function handleSearch(keyword) {
        keyword = keyword.toLowerCase().trim();

        resultsBox.innerHTML = '';
        resultsBox.classList.add('hidden');

        activeIndex = -1;
        currentResults = [];

        // toggle tombol clear
        if (keyword.length > 0) {
            clearBtn.classList.remove('hidden');
        } else {
            clearBtn.classList.add('hidden');
        }

        if (keyword.length < 2) return;

        try {
            const res = await fetch(`/search?q=${encodeURIComponent(keyword)}&ajax=1`);
            const data = await res.json();

            currentResults = data;

            renderResults(keyword);

        } catch (err) {
            console.error('Search error:', err);
        }
    }

    function renderResults(keyword) {
        if (currentResults.length === 0) {
            resultsBox.innerHTML = `<div class="p-3 text-sm text-gray-500">Tidak ditemukan</div>`;
        } else {
            currentResults.slice(0, 5).forEach((res, index) => {
                const item = document.createElement('div');

                item.className = `
                    search-card p-3 text-sm cursor-pointer rounded-lg
                    transition-all duration-150 ease-in-out
                    hover:bg-emerald-50
                `;

                item.innerHTML = `
                    <div class="title-text font-medium text-slate-800 transition-colors">
                        ${res.title}
                    </div>
                    <div class="text-xs text-slate-500 mt-0.5">
                        ${(res.excerpt || '').substring(0, 60)}...
                    </div>
                `;

                item.addEventListener('click', () => {
                    window.location.href = res.link;
                });

                resultsBox.appendChild(item);
            });
            if (currentResults.length > 0) {
                const seeAll = document.createElement('div');
                seeAll.className = 'p-3 text-sm text-center border-t border-slate-100 text-emerald-600 font-bold cursor-pointer hover:bg-slate-50';
                seeAll.innerHTML = `Lihat semua hasil untuk "${keyword}"`;
                seeAll.onclick = () => window.location.href = `/search?q=${encodeURIComponent(keyword)}`;
                resultsBox.appendChild(seeAll);
            }
        }

        resultsBox.classList.remove('hidden');
    }

    input.addEventListener('keydown', function (e) {
        const items = resultsBox.querySelectorAll('div[cursor-pointer]');
        const keyword = this.value.trim();

        if (e.key === 'Enter') {
            e.preventDefault();

            if (activeIndex >= 0 && items[activeIndex]) {
                items[activeIndex].click();
            }
            else if (keyword.length > 0) {
                window.location.href = `/search?q=${encodeURIComponent(keyword)}`;
            }
        }

        if (e.key === 'ArrowDown') {
            const resultItems = resultsBox.querySelectorAll('.cursor-pointer');
            if (resultItems.length === 0) return;
            e.preventDefault();
            activeIndex = (activeIndex + 1) % resultItems.length;
            updateActive(resultItems);
        }

        if (e.key === 'ArrowUp') {
            const resultItems = resultsBox.querySelectorAll('.cursor-pointer');
            if (resultItems.length === 0) return;
            e.preventDefault();
            activeIndex = (activeIndex - 1 + resultItems.length) % resultItems.length;
            updateActive(resultItems);
        }
    });

    function updateActive(items) {
        items.forEach((item, index) => {
            item.classList.remove('bg-emerald-100');

            if (index === activeIndex) {
                item.classList.add('bg-emerald-100');
            }
        });
    }

    clearBtn.addEventListener('click', function () {
        input.value = '';
        resultsBox.innerHTML = '';
        resultsBox.classList.add('hidden');

        clearBtn.classList.add('hidden');
        input.focus();
    });

    document.addEventListener('click', function (e) {
        if (!resultsBox.contains(e.target) && e.target !== input) {
            resultsBox.classList.add('hidden');
        }
    });
});