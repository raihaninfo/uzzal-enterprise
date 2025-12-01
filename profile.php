<?php include 'includes/header.php'; ?>

<style>
    /* Custom Toggle Switch CSS */
    .toggle-checkbox:checked {
        right: 0;
        border-color: #3b82f6;
    }

    .toggle-checkbox:checked+.toggle-label {
        background-color: #3b82f6;
    }

    .toggle-checkbox {
        right: 0;
        z-index: 1;
        transition: all 0.3s;
    }

    .toggle-label {
        width: 2.5rem;
        height: 1.25rem;
    }
    
    .glass-panel {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.5);
    }
</style>

<div class="app-container flex flex-col bg-gray-50 min-h-screen pb-24">

    <!-- Header Section -->
    <div class="bg-gradient-to-br from-blue-600 to-purple-700 text-white p-6 pb-12 rounded-b-[2.5rem] shadow-xl z-10 relative overflow-hidden">
        
        <!-- Decorative Elements -->
        <div class="absolute top-0 left-0 w-32 h-32 bg-white/10 rounded-full blur-2xl -translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 right-0 w-40 h-40 bg-purple-500/30 rounded-full blur-2xl translate-x-1/3 translate-y-1/3"></div>

        <div class="flex justify-between items-center relative z-10">
            <button onclick="window.location.href='index.php'" class="w-10 h-10 flex items-center justify-center rounded-full bg-white/20 backdrop-blur-md hover:bg-white/30 transition">
                <i class="fas fa-arrow-left text-lg"></i>
            </button>
            <h2 class="text-lg font-bold tracking-wide">প্রোফাইল</h2>
            <div class="w-10"></div> <!-- Spacer for centering -->
        </div>

        <div class="flex flex-col items-center mt-6 relative z-10">
            <div class="w-28 h-28 p-1 bg-white/30 backdrop-blur-md rounded-full shadow-lg mb-4 relative group">
                <img src="https://ui-avatars.com/api/?name=Mostak+Ahmed&background=random&size=128" alt="Profile"
                    class="w-full h-full rounded-full object-cover border-4 border-white shadow-sm group-hover:scale-105 transition duration-300">
                <button onclick="openEditProfile()" class="absolute bottom-0 right-0 bg-blue-500 text-white w-8 h-8 rounded-full flex items-center justify-center shadow-lg border-2 border-white hover:bg-blue-600 transition">
                    <i class="fas fa-camera text-xs"></i>
                </button>
            </div>

            <h1 class="text-2xl font-bold tracking-tight" id="userName">Loading...</h1>
            <p class="text-blue-100 text-sm font-medium mb-3" id="userBusiness">Loading...</p>
            
            <div class="flex items-center gap-2 bg-white/20 backdrop-blur-md px-4 py-1.5 rounded-full text-sm font-medium border border-white/10">
                <i class="fas fa-phone-alt text-xs"></i> 
                <span id="userPhone">...</span>
            </div>
        </div>
    </div>

    <!-- Content Section -->
    <div class="flex-1 px-5 -mt-8 relative z-20 flex flex-col gap-5">

        <!-- Account Settings -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <h3 class="bg-gray-50/50 px-5 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">অ্যাকাউন্ট সেটিংস</h3>

            <button onclick="openEditProfile()"
                class="w-full flex justify-between items-center p-4 hover:bg-blue-50/50 border-b border-gray-50 transition group">
                <div class="flex items-center gap-4 text-gray-700">
                    <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition duration-300">
                        <i class="fas fa-user-edit"></i>
                    </div>
                    <span class="font-medium">তথ্য পরিবর্তন করুন</span>
                </div>
                <i class="fas fa-chevron-right text-gray-300 text-xs group-hover:translate-x-1 transition"></i>
            </button>

            <button onclick="openChangePassword()" class="w-full flex justify-between items-center p-4 hover:bg-blue-50/50 transition group">
                <div class="flex items-center gap-4 text-gray-700">
                    <div class="w-10 h-10 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center group-hover:bg-purple-600 group-hover:text-white transition duration-300">
                        <i class="fas fa-lock"></i>
                    </div>
                    <span class="font-medium">পাসওয়ার্ড পরিবর্তন</span>
                </div>
                <i class="fas fa-chevron-right text-gray-300 text-xs group-hover:translate-x-1 transition"></i>
            </button>
        </div>

        <!-- App Settings -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <h3 class="bg-gray-50/50 px-5 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">অ্যাপ সেটিংস</h3>

            <div class="flex justify-between items-center p-4 border-b border-gray-50">
                <div class="flex items-center gap-4 text-gray-700">
                    <div class="w-10 h-10 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center">
                        <i class="fas fa-moon"></i>
                    </div>
                    <span class="font-medium">ডার্ক মোড</span>
                </div>
                <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
                    <input type="checkbox" name="darkModeToggle" id="darkModeToggle" onclick="UI.toggleDarkMode()"
                        class="toggle-checkbox absolute block w-5 h-5 rounded-full bg-white border-4 appearance-none cursor-pointer checked:right-0 right-5" />
                    <label for="darkModeToggle"
                        class="toggle-label block overflow-hidden h-5 rounded-full bg-gray-300 cursor-pointer"></label>
                </div>
            </div>

            <div class="flex justify-between items-center p-4 border-b border-gray-50">
                <div class="flex items-center gap-4 text-gray-700">
                    <div class="w-10 h-10 rounded-full bg-yellow-50 text-yellow-600 flex items-center justify-center">
                        <i class="fas fa-bell"></i>
                    </div>
                    <span class="font-medium">নোটিফিকেশন</span>
                </div>
                <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
                    <input type="checkbox" name="toggle" id="toggle"
                        class="toggle-checkbox absolute block w-5 h-5 rounded-full bg-white border-4 appearance-none cursor-pointer checked:right-0 right-5" />
                    <label for="toggle"
                        class="toggle-label block overflow-hidden h-5 rounded-full bg-blue-300 cursor-pointer"></label>
                </div>
            </div>

            <button class="w-full flex justify-between items-center p-4 hover:bg-blue-50/50 transition group">
                <div class="flex items-center gap-4 text-gray-700">
                    <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition duration-300">
                        <i class="fas fa-language"></i>
                    </div>
                    <span class="font-medium">ভাষা (Language)</span>
                </div>
                <span class="text-xs font-bold text-indigo-500 bg-indigo-50 px-2 py-1 rounded-md">বাংলা</span>
            </button>
        </div>

        <!-- Data Management -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <h3 class="bg-gray-50/50 px-5 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">ডাটা ম্যানেজমেন্ট</h3>

            <button onclick="store.exportData()"
                class="w-full flex justify-between items-center p-4 hover:bg-blue-50/50 border-b border-gray-50 transition group">
                <div class="flex items-center gap-4 text-gray-700">
                    <div class="w-10 h-10 rounded-full bg-green-50 text-green-600 flex items-center justify-center group-hover:bg-green-600 group-hover:text-white transition duration-300">
                        <i class="fas fa-download"></i>
                    </div>
                    <span class="font-medium">ডাটা ব্যাকআপ (Export)</span>
                </div>
                <i class="fas fa-chevron-right text-gray-300 text-xs group-hover:translate-x-1 transition"></i>
            </button>

            <button onclick="document.getElementById('importFile').click()"
                class="w-full flex justify-between items-center p-4 hover:bg-blue-50/50 transition group">
                <div class="flex items-center gap-4 text-gray-700">
                    <div class="w-10 h-10 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center group-hover:bg-orange-600 group-hover:text-white transition duration-300">
                        <i class="fas fa-upload"></i>
                    </div>
                    <span class="font-medium">ডাটা রিস্টোর (Import)</span>
                </div>
                <i class="fas fa-chevron-right text-gray-300 text-xs group-hover:translate-x-1 transition"></i>
            </button>
            <input type="file" id="importFile" class="hidden" accept=".json" onchange="handleImport(this)">
        </div>

        <button onclick="handleLogout()"
            class="w-full bg-white text-red-500 p-4 rounded-2xl font-bold text-sm border border-red-100 hover:bg-red-50 hover:border-red-200 hover:shadow-md transition mt-2 flex items-center justify-center gap-3 group">
            <i class="fas fa-sign-out-alt group-hover:scale-110 transition"></i> লগআউট করুন
        </button>

        <p class="text-center text-xs text-gray-400 mt-4 font-medium">Uzzal Enterprise v1.0.0</p>

    </div>

    <!-- Edit Profile Modal -->
    <div id="editProfileModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4 transition-opacity duration-300">
        <div class="bg-white rounded-2xl w-full max-w-sm p-6 shadow-2xl transform scale-100 transition-transform duration-300">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800">প্রোফাইল এডিট</h3>
                <button onclick="closeModal('editProfileModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="flex flex-col gap-4">
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">আপনার নাম</label>
                    <input type="text" id="editName" class="p-3 bg-gray-50 border border-gray-200 rounded-xl w-full focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">ব্যবসার নাম</label>
                    <input type="text" id="editBusiness" class="p-3 bg-gray-50 border border-gray-200 rounded-xl w-full focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">মোবাইল নম্বর</label>
                    <input type="text" id="editPhone" class="p-3 bg-gray-50 border border-gray-200 rounded-xl w-full focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                </div>
                <div class="flex gap-3 mt-4">
                    <button onclick="closeModal('editProfileModal')" class="flex-1 py-3 bg-gray-100 rounded-xl text-gray-600 font-medium hover:bg-gray-200 transition">বাতিল</button>
                    <button onclick="saveProfile()" class="flex-1 py-3 bg-blue-600 text-white rounded-xl font-bold shadow-lg shadow-blue-200 hover:bg-blue-700 transition">সেভ করুন</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div id="changePasswordModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4 transition-opacity duration-300">
        <div class="bg-white rounded-2xl w-full max-w-sm p-6 shadow-2xl">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800">পাসওয়ার্ড পরিবর্তন</h3>
                <button onclick="closeModal('changePasswordModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="flex flex-col gap-4">
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">বর্তমান পাসওয়ার্ড</label>
                    <input type="password" id="oldPass" class="p-3 bg-gray-50 border border-gray-200 rounded-xl w-full focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">নতুন পাসওয়ার্ড</label>
                    <input type="password" id="newPass" class="p-3 bg-gray-50 border border-gray-200 rounded-xl w-full focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                </div>
                <div class="flex gap-3 mt-4">
                    <button onclick="closeModal('changePasswordModal')" class="flex-1 py-3 bg-gray-100 rounded-xl text-gray-600 font-medium hover:bg-gray-200 transition">বাতিল</button>
                    <button onclick="savePassword()" class="flex-1 py-3 bg-blue-600 text-white rounded-xl font-bold shadow-lg shadow-blue-200 hover:bg-blue-700 transition">পরিবর্তন করুন</button>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/nav.php'; ?>
