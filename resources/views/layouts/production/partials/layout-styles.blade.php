/* Production-specific color overrides (inherits from global) */
:root {
    --prod-primary: #2d6a4f;
    --prod-secondary: #1b4332;
    --prod-accent: #52b788;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen',
        'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue',
        sans-serif;
    background-color: var(--bg-light);
    color: var(--text-dark);
    overflow-x: hidden;
    margin: 0;
}

a { text-decoration: none; }

::-webkit-scrollbar { width: 8px; height: 8px; }
::-webkit-scrollbar-track { background: #f1f1f1; }
::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 4px; }
::-webkit-scrollbar-thumb:hover { background: #a8a8a8; }

.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: var(--sidebar-width);
    height: 100vh;
    background: linear-gradient(180deg, var(--prod-primary) 0%, var(--prod-secondary) 100%);
    color: #fff;
    z-index: var(--z-modal);
    transition: transform var(--transition) ease-in-out;
    overflow-y: auto;
    box-shadow: var(--shadow-xl);
    display: flex;
    flex-direction: column;
}

.main-wrapper {
    margin-left: var(--sidebar-width);
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    transition: margin-left var(--transition) ease-in-out;
}

.main-header {
    position: sticky;
    top: 0;
    height: var(--header-height);
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 1.5rem;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    z-index: var(--z-fixed);
}

.main-content {
    flex: 1;
    padding: 2rem;
}

.sidebar-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: var(--z-modal-backdrop);
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transition: all var(--transition);
}

.sidebar-backdrop.show {
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
}

@media (max-width: 991.98px) {
    .sidebar { transform: translateX(-100%); }
    .sidebar.show { transform: translateX(0); }
    .main-wrapper { margin-left: 0; }
}

.sidebar-nav-link {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    border-radius: var(--radius-md);
    color: rgba(255, 255, 255, 0.82);
    font-size: 0.93rem;
    font-weight: 500;
    transition: background var(--transition-fast), color var(--transition-fast);
    text-decoration: none;
}

.sidebar-nav-link:hover {
    background: rgba(255, 255, 255, 0.14);
    color: #fff;
}

.sidebar-nav-link.active {
    background: rgba(255, 255, 255, 0.25);
    color: #fff;
    font-weight: 700;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.sidebar-nav-link i {
    font-size: 1.1rem;
    margin-right: 0.75rem;
    flex-shrink: 0;
}

.sidebar-section-label {
    font-size: 0.72rem;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.45);
    font-weight: 700;
    padding: 0.25rem 1rem;
    margin-top: 1.25rem;
    margin-bottom: 0.4rem;
}

.hover-lift {
    transition: transform var(--transition), box-shadow var(--transition);
}

.hover-lift:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1) !important;
}
