// resources/js/info-support.js

// ========== MODAL INFO & SUPPORT ==========

function openInfoModal() {
    const modal = document.getElementById('infoModal');
    const modalContent = document.getElementById('infoModalContent');
    if (modal && modalContent) {
        modal.classList.remove('hidden');
        setTimeout(() => {
            modalContent.classList.remove('scale-95');
            modalContent.classList.add('scale-100');
        }, 10);
    }
}

function closeInfoModal() {
    const modal = document.getElementById('infoModal');
    const modalContent = document.getElementById('infoModalContent');
    if (modal && modalContent) {
        modalContent.classList.remove('scale-100');
        modalContent.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
}

function contactSupport() {
    // Utilisation du système de toast existant
    if (typeof showToast === 'function') {
        showToast('info', '📧 Redirection vers le support en cours...');
    }
    
    setTimeout(() => {
        // Redirection vers l'email de support
        window.location.href = 'mailto:support@smartdatavault.com?subject=Support%20SmartDataVault&body=Bonjour,%0D%0A%0D%0AJ\'ai besoin d\'aide concernant...';
    }, 1000);
}

// ========== GESTION DES ÉVÉNEMENTS ==========

document.addEventListener('DOMContentLoaded', function() {
    const infoModal = document.getElementById('infoModal');
    
    if (infoModal) {
        infoModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeInfoModal();
            }
        });
    }

    // Gestion de la touche Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const infoModal = document.getElementById('infoModal');
            if (infoModal && !infoModal.classList.contains('hidden')) {
                closeInfoModal();
            }
        }
    });
});