<?php include 'includes/header.php'; ?>

    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-600 to-purple-700 p-4">
        <div class="bg-white/10 backdrop-blur-lg border border-white/20 p-8 rounded-3xl shadow-2xl w-full max-w-md relative overflow-hidden">
            
            <!-- Decorative Circles -->
            <div class="absolute -top-10 -left-10 w-32 h-32 bg-blue-500 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
            <div class="absolute -bottom-10 -right-10 w-32 h-32 bg-purple-500 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>

            <div class="text-center mb-10 relative z-10">
                <div class="w-24 h-24 bg-gradient-to-tr from-blue-500 to-purple-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg shadow-blue-500/30 text-white text-4xl animate-bounce-slow">
                    <i class="fas fa-wallet"></i>
                </div>
                <h1 class="text-3xl font-bold text-white mb-2 tracking-wide">স্বাগতম!</h1>
                <p class="text-blue-100 text-sm font-light">আপনার দৈনন্দিন হিসাব রাখতে লগইন করুন</p>
            </div>

            <div class="flex flex-col gap-6 relative z-10">
                <div>
                    <label class="block text-blue-100 text-xs font-bold mb-2 ml-1 uppercase tracking-wider">মোবাইল নম্বর</label>
                    <div class="relative group">
                        <span class="absolute left-4 top-3.5 text-blue-300 group-focus-within:text-white transition">
                            <i class="fas fa-phone-alt"></i>
                        </span>
                        <input type="tel" id="mobile" placeholder="017XXXXXXXX"
                            class="w-full bg-white/10 text-white placeholder-blue-200/50 p-3 pl-10 rounded-xl border border-white/10 focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400 transition backdrop-blur-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-blue-100 text-xs font-bold mb-2 ml-1 uppercase tracking-wider">পাসওয়ার্ড</label>
                    <div class="relative group">
                        <span class="absolute left-4 top-3.5 text-blue-300 group-focus-within:text-white transition">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" id="password" placeholder="******"
                            class="w-full bg-white/10 text-white placeholder-blue-200/50 p-3 pl-10 rounded-xl border border-white/10 focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400 transition backdrop-blur-sm">
                        <span onclick="togglePassword()"
                            class="absolute right-4 top-3.5 text-blue-300 cursor-pointer hover:text-white transition">
                            <i id="eyeIcon" class="fas fa-eye"></i>
                        </span>
                    </div>
                    <div class="text-right mt-3">
                        <a href="#" class="text-xs text-blue-200 hover:text-white transition">পাসওয়ার্ড ভুলে গেছেন?</a>
                    </div>
                </div>

                <button onclick="handleLogin()"
                    class="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white p-4 rounded-xl font-bold shadow-lg shadow-blue-500/30 active:scale-95 transition hover:shadow-blue-500/50 mt-2 uppercase tracking-wider text-sm">
                    লগইন করুন
                </button>
            </div>
        </div>
    </div>

    <style>
        .animate-blob {
            animation: blob 7s infinite;
        }
        .animation-delay-2000 {
            animation-delay: 2s;
        }
        .animate-bounce-slow {
            animation: bounce 3s infinite;
        }
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
    </style>

<?php include 'includes/footer.php'; ?>

<script>
    async function handleLogin() {
        const mobile = document.getElementById('mobile').value;
        const password = document.getElementById('password').value;

        if (!mobile || !password) {
            alert('অনুগ্রহ করে মোবাইল নম্বর এবং পাসওয়ার্ড দিন');
            return;
        }

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
                window.location.href = 'index.php';
            } else {
                alert(result.message || 'লগইন ব্যর্থ হয়েছে');
            }
        } catch (error) {
            console.error('Login error:', error);
            alert('সার্ভার এরর! অনুগ্রহ করে পরে আবার চেষ্টা করুন।');
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
</script>