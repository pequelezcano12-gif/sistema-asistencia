// Auto-dismiss alerts after 4s
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.alert-dismissible').forEach(el => {
        setTimeout(() => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
            bsAlert.close();
        }, 4000);
    });

    // Confirm delete links
    document.querySelectorAll('[data-confirm]').forEach(el => {
        el.addEventListener('click', e => {
            if (!confirm(el.dataset.confirm)) e.preventDefault();
        });
    });

    // Preview foto antes de subir
    const fotoInput = document.querySelector('input[name="foto"]');
    if (fotoInput) {
        fotoInput.addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => {
                let preview = document.getElementById('foto-preview');
                if (!preview) {
                    preview = document.createElement('img');
                    preview.id = 'foto-preview';
                    preview.className = 'rounded mt-2';
                    preview.style.height = '80px';
                    this.parentNode.appendChild(preview);
                }
                preview.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });
    }
});
