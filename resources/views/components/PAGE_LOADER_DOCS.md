# Page Loader Component - Documentation

## Overview
Komponen Page Loader adalah fitur loading yang menarik dan modern untuk menampilkan animasi saat berpindah halaman atau sedang memproses data.

## Features
✨ **Animated Spinner** - SVG circle dengan bouncing dots
🎨 **Modern Design** - Gradient background dengan blur effect
📊 **Progress Bar** - Shimmer effect progress bar
⚡ **Auto Detection** - Otomatis tampil saat link/form ditrigger
🎯 **Customizable** - Bisa custom message dan subtitle
📱 **Responsive** - Adapts to all screen sizes

## Usage

### Basic Usage (Automatic)
Loader akan otomatis tampil saat:
1. **Klik link internal** - Semua link yang mengarah ke halaman lain
2. **Submit form** - POST/PUT/PATCH/DELETE forms

```blade
<!-- Link otomatis trigger loader -->
<a href="/products">Lihat Produk</a>

<!-- Form otomatis trigger loader -->
<form action="/orders" method="POST">
    @csrf
    <button type="submit">Pesan Sekarang</button>
</form>
```

### Manual Control
Anda bisa mengontrol loader secara manual via JavaScript:

```javascript
// Tampilkan loader
showPageLoader();

// Tampilkan dengan custom message
showPageLoader('Sedang memproses pembayaran...', 'Jangan refresh halaman');

// Sembunyikan loader
hidePageLoader();

// Sembunyikan dengan delay (ms)
hidePageLoader(500);
```

### Skip Loader
Untuk menghindari loader di elemen tertentu, gunakan attribute `data-no-loader`:

```blade
<!-- Link yang tidak trigger loader -->
<a href="#" data-no-loader>Scroll to section</a>

<!-- Form GET yang tidak trigger loader -->
<form action="/search" method="GET" data-no-loader>
    <input type="text" name="q" />
    <button type="submit">Search</button>
</form>
```

## Examples

### Example 1: Product Purchase Flow
```blade
<div class="purchase-section">
    <form action="{{ route('customer.orders.payment.process', $order) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="payment_proof" />
        <button type="submit" class="btn btn-primary">
            Kirim Bukti Pembayaran
        </button>
    </form>
</div>
```

Saat user klik submit → Loader tampil → Form diproses → Page baru dimuat → Loader hilang

### Example 2: Manual Trigger in Payment
```javascript
// In payment processing
const submitBtn = document.getElementById('submitBtn');
submitBtn.addEventListener('click', function() {
    // Custom message saat processing
    showPageLoader('Memproses Pembayaran...', 'Sistem sedang memverifikasi pembayaran');
    
    // Fetch data or process
    fetch('/api/payment/process')
        .then(/* ... */)
        .finally(() => {
            hidePageLoader(500); // Hide after 500ms delay
        });
});
```

### Example 3: Redirect dengan Loader
```javascript
function redirectWithLoader(url) {
    showPageLoader('Mempersiapkan halaman...', 'Mohon tunggu sebentar');
    setTimeout(() => {
        window.location.href = url;
    }, 300);
}
```

## Customization

### Change Colors
Ubah gradient colors di style:
```css
.page-loader {
    background: linear-gradient(135deg, #your-color-1 0%, #your-color-2 100%);
}
```

### Change Messages
Update default message di JavaScript:
```javascript
showPageLoader('Custom Title', 'Custom Subtitle');
```

### Change Animation Speed
Modify animation duration (dalam ms):
```css
.page-loader {
    animation: fadeIn 0.5s ease; /* Ubah 0.5s */
}

.spinner-svg {
    animation: rotate 2s linear infinite; /* Ubah 2s */
}
```

## Technical Details

### HTML Structure
```html
<div id="pageLoader" class="page-loader">
    <div class="loader-container">
        <div class="loader-content">
            <div class="loader-spinner">
                <!-- SVG Circle Spinner -->
                <!-- Bouncing Dots -->
            </div>
            <div class="loader-text">
                <h4 class="loader-title">Memproses...</h4>
                <p class="loader-subtitle">Mohon tunggu sebentar</p>
            </div>
            <div class="loader-progress">
                <!-- Progress Bar -->
            </div>
        </div>
    </div>
</div>
```

### CSS Animations
- **fadeIn/fadeOut** - Opacity transition
- **slideUp** - Container entrance animation
- **rotate** - SVG circle rotation
- **dash** - Circle stroke animation
- **bounce** - Dots bouncing effect
- **shimmer** - Progress bar shimmer effect

### JavaScript Functions
1. `showPageLoader(message?, subtitle?)` - Show loader
2. `hidePageLoader(delay?)` - Hide loader
3. Auto event listeners untuk link dan form

## Performance Notes
✅ Uses CSS animations (GPU accelerated)
✅ Minimal JavaScript overhead
✅ No external dependencies (Bootstrap CSS only)
✅ Graceful fallback if JS disabled

## Browser Support
- Chrome/Edge: 90+
- Firefox: 88+
- Safari: 14+
- Opera: 76+

## Troubleshooting

### Loader tidak disappear
- Check browser console untuk errors
- Pastikan page fully loaded (DOMContentLoaded)
- Manual hide: `hidePageLoader(0)`

### Loader stuck
- Fallback timer otomatis hide setelah 5 detik
- Manual: `hidePageLoader()`

### Customize tidak work
- Clear browser cache
- Pastikan CSS di-compile dengan benar
- Check untuk CSS conflicts dengan library lain

## Future Enhancements
🔄 Different spinner styles
🎬 Fade/Slide/Scale transitions
📝 Custom loader templates
🔧 Progress tracking integration
