:root {
    --prod-primary: #2d6a4f;
    --prod-secondary: #1b4332;
    --prod-accent: #52b788;
    --light-bg: #f0faf4;
    --sidebar-width: 250px;
    --header-height: 70px;
    --transition-speed: 0.3s;
    --box-shadow: 0 .15rem 1.75rem 0 rgba(58, 59, 69, .15);
}

body {
    font-family: 'Nunito', 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    background-color: var(--light-bg);
    color: #3a3a3a;
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
    z-index: 1040;
    transition: transform var(--transition-speed) ease-in-out;
    overflow-y: auto;
    box-shadow: var(--box-shadow);
    display: flex;
    flex-direction: column;
}

.main-wrapper {
    margin-left: var(--sidebar-width);
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    transition: margin-left var(--transition-speed) ease-in-out;
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
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    z-index: 1030;
}

.main-content {
    flex: 1;
    padding: 2rem;
}

.sidebar-backdrop {
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1035;
    opacity: 0;
    visibility: hidden;
    transition: all var(--transition-speed);
}

.sidebar-backdrop.show { opacity: 1; visibility: visible; }

@media (max-width: 991.98px) {
    .sidebar { transform: translateX(-100%); }
    .sidebar.show { transform: translateX(0); }
    .main-wrapper { margin-left: 0; }
}

.sidebar-nav-link {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    border-radius: 8px;
    color: rgba(255,255,255,0.82);
    font-size: 0.93rem;
    font-weight: 500;
    transition: background 0.2s, color 0.2s;
    text-decoration: none;
}
.sidebar-nav-link:hover {
    background: rgba(255,255,255,0.14);
    color: #fff;
}
.sidebar-nav-link.active {
    background: rgba(255,255,255,0.25);
    color: #fff;
    font-weight: 700;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}
.sidebar-nav-link i { font-size: 1.1rem; margin-right: 0.75rem; flex-shrink: 0; }

.sidebar-section-label {
    font-size: 0.72rem;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: rgba(255,255,255,0.45);
    font-weight: 700;
    padding: 0.25rem 1rem;
    margin-top: 1.25rem;
    margin-bottom: 0.4rem;
}

.hover-lift {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.hover-lift:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1) !important;
}
