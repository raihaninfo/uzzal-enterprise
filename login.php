<?php include 'includes/header.php'; ?>

<div class="min-h-screen flex items-center justify-center bg-[#0f172a] relative overflow-hidden">
    <!-- Background Effects -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-purple-600/20 rounded-full blur-[120px] animate-pulse"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-blue-600/20 rounded-full blur-[120px] animate-pulse" style="animation-delay: 2s;"></div>
    </div>

    <div class="w-full max-w-md p-6 relative z-10">
        <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 p-8 rounded-3xl shadow-2xl shadow-black/50">
            
            <div class="text-center mb-10">
                <div class="w-20 h-20 bg-gradient-to-tr from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg shadow-blue-500/20 transform rotate-3 hover:rotate-6 transition-transform duration-300">
                    <i class="fas fa-wallet text-3xl text-white"></i>
                </div>
                <h1 class="text-3xl font-bold text-white mb-2 font-sans tracking-tight">স্বাগতম!</h1>
                <p class="text-slate-400 text-sm">আপনার দৈনন্দিন হিসাব রাখতে লগইন করুন</p>
            </div>

            <div class="space-y-6">
                <div class="group">
                    <label class="block text-slate-400 text-xs font-bold mb-2 ml-1 uppercase tracking-wider group-focus-within:text-blue-400 transition-colors">মোবাইল নম্বর</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-blue-400 transition-colors">
                            <i class="fas fa-phone-alt"></i>
                        </span>
                        <input type="tel" id="mobile" placeholder="017XXXXXXXX"
                            class="w-full bg-slate-900/50 text-white placeholder-slate-600 p-4 pl-12 rounded-xl border border-slate-700 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all duration-300">
                    </div>
                </div>

                <div class="group">
                    <label class="block text-slate-400 text-xs font-bold mb-2 ml-1 uppercase tracking-wider group-focus-within:text-blue-400 transition-colors">পাসওয়ার্ড</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-blue-400 transition-colors">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" id="password" placeholder="••••••"
                            class="w-full bg-slate-900/50 text-white placeholder-slate-600 p-4 pl-12 rounded-xl border border-slate-700 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all duration-300">
                        <button onclick="togglePassword()" type="button"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 hover:text-white transition-colors focus:outline-none">
                            <i id="eyeIcon" class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="text-right mt-3">
                        <a href="#" class="text-xs text-slate-400 hover:text-blue-400 transition-colors">পাসওয়ার্ড ভুলে গেছেন?</a>
                    </div>
                </div>

                <button onclick="handleLogin()"
                    class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-500 hover:to-purple-500 text-white p-4 rounded-xl font-bold shadow-lg shadow-blue-600/20 active:scale-[0.98] transition-all duration-300 uppercase tracking-wider text-sm mt-4">
                    লগইন করুন
                </button>
            </div>
        </div>
        
        <p class="text-center text-slate-500 text-xs mt-8">
            &copy; <?php echo date('Y'); ?> Uzzal Enterprise. All rights reserved.
        </p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
    async function handleLogin() {
        const mobile = document.getElementById('mobile').value;
        const password = document.getElementById('password').value;

        if (!mobile || !password) {
            Swal.fire({
                icon: 'warning',
                title: 'অসম্পূর্ণ তথ্য',
                text: 'অনুগ্রহ করে মোবাইল নম্বর এবং পাসওয়ার্ড দিন',
                confirmButtonColor: '#3b82f6',
                background: '#1e293b',
                color: '#fff'
            });
            return;
        }

        const loginBtn = document.querySelector('button[onclick="handleLogin()"]');
        const originalText = loginBtn.innerHTML;
        loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> যাচাই করা হচ্ছে...';
        loginBtn.disabled = true;

        try {
            const response = await fetch('api/auth.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ phone: mobile, password: password })
            });

            const result = await response.json();

            if (response.ok) {
                localStorage.setItem('isLoggedIn', 'true');
                localStorage.setItem('user', JSON.stringify(result.user));
                
                // Success animation or toast could go here
                window.location.href = 'index.php';
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'লগইন ব্যর্থ',
                    text: result.message || 'লগইন ব্যর্থ হয়েছে',
                    confirmButtonColor: '#ef4444',
                    background: '#1e293b',
                    color: '#fff'
                });
                loginBtn.innerHTML = originalText;
                loginBtn.disabled = false;
            }
        } catch (error) {
            console.error('Login error:', error);
            Swal.fire({
                icon: 'error',
                title: 'সার্ভার এরর',
                text: 'অনুগ্রহ করে পরে আবার চেষ্টা করুন।',
                confirmButtonColor: '#ef4444',
                background: '#1e293b',
                color: '#fff'
            });
            loginBtn.innerHTML = originalText;
            loginBtn.disabled = false;
        }
    }

    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    }
    
    // Add enter key support
    document.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            handleLogin();
        }
    });
</script>