// ======================
// Brgy. San Antonio Lending Tracker - Shared JS
// ======================

(function () {
    const body = document.body;
    const sidebar = document.querySelector('.sidebar');
    const hamburger = document.querySelector('.btn-hamburger');
    const overlay = document.querySelector('.overlay');
    const todayEls = document.querySelectorAll('[data-today]');

    // ----------------------
    // Helper Functions
    // ----------------------
    const debounce = (fn, delay = 200) => {
        let timeout;
        return (...args) => {
            clearTimeout(timeout);
            timeout = setTimeout(() => fn(...args), delay);
        };
    };

    const setToday = () => {
        const opts = { year: 'numeric', month: 'short', day: 'numeric' };
        const txt = new Date().toLocaleDateString(undefined, opts);
        todayEls.forEach(el => el.textContent = txt);
    };

    const updateTotalCount = (totalEl) => {
        const tableBody = document.querySelector(totalEl.getAttribute('data-target'));
        const count = tableBody ? tableBody.querySelectorAll('tr').length : 0;
        totalEl.textContent = count;
    };

    // ----------------------
    // Sidebar Functions
    // ----------------------
    const openSidebar = () => {
        sidebar?.classList.add('open');
        body.classList.add('sidebar-open');
        hamburger?.classList.add('active');
        hamburger?.setAttribute('aria-expanded', 'true');
    };

    const closeSidebar = () => {
        sidebar?.classList.remove('open');
        body.classList.remove('sidebar-open');
        hamburger?.classList.remove('active');
        hamburger?.setAttribute('aria-expanded', 'false');
    };

    const toggleSidebar = () => sidebar?.classList.contains('open') ? closeSidebar() : openSidebar();

    hamburger?.addEventListener('click', toggleSidebar);
    overlay?.addEventListener('click', closeSidebar);
    document.addEventListener('keydown', e => e.key === 'Escape' && closeSidebar());

    // ----------------------
    // Table Filtering
    // ----------------------
    const filterTable = debounce((input) => {
        const query = input.value.trim().toLowerCase();
        const targetSelector = input.getAttribute('data-filter-target');
        if (!targetSelector) return;
        document.querySelectorAll(targetSelector).forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(query) ? '' : 'none';
        });
    });

    document.addEventListener('input', (e) => {
        const input = e.target.closest('[data-filter-input]');
        if (input) filterTable(input);
    });

    // ----------------------
    // Row Removal
    // ----------------------
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-remove-row]');
        if (!btn) return;
        const row = btn.closest('tr');
        row?.remove();

        // Update total count
        document.querySelectorAll('[data-total-count]').forEach(updateTotalCount);
    });

    // ----------------------
    // Profile Dropdown
    // ----------------------
    const profile = document.getElementById("profile-menu");
    const dropdown = document.getElementById("dropdown-menu");
    const logoutBtn = document.getElementById("logout-btn");

    if (profile && dropdown) {
        profile.addEventListener("click", (e) => {
            e.stopPropagation();
            dropdown.classList.toggle('show');
        });

        document.addEventListener("click", (e) => {
            if (!profile.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        });
    }

    // ----------------------
    // Logout
    // ----------------------
    logoutBtn?.addEventListener("click", (e) => {
        e.preventDefault();
        alert("You have been logged out.");
        window.location.href = "Login.html";
    });

    // ----------------------
    // Login Form Handler
    // ----------------------
    const loginForm = document.querySelector(".login-form");
    if (loginForm) {
        loginForm.addEventListener("submit", (e) => {
            e.preventDefault();
            const username = document.querySelector('input[placeholder="Username"]').value.trim();
            const password = document.querySelector('input[placeholder="Password"]').value.trim();
            if (username === "admin" && password === "admin123") {
                window.location.href = "Dashboard.html";
            } else {
                alert("Invalid username or password.");
            }
        });
    }

    // ----------------------
    // Return Tracking
    // ----------------------
    document.addEventListener("click", (e) => {
        const btn = e.target.closest(".btn");
        if (!btn) return;
        if (btn.innerText === "Mark Returned") {
            const row = btn.closest("tr");
            if (!row) return;
            row.cells[6].classList.add('returned'); // use CSS class instead
            btn.innerText = "Returned";
            btn.disabled = true;
        }
    });

    // ----------------------
    // Initialize
    // ----------------------
    setToday();

    // Expose Sidebar API
    window.App = { openSidebar, closeSidebar, toggleSidebar };
})();
