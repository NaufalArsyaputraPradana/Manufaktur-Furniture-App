function googleTranslateElementInit() {
    new google.translate.TranslateElement({
        pageLanguage: 'id',
        layout: google.translate.TranslateElement.InlineLayout.HORIZONTAL,
        autoDisplay: false
    }, 'google_translate_element');
}

const CurrencyConverter = {
    rateCache: {},
    availableCurrencies: [
        { code: 'IDR', label: '🇮🇩 IDR', locale: 'id-ID' },
        { code: 'USD', label: '🇺🇸 USD', locale: 'en-US' },
        { code: 'EUR', label: '🇪🇺 EUR', locale: 'de-DE' },
        { code: 'GBP', label: '🇬🇧 GBP', locale: 'en-GB' },
        { code: 'SAR', label: '🇸🇦 SAR', locale: 'ar-SA' },
        { code: 'AED', label: '🇦🇪 AED', locale: 'ar-AE' },
        { code: 'MYR', label: '🇲🇾 MYR', locale: 'ms-MY' },
        { code: 'SGD', label: '🇸🇬 SGD', locale: 'en-SG' },
        { code: 'AUD', label: '🇦🇺 AUD', locale: 'en-AU' },
        { code: 'JPY', label: '🇯🇵 JPY', locale: 'ja-JP' },
        { code: 'CNY', label: '🇨🇳 CNY', locale: 'zh-CN' },
        { code: 'KRW', label: '🇰🇷 KRW', locale: 'ko-KR' },
        { code: 'TRY', label: '🇹🇷 TRY', locale: 'tr-TR' }
    ],

    formatCurrency(amount, currencyCode) {
        const config = this.availableCurrencies.find(c => c.code === currencyCode) || { locale: 'id-ID' };
        try {
            return new Intl.NumberFormat(config.locale, {
                style: 'currency',
                currency: currencyCode,
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(amount);
        } catch (e) {
            return `${currencyCode} ${Number(amount).toLocaleString()}`;
        }
    },

    async getExchangeRate(from, to) {
        const cacheKey = `${from}_${to}`;
        if (this.rateCache[cacheKey]) return this.rateCache[cacheKey];

        try {
            const res = await fetch(`https://open.er-api.com/v6/latest/${from}`);
            const data = await res.json();
            if (data.result === 'success' && data.rates[to]) {
                this.rateCache[cacheKey] = data.rates[to];
                return data.rates[to];
            }
            throw new Error('Rate not found');
        } catch (err) {
            console.error('Currency API Error:', err);
            return null;
        }
    },

    async updatePrices(targetCurrency) {
        const priceElements = document.querySelectorAll('[data-price]');
        if (priceElements.length === 0) return;

        const toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });

        Swal.fire({
            title: 'Mengonversi Harga...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        try {
            for (const el of priceElements) {
                const basePrice = parseFloat(el.getAttribute('data-price'));
                const baseCurrency = el.getAttribute('data-currency') || 'IDR';

                if (targetCurrency === baseCurrency) {
                    el.textContent = this.formatCurrency(basePrice, baseCurrency);
                } else {
                    const rate = await this.getExchangeRate(baseCurrency, targetCurrency);
                    if (rate) {
                        el.textContent = this.formatCurrency(basePrice * rate, targetCurrency);
                    }
                }
            }

            localStorage.setItem('selectedCurrency', targetCurrency);
            Swal.close();
            toast.fire({ icon: 'success', title: `Mata uang diubah ke ${targetCurrency}` });
        } catch (error) {
            Swal.fire({ icon: 'error', title: 'Gagal', text: 'Koneksi API bermasalah.' });
        }
    },

    init() {
        const selectors = document.querySelectorAll('.currency-selector');
        const savedCurrency = localStorage.getItem('selectedCurrency') || 'IDR';

        selectors.forEach(select => {
            select.innerHTML = this.availableCurrencies
                .map(c => `<option value="${c.code}" ${c.code === savedCurrency ? 'selected' : ''}>${c.label}</option>`)
                .join('');

            select.addEventListener('change', (e) => {
                const val = e.target.value;
                selectors.forEach(s => (s.value = val));
                this.updatePrices(val);
            });
        });

        if (savedCurrency !== 'IDR') {
            setTimeout(() => this.updatePrices(savedCurrency), 500);
        } else {
            document.querySelectorAll('[data-price]').forEach(el => {
                el.textContent = this.formatCurrency(el.getAttribute('data-price'), 'IDR');
            });
        }
    }
};

document.addEventListener('DOMContentLoaded', () => {
    CurrencyConverter.init();

    // Auto-close session alerts ONLY (success/error), NOT structural elements
    // Use .alert-dismissible to indicate alerts that should auto-close
    document.querySelectorAll('.alert.alert-dismissible').forEach(alert => {
        setTimeout(() => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            if (bsAlert) bsAlert.close();
        }, 5000);
    });
});
