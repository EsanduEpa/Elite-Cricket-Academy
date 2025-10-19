// Common Sidebar JavaScript for All Dashboards - Elite Cricket Academy
// Universal sidebar functionality that works across admin, player, coach, trainer, and shop dashboards

class UniversalSidebar {
    constructor(config = {}) {
        this.config = {
            sidebarId: config.sidebarId || 'sidebar',
            toggleId: config.toggleId || 'sidebarToggle',
            mainContentId: config.mainContentId || 'mainContent',
            collapsedWidth: config.collapsedWidth || '80px',
            expandedWidth: config.expandedWidth || '280px',
            breakpoint: config.breakpoint || 1024,
            enableOutsideClick: config.enableOutsideClick !== false,
            enableKeyboardShortcuts: config.enableKeyboardShortcuts !== false,
            animationDuration: config.animationDuration || 300,
            ...config
        };
        
        this.sidebar = null;
        this.toggle = null;
        this.mainContent = null;
        this.isCollapsed = false;
        this.isMobile = false;
        
        this.init();
    }
    
    init() {
        this.findElements();
        this.setupEventListeners();
        this.handleResize();
        this.setInitialState();
        
        if (this.config.enableKeyboardShortcuts) {
            this.setupKeyboardShortcuts();
        }
        
        console.log('Universal Sidebar initialized:', this.config);
    }
    
    findElements() {
        // Try multiple possible sidebar selectors
        const sidebarSelectors = [
            `#${this.config.sidebarId}`,
            '.sidebar',
            '.admin-sidebar',
            '.player-sidebar',
            '.coach-sidebar',
            '.trainer-sidebar',
            '.shop-sidebar'
        ];
        
        for (const selector of sidebarSelectors) {
            this.sidebar = document.querySelector(selector);
            if (this.sidebar) break;
        }
        
        // Try multiple possible toggle selectors
        const toggleSelectors = [
            `#${this.config.toggleId}`,
            '.sidebar-toggle',
            '[data-sidebar-toggle]'
        ];
        
        for (const selector of toggleSelectors) {
            this.toggle = document.querySelector(selector);
            if (this.toggle) break;
        }
        
        // Try multiple possible main content selectors
        const contentSelectors = [
            `#${this.config.mainContentId}`,
            '.main-content',
            '.content',
            '.dashboard-content'
        ];
        
        for (const selector of contentSelectors) {
            this.mainContent = document.querySelector(selector);
            if (this.mainContent) break;
        }
        
        console.log('Sidebar elements found:', {
            sidebar: !!this.sidebar,
            toggle: !!this.toggle,
            mainContent: !!this.mainContent
        });
    }
    
