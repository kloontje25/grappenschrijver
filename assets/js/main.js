// Grappenschrijver JavaScript

// Smooth scrolling naar boven
function scrollToTop() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Toon confirmatie voordat sessie wordt gereset
function confirmReset() {
    return confirm('Weet je zeker dat je het proces wilt starten? Dit zal je huidge voortgang verwijderen.');
}

// Voeg dynamische validatie toe
document.addEventListener('DOMContentLoaded', function() {
    // Form validatie
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const textareas = form.querySelectorAll('textarea[required]');
            let isValid = true;
            
            textareas.forEach(textarea => {
                if (textarea.value.trim() === '') {
                    textarea.style.borderColor = '#ef4444';
                    isValid = false;
                } else {
                    textarea.style.borderColor = '#e2e8f0';
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Vul alstublieft alle velden in');
            }
        });
    });

    // Focus handlers voor inputs
    const inputs = document.querySelectorAll('input[type="text"], textarea');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.style.borderColor = '#6366f1';
        });
        
        input.addEventListener('blur', function() {
            this.style.borderColor = '#e2e8f0';
        });
    });
});

// Countdown of andere interactieve elementen
function showNotification(message, type = 'info') {
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.textContent = message;
    
    const container = document.querySelector('.container');
    container.insertBefore(alert, container.firstChild);
    
    setTimeout(() => {
        alert.style.opacity = '0';
        alert.style.transition = 'opacity 0.3s ease';
        setTimeout(() => alert.remove(), 300);
    }, 3000);
}
