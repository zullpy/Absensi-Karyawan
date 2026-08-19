import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.store('darkMode', {
    on: localStorage.getItem('hadirin-theme') === 'dark' || (!localStorage.getItem('hadirin-theme') && window.matchMedia('(prefers-color-scheme: dark)').matches),
    toggle() {
        this.on = !this.on;
        if (this.on) {
            document.documentElement.classList.add('dark');
            localStorage.setItem('hadirin-theme', 'dark');
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('hadirin-theme', 'light');
        }
    }
});

Alpine.start();