    setupEventListeners() {
        if (this.toggle) {
            this.toggle.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleSidebar();
            });
        }
        
        // Handle window resize
        window.addEventListener('resize', () => {
            this.handleResize();
        });
        
        // Handle outside clicks on mobile
        if (this.config.enableOutsideClick) {
            document.addEventListener('click', (e) => {
                this.handleOutsideClick(e);
            });
        }
        
        // Handle navigation links
        this.setupNavigation();
    }
    
    setupNavigation() {
        if (!this.sidebar) return;
        
        const navLinks = this.sidebar.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                this.handleNavClick(e, link);
            });
        });
    }
    
    setupKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            // Ctrl + B or Cmd + B to toggle sidebar
            if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
                e.preventDefault();
                this.toggleSidebar();
            }
            
            // Escape to close sidebar on mobile
            if (e.key === 'Escape' && this.isMobile && this.isOpen()) {
                this.closeSidebar();
            }
        });
    }
    
    handleResize() {
        const wasMobile = this.isMobile;
        this.isMobile = window.innerWidth <= this.config.breakpoint;
        
        if (wasMobile !== this.isMobile) {
            if (this.isMobile) {
                this.setupMobileMode();
            } else {
                this.setupDesktopMode();
            }
        }
    }
    
    setupMobileMode() {
        if (!this.sidebar) return;
        
        this.sidebar.classList.remove('collapsed');
        this.isCollapsed = false;
        
        if (this.mainContent) {
            this.mainContent.style.marginLeft = '0';
        }
    }
    
    setupDesktopMode() {
        if (!this.sidebar) return;
        
        this.sidebar.classList.remove('sidebar-open');
        
        if (this.mainContent) {
            const marginLeft = this.isCollapsed ? this.config.collapsedWidth : this.config.expandedWidth;
            this.mainContent.style.marginLeft = marginLeft;
        }
    }
    
    setInitialState() {
        // Check for saved state in localStorage
        const savedState = localStorage.getItem('sidebarCollapsed');
        if (savedState && !this.isMobile) {
            this.isCollapsed = JSON.parse(savedState);
            if (this.isCollapsed) {
                this.collapseSidebar(false);
            }
        }
    }
    
    toggleSidebar() {
        if (this.isMobile) {
            this.toggleMobileSidebar();
        } else {
            this.toggleDesktopSidebar();
        }
    }
    
    toggleMobileSidebar() {
        if (!this.sidebar) return;
        
        this.sidebar.classList.toggle('sidebar-open');
        
        // Trigger custom event
        this.dispatchEvent('sidebarToggle', {
            isOpen: this.sidebar.classList.contains('sidebar-open'),
            isMobile: true
        });
    }
    
    toggleDesktopSidebar() {
        if (this.isCollapsed) {
            this.expandSidebar();
        } else {
            this.collapseSidebar();
        }
    }
    
    collapseSidebar(animate = true) {
        if (!this.sidebar) return;
        
        this.sidebar.classList.add('collapsed');
        this.isCollapsed = true;
        
        if (this.mainContent) {
            if (animate) {
                this.animateContent(this.config.collapsedWidth);
            } else {
                this.mainContent.style.marginLeft = this.config.collapsedWidth;
            }
        }
        
        // Update toggle icon
        this.updateToggleIcon(true);
        
        // Save state
        localStorage.setItem('sidebarCollapsed', 'true');
        
        // Trigger custom event
        this.dispatchEvent('sidebarCollapse', { isCollapsed: true });
    }
    
    expandSidebar(animate = true) {
        if (!this.sidebar) return;
        
        this.sidebar.classList.remove('collapsed');
        this.isCollapsed = false;
        
        if (this.mainContent) {
            if (animate) {
                this.animateContent(this.config.expandedWidth);
            } else {
                this.mainContent.style.marginLeft = this.config.expandedWidth;
            }
        }
        
        // Update toggle icon
        this.updateToggleIcon(false);
        
        // Save state
        localStorage.setItem('sidebarCollapsed', 'false');
        
        // Trigger custom event
        this.dispatchEvent('sidebarExpand', { isCollapsed: false });
    }
    
    closeSidebar() {
        if (this.isMobile && this.sidebar) {
            this.sidebar.classList.remove('sidebar-open');
            this.dispatchEvent('sidebarClose', { isMobile: true });
        }
    }
    
    openSidebar() {
        if (this.isMobile && this.sidebar) {
            this.sidebar.classList.add('sidebar-open');
            this.dispatchEvent('sidebarOpen', { isMobile: true });
        }
    }
    
    isOpen() {
        if (!this.sidebar) return false;
        return this.isMobile ? 
            this.sidebar.classList.contains('sidebar-open') : 
            !this.sidebar.classList.contains('collapsed');
    }
    
    animateContent(marginLeft) {
        if (!this.mainContent) return;
        
        this.mainContent.style.transition = `margin-left ${this.config.animationDuration}ms cubic-bezier(0.25, 0.8, 0.25, 1)`;
        this.mainContent.style.marginLeft = marginLeft;
        
        setTimeout(() => {
            this.mainContent.style.transition = '';
        }, this.config.animationDuration);
    }
    
    updateToggleIcon(isCollapsed) {
        if (!this.toggle) return;
        
        const icon = this.toggle.querySelector('i');
        if (icon) {
            if (isCollapsed) {
                icon.className = 'fas fa-angle-right';
            } else {
                icon.className = 'fas fa-angle-left';
            }
        }
    }
    
    handleOutsideClick(e) {
        if (!this.isMobile || !this.sidebar) return;
        
        // Don't close if clicking on sidebar or toggle
        if (this.sidebar.contains(e.target) || 
            (this.toggle && this.toggle.contains(e.target))) {
            return;
        }
        
        // Close sidebar if it's open
        if (this.sidebar.classList.contains('sidebar-open')) {
            this.closeSidebar();
        }
    }
    
    handleNavClick(e, link) {
        // Remove active class from all links
        const allLinks = this.sidebar.querySelectorAll('.nav-link');
        allLinks.forEach(l => {
            l.classList.remove('active');
            l.parentElement.classList.remove('active');
        });
        
        // Add active class to clicked link
        link.classList.add('active');
        link.parentElement.classList.add('active');
        
        // Close mobile sidebar after navigation
        if (this.isMobile) {
            setTimeout(() => {
                this.closeSidebar();
            }, 150);
        }
        
        // Trigger custom event
        this.dispatchEvent('navClick', { 
            link, 
            href: link.href,
            text: link.textContent.trim()
        });
    }
    
    dispatchEvent(eventName, detail = {}) {
        const event = new CustomEvent(`sidebar:${eventName}`, {
            detail: {
                sidebar: this.sidebar,
                config: this.config,
                ...detail
            }
        });
        document.dispatchEvent(event);
    }
    
    // Public API methods
    destroy() {
        // Remove event listeners and cleanup
        if (this.toggle) {
            this.toggle.removeEventListener('click', this.toggleSidebar);
        }
        window.removeEventListener('resize', this.handleResize);
        document.removeEventListener('click', this.handleOutsideClick);
        document.removeEventListener('keydown', this.setupKeyboardShortcuts);
    }
    
    getState() {
        return {
            isCollapsed: this.isCollapsed,
            isMobile: this.isMobile,
            isOpen: this.isOpen()
        };
    }
}

