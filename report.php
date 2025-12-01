<?php include 'includes/header.php'; ?>

<style>
    /* CSS for the Donut Chart */
    .donut-chart {
        width: 180px;
        height: 180px;
        border-radius: 50%;
        /* Default gradient, will be updated by JS */
        background: conic-gradient(#3b82f6 0% 100%);
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0 auto;
    }

    .donut-inner {
        width: 130px;
        height: 130px;
        background: white;
        border-radius: 50%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
</style>

<div class="app-container flex flex-col">

    <div class="bg-white p-6 sticky top-0 z-10">
        <h1 class="text-xl font-bold text-gray-800 text-center mb-4">আয়-ব্যয়ের রিপোর্ট</h1>

        <div class="bg-gray-100 p-1 rounded-lg flex text-sm font-semibold mb-4">
            <button id="btnWeek" onclick="setFilter('week')"
                class="flex-1 bg-white text-blue-600 py-2 rounded-md shadow-sm transition">এই সপ্তাহ</button>
            <button id="btnMonth" onclick="setFilter('month')"
                class="flex-1 text-gray-500 py-2 hover:text-gray-700 transition">এই মাস</button>
            <button id="btnYear" onclick="setFilter('year')"
                class="flex-1 text-gray-500 py-2 hover:text-gray-700 transition">এই বছর</button>
        </div>

        <div class="flex gap-2">
            <button onclick="exportToExcel()" class="flex-1 bg-green-50 text-green-600 py-2 rounded-lg text-sm font-bold border border-green-100 hover:bg-green-100 transition flex items-center justify-center gap-2">
                <i class="fas fa-file-excel"></i> এক্সেল
            </button>
            <button onclick="exportToPDF()" class="flex-1 bg-red-50 text-red-600 py-2 rounded-lg text-sm font-bold border border-red-100 hover:bg-red-100 transition flex items-center justify-center gap-2">
                <i class="fas fa-file-pdf"></i> পিডিএফ
            </button>
        </div>
    </div>

    <div class="flex-1 p-5 overflow-y-auto pb-24 no-scrollbar">

        <div class="mb-8 relative">
            <div class="donut-chart shadow-lg" id="chart">
                <div class="donut-inner">
                    <p class="text-xs text-gray-400">বর্তমান ব্যালেন্স</p>
                    <h2 class="text-xl font-bold text-gray-800" id="totalBalance">0</h2>
                </div>
            </div>

            <div class="flex justify-center gap-6 mt-6" id="chartLegend">
                <!-- Legend injected here -->
            </div>
        </div>

        <h3 class="font-bold text-gray-700 mb-3 text-sm">সংক্ষিপ্ত পরিসংখ্যান</h3>
        <div class="grid grid-cols-2 gap-3">

            <div class="bg-green-50 p-4 rounded-xl border border-green-100">
                <i class="fas fa-arrow-down text-green-500 mb-2 text-xl"></i>
                <p class="text-xs text-gray-500">মোট আয়</p>
                <h4 class="font-bold text-gray-800 text-lg" id="totalIncome">0</h4>
            </div>

            <div class="bg-red-50 p-4 rounded-xl border border-red-100">
                <i class="fas fa-arrow-up text-red-500 mb-2 text-xl"></i>
                <p class="text-xs text-gray-500">মোট ব্যয়</p>
                <h4 class="font-bold text-gray-800 text-lg" id="totalExpense">0</h4>
            </div>

            <div class="bg-purple-50 p-4 rounded-xl border border-purple-100">
                <i class="fas fa-calendar-check text-purple-500 mb-2 text-xl"></i>
                <p class="text-xs text-gray-500">কর্মদিবস</p>
                <h4 class="font-bold text-gray-800 text-lg" id="workDays">0 দিন</h4>
            </div>

            <div class="bg-orange-50 p-4 rounded-xl border border-orange-100">
                <i class="fas fa-file-invoice text-orange-500 mb-2 text-xl"></i>
                <p class="text-xs text-gray-500">মোট এন্ট্রি</p>
                <h4 class="font-bold text-gray-800 text-lg" id="totalEntries">0 টি</h4>
            </div>
        </div>

        <!-- Detailed Transactions Table -->
        <div class="mt-8" id="printableArea">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">উজ্জল এন্টারপ্রাইজ</h2>
                <p class="text-gray-500">আয়-ব্যয়ের হিসাব</p>
                <p class="text-xs text-gray-400 mt-1">তারিখ: <span id="reportDate"></span></p>
            </div>

            <h3 class="font-bold text-gray-700 mb-3 text-sm">বিস্তারিত লেনদেন</h3>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-500 font-bold border-b border-gray-100">
                        <tr>
                            <th class="p-3">তারিখ</th>
                            <th class="p-3">বিবরণ</th>
                            <th class="p-3 text-right">পরিমাণ</th>
                        </tr>
                    </thead>
                    <tbody id="transactionTableBody" class="divide-y divide-gray-50">
                        <!-- Rows will be injected here -->
                    </tbody>
                    <tfoot id="transactionTableFoot" class="bg-gray-50 font-bold border-t border-gray-100">
                        <!-- Footer injected here -->
                    </tfoot>
                </table>
            </div>
        </div>

    </div>

    <?php include 'includes/nav.php'; ?>
</div>

<?php include 'includes/footer.php'; ?>

<!-- Export Libraries -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
    // UI.init('report.html'); // Handled by PHP nav

    let currentFilter = 'week';

    // Async Init
    (async () => {
        await store.init();
        document.getElementById('reportDate').innerText = new Date().toLocaleDateString('bn-BD');
        renderStats();
    })();

    function setFilter(filter) {
        currentFilter = filter;

        // Update Buttons
        const btnWeek = document.getElementById('btnWeek');
        const btnMonth = document.getElementById('btnMonth');
        const btnYear = document.getElementById('btnYear');

        const activeClass = 'bg-white text-blue-600 shadow-sm';
        const inactiveClass = 'text-gray-500 hover:text-gray-700';

        // Reset all
        btnWeek.className = `flex-1 py-2 rounded-md transition ${inactiveClass}`;
        btnMonth.className = `flex-1 py-2 rounded-md transition ${inactiveClass}`;
        btnYear.className = `flex-1 py-2 rounded-md transition ${inactiveClass}`;

        // Set active
        if (filter === 'week') btnWeek.className = `flex-1 py-2 rounded-md transition ${activeClass}`;
        if (filter === 'month') btnMonth.className = `flex-1 py-2 rounded-md transition ${activeClass}`;
        if (filter === 'year') btnYear.className = `flex-1 py-2 rounded-md transition ${activeClass}`;

        renderStats();
    }

    function renderStats() {
        const allTransactions = store.getTransactions();
        let transactions = [];

        const now = new Date();

        if (currentFilter === 'week') {
            const oneWeekAgo = new Date();
            oneWeekAgo.setDate(now.getDate() - 7);
            transactions = allTransactions.filter(t => new Date(t.transaction_date) >= oneWeekAgo);
        } else if (currentFilter === 'month') {
            const thisMonth = now.getMonth();
            const thisYear = now.getFullYear();
            transactions = allTransactions.filter(t => {
                const d = new Date(t.transaction_date);
                return d.getMonth() === thisMonth && d.getFullYear() === thisYear;
            });
        } else if (currentFilter === 'year') {
            const thisYear = now.getFullYear();
            transactions = allTransactions.filter(t => new Date(t.transaction_date).getFullYear() === thisYear);
        }

        // Store filtered transactions globally for export
        window.filteredTransactions = transactions;

        const totalIncome = transactions
            .filter(t => t.type === 'income')
            .reduce((sum, t) => sum + parseFloat(t.amount), 0);

        const totalExpense = transactions
            .filter(t => t.type === 'expense')
            .reduce((sum, t) => sum + parseFloat(t.amount), 0);

        const balance = totalIncome - totalExpense;

        // Update Stats
        document.getElementById('totalBalance').innerText = UI.formatCurrency(balance);
        document.getElementById('totalIncome').innerText = UI.formatCurrency(totalIncome);
        document.getElementById('totalExpense').innerText = UI.formatCurrency(totalExpense);
        document.getElementById('totalEntries').innerText = transactions.length + ' টি';

        // Work Days
        const byDate = {};
        transactions.forEach(i => {
            const d = new Date(i.transaction_date).toDateString();
            byDate[d] = true;
        });
        document.getElementById('workDays').innerText = Object.keys(byDate).length + ' দিন';

        // Chart Logic (Income vs Expense)
        const totalVolume = totalIncome + totalExpense;

        if (totalVolume > 0) {
            const incomePercent = Math.round((totalIncome / totalVolume) * 100);

            // Update Chart Gradient
            const chart = document.getElementById('chart');
            chart.style.background = `conic-gradient(#10b981 0% ${incomePercent}%, #ef4444 ${incomePercent}% 100%)`;

            // Update Legend
            const legend = document.getElementById('chartLegend');
            legend.innerHTML = `
    <div class="flex items-center gap-2">
        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
        <span class="text-xs text-gray-600 font-bold">আয় (${incomePercent}%)</span>
    </div>
    <div class="flex items-center gap-2">
        <div class="w-3 h-3 bg-red-500 rounded-full"></div>
        <span class="text-xs text-gray-600 font-bold">ব্যয় (${100 - incomePercent}%)</span>
    </div>
`;
        } else {
            // Reset Chart if no data
            const chart = document.getElementById('chart');
            chart.style.background = `conic-gradient(#e5e7eb 0% 100%)`;
            document.getElementById('chartLegend').innerHTML = '<span class="text-xs text-gray-400">কোনো তথ্য নেই</span>';
        }

        // Render Table
        const tbody = document.getElementById('transactionTableBody');
        tbody.innerHTML = '';
        const tfoot = document.getElementById('transactionTableFoot');
        tfoot.innerHTML = '';
        
        if (transactions.length === 0) {
            tbody.innerHTML = '<tr><td colspan="3" class="p-4 text-center text-gray-400">কোনো তথ্য নেই</td></tr>';
        } else {
            transactions.forEach(t => {
                const colorClass = t.type === 'income' ? 'text-green-600' : 'text-red-600';
                const sign = t.type === 'income' ? '+' : '-';
                
                const row = `
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-3 text-gray-600 text-xs">
                            ${new Date(t.transaction_date).toLocaleDateString('bn-BD')}
                            <br><span class="text-[10px] text-gray-400">${t.category_name || 'General'}</span>
                        </td>
                        <td class="p-3 font-medium text-gray-700">${t.source}</td>
                        <td class="p-3 text-right font-bold ${colorClass}">
                            ${sign}${UI.formatCurrency(t.amount)}
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });

            // Render Footer with Total Balance
            tfoot.innerHTML = `
                <tr>
                    <td class="p-3 text-right font-bold text-gray-700" colspan="2">সর্বমোট ব্যালেন্স</td>
                    <td class="p-3 text-right font-bold text-blue-600 text-base">${UI.formatCurrency(balance)}</td>
                </tr>
            `;
        }
    }

    function exportToExcel() {
        if (!window.filteredTransactions || window.filteredTransactions.length === 0) {
            alert('এক্সপোর্ট করার জন্য কোনো ডাটা নেই');
            return;
        }

        const data = window.filteredTransactions.map(t => ({
            'তারিখ': new Date(t.transaction_date).toLocaleDateString('bn-BD'),
            'বিবরণ': t.source,
            'ক্যাটাগরি': t.category_name || 'General',
            'ধরন': t.type === 'income' ? 'আয়' : 'ব্যয়',
            'পরিমাণ': t.amount
        }));

        const ws = XLSX.utils.json_to_sheet(data);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Report");
        XLSX.writeFile(wb, `Report_${new Date().toISOString().split('T')[0]}.xlsx`);
    }

    function exportToPDF() {
        const element = document.getElementById('printableArea');
        const opt = {
            margin: 0.5,
            filename: `Report_${new Date().toISOString().split('T')[0]}.pdf`,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(element).save();
    }
</script>