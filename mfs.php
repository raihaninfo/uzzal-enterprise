<?php include 'includes/header.php'; ?>

    <div class="app-container flex flex-col h-screen bg-gray-50">

        <!-- Header Section -->
        <div class="bg-pink-600 text-white p-6 pb-12 rounded-b-3xl shadow-lg z-10 relative">
            <div class="flex justify-between items-center mb-6">
                <button onclick="window.location.href='index.php'" class="bg-pink-500/50 p-2 rounded-full hover:bg-pink-500 transition">
                    <i class="fas fa-arrow-left"></i>
                </button>
                <h1 class="text-xl font-bold">MFS অ্যাকাউন্ট</h1>
                <div class="w-8"></div>
            </div>

            <!-- Total Balance Summary -->
            <div class="text-center mb-6">
                <p class="text-pink-200 text-sm mb-1">মোট ব্যালেন্স</p>
                <h3 class="font-bold text-4xl text-white">৳ <span id="totalMFSBalance">0</span></h3>
            </div>
        </div>

        <!-- Add Account Button -->
        <div class="px-5 -mt-8 z-20 relative mb-4 text-center">
            <button onclick="openAddModal()" 
                class="bg-white text-pink-600 px-6 py-3 rounded-full font-bold shadow-lg shadow-pink-200 active:scale-95 transition hover:bg-gray-50 flex items-center gap-2 mx-auto">
                <i class="fas fa-plus-circle"></i> নতুন অ্যাকাউন্ট যুক্ত করুন
            </button>
        </div>

        <!-- Accounts List -->
        <div class="flex-1 overflow-y-auto px-5 pb-24 no-scrollbar">
            <div id="mfsList" class="flex flex-col gap-3">
                <!-- Items injected here -->
            </div>
        </div>

        <!-- Add Account Modal -->
        <div id="mfsModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-5 backdrop-blur-sm">
            <div class="bg-white w-full max-w-sm rounded-3xl p-6 shadow-2xl transform transition-all scale-100">
                <h3 class="text-xl font-bold text-gray-800 mb-4">নতুন অ্যাকাউন্ট</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="text-xs text-gray-500 ml-1">প্রোভাইডার</label>
                        <div class="grid grid-cols-3 gap-2 mt-1">
                            <button type="button" onclick="selectProvider('Bkash')" class="provider-btn p-2 rounded-xl border-2 border-transparent bg-gray-100 hover:bg-pink-50 text-gray-600 font-bold text-sm transition" data-val="Bkash">
                                Bkash
                            </button>
                            <button type="button" onclick="selectProvider('Nagad')" class="provider-btn p-2 rounded-xl border-2 border-transparent bg-gray-100 hover:bg-orange-50 text-gray-600 font-bold text-sm transition" data-val="Nagad">
                                Nagad
                            </button>
                            <button type="button" onclick="selectProvider('Rocket')" class="provider-btn p-2 rounded-xl border-2 border-transparent bg-gray-100 hover:bg-purple-50 text-gray-600 font-bold text-sm transition" data-val="Rocket">
                                Rocket
                            </button>
                        </div>
                        <input type="hidden" id="mfsProvider">
                    </div>

                    <!-- Number field hidden as per user preference, but kept in code if needed later -->
                    <input type="hidden" id="mfsNumber" value="">

                    <div>
                        <label class="text-xs text-gray-500 ml-1">বর্তমান ব্যালেন্স</label>
                        <input type="number" id="mfsInitialBalance" placeholder="0.00" 
                            class="w-full bg-gray-50 p-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-pink-100 focus:border-pink-500 outline-none font-bold text-lg">
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button onclick="closeModal()" class="flex-1 py-3 rounded-xl bg-gray-100 text-gray-600 font-bold hover:bg-gray-200 transition">বাতিল</button>
                    <button onclick="saveMFS()" class="flex-1 py-3 rounded-xl bg-pink-600 text-white font-bold shadow-lg shadow-pink-200 hover:bg-pink-700 transition">সেভ করুন</button>
                </div>
            </div>
        </div>

        <!-- Transaction Modal -->
        <div id="updateBalanceModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-5 backdrop-blur-sm">
            <div class="bg-white w-full max-w-sm rounded-3xl p-6 shadow-2xl transform transition-all scale-100">
                <h3 class="text-xl font-bold text-gray-800 mb-1" id="updateModalTitle">লেনদেন</h3>
                <p class="text-xs text-gray-500 mb-4" id="updateModalSubtitle">...</p>
                
                <input type="hidden" id="updateAccountId">
                <input type="hidden" id="txType" value="">
                
                <div class="bg-pink-50 p-4 rounded-2xl mb-6 text-center">
                    <p class="text-xs text-pink-400 mb-1">বর্তমান ব্যালেন্স</p>
                    <h2 class="text-2xl font-bold text-pink-600">৳ <span id="currentBalanceDisplay">0</span></h2>
                </div>

                <!-- Option Selection -->
                <div id="txOptions" class="grid grid-cols-2 gap-4 mb-4">
                    <button onclick="selectTxType('out')" class="p-4 rounded-2xl border-2 border-red-100 bg-red-50 text-red-600 hover:bg-red-100 transition flex flex-col items-center gap-2">
                        <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center shadow-sm">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                        <span class="font-bold text-sm">Send Money</span>
                    </button>
                    <button onclick="selectTxType('in')" class="p-4 rounded-2xl border-2 border-green-100 bg-green-50 text-green-600 hover:bg-green-100 transition flex flex-col items-center gap-2">
                        <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center shadow-sm">
                            <i class="fas fa-download"></i>
                        </div>
                        <span class="font-bold text-sm">Receive Money</span>
                    </button>
                </div>

                <!-- Input Form (Hidden initially) -->
                <div id="txInputForm" class="hidden space-y-4">
                    <div>
                        <label class="text-xs text-gray-500 ml-1" id="amountLabel">টাকার পরিমাণ</label>
                        <input type="number" id="txAmountInput" placeholder="0.00" 
                            class="w-full bg-gray-50 p-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-pink-100 focus:border-pink-500 outline-none font-bold text-2xl text-center text-gray-800">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 ml-1">নোট (অপশনাল)</label>
                        <input type="text" id="txNoteInput" placeholder="কি বাবদ?" 
                            class="w-full bg-gray-50 p-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-pink-100 outline-none">
                    </div>
                    
                    <div class="flex gap-3 mt-2">
                        <button onclick="resetTxModal()" class="flex-1 py-3 rounded-xl bg-gray-100 text-gray-600 font-bold hover:bg-gray-200 transition">পেছনে যান</button>
                        <button onclick="confirmTransaction()" class="flex-1 py-3 rounded-xl bg-pink-600 text-white font-bold shadow-lg shadow-pink-200 hover:bg-pink-700 transition">নিশ্চিত করুন</button>
                    </div>
                </div>

                <div class="mt-4 text-center">
                    <button onclick="closeUpdateModal()" class="text-xs text-gray-400 hover:text-gray-600">বন্ধ করুন</button>
                </div>
            </div>
        </div>

        <!-- Transaction History Modal -->
        <div id="txModal" class="fixed inset-0 bg-black/50 z-50 hidden flex flex-col justify-end sm:justify-center backdrop-blur-sm">
            <div class="bg-white w-full sm:max-w-md sm:mx-auto sm:rounded-3xl rounded-t-3xl p-0 shadow-2xl h-[85vh] flex flex-col">
                <div class="p-5 border-b flex justify-between items-center bg-gray-50 rounded-t-3xl">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">লেনদেন বিবরণী</h3>
                    </div>
                    <button onclick="closeTxModal()" class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="flex-1 overflow-y-auto p-5 bg-gray-50" id="txList"></div>
            </div>
        </div>

        <?php include 'includes/nav.php'; ?>
    </div>