// Auto-initialize for common dashboard patterns
document.addEventListener('DOMContentLoaded', function() {
    // Try to detect which dashboard type we're on and initialize accordingly
    const dashboardConfigs = [
        {
            sidebarId: 'adminSidebar',
            toggleId: 'sidebarToggle',
            mainContentId: 'mainContent'
        },
        {
            sidebarId: 'playerSidebar',
            toggleId: 'sidebarToggle',
            mainContentId: 'mainContent'
        },
        {
            sidebarId: 'coachSidebar',
            toggleId: 'sidebarToggle',
            mainContentId: 'mainContent'
        },
        {
            sidebarId: 'trainerSidebar',
            toggleId: 'sidebarToggle',
            mainContentId: 'mainContent'
        },
        {
            sidebarId: 'shopSidebar',
            toggleId: 'sidebarToggle',
            mainContentId: 'mainContent'
        }
    ];
    
    // Try each configuration until we find one that works
    for (const config of dashboardConfigs) {
        const sidebar = document.getElementById(config.sidebarId);
        if (sidebar) {
            window.universalSidebar = new UniversalSidebar(config);
            console.log(`Initialized Universal Sidebar for: ${config.sidebarId}`);
            break;
        }
    }
    
    // Fallback: try to find any sidebar and initialize with default config
    if (!window.universalSidebar) {
        const anySidebar = document.querySelector('.sidebar, .admin-sidebar, .player-sidebar, .coach-sidebar, .trainer-sidebar, .shop-sidebar');
        if (anySidebar) {
            window.universalSidebar = new UniversalSidebar();
            console.log('Initialized Universal Sidebar with fallback detection');
        }
    }
});

// Export for manual initialization if needed
window.UniversalSidebar = UniversalSidebar;

// Utility functions for dashboard-specific needs
window.SidebarUtils = {
    // Set active navigation item by href
    setActiveNav: function(href) {
        const links = document.querySelectorAll('.nav-link');
        links.forEach(link => {
            link.classList.remove('active');
            link.parentElement.classList.remove('active');
            if (link.href === href || link.getAttribute('href') === href) {
                link.classList.add('active');
                link.parentElement.classList.add('active');
            }
        });
    },
    
    // Set active navigation item by text content
    setActiveNavByText: function(text) {
        const links = document.querySelectorAll('.nav-link');
        links.forEach(link => {
            link.classList.remove('active');
            link.parentElement.classList.remove('active');
            if (link.textContent.trim().toLowerCase().includes(text.toLowerCase())) {
                link.classList.add('active');
                link.parentElement.classList.add('active');
            }
        });
    },
    
    // Add notification badge to nav item
    addNotificationBadge: function(navText, count) {
        const links = document.querySelectorAll('.nav-link');
        links.forEach(link => {
            if (link.textContent.trim().toLowerCase().includes(navText.toLowerCase())) {
                let badge = link.querySelector('.badge');
                if (!badge) {
                    badge = document.createElement('span');
                    badge.className = 'badge';
                    link.appendChild(badge);
                }
                badge.textContent = count;
                badge.style.display = count > 0 ? 'inline-block' : 'none';
            }
        });
    },
    
    // Remove notification badge
    removeNotificationBadge: function(navText) {
        const links = document.querySelectorAll('.nav-link');
        links.forEach(link => {
            if (link.textContent.trim().toLowerCase().includes(navText.toLowerCase())) {
                const badge = link.querySelector('.badge');
                if (badge) {
                    badge.remove();
                }
            }
        });
    }
};