</div>

<?php include 'includes/footer.php'; ?>

<script>
    (async () => {
        await store.init();
        // UI.init('profile.html'); // Handled by PHP nav

        const user = store.getUser();
        if (user) {
            updateProfileUI(user);
        }
    })();

    function updateProfileUI(user) {
        document.getElementById('userName').innerText = user.name || 'Uzzal Enterprise';
        document.getElementById('userBusiness').innerText = user.business_name || 'Business';
        document.getElementById('userPhone').innerText = user.phone || '...';
    }

    function handleLogout() {
        if (confirm("আপনি কি নিশ্চিত যে আপনি লগআউট করতে চান?")) {
            store.logout();
        }
    }

    function handleImport(input) {
        const file = input.files[0];
        if (!file) return;

        if (confirm('সতর্কতা: ডাটা রিস্টোর করলে ব্যাকআপ ফাইলের ডাটা বর্তমান ডাটাবেসে যুক্ত হবে। আপনি কি নিশ্চিত?')) {
            const reader = new FileReader();
            reader.onload = async function (e) {
                const content = e.target.result;
                if (await store.importData(content)) {
                    alert('ডাটা সফলভাবে রিস্টোর করা হয়েছে!');
                    window.location.reload();
                } else {
                    alert('ডাটা রিস্টোর ব্যর্থ হয়েছে। সঠিক ফাইল নির্বাচন করুন।');
                }
            };
            reader.readAsText(file);
        }
        // Reset input
        input.value = '';
    }

    // Modal Functions
    function openEditProfile() {
        const user = store.getUser();
        if (user) {
            document.getElementById('editName').value = user.name || '';
            document.getElementById('editBusiness').value = user.business_name || '';
            document.getElementById('editPhone').value = user.phone || '';
            document.getElementById('editProfileModal').classList.remove('hidden');
        }
    }

    function openChangePassword() {
        document.getElementById('oldPass').value = '';
        document.getElementById('newPass').value = '';
        document.getElementById('changePasswordModal').classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    async function saveProfile() {
        const name = document.getElementById('editName').value;
        const business_name = document.getElementById('editBusiness').value;
        const phone = document.getElementById('editPhone').value;

        if (await store.updateProfile({ name, business_name, phone })) {
            alert('প্রোফাইল আপডেট হয়েছে!');
            closeModal('editProfileModal');
            updateProfileUI(store.getUser());
        } else {
            alert('প্রোফাইল আপডেট ব্যর্থ হয়েছে!');
        }
    }

    async function savePassword() {
        const oldPass = document.getElementById('oldPass').value;
        const newPass = document.getElementById('newPass').value;

        if (!oldPass || !newPass) {
            alert('সব তথ্য পূরণ করুন');
            return;
        }

        const res = await store.changePassword(oldPass, newPass);
        if (res.success) {
            alert(res.message);
            closeModal('changePasswordModal');
        } else {
            alert(res.message);
        }
    }
</script>