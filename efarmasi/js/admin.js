// Admin Dashboard Management
let currentTab = 'dashboard';

document.addEventListener('DOMContentLoaded', function() {
    initializeAdminDashboard();
    loadDashboardStats();
});

function initializeAdminDashboard() {
    // Tab switching
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            switchTab(this.dataset.tab);
        });
    });
    
    // Modal handling
    document.querySelectorAll('.modal-close').forEach(btn => {
        btn.addEventListener('click', closeModal);
    });
    
    // Load initial data
    loadUsers();
    loadRules();
    loadMedicines();
    loadConsultations();
}

function switchTab(tabName) {
    currentTab = tabName;
    
    // Update active tab button
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');
    
    // Show active tab content
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
    });
    document.getElementById(`${tabName}-tab`).classList.add('active');
    
    // Load tab-specific data
    switch(tabName) {
        case 'users':
            loadUsers();
            break;
        case 'rules':
            loadRules();
            break;
        case 'medicines':
            loadMedicines();
            break;
        case 'consultations':
            loadConsultations();
            break;
    }
}

function loadDashboardStats() {
    fetch('api/admin.php?action=stats')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateStatsDisplay(data.data);
            }
        })
        .catch(error => {
            console.error('Error loading stats:', error);
        });
}

function updateStatsDisplay(stats) {
    document.querySelectorAll('.stat-number').forEach(element => {
        const statType = element.parentNode.querySelector('.stat-label').textContent.toLowerCase();
        if (stats[statType] !== undefined) {
            element.textContent = stats[statType];
        }
    });
}

function loadUsers() {
    fetch('api/admin.php?action=users')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderUsersTable(data.data);
            }
        })
        .catch(error => {
            console.error('Error loading users:', error);
        });
}

function loadRules() {
    fetch('api/admin.php?action=rules')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderRulesTable(data.data);
            }
        })
        .catch(error => {
            console.error('Error loading rules:', error);
        });
}

function loadMedicines() {
    fetch('api/admin.php?action=medicines')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderMedicinesTable(data.data);
            }
        })
        .catch(error => {
            console.error('Error loading medicines:', error);
        });
}

function loadConsultations() {
    fetch('api/admin.php?action=consultations')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderConsultationsTable(data.data);
            }
        })
        .catch(error => {
            console.error('Error loading consultations:', error);
        });
}

function renderUsersTable(users) {
    const tbody = document.querySelector('#users-tab tbody');
    tbody.innerHTML = '';
    
    users.forEach(user => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${user.id}</td>
            <td>${user.full_name}</td>
            <td>${user.email}</td>
            <td>${user.role}</td>
            <td>${new Date(user.created_at).toLocaleDateString('id-ID')}</td>
            <td>
                <button class="btn-action btn-edit" onclick="editUser(${user.id})">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn-action btn-delete" onclick="deleteUser(${user.id})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function renderRulesTable(rules) {
    const tbody = document.querySelector('#rules-tab tbody');
    tbody.innerHTML = '';
    
    rules.forEach(rule => {
        const conditions = JSON.parse(rule.conditions);
        const conclusions = JSON.parse(rule.conclusions);
        
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${rule.id}</td>
            <td>${rule.rule_name}</td>
            <td>
                <div class="rule-conditions">
                    ${Object.entries(conditions).map(([key, value]) => 
                        `<div>${key}: ${JSON.stringify(value)}</div>`
                    ).join('')}
                </div>
            </td>
            <td>${conclusions.medicine || 'N/A'}</td>
            <td>${(rule.confidence_level * 100).toFixed(0)}%</td>
            <td>
                <span class="status-badge ${rule.is_active ? 'status-active' : 'status-inactive'}">
                    ${rule.is_active ? 'Aktif' : 'Nonaktif'}
                </span>
            </td>
            <td>
                <button class="btn-action btn-edit" onclick="editRule(${rule.id})">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn-action btn-delete" onclick="deleteRule(${rule.id})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function renderMedicinesTable(medicines) {
    const tbody = document.querySelector('#medicines-tab tbody');
    tbody.innerHTML = '';
    
    medicines.forEach(medicine => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${medicine.id}</td>
            <td>${medicine.name}</td>
            <td>${medicine.category}</td>
            <td>${medicine.dosage}</td>
            <td>${medicine.is_prescription ? 'Ya' : 'Tidak'}</td>
            <td>
                <button class="btn-action btn-edit" onclick="editMedicine(${medicine.id})">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn-action btn-delete" onclick="deleteMedicine(${medicine.id})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function renderConsultationsTable(consultations) {
    const tbody = document.querySelector('#consultations-tab tbody');
    tbody.innerHTML = '';
    
    consultations.forEach(consultation => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${consultation.id}</td>
            <td>${consultation.full_name}</td>
            <td>${consultation.main_symptom}</td>
            <td>${new Date(consultation.created_at).toLocaleDateString('id-ID')}</td>
            <td>
                <span class="status-badge status-completed">
                    Selesai
                </span>
            </td>
            <td>
                <button class="btn-action btn-edit" onclick="viewConsultation(${consultation.id})">
                    <i class="fas fa-eye"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function openModal(modalId) {
    document.getElementById(modalId).style.display = 'flex';
}

function closeModal() {
    document.querySelectorAll('.form-modal').forEach(modal => {
        modal.style.display = 'none';
    });
}

// Placeholder functions for CRUD operations
function editUser(userId) {
    alert('Edit user: ' + userId);
    // Implement edit user functionality
}

function deleteUser(userId) {
    if (confirm('Apakah Anda yakin ingin menghapus user ini?')) {
        // Implement delete user functionality
    }
}

function editRule(ruleId) {
    alert('Edit rule: ' + ruleId);
    // Implement edit rule functionality
}

function deleteRule(ruleId) {
    if (confirm('Apakah Anda yakin ingin menghapus rule ini?')) {
        // Implement delete rule functionality
    }
}

function editMedicine(medicineId) {
    alert('Edit medicine: ' + medicineId);
    // Implement edit medicine functionality
}

function deleteMedicine(medicineId) {
    if (confirm('Apakah Anda yakin ingin menghapus obat ini?')) {
        // Implement delete medicine functionality
    }
}

function viewConsultation(consultationId) {
    window.open(`result.php?consultation_id=${consultationId}&admin=true`, '_blank');
}