<?php include 'includes/footer.php'; ?>

<script>
    let currentUser = null;

    (async () => {
        await store.init();
        currentUser = store.getUser();
        await renderMFS();
    })();

    async function renderMFS() {
        const accounts = await store.getMFSAccounts();
        const list = document.getElementById('mfsList');
        const totalEl = document.getElementById('totalMFSBalance');
        
        let total = 0;
        list.innerHTML = '';

        accounts.forEach(acc => {
            total += parseFloat(acc.balance);
            
            const isOwner = currentUser && currentUser.id == acc.user_id;
            const providerColors = {
                'Bkash': 'bg-pink-500',
                'Nagad': 'bg-orange-500',
                'Rocket': 'bg-purple-500'
            };
            const color = providerColors[acc.provider] || 'bg-gray-500';

            const el = document.createElement('div');
            el.className = 'bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex justify-between items-center transition hover:shadow-md cursor-pointer';
            
            // Only allow click if owner
            if (isOwner) {
                el.onclick = () => openUpdateModal(acc);
            } else {
                el.classList.add('opacity-90'); // Slight visual cue
            }

            // Format Last Updated
            let lastUpdatedText = 'নতুন';
            if (acc.last_updated) {
                const date = new Date(acc.last_updated);
                lastUpdatedText = date.toLocaleDateString('bn-BD') + ' ' + date.toLocaleTimeString('bn-BD', { hour: '2-digit', minute: '2-digit' });
            }

            el.innerHTML = `
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full ${color} text-white flex items-center justify-center font-bold text-xs shadow-md">
                        ${acc.provider}
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800 text-sm">
                            ${acc.user_name}-এর ${acc.provider}
                        </h4>
                        <p class="text-[10px] text-gray-400">ব্যালেন্স আছে</p>
                    </div>
                </div>
                <div class="text-right">
                    <h4 class="font-bold text-gray-800 text-lg">৳ ${acc.balance}</h4>
                    <p class="text-[10px] text-gray-400 mb-1">${lastUpdatedText}</p>
                    <div class="flex gap-2 justify-end">
                        <button onclick="event.stopPropagation(); viewHistory(${acc.id})" class="text-[10px] text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full hover:bg-gray-200">ইতিহাস</button>
                        ${isOwner ? '<span class="text-[10px] text-blue-500 bg-blue-50 px-2 py-0.5 rounded-full">আপডেট করুন</span>' : ''}
                    </div>
                </div>
            `;
            list.appendChild(el);
        });

        totalEl.innerText = UI.formatCurrency(total);
    }

    function selectProvider(val) {
        document.getElementById('mfsProvider').value = val;
        document.querySelectorAll('.provider-btn').forEach(btn => {
            if (btn.dataset.val === val) {
                btn.classList.add('border-pink-500', 'ring-2', 'ring-pink-100');
            } else {
                btn.classList.remove('border-pink-500', 'ring-2', 'ring-pink-100');
            }
        });
    }

    function openAddModal() {
        document.getElementById('mfsInitialBalance').value = '';
        selectProvider('Bkash');
        document.getElementById('mfsModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('mfsModal').classList.add('hidden');
    }

    async function saveMFS() {
        const provider = document.getElementById('mfsProvider').value;
        const number = document.getElementById('mfsNumber').value;
        const balance = document.getElementById('mfsInitialBalance').value;

        if (!provider) {
            UI.toast('প্রোভাইডার সিলেক্ট করুন');
            return;
        }

        const result = await store.addMFSAccount(provider, number, balance);
        if (result && result.id) {
            UI.toast('অ্যাকাউন্ট যুক্ত হয়েছে');
            closeModal();
            await renderMFS();
        } else {
            UI.alert('ত্রুটি', result.message || 'যুক্ত করা যায়নি', 'error');
        }
    }

    // Transaction Logic
    let currentEditingAccount = null;

    function openUpdateModal(acc) {
        currentEditingAccount = acc;
        document.getElementById('updateModalTitle').innerText = `${acc.user_name}-এর ${acc.provider}`;
        document.getElementById('updateModalSubtitle').innerText = 'লেনদেন করুন';
        document.getElementById('updateAccountId').value = acc.id;
        document.getElementById('currentBalanceDisplay').innerText = acc.balance;
        
        resetTxModal();
        document.getElementById('updateBalanceModal').classList.remove('hidden');
    }

    function resetTxModal() {
        document.getElementById('txOptions').classList.remove('hidden');
        document.getElementById('txInputForm').classList.add('hidden');
        document.getElementById('txAmountInput').value = '';
        document.getElementById('txNoteInput').value = '';
        document.getElementById('txType').value = '';
    }

    function selectTxType(type) {
        document.getElementById('txType').value = type;
        document.getElementById('txOptions').classList.add('hidden');
        document.getElementById('txInputForm').classList.remove('hidden');
        
        const label = type === 'out' ? 'Send Money (পরিমাণ)' : 'Receive Money (পরিমাণ)';
        document.getElementById('amountLabel').innerText = label;
        document.getElementById('txAmountInput').focus();
    }

    function closeUpdateModal() {
        document.getElementById('updateBalanceModal').classList.add('hidden');
        currentEditingAccount = null;
    }

    async function confirmTransaction() {
        const id = document.getElementById('updateAccountId').value;
        const type = document.getElementById('txType').value;
        const amount = document.getElementById('txAmountInput').value;
        const note = document.getElementById('txNoteInput').value;

        if (!amount) {
            UI.toast('টাকার পরিমাণ দিন');
            return;
        }

        // Use addMFSTransaction instead of setMFSBalance
        const success = await store.addMFSTransaction(id, type, amount, note || (type === 'out' ? 'Send Money' : 'Receive Money'));
        
        if (success) {
            UI.toast('লেনদেন সফল হয়েছে');
            closeUpdateModal();
            await renderMFS();
        } else {
            UI.alert('ত্রুটি', 'লেনদেন করা যায়নি', 'error');
        }
    }

    async function viewHistory(accountId = null) {
        // If called from modal (no arg), use currentEditingAccount
        const id = accountId || (currentEditingAccount ? currentEditingAccount.id : null);
        if (!id) return;

        closeUpdateModal();
        
        const list = document.getElementById('txList');
        list.innerHTML = '<div class="text-center py-10"><i class="fas fa-spinner fa-spin text-pink-500"></i></div>';
        document.getElementById('txModal').classList.remove('hidden');
        
        const txs = await store.getMFSTransactions(id);
        list.innerHTML = '';

        if (txs.length === 0) {
            list.innerHTML = `
                <div class="text-center py-10 text-gray-400">
                    <i class="fas fa-history text-3xl mb-2"></i>
                    <p>কোনো লেনদেন নেই</p>
                </div>
            `;
            return;
        }

        txs.forEach(tx => {
            const isIncome = tx.type === 'in';
            const colorClass = isIncome ? 'text-green-600' : 'text-red-500';
            const icon = isIncome ? 'fa-arrow-down' : 'fa-arrow-up';
            const bgClass = isIncome ? 'bg-green-50' : 'bg-red-50';

            const el = document.createElement('div');
            el.className = 'bg-white p-3 rounded-xl border border-gray-100 mb-2 flex justify-between items-center';
            el.innerHTML = `
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full ${bgClass} ${colorClass} flex items-center justify-center">
                        <i class="fas ${icon}"></i>
                    </div>
                    <div>
                        <p class="font-bold text-gray-800 text-sm">${tx.note || (isIncome ? 'ক্যাশ ইন' : 'ক্যাশ আউট')}</p>
                        <p class="text-[10px] text-gray-400">
                            ${new Date(tx.created_at.replace(' ', 'T')).toLocaleString('bn-BD', { dateStyle: 'medium', timeStyle: 'short' })} - ${tx.user_name}
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-bold ${colorClass}">
                        ${isIncome ? '+' : '-'} ৳ ${tx.amount}
                    </p>
                </div>
            `;
            list.appendChild(el);
        });
    }

    function closeTxModal() {
        document.getElementById('txModal').classList.add('hidden');
    }
</script>
