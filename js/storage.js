const API_URL = 'api';

class Store {
    constructor() {
        this.data = {
            transactions: [],
            categories: [],
            dues: [],
            user: null
        };
    }

    async init() {
        await this.checkSession();
        if (this.data.user) {
            await this.loadData();
            await this.getCategories();
            await this.getDues();
        }
    }

    async checkSession() {
        try {
            const res = await fetch(`${API_URL}/auth.php?action=check`);
            const result = await res.json();
            if (result.isLoggedIn) {
                // Fetch full user details
                const userRes = await fetch(`${API_URL}/profile.php`);
                this.data.user = await userRes.json();
                localStorage.setItem('user', JSON.stringify(this.data.user));
            } else {
                this.data.user = null;
                localStorage.removeItem('user');
                // Redirect if not on login page
                if (!window.location.pathname.includes('login.php')) {
                    window.location.href = 'login.php';
                }
            }
        } catch (e) {
            console.error('Session check failed:', e);
        }
    }

    async loadData() {
        try {
            const res = await fetch(`${API_URL}/transactions.php`);
            if (res.ok) {
                this.data.transactions = await res.json();
            }
            return this.data;
        } catch (e) {
            console.error('Failed to load transactions:', e);
            return [];
        }
    }

    getTransactions() {
        return this.data.transactions;
    }

