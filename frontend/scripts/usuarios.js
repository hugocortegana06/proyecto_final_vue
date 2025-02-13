import { BASE_URL } from './config.js';

const app = Vue.createApp({
    data() {
        return {
            users: [],
            showDeleteModal: false,
            showSuccessModal: false,
            selectedUser: null
        };
    },
    methods: {
        async getUsers() {
            try {
                const response = await fetch(BASE_URL + '/admin/users.php');
                this.users = await response.json();
            } catch (error) {
                console.error("Error al obtener usuarios:", error);
            }
        },
        openDeleteModal(user) {
            this.selectedUser = user;
            this.showDeleteModal = true;
        },
        closeDeleteModal() {
            this.showDeleteModal = false;
            this.selectedUser = null;
        },
        closeSuccessModal() {
            this.showSuccessModal = false;
        },
        async confirmDeleteUser() {
            if (!this.selectedUser) return;

            try {
                const response = await fetch(BASE_URL + '/admin/users.php', {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: this.selectedUser.id })
                });

                const data = await response.json();

                if (data.success) {
                    this.showDeleteModal = false;
                    this.showSuccessModal = true;
                    this.getUsers();
                }
            } catch (error) {
                console.error("Error al eliminar usuario:", error);
            }
        }
    },
    mounted() {
        this.getUsers();
    }
});

app.mount('#app');
