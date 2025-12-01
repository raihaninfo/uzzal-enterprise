/**
 * UI Utilities for Uzzal Enterprise
 */

const UI = {
    formatCurrency: (amount) => {
        return '৳ ' + amount.toLocaleString('bn-BD');
    },

    formatDate: (dateString) => {
        const options = { year: 'numeric', month: 'short', day: 'numeric' };
        return new Date(dateString).toLocaleDateString('bn-BD', options);
    },

    formatTime: (dateString) => {
        return new Date(dateString).toLocaleTimeString('bn-BD', { hour: '2-digit', minute: '2-digit' });
    },

    // renderBottomNav: (activePage) => {
    //     const nav = document.createElement('div');
    //     // Fixed position at bottom, max-width to match app container
    //     nav.className = 'bg-white border-t p-3 flex justify-around items-center fixed bottom-0 w-full max-w-[480px] text-gray-400 z-50';

    //     const items = [
    //         { icon: 'fa-home', label: 'হোম', page: 'index.html' },
    //         { icon: 'fa-history', label: 'পূর্বের হিসাব', page: 'history.html' },
    //         { icon: 'fa-book', label: 'বাকি', 'page': 'due.html' },
    //         { icon: 'fa-chart-pie', label: 'রিপোর্ট', page: 'report.html' }
    //     ];

    //     items.forEach(item => {
    //         const isActive = activePage === item.page;
    //         const colorClass = isActive ? 'text-blue-600' : 'hover:text-blue-600 transition';

    //         nav.innerHTML += `
    //             <button onclick="window.location.href='${item.page}'" class="flex flex-col items-center ${colorClass}">
    //                 <i class="fas ${item.icon} text-xl"></i>
    //                 <span class="text-[10px] mt-1">${item.label}</span>
    //             </button>
    //         `;
    //     });

    //     return nav;
    // },

    init: (activePage) => {
        // const container = document.querySelector('.app-container');
        // if (container && activePage !== 'login.html') {
        //     container.appendChild(UI.renderBottomNav(activePage));
        // }
        UI.initDarkMode();
    },

    toggleDarkMode: () => {
        const body = document.body;
        body.classList.toggle('dark');
        const isDark = body.classList.contains('dark');
        localStorage.setItem('darkMode', isDark);

        // Update toggle button if exists
        const toggle = document.getElementById('darkModeToggle');
        if (toggle) {
            toggle.checked = isDark;
        }
    },

    initDarkMode: () => {
        const isDark = localStorage.getItem('darkMode') === 'true';
        if (isDark) {
            document.body.classList.add('dark');
        }
        const toggle = document.getElementById('darkModeToggle');
        if (toggle) {
            toggle.checked = isDark;
        }
    }
};
