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

            <div class="bg-gray-100 p-1 rounded-lg flex text-sm font-semibold">
                <button id="btnWeek" onclick="setFilter('week')"
                    class="flex-1 bg-white text-blue-600 py-2 rounded-md shadow-sm transition">এই সপ্তাহ</button>
                <button id="btnMonth" onclick="setFilter('month')"
                    class="flex-1 text-gray-500 py-2 hover:text-gray-700 transition">এই মাস</button>
                <button id="btnYear" onclick="setFilter('year')"
                    class="flex-1 text-gray-500 py-2 hover:text-gray-700 transition">এই বছর</button>
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

        </div>

        <?php include 'includes/nav.php'; ?>
    </div>

<?php include 'includes/footer.php'; ?>

<script>
    // UI.init('report.html'); // Handled by PHP nav

    let currentFilter = 'week';

    // Async Init
    (async () => {
        await store.init();
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
    }
</script>