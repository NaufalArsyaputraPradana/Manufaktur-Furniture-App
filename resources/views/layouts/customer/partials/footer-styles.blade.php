/* UTILITIES */
.ls-1 {
    letter-spacing: 1px;
}

.extra-small {
    font-size: 0.75rem;
}

/* BRAND LOGO */
.footer-brand {
    display: flex;
    align-items: center;
    gap: 12px;
}

.footer-logo-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* LINKS HOVER */
.footer-links a {
    transition: all 0.3s ease;
}

.footer-links a:hover {
    color: #fff !important;
    transform: translateX(5px);
}

/* SOCIAL BUTTONS */
.footer-social-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: 0.3s;
}

.footer-social-btn:hover {
    background: #667eea;
    border-color: #667eea;
    transform: translateY(-3px);
    color: #fff;
}

/* OPERATIONAL HOURS */
.op-hour-row {
    background: rgba(255, 255, 255, 0.05);
}

.op-hour-row .badge {
    font-size: 0.7rem;
    font-weight: 600;
}

/* CTA CARD */
.footer-cta-card {
    background: rgba(102, 126, 234, 0.1);
    border: 1px solid rgba(102, 126, 234, 0.2);
}

/* BOTTOM BAR */
.footer-bottom {
    border-top: 1px solid rgba(255, 255, 255, 0.05);
}

/* BACK TO TOP — kanan bawah, di atas area aman; tidak tertutup WA (WA di kiri) */
.back-to-top-btn {
    position: fixed;
    bottom: calc(25px + env(safe-area-inset-bottom, 0px));
    right: calc(25px + env(safe-area-inset-right, 0px));
    width: 45px;
    height: 45px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    border: none;
    border-radius: 50%;
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 1045;
    transition: 0.3s;
    font-size: 1.2rem;
}

.back-to-top-btn:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

@media (max-width: 768px) {
    .back-to-top-btn {
        width: 40px;
        height: 40px;
        bottom: 20px;
        right: 20px;
    }
}
