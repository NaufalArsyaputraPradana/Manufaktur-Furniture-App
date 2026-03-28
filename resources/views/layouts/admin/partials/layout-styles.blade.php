:root {
    --primary-color: #4e73df;
    --secondary-color: #858796;
    --success-color: #1cc88a;
    --info-color: #36b9cc;
    --warning-color: #f6c23e;
    --danger-color: #e74a3b;
    --light-bg: #f8f9fc;
    --dark-text: #5a5c69;
    --sidebar-width: 250px;
    --header-height: 70px;
    --transition-speed: 0.3s;
    --box-shadow: 0 .15rem 1.75rem 0 rgba(58, 59, 69, .15);
}

body {
    font-family: 'Nunito', 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    background-color: var(--light-bg);
    color: var(--dark-text);
    overflow-x: hidden;
    margin: 0;
}

a {
    text-decoration: none;
}

::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: var(--sidebar-width);
    height: 100vh;
    background: linear-gradient(180deg, var(--primary-color) 10%, #224abe 100%);
    color: #fff;
    z-index: 1040;
    transition: transform var(--transition-speed) ease-in-out;
    overflow-y: auto;
    box-shadow: var(--box-shadow);
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
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    z-index: 1030;
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
    z-index: 1035;
    opacity: 0;
    visibility: hidden;
    transition: all var(--transition-speed);
}

.sidebar-backdrop.show {
    opacity: 1;
    visibility: visible;
}

@media (max-width: 991.98px) {
    .sidebar {
        transform: translateX(-100%);
    }

    .sidebar.show {
        transform: translateX(0);
    }

    .main-wrapper {
        margin-left: 0;
    }
}
