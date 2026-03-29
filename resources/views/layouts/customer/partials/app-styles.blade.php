/* ============================================
   CSS VARIABLES & BASE
============================================ */
:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --primary-color: #667eea;
    --secondary-color: #764ba2;
    --text-dark: #2d3748;
    --text-muted: #718096;
    --bg-light: #f8f9fa;
    --border-color: #e2e8f0;
    --transition: 0.3s ease;
}

body {
    font-family: 'Inter', sans-serif;
    background-color: var(--bg-light);
    color: var(--text-dark);
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    overflow-x: hidden;
    position: relative;
    top: 0 !important;
}

main {
    flex: 1;
}

.rounded-modern {
    border-radius: 12px !important;
}

.text-gradient {
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.hover-lift {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
}

.goog-logo-link,
.goog-te-gadget span {
    display: none !important;
}

.goog-te-gadget {
    color: transparent !important;
    font-size: 0 !important;
}

.skiptranslate,
.goog-te-banner-frame {
    display: none !important;
}

body {
    top: 0 !important;
    position: relative !important;
}

.translated-ltr,
.translated-rtl {
    margin-top: 0 !important;
    padding-top: 0 !important;
}

.translated-ltr .navbar-modern,
.translated-rtl .navbar-modern {
    top: 0 !important;
    margin-top: 0 !important;
}

/* Kiri bawah agar tidak bertabrakan dengan tombol "kembali ke atas" (kanan bawah) */
.whatsapp-float {
    position: fixed;
    bottom: 30px;
    left: 30px;
    right: auto;
    background: #25D366;
    color: #fff;
    width: 55px;
    height: 55px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
    z-index: 1035;
    text-decoration: none;
    transition: all 0.3s ease;
}

.whatsapp-float:hover {
    transform: scale(1.1) rotate(5deg);
    background: #128C7E;
    color: #fff;
}

@media (max-width: 576px) {
    .whatsapp-float {
        bottom: calc(20px + env(safe-area-inset-bottom, 0px));
        left: 20px;
        width: 48px;
        height: 48px;
        font-size: 1.5rem;
    }
}
