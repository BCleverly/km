/**
 * Theme management functionality
 */

class ThemeManager {
    constructor() {
        this.theme = this.getStoredTheme() || 'system';
        this.init();
    }

    init() {
        // Apply theme on page load
        this.applyTheme(this.theme);
        
        // Listen for system theme changes
        if (window.matchMedia) {
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                if (this.theme === 'system') {
                    this.applyTheme('system');
                }
            });
        }

        // Listen for theme changes from Livewire
        document.addEventListener('theme-changed', (event) => {
            this.setTheme(event.detail.theme);
        });
    }

    getStoredTheme() {
        return localStorage.getItem('theme');
    }

    setTheme(theme) {
        this.theme = theme;
        localStorage.setItem('theme', theme);
        this.applyTheme(theme);
    }

    applyTheme(theme) {
        const html = document.documentElement;
        
        // Remove existing theme classes
        html.classList.remove('dark');
        
        if (theme === 'system') {
            // Use system preference
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                html.classList.add('dark');
            }
        } else if (theme === 'dark') {
            html.classList.add('dark');
        }
        // For light theme, we don't add any class (default state)
        
        // Force a reflow to ensure the theme is applied
        html.offsetHeight;
    }

    getSystemTheme() {
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            return 'dark';
        }
        return 'light';
    }
}

// Initialize theme manager
window.themeManager = new ThemeManager();
