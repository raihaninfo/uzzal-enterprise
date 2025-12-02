<?php include 'includes/header.php'; ?>

    <div class="app-container flex flex-col">

        <!-- Header -->
        <div class="bg-blue-600 text-white p-6 pb-8 rounded-b-3xl shadow-lg z-10 relative">
            <div class="flex items-center justify-between mb-4">
                <button onclick="window.location.href='index.php'" class="text-white hover:text-gray-200">
                    <i class="fas fa-arrow-left text-xl"></i>
                </button>
                <h1 class="text-xl font-bold">পূর্বের হিসাব</h1>
                <div class="w-6"></div> <!-- Spacer for centering -->
            </div>

            <div class="text-center">
                <div class="inline-block bg-blue-700 px-4 py-1 rounded-full text-sm mb-2">
                    <i class="far fa-calendar-alt mr-2"></i>
                    <span id="monthDisplay">Loading...</span>
                </div>
                <h2 id="monthTotal" class="text-2xl font-bold">Loading...</h2>
            </div>
        </div>

        <div class="flex-1 p-5 overflow-y-auto pb-24 no-scrollbar">

            <div class="mb-4">
                <div class="relative">
                    <input type="text" id="searchInput" placeholder="খুঁজুন (নাম বা টাকার পরিমাণ)..."
                        class="w-full p-3 pl-10 rounded-xl border border-gray-200 focus:outline-none focus:border-blue-500 shadow-sm text-sm">
                    <i class="fas fa-search absolute left-3 top-3.5 text-gray-400"></i>
                </div>
            </div>

            <h3 class="font-bold text-gray-600 mb-4 text-sm uppercase">হিসাবের তালিকা</h3>

            <div id="historyList" class="flex flex-col gap-3">
                <!-- Items injected here -->
            </div>
        </div>

        <?php include 'includes/nav.php'; ?>
    </div>

<?php include 'includes/footer.php'; ?>

<script>
    // Async Init
    (async () => {
        await store.init();

        // Set Month Header
        const now = new Date();
        const monthName = now.toLocaleDateString('bn-BD', { month: 'long', year: 'numeric' });
        document.getElementById('monthDisplay').innerText = monthName;
        document.getElementById('monthTotal').innerText = 'ব্যালেন্স: ' + UI.formatCurrency(store.getBalance());

        renderHistory();
    })();

    // Search Listener
    document.getElementById('searchInput').addEventListener('input', (e) => {
        renderHistory(e.target.value);
    });

    function renderHistory(filterText = '') {
        const transactions = store.getTransactions();
        const list = document.getElementById('historyList');
        list.innerHTML = '';

        // Filter transactions
        const filtered = transactions.filter(item => {
            const search = filterText.toLowerCase();
            return item.source.toLowerCase().includes(search) ||
                item.amount.toString().includes(search) ||
                (item.category_name && item.category_name.toLowerCase().includes(search));
        });

        // Group by Date
        const groups = {};
        filtered.forEach(item => {
            const date = new Date(item.transaction_date).toLocaleDateString('bn-BD', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            if (!groups[date]) groups[date] = [];
            groups[date].push(item);
        });

        if (Object.keys(groups).length === 0) {
            list.innerHTML = `
                <div class="text-center py-10 text-gray-400">
                    <i class="fas fa-search text-4xl mb-2 opacity-50"></i>
                    <p class="text-sm">কোনো তথ্য পাওয়া যায়নি</p>
                </div>
            `;
            return;
        }

        Object.keys(groups).forEach(date => {
            const items = groups[date];

            // Create Date Header
            const dateHeader = document.createElement('div');
            dateHeader.className = 'flex items-center gap-2 mt-2 mb-1';
            dateHeader.innerHTML = `
                <div class="h-px bg-gray-200 flex-1"></div>
                <span class="text-xs font-bold text-gray-400">${date}</span>
                <div class="h-px bg-gray-200 flex-1"></div>
            `;
            list.appendChild(dateHeader);

            items.forEach(item => {
                const isIncome = item.type === 'income';
                const colorClass = isIncome ? 'text-green-600' : 'text-red-600';
                const icon = isIncome ? 'fa-arrow-down' : 'fa-arrow-up';
                
                const el = document.createElement('div');
                el.className = 'bg-white p-3 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center';
                el.innerHTML = `
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full ${isIncome ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600'} flex items-center justify-center">
                            <i class="fas ${icon}"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800 text-sm">${item.source}</h4>
                            <p class="text-xs text-gray-500">${item.category || 'General'}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <h4 class="font-bold ${colorClass}">৳ ${item.amount}</h4>
                        <div class="flex gap-3 mt-1 justify-end">
                            <button onclick="editItem(${item.id})" class="text-blue-500 hover:text-blue-700 text-xs"><i class="fas fa-edit"></i></button>
                            <button onclick="deleteItem(${item.id})" class="text-red-500 hover:text-red-700 text-xs"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                `;
                list.appendChild(el);
            });
        });
    }

    function editItem(id) {
        const transactions = store.getTransactions();
        const tx = transactions.find(t => t.id == id);
        if (tx) {
            const user = store.getUser();
            if (user.role !== 'admin' && tx.user_id != user.id) {
                UI.alert('অনুমতি নেই', 'এটি ডিলেট বা আপডেট করার আপনার অনুমতি নেই', 'error');
                return;
            }
            window.location.href = 'index.php?edit=' + id;
        }
    }

    async function deleteItem(id) {
        const transactions = store.getTransactions();
        const tx = transactions.find(t => t.id == id);
        
        if (tx) {
            const user = store.getUser();
            if (user.role !== 'admin' && tx.user_id != user.id) {
                UI.alert('অনুমতি নেই', 'এটি ডিলেট বা আপডেট করার আপনার অনুমতি নেই', 'error');
                return;
            }
            
            const result = await UI.confirm('মুছে ফেলতে চান?', 'আপনি কি নিশ্চিত যে আপনি এই হিসাবটি মুছে ফেলতে চান?');
            if (result.isConfirmed) {
                await store.deleteTransaction(id);
                renderHistory();
                // Update balance header
                document.getElementById('monthTotal').innerText = 'ব্যালেন্স: ' + UI.formatCurrency(store.getBalance());
                UI.toast('হিসাব মুছে ফেলা হয়েছে');
            }
        }
    }
</script>