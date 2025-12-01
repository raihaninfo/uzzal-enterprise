<?php include 'includes/header.php'; ?>

    <div class="app-container flex flex-col">

        <div class="bg-blue-600 text-white p-6 rounded-b-3xl shadow-lg z-10">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-xl font-bold"><i class="fas fa-book mr-2"></i>বাকির খাতা</h1>
                <button onclick="window.location.href='index.php'"
                    class="bg-blue-500 p-2 rounded-full hover:bg-blue-400"><i class="fas fa-home"></i></button>
            </div>

            <div class="bg-blue-700/50 p-4 rounded-xl backdrop-blur-sm text-center">
                <p class="text-blue-200 text-xs">মোট বাকি</p>
                <h3 class="font-bold text-2xl text-yellow-300">৳ <span id="totalDue">0</span></h3>
            </div>
        </div>

        <div class="flex-1 p-5 overflow-y-auto pb-24 no-scrollbar">

            <!-- Add Due Form -->
            <div class="bg-white border border-gray-100 p-4 rounded-xl shadow-sm mb-6">
                <h3 class="text-gray-700 font-semibold text-sm mb-3">নতুন বাকি যোগ করুন</h3>
                <div class="flex flex-col gap-3">
                    <input type="text" id="dueName" placeholder="নাম (Name)"
                        class="w-full bg-gray-50 p-3 rounded-lg text-sm border focus:outline-none focus:border-blue-500">
                    <div class="flex gap-2">
                        <input type="number" id="dueAmount" placeholder="টাকার পরিমাণ"
                            class="w-full bg-gray-50 p-3 rounded-lg text-sm border focus:outline-none focus:border-blue-500">
                        <button onclick="addDue()"
                            class="bg-yellow-500 text-white px-6 py-3 rounded-lg font-bold shadow-md active:scale-95 transition hover:bg-yellow-600">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <input type="text" id="dueNote" placeholder="নোট (অপশনাল)"
                        class="w-full bg-gray-50 p-2 rounded-lg text-xs border focus:outline-none focus:border-blue-500">
                </div>
            </div>

            <h3 class="font-bold text-gray-600 mb-4 text-sm uppercase">বাকির তালিকা</h3>

            <div id="dueList" class="flex flex-col gap-3">
                <!-- Items injected here -->
            </div>

            <div id="emptyState" class="text-center py-10 text-gray-400 hidden">
                <i class="fas fa-check-circle text-4xl mb-2 opacity-50"></i>
                <p class="text-sm">কোনো বাকি নেই</p>
            </div>
        </div>

        <?php include 'includes/nav.php'; ?>
    </div>

<?php include 'includes/footer.php'; ?>

<script>
    // Initialize store and UI
    (async () => {
        await store.init();
        // UI.init('due.html'); // Handled by PHP nav
        await renderDues();
    })();

    async function renderDues() {
        const dues = await store.getDues();
        const list = document.getElementById('dueList');
        const emptyState = document.getElementById('emptyState');
        let total = 0;

        list.innerHTML = '';

        if (dues.length === 0) {
            emptyState.classList.remove('hidden');
        } else {
            emptyState.classList.add('hidden');
            dues.forEach(item => {
                total += parseFloat(item.amount);
                const el = document.createElement('div');
                el.className = 'bg-white p-4 rounded-xl shadow-sm border-l-4 border-yellow-400 flex justify-between items-center animate-fade-in';
                el.innerHTML = `
        <div>
            <h4 class="font-bold text-gray-800 text-sm">${item.name}</h4>
            <p class="text-xs text-gray-500">${UI.formatDate(item.date)} ${item.note ? '• ' + item.note : ''}</p>
        </div>
        <div class="flex flex-col items-end gap-1">
            <span class="font-bold text-yellow-600 text-sm">৳ ${parseFloat(item.amount).toLocaleString()}</span>
            <div class="flex gap-2">
                 <button onclick="markPaid(${item.id})" class="text-xs bg-green-100 text-green-600 px-2 py-1 rounded hover:bg-green-200 transition">
                    আদায়
                </button>
                <button onclick="deleteDue(${item.id})" class="text-gray-300 hover:text-red-500 transition">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        </div>
    `;
                list.appendChild(el);
            });
        }
        document.getElementById('totalDue').innerText = total.toLocaleString();
    }

    async function addDue() {
        const name = document.getElementById('dueName').value.trim();
        const amount = document.getElementById('dueAmount').value.trim();
        const note = document.getElementById('dueNote').value.trim();

        if (!name || !amount) {
            alert('নাম এবং টাকার পরিমাণ দিন');
            return;
        }

        const success = await store.addDue(name, amount, note);
        if (success) {
            document.getElementById('dueName').value = '';
            document.getElementById('dueAmount').value = '';
            document.getElementById('dueNote').value = '';
            await renderDues();
        } else {
            alert('বাকি যোগ করতে সমস্যা হয়েছে।');
        }
    }

    async function deleteDue(id) {
        if (confirm('মুছে ফেলতে চান?')) {
            await store.deleteDue(id);
            await renderDues();
        }
    }

    async function markPaid(id) {
        if (confirm('টাকা আদায় হয়েছে? এটি আয়ের তালিকায় যুক্ত হবে।')) {
            await store.markDueAsPaid(id);
            await renderDues();
            alert('আদায় সফল হয়েছে!');
        }
    }
</script>