    async addTransaction(source, amount, type, category = 'General') {
        try {
            const res = await fetch(`${API_URL}/transactions.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ source, amount, type, category })
            });
            const result = await res.json();
            if (result.id) {
                await this.loadData(); // Reload to get updated list
                return result;
            }
        } catch (e) {
            console.error('Failed to add transaction:', e);
        }
        return null;
    }

    async deleteTransaction(id) {
        try {
            await fetch(`${API_URL}/transactions.php?id=${id}`, { method: 'DELETE' });
            this.data.transactions = this.data.transactions.filter(t => t.id != id);
        } catch (e) {
            console.error('Failed to delete transaction:', e);
        }
    }

    async updateTransaction(id, updatedData) {
        try {
            await fetch(`${API_URL}/transactions.php`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id, ...updatedData })
            });
            await this.loadData();
            return true;
        } catch (e) {
            console.error('Failed to update transaction:', e);
            return false;
        }
    }

    getBalance() {
        return this.data.transactions.reduce((acc, t) => {
            return t.type === 'income' ? acc + parseFloat(t.amount) : acc - parseFloat(t.amount);
        }, 0);
    }

    getTotalIncome() {
        return this.data.transactions
            .filter(t => t.type === 'income')
            .reduce((total, item) => total + parseFloat(item.amount), 0);
    }

    getTotalExpense() {
        return this.data.transactions
            .filter(t => t.type === 'expense')
            .reduce((total, item) => total + parseFloat(item.amount), 0);
    }

    getTodayStats() {
        const today = new Date().toDateString();
        const todayTx = this.data.transactions.filter(item => new Date(item.transaction_date).toDateString() === today);

        const income = todayTx.filter(t => t.type === 'income').reduce((sum, t) => sum + parseFloat(t.amount), 0);
        const expense = todayTx.filter(t => t.type === 'expense').reduce((sum, t) => sum + parseFloat(t.amount), 0);

        return { income, expense, balance: income - expense };
    }

    getUser() {
        return this.data.user || JSON.parse(localStorage.getItem('user'));
    }

    async login(phone, password) {
        try {
            const res = await fetch(`${API_URL}/auth.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ phone, password })
            });
            const result = await res.json();
            if (res.ok) {
                this.data.user = result.user;
                localStorage.setItem('user', JSON.stringify(result.user));
                return true;
            }
            return false;
        } catch (e) {
            console.error('Login failed:', e);
            return false;
        }
    }

    logout() {
        this.data.user = null;
        localStorage.removeItem('user');
        localStorage.removeItem('isLoggedIn');
        window.location.href = 'logout.php';
    }

    async updateProfile(data) {
        try {
            const res = await fetch(`${API_URL}/profile.php`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            if (res.ok) {
                // Update local data
                this.data.user = { ...this.data.user, ...data };
                localStorage.setItem('user', JSON.stringify(this.data.user));
                return true;
            }
            return false;
        } catch (e) {
            console.error('Profile update failed:', e);
            return false;
        }
    }

    async changePassword(old_password, new_password) {
        try {
            const res = await fetch(`${API_URL}/profile.php`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ old_password, new_password })
            });
            const result = await res.json();
            if (res.ok) {
                return { success: true, message: result.message };
            } else {
                return { success: false, message: result.message };
            }
        } catch (e) {
            console.error('Password change failed:', e);
            return { success: false, message: 'Network error' };
        }
    }

    // Due Methods
    async getDues() {
        try {
            const res = await fetch(`${API_URL}/dues.php`);
            if (res.ok) {
                this.data.dues = await res.json();
            }
            return this.data.dues;
        } catch (e) {
            console.error('Failed to load dues:', e);
            return [];
        }
    }

    async addDue(name, amount, note) {
        try {
            const res = await fetch(`${API_URL}/dues.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ name, amount, note })
            });
            await this.getDues();
            return await res.json();
        } catch (e) {
            console.error('Failed to add due:', e);
        }
    }

    async deleteDue(id) {
        try {
            await fetch(`${API_URL}/dues.php?id=${id}`, { method: 'DELETE' });
            this.data.dues = this.data.dues.filter(d => d.id != id);
        } catch (e) {
            console.error('Failed to delete due:', e);
        }
    }

    async markDueAsPaid(id) {
        const due = this.data.dues.find(d => d.id == id);
        if (due) {
            // Add to income
            await this.addTransaction(due.name + ' (বাকি আদায়)', due.amount, 'income', 'Due Collection');
            // Mark as paid in DB
            await fetch(`${API_URL}/dues.php`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id, is_paid: 1 })
            });
            await this.getDues();
        }
    }

    // Category Methods
    async getCategories() {
        try {
            const res = await fetch(`${API_URL}/categories.php`);
            if (res.ok) {
                this.data.categories = await res.json();
            }
            return this.data.categories;
        } catch (e) {
            console.error('Failed to load categories:', e);
            return [];
        }
    }

    async addCategory(name) {
        try {
            const res = await fetch(`${API_URL}/categories.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ name })
            });
            await this.getCategories();
            return true;
        } catch (e) {
            console.error('Failed to add category:', e);
            return false;
        }
    }

    // Data Management (Export only for now)
    exportData() {
        const dataStr = JSON.stringify(this.data, null, 2);
        const blob = new Blob([dataStr], { type: 'application/json' });
        const url = URL.createObjectURL(blob);

        const a = document.createElement('a');
        a.href = url;
        a.download = `uzzal_enterprise_backup_${new Date().toISOString().split('T')[0]}.json`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }

    async importData(jsonContent) {
        try {
            const parsed = JSON.parse(jsonContent);

            // Restore Categories
            if (parsed.categories && Array.isArray(parsed.categories)) {
                for (const cat of parsed.categories) {
                    await this.addCategory(cat);
                }
            }

            // Restore Transactions
            if (parsed.transactions && Array.isArray(parsed.transactions)) {
                for (const t of parsed.transactions) {
                    await this.addTransaction(t.source, t.amount, t.type, t.category);
                }
            }

            // Restore Dues
            if (parsed.dues && Array.isArray(parsed.dues)) {
                for (const d of parsed.dues) {
                    await this.addDue(d.name, d.amount, d.note);
                }
            }

            await this.init(); // Reload all data
            return true;
        } catch (e) {
            console.error('Import failed:', e);
            return false;
        }
    }
    // MFS Methods
    async getMFSAccounts() {
        try {
            const res = await fetch(`${API_URL}/mfs.php`);
            if (res.ok) {
                return await res.json();
            }
            return [];
        } catch (e) {
            console.error('Failed to load MFS accounts:', e);
            return [];
        }
    }

    async addMFSAccount(provider, number, balance) {
        try {
            const res = await fetch(`${API_URL}/mfs.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ provider, number, balance })
            });
            return await res.json();
        } catch (e) {
            console.error('Failed to add MFS account:', e);
            return { message: 'Failed to add account' };
        }
    }

    async updateMFSAccount(id, provider, number) {
        try {
            const res = await fetch(`${API_URL}/mfs.php`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id, provider, number })
            });
            return res.ok;
        } catch (e) {
            console.error('Failed to update MFS account:', e);
            return false;
        }
    }

    async deleteMFSAccount(id) {
        try {
            const res = await fetch(`${API_URL}/mfs.php?id=${id}`, { method: 'DELETE' });
            return res.ok;
        } catch (e) {
            console.error('Failed to delete MFS account:', e);
            return false;
        }
    }

    // MFS Transaction Methods
    async getMFSTransactions(accountId) {
        try {
            const res = await fetch(`${API_URL}/mfs.php?action=transactions&account_id=${accountId}`);
            if (res.ok) {
                return await res.json();
            }
            return [];
        } catch (e) {
            console.error('Failed to load MFS transactions:', e);
            return [];
        }
    }

    async addMFSTransaction(accountId, type, amount, note) {
        try {
            const res = await fetch(`${API_URL}/mfs.php?action=transaction`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ account_id: accountId, type, amount, note })
            });
            return res.ok;
        } catch (e) {
            console.error('Failed to add MFS transaction:', e);
            return false;
        }
    }

    async deleteMFSTransaction(id) {
        try {
            const res = await fetch(`${API_URL}/mfs.php?action=transaction&id=${id}`, { method: 'DELETE' });
            return res.ok;
        } catch (e) {
            console.error('Failed to delete MFS transaction:', e);
            return false;
        }
    }

    async setMFSBalance(accountId, balance) {
        try {
            const res = await fetch(`${API_URL}/mfs.php?action=set_balance`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ account_id: accountId, balance })
            });
            return res.ok;
        } catch (e) {
            console.error('Failed to set MFS balance:', e);
            return false;
        }
    }
}

const store = new Store();
