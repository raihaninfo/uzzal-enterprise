<?php include 'includes/header.php'; ?>

    <div class="app-container flex flex-col">

        <div class="bg-blue-600 text-white p-6 rounded-b-3xl shadow-lg z-10">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-xl font-bold"><i class="fas fa-wallet mr-2"></i>আমার হিসাব</h1>
                <button onclick="window.location.href='profile.php'"
                    class="bg-blue-500 p-2 rounded-full hover:bg-blue-400"><i class="fas fa-cog"></i></button>
            </div>

            <div class="flex justify-center mb-4">
                <div class="bg-blue-700 px-4 py-1 rounded-full text-sm flex items-center gap-2">
                    <i class="far fa-calendar-alt"></i>
                    <span id="currentDate">Loading...</span>
                </div>
            </div>

            <!-- Balance Cards -->
            <div class="grid grid-cols-3 gap-2 text-center mt-2">
                <div class="bg-blue-700/50 p-2 rounded-lg backdrop-blur-sm">
                    <p class="text-blue-200 text-xs">আয়</p>
                    <h3 class="font-bold text-sm text-green-300"> <span id="todayIncome">0</span></h3>
                </div>
                <div class="bg-blue-700/50 p-2 rounded-lg backdrop-blur-sm">
                    <p class="text-blue-200 text-xs">ব্যয়</p>
                    <h3 class="font-bold text-sm text-red-300"> <span id="todayExpense">0</span></h3>
                </div>
                <div class="bg-blue-700/50 p-2 rounded-lg backdrop-blur-sm border border-blue-400">
                    <p class="text-blue-200 text-xs">ব্যালেন্স</p>
                    <h3 class="font-bold text-sm"> <span id="todayBalance">0</span></h3>
                </div>
            </div>
        </div>

        <div class="flex-1 p-5 overflow-y-auto pb-24 no-scrollbar">

            <div class="bg-white border border-gray-100 p-4 rounded-xl shadow-sm mb-6">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-gray-700 font-semibold text-sm">নতুন হিসাব যোগ করুন</h3>
                    <div class="flex bg-gray-100 rounded-lg p-1">
                        <button id="btnIncome" onclick="setType('income')"
                            class="px-3 py-1 rounded-md bg-green-500 text-white shadow-sm transition text-xs">আয়</button>
                        <button id="btnExpense" onclick="setType('expense')"
                            class="px-3 py-1 rounded-md text-gray-500 hover:text-gray-700 transition text-xs">ব্যয়</button>
                    </div>
                </div>

                <div class="flex flex-col gap-3">
                    <!-- Category Select -->
                    <div class="flex gap-2">
                        <select id="categoryInput"
                            class="w-full bg-gray-50 p-3 rounded-lg text-sm border focus:outline-none focus:border-blue-500">
                            <!-- Options injected by JS -->
                        </select>
                        <button onclick="addNewCategory()"
                            class="bg-gray-100 px-3 rounded-lg text-gray-500 hover:bg-gray-200">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>

                    <input type="text" id="sourceInput" placeholder="বিবরণ (যেমন: দোকান বিক্রি)"
                        class="w-full bg-gray-50 p-3 rounded-lg text-sm border focus:outline-none focus:border-blue-500">

                    <div class="flex gap-2">
                        <input type="number" id="amountInput" placeholder="টাকার পরিমাণ"
                            class="w-full bg-gray-50 p-3 rounded-lg text-sm border focus:outline-none focus:border-blue-500">
                        <button id="addBtn" onclick="handleTransactionSubmit()"
                            class="bg-green-600 text-white px-6 py-3 rounded-lg font-bold shadow-md active:scale-95 transition hover:bg-green-700">
                            <i class="fas fa-plus" id="addBtnIcon"></i>
                            <span id="addBtnText" class="hidden">Update</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex justify-between items-end mb-3">
                <h3 class="font-bold text-gray-800">আজকের তালিকা</h3>
                <button onclick="window.location.href='history.php'" class="text-xs text-blue-600 font-semibold">সব
                    দেখুন</button>
            </div>

            <div id="incomeList" class="flex flex-col gap-3">
                <!-- Items will be injected here -->
            </div>

            <div id="emptyState" class="text-center py-10 text-gray-400 hidden">
                <i class="fas fa-receipt text-4xl mb-2 opacity-50"></i>
                <p class="text-sm">আজকের কোনো হিসাব নেই</p>
            </div>
        </div>

        <?php include 'includes/nav.php'; ?>

    </div>

