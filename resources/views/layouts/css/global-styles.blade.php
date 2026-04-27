/* ============================================
   GLOBAL CSS VARIABLES & FOUNDATION
   Standardized across all layouts
============================================ */

:root {
    /* PRIMARY COLORS */
    --primary: #667eea;
    --primary-dark: #5568d3;
    --secondary: #764ba2;
    --success: #1cc88a;
    --info: #36b9cc;
    --warning: #f6c23e;
    --danger: #e74a3b;

    /* ADMIN COLORS */
    --admin-primary: #4e73df;
    --admin-secondary: #224abe;

    /* PRODUCTION COLORS */
    --prod-primary: #2d6a4f;
    --prod-secondary: #1b4332;
    --prod-accent: #52b788;

    /* TEXT COLORS */
    --text-dark: #2d3748;
    --text-muted: #718096;
    --text-light: #a0aec0;

    /* BACKGROUND COLORS */
    --bg-light: #f8f9fa;
    --bg-white: #ffffff;
    --bg-gray: #e2e8f0;

    /* LAYOUT DIMENSIONS */
    --sidebar-width: 250px;
    --header-height: 70px;

    /* ANIMATIONS */
    --transition: 0.3s ease;
    --transition-fast: 0.15s ease;

    /* SHADOWS */
    --shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    --shadow: 0 4px 6px 0 rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 25px 0 rgba(0, 0, 0, 0.15);
    --shadow-xl: 0 15rem 1.75rem 0 rgba(58, 59, 69, 0.15);

    /* BORDER RADIUS */
    --radius-sm: 4px;
    --radius-md: 8px;
    --radius-lg: 12px;
    --radius-xl: 16px;

    /* SPACING SCALE */
    --spacing-xs: 0.25rem;
    --spacing-sm: 0.5rem;
    --spacing-md: 1rem;
    --spacing-lg: 1.5rem;
    --spacing-xl: 2rem;
    --spacing-2xl: 3rem;

    /* Z-INDEX SCALE */
    --z-dropdown: 1000;
    --z-sticky: 1020;
    --z-fixed: 1030;
    --z-modal-backdrop: 1040;
    --z-modal: 1050;
    --z-popover: 1060;
    --z-tooltip: 1070;
}

/* ============================================
   BASE STYLES
============================================ */

* {
    box-sizing: border-box;
}

html {
    scroll-behavior: smooth;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen',
        'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue',
        sans-serif;
    font-size: 0.95rem;
    line-height: 1.5;
    color: var(--text-dark);
    background-color: var(--bg-light);
    overflow-x: hidden;
    margin: 0;
    padding: 0;
}

main {
    flex: 1;
}

a {
    color: var(--primary);
    text-decoration: none;
    transition: color var(--transition);
}

a:hover {
    color: var(--primary-dark);
}

/* ============================================
   SCROLLBAR STYLING
============================================ */

::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: var(--bg-gray);
}

::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a0aec0;
}

/* ============================================
   UTILITY CLASSES
============================================ */

.rounded-modern {
    border-radius: var(--radius-lg) !important;
}

.rounded-xl {
    border-radius: var(--radius-xl) !important;
}

.text-gradient {
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.hover-lift {
    transition: transform var(--transition), box-shadow var(--transition);
}

.hover-lift:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg) !important;
}

.hover-scale {
    transition: transform var(--transition), box-shadow var(--transition);
}

.hover-scale:hover {
    transform: scale(1.02);
    box-shadow: var(--shadow-lg) !important;
}

.fade-in {
    animation: fadeIn 0.6s ease-in forwards;
}

.slide-in {
    animation: slideInUp 0.6s ease-in forwards;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes slideInUp {
    from {
        transform: translateY(30px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

/* ============================================
   RESPONSIVE UTILITIES
============================================ */

@media (max-width: 991.98px) {
    body {
        font-size: 0.9rem;
    }

    h1 {
        font-size: 1.75rem !important;
    }

    h2 {
        font-size: 1.4rem !important;
    }

    h3 {
        font-size: 1.15rem !important;
    }
}

@media (max-width: 576px) {
    :root {
        --spacing-lg: 1rem;
        --spacing-xl: 1.5rem;
    }

    h1 {
        font-size: 1.5rem !important;
    }

    h2 {
        font-size: 1.2rem !important;
    }

    h3 {
        font-size: 1rem !important;
    }

    .d-flex.flex-wrap {
        flex-wrap: wrap !important;
    }
}
