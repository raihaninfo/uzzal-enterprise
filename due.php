<?php include 'includes/header.php'; ?>

    <div class="app-container flex flex-col h-screen bg-gray-50">

        <!-- Header Section -->
        <div class="bg-blue-600 text-white p-6 pb-12 rounded-b-3xl shadow-lg z-10 relative">
            <div class="flex justify-between items-center mb-6">
                <button onclick="window.location.href='index.php'" class="bg-blue-500/50 p-2 rounded-full hover:bg-blue-500 transition">
                    <i class="fas fa-arrow-left"></i>
                </button>
                <h1 class="text-xl font-bold">বাকির খাতা</h1>
                <div class="w-8"></div> <!-- Spacer -->
            </div>

            <!-- Total Due Summary -->
            <div class="text-center mb-6">
                <p class="text-blue-200 text-sm mb-1">মোট বাকি</p>
                <h3 class="font-bold text-4xl text-white">৳ <span id="totalDue">0</span></h3>
            </div>
        </div>

        <!-- Add Due Form (Floating Card) -->
        <div class="px-5 -mt-8 z-20 relative mb-4">
            <div class="bg-white p-5 rounded-2xl shadow-lg border border-gray-100">
                <div class="flex gap-3 mb-3">
                    <div class="flex-1">
                        <label class="text-xs text-gray-400 ml-1">নাম</label>
                        <input type="text" id="dueName" placeholder="নাম লিখুন" 
                            class="w-full bg-gray-50 p-3 rounded-xl border-none focus:ring-2 focus:ring-blue-100 text-gray-700 font-medium">
                    </div>
                    <div class="w-1/3">
                        <label class="text-xs text-gray-400 ml-1">টাকা</label>
                        <input type="number" id="dueAmount" placeholder="0.00" 
                            class="w-full bg-gray-50 p-3 rounded-xl border-none focus:ring-2 focus:ring-blue-100 text-gray-700 font-bold text-center">
                    </div>
                </div>
                <div class="mb-4">
                    <input type="text" id="dueNote" placeholder="নোট (অপশনাল)" 
                        class="w-full bg-gray-50 p-3 rounded-xl border-none focus:ring-2 focus:ring-blue-100 text-sm">
                </div>
                <button onclick="addDue()" 
                    class="w-full bg-blue-600 text-white py-3.5 rounded-xl font-bold shadow-lg shadow-blue-200 active:scale-95 transition hover:bg-blue-700 flex justify-center items-center gap-2">
                    <i class="fas fa-plus-circle"></i> বাকি যুক্ত করুন
                </button>
            </div>
        </div>

        <!-- Due List -->
        <div class="flex-1 overflow-y-auto px-5 pb-24 no-scrollbar">
            <div class="flex justify-between items-end mb-4">
                <h3 class="font-bold text-gray-600 text-sm uppercase">সকল বাকি</h3>
                <span class="text-xs text-gray-400 bg-gray-200 px-2 py-1 rounded-full" id="dueCount">0 টি</span>
            </div>

            <div id="dueList" class="flex flex-col gap-3">
                <!-- Items injected here -->
            </div>

            <div id="emptyState" class="text-center py-12 text-gray-400 hidden">
                <div class="bg-gray-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check text-3xl text-gray-300"></i>
                </div>
                <p class="font-medium text-gray-500">কোনো বাকি নেই</p>
                <p class="text-xs text-gray-400 mt-1">সব টাকা আদায় হয়েছে!</p>
            </div>
        </div>

        <?php include 'includes/nav.php'; ?>
    </div>

<?php include 'includes/footer.php'; ?>

<script>
    // Initialize
    (async () => {
        await store.init();
        await renderDues();
    })();

    async function renderDues() {
        const dues = await store.getDues();
        const list = document.getElementById('dueList');
        const emptyState = document.getElementById('emptyState');
        const totalEl = document.getElementById('totalDue');
        const countEl = document.getElementById('dueCount');
        
        let total = 0;
        list.innerHTML = '';

        if (dues.length === 0) {
            emptyState.classList.remove('hidden');
            totalEl.innerText = '0';
            countEl.innerText = '0 টি';
        } else {
            emptyState.classList.add('hidden');
            
            dues.forEach(item => {
                total += parseFloat(item.amount);
                
                const el = document.createElement('div');
                el.className = 'bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex justify-between items-center transition hover:shadow-md';
                el.innerHTML = `
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center text-red-500 font-bold text-sm">
                            ${item.name.charAt(0)}
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">${item.name}</h4>
                            <p class="text-xs text-gray-500">${item.note || 'নোট নেই'}</p>
                            <p class="text-[10px] text-gray-400 mt-0.5">
                                <i class="far fa-clock"></i> ${new Date(item.created_at).toLocaleDateString('bn-BD')}
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <h4 class="font-bold text-red-500 text-lg">৳ ${item.amount}</h4>
                        <div class="flex gap-2 mt-2 justify-end">
                            <button onclick="markPaid(${item.id})" class="w-8 h-8 rounded-full bg-green-50 text-green-600 hover:bg-green-500 hover:text-white transition flex items-center justify-center" title="পরিশোধিত">
                                <i class="fas fa-check"></i>
                            </button>
                            <button onclick="deleteDue(${item.id})" class="w-8 h-8 rounded-full bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition flex items-center justify-center" title="মুছে ফেলুন">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
                list.appendChild(el);
            });
            
            totalEl.innerText = UI.formatCurrency(total);
            countEl.innerText = dues.length + ' টি';
        }
    }

    async function addDue() {
        const name = document.getElementById('dueName').value.trim();
        const amount = document.getElementById('dueAmount').value.trim();
        const note = document.getElementById('dueNote').value.trim();

        if (!name || !amount) {
            UI.alert('ত্রুটি', 'অনুগ্রহ করে নাম এবং টাকার পরিমাণ দিন', 'error');
            return;
        }

        const btn = document.querySelector('button[onclick="addDue()"]');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> যুক্ত হচ্ছে...';
        btn.disabled = true;

        const success = await store.addDue(name, amount, note);
        
        if (success) {
            document.getElementById('dueName').value = '';
            document.getElementById('dueAmount').value = '';
            document.getElementById('dueNote').value = '';
            await renderDues();
            UI.toast('বাকি যুক্ত করা হয়েছে');
        } else {
            UI.alert('ত্রুটি', 'বাকি যোগ করতে সমস্যা হয়েছে।', 'error');
        }
        
        btn.innerHTML = originalText;
        btn.disabled = false;
    }

    async function deleteDue(id) {
        const result = await UI.confirm('মুছে ফেলতে চান?', 'আপনি কি নিশ্চিত যে আপনি এই বাকি মুছে ফেলতে চান?');
        if (result.isConfirmed) {
            await store.deleteDue(id);
            await renderDues();
            UI.toast('বাকি মুছে ফেলা হয়েছে');
        }
    }

    async function markPaid(id) {
        const result = await UI.confirm('পরিশোধ নিশ্চিত করুন', 'আপনি কি নিশ্চিত যে এই বাকি পরিশোধ হয়েছে?');
        if (result.isConfirmed) {
            await store.markDueAsPaid(id);
            await renderDues();
            UI.toast('বাকি পরিশোধ হিসেবে মার্ক করা হয়েছে');
        }
    }
</script>