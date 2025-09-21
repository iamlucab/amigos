{{-- PWA Install Button --}}
<button id="pwaInstallBtn" class="btn btn-success shadow position-fixed px-4 py-2"
    style="bottom: 100px; right: 16px; z-index: 9999; display: none; border-radius: 50px;">
    <i class="bi bi-download me-1"></i> Install App
</button>

<script>
let deferredPrompt;

// Show install button when browser is ready
window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    
    const installBtn = document.getElementById('pwaInstallBtn');
    if (installBtn) {
        installBtn.style.display = 'block';
        
        installBtn.addEventListener('click', async () => {
            if (!deferredPrompt) return;
            
            // Hide the install button
            installBtn.style.display = 'none';
            
            // Show the install prompt
            deferredPrompt.prompt();
            
            // Wait for the user's response
            const { outcome } = await deferredPrompt.userChoice;
            
            if (outcome === 'accepted') {
                console.log('[PWA] User accepted the install prompt');
                // Track installation
                localStorage.setItem('Amigos98CommunityAppInstalled', 'yes');
            } else {
                console.log('[PWA] User dismissed the install prompt');
                // Allow user to install later
                localStorage.setItem('Amigos98CommunityAppInstalled', 'dismissed');
            }
            
            // Clear the deferred prompt
            deferredPrompt = null;
        });
    }
});

// Hide install button if app is already installed
window.addEventListener('DOMContentLoaded', () => {
    const flag = localStorage.getItem('Amigos98CommunityAppInstalled');
    const installBtn = document.getElementById('pwaInstallBtn');
    
    if ((flag === 'yes' || flag === 'dismissed') && installBtn) {
        installBtn.style.display = 'none';
    }
});

// Hide install button when app is installed
window.addEventListener('appinstalled', () => {
    const installBtn = document.getElementById('pwaInstallBtn');
    if (installBtn) {
        installBtn.style.display = 'none';
    }
    
    // Track installation
    localStorage.setItem('Amigos98CommunityAppInstalled', 'yes');
    
    // Show success message if there's a toast system
    if (typeof bootstrap !== 'undefined' && document.getElementById('installSuccessToast')) {
        const toastEl = document.getElementById('installSuccessToast');
        const toast = new bootstrap.Toast(toastEl);
        toast.show();
    }
});
</script>