<?php include 'includes/footer.php'; ?>

<script>
    // Initialize UI
    // UI.init('index.html'); // Removed, handled by PHP nav
    document.getElementById('currentDate').innerText = UI.formatDate(new Date());

    let currentType = 'income';

    // Async Init
    (async () => {
        await store.init();
        renderToday();
        updateCategories();
    })();

    function setType(type) {
        currentType = type;
        const btnIncome = document.getElementById('btnIncome');
        const btnExpense = document.getElementById('btnExpense');
        const addBtn = document.getElementById('addBtn');
        const sourceInput = document.getElementById('sourceInput');

        if (type === 'income') {
            btnIncome.className = 'px-3 py-1 rounded-md bg-green-500 text-white shadow-sm transition';
            btnExpense.className = 'px-3 py-1 rounded-md text-gray-500 hover:text-gray-700 transition';
            if (!editId) { // Only change add button color if not in edit mode
                addBtn.classList.remove('bg-red-600', 'hover:bg-red-700');
                addBtn.classList.add('bg-green-600', 'hover:bg-green-700');
            }
            sourceInput.placeholder = 'বিবরণ (যেমন: দোকান বিক্রি)';
        } else {
            btnIncome.className = 'px-3 py-1 rounded-md text-gray-500 hover:text-gray-700 transition';
            btnExpense.className = 'px-3 py-1 rounded-md bg-red-500 text-white shadow-sm transition';
            if (!editId) { // Only change add button color if not in edit mode
                addBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
                addBtn.classList.add('bg-red-600', 'hover:bg-red-700');
            }
            sourceInput.placeholder = 'বিবরণ (যেমন: নাস্তা, ভাড়া)';
        }
    }

    function renderToday() {
        const stats = store.getTodayStats();
        const transactions = store.getTransactions();
        const today = new Date().toDateString();
        const todaysTx = transactions.filter(item => new Date(item.transaction_date).toDateString() === today);

        const list = document.getElementById('incomeList');
        const emptyState = document.getElementById('emptyState');

        list.innerHTML = '';

        if (todaysTx.length === 0) {
            emptyState.classList.remove('hidden');
        } else {
            emptyState.classList.add('hidden');
            todaysTx.forEach(item => {
                const isIncome = item.type === 'income';
                const colorClass = isIncome ? 'text-green-600' : 'text-red-600';
                
                const el = document.createElement('div');
                el.className = 'bg-white p-3 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center';
                el.innerHTML = `
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full ${isIncome ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600'} flex items-center justify-center">
                            <i class="fas ${isIncome ? 'fa-arrow-down' : 'fa-arrow-up'}"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800 text-sm">${item.source}</h4>
                            <p class="text-xs text-gray-500">${item.category || 'General'}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <h4 class="font-bold ${colorClass}">৳ ${item.amount}</h4>
                        <p class="text-[10px] text-gray-400 mt-1"><i class="far fa-clock"></i> ${new Date(item.transaction_date).toLocaleTimeString('bn-BD', {hour: '2-digit', minute:'2-digit'})}</p>
                    </div>
                `;
                list.appendChild(el);
            });
        }
        
        document.getElementById('todayIncome').innerText = UI.formatCurrency(stats.income);
        document.getElementById('todayExpense').innerText = UI.formatCurrency(stats.expense);
        document.getElementById('todayBalance').innerText = UI.formatCurrency(stats.balance);
    }

    let editId = null;

    async function handleTransactionSubmit() {
        if (editId) {
            await updateTransaction();
        } else {
            await addTransaction();
        }
    }

    async function addTransaction() {
        const sourceInput = document.getElementById('sourceInput');
        const amountInput = document.getElementById('amountInput');
        const categoryInput = document.getElementById('categoryInput');

        const source = sourceInput.value.trim();
        const amount = amountInput.value.trim();
        const category = categoryInput.value;

        if (source && amount) {
            await store.addTransaction(source, amount, currentType, category);
            sourceInput.value = '';
            amountInput.value = '';
            renderToday();
        } else {
            UI.alert('ত্রুটি', 'টাকার পরিমান ও বিবরন উল্লেখ করুন', 'error');
        }
    }

    async function updateTransaction() {
        const sourceInput = document.getElementById('sourceInput');
        const amountInput = document.getElementById('amountInput');
        const categoryInput = document.getElementById('categoryInput');

        const source = sourceInput.value.trim();
        const amount = amountInput.value.trim();
        const category = categoryInput.value;

        if (source && amount && editId) {
            await store.updateTransaction(editId, { source, amount, category });

            // Reset UI
            sourceInput.value = '';
            amountInput.value = '';
            editId = null;

            const addBtn = document.getElementById('addBtn');
            const addBtnText = document.getElementById('addBtnText');
            const addBtnIcon = document.getElementById('addBtnIcon');

            addBtnText.classList.add('hidden');
            addBtnIcon.classList.remove('hidden');

            // Reset button color based on current type
            setType(currentType);

            // Remove query param
            window.history.replaceState({}, document.title, window.location.pathname);

            renderToday();
        }
    }

    function checkForEdit() {
        const urlParams = new URLSearchParams(window.location.search);
        const editParam = urlParams.get('edit');

        if (editParam) {
            editId = parseInt(editParam); // Ensure ID is integer
            const transactions = store.getTransactions();
            const tx = transactions.find(t => t.id == editId); // Loose equality for string/int match

            if (tx) {
                // Permission Check
                const user = store.getUser();
                if (user.role !== 'admin' && tx.user_id != user.id) {
                    UI.alert('অনুমতি নেই', 'এটি ডিলেট বা আপডেট করার আপনার অনুমতি নেই', 'error');
                    // Clear URL param
                    window.history.replaceState({}, document.title, window.location.pathname);
                    return;
                }

                document.getElementById('sourceInput').value = tx.source;
                document.getElementById('amountInput').value = tx.amount;
                document.getElementById('categoryInput').value = tx.category_name || tx.category || 'General';

                // Set Type
                setType(tx.type);

                // Update Button UI
                const addBtn = document.getElementById('addBtn');
                const addBtnText = document.getElementById('addBtnText');
                const addBtnIcon = document.getElementById('addBtnIcon');

                addBtnText.innerText = 'Save';
                addBtnText.classList.remove('hidden');
                addBtnIcon.classList.add('hidden');

                addBtn.classList.remove('bg-green-600', 'bg-red-600', 'hover:bg-green-700', 'hover:bg-red-700');
                addBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
            }
        }
    }

    async function updateCategories() {
        const categories = store.data.categories; // Already loaded in init
        const select = document.getElementById('categoryInput');
        select.innerHTML = '';
        categories.forEach(cat => {
            const opt = document.createElement('option');
            opt.value = cat;
            opt.innerText = cat;
            select.appendChild(opt);
        });

        // Check for edit after categories are loaded
        checkForEdit();
    }

    async function addNewCategory() {
        const { value: name } = await Swal.fire({
            title: 'নতুন ক্যাটাগরি',
            input: 'text',
            inputLabel: 'ক্যাটাগরির নাম লিখুন',
            showCancelButton: true,
            confirmButtonText: 'যোগ করুন',
            cancelButtonText: 'বাতিল',
            inputValidator: (value) => {
                if (!value) {
                    return 'নাম লিখতে হবে!';
                }
            }
        });

        if (name) {
            if (await store.addCategory(name)) {
                await updateCategories();
                document.getElementById('categoryInput').value = name;
                UI.toast('ক্যাটাগরি যোগ করা হয়েছে');
            } else {
                UI.alert('ত্রুটি', 'এই ক্যাটাগরি ইতিমধ্যে আছে!', 'error');
            }
        }
    }
</script>