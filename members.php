<?php include 'includes/header.php'; ?>

<div class="app-container flex flex-col">
    <div class="bg-white p-6 sticky top-0 z-10 shadow-sm">
        <h1 class="text-xl font-bold text-gray-800 text-center mb-4">মেম্বার ম্যানেজমেন্ট</h1>
        <button onclick="openAddMemberModal()" class="w-full bg-blue-600 text-white py-3 rounded-xl font-bold shadow-lg shadow-blue-500/30 active:scale-95 transition">
            <i class="fas fa-user-plus mr-2"></i> নতুন মেম্বার যোগ করুন
        </button>
    </div>

    <div class="flex-1 p-5 overflow-y-auto pb-24 no-scrollbar">
        <div id="membersList" class="flex flex-col gap-3">
            <!-- Members injected here -->
        </div>
    </div>

    <?php include 'includes/nav.php'; ?>
</div>

<!-- Add Member Modal -->
<div id="addMemberModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl w-full max-w-sm p-6 shadow-2xl transform transition-all scale-100">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold text-gray-800">নতুন মেম্বার</h3>
            <button onclick="closeAddMemberModal()" class="text-gray-400 hover:text-red-500 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">নাম</label>
                <input type="text" id="memberName" class="w-full p-3 rounded-lg bg-gray-50 border border-gray-200 focus:outline-none focus:border-blue-500 transition" placeholder="মেম্বারের নাম">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">মোবাইল নম্বর</label>
                <input type="tel" id="memberPhone" class="w-full p-3 rounded-lg bg-gray-50 border border-gray-200 focus:outline-none focus:border-blue-500 transition" placeholder="017XXXXXXXX">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">পাসওয়ার্ড</label>
                <input type="password" id="memberPassword" class="w-full p-3 rounded-lg bg-gray-50 border border-gray-200 focus:outline-none focus:border-blue-500 transition" placeholder="******">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">রোল</label>
                <select id="memberRole" class="w-full p-3 rounded-lg bg-gray-50 border border-gray-200 focus:outline-none focus:border-blue-500 transition">
                    <option value="member">মেম্বার</option>
                    <option value="admin">অ্যাডমিন</option>
                </select>
            </div>
            
            <button onclick="addMember()" class="w-full bg-blue-600 text-white py-3 rounded-xl font-bold shadow-lg shadow-blue-500/30 active:scale-95 transition mt-2">
                সেভ করুন
            </button>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
    // Check if admin
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    if (user.role !== 'admin') {
        UI.alert('প্রবেশ নিষেধ', 'আপনার এই পেজে প্রবেশ করার অনুমতি নেই', 'error').then(() => {
            window.location.href = 'index.php';
        });
    }

    async function loadMembers() {
        try {
            const response = await fetch('api/members.php');
            const members = await response.json();
            
            const list = document.getElementById('membersList');
            list.innerHTML = '';

            members.forEach(m => {
                const isMe = m.id == user.id;
                const deleteBtn = isMe ? '' : `
                    <button onclick="deleteMember(${m.id})" class="p-2 text-red-500 hover:bg-red-50 rounded-full transition">
                        <i class="fas fa-trash"></i>
                    </button>
                `;

                const roleBadge = m.role === 'admin' 
                    ? '<span class="px-2 py-0.5 bg-purple-100 text-purple-600 text-[10px] rounded-full font-bold">অ্যাডমিন</span>'
                    : '<span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-[10px] rounded-full font-bold">মেম্বার</span>';

                const html = `
                    <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                                ${m.name.charAt(0)}
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                                    ${m.name} ${roleBadge}
                                </h4>
                                <p class="text-xs text-gray-500">${m.phone}</p>
                            </div>
                        </div>
                        ${deleteBtn}
                    </div>
                `;
                list.innerHTML += html;
            });
        } catch (error) {
            console.error('Error loading members:', error);
        }
    }

    function openAddMemberModal() {
        document.getElementById('addMemberModal').classList.remove('hidden');
        document.getElementById('addMemberModal').classList.add('flex');
    }

    function closeAddMemberModal() {
        document.getElementById('addMemberModal').classList.add('hidden');
        document.getElementById('addMemberModal').classList.remove('flex');
    }

    async function addMember() {
        const name = document.getElementById('memberName').value;
        const phone = document.getElementById('memberPhone').value;
        const password = document.getElementById('memberPassword').value;
        const role = document.getElementById('memberRole').value;

        if (!name || !phone || !password) {
            alert('সব তথ্য পূরণ করুন');
            return;
        }

        try {
            const response = await fetch('api/members.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ name, phone, password, role })
            });

            const result = await response.json();

            if (response.ok) {
                UI.toast('মেম্বার যোগ করা হয়েছে');
                closeAddMemberModal();
                loadMembers();
                // Clear inputs
                document.getElementById('memberName').value = '';
                document.getElementById('memberPhone').value = '';
                document.getElementById('memberPassword').value = '';
            } else {
                UI.alert('ত্রুটি', result.message || 'সমস্যা হয়েছে', 'error');
            }
        } catch (error) {
            console.error('Error adding member:', error);
        }
    }

    async function deleteMember(id) {
        const result = await UI.confirm('মুছে ফেলতে চান?', 'আপনি কি নিশ্চিত এই মেম্বারকে ডিলিট করতে চান?');
        if (!result.isConfirmed) return;

        try {
            const response = await fetch(`api/members.php?id=${id}`, {
                method: 'DELETE'
            });

            if (response.ok) {
                loadMembers();
                UI.toast('মেম্বার ডিলিট করা হয়েছে');
            } else {
                const result = await response.json();
                UI.alert('ত্রুটি', result.message || 'ডিলিট করা যায়নি', 'error');
            }
        } catch (error) {
            console.error('Error deleting member:', error);
        }
    }

    loadMembers();
</script>
