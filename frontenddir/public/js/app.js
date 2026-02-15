// Tab Navigation
function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Remove active class from all buttons
    document.querySelectorAll('.nav-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Show selected tab
    document.getElementById(tabName).classList.add('active');
    
    // Add active class to clicked button
    event.target.classList.add('active');
    
    // Load data for the tab
    if (tabName === 'dashboard') {
        loadDashboard();
    } else if (tabName === 'patients') {
        loadPatients();
    } else if (tabName === 'appointments') {
        loadAppointments();
        loadPatientsForAppointment();
    }
}

// ==================== DASHBOARD ====================
async function loadDashboard() {
    try {
        const response = await fetch(`${API_URL}/analytics`);
        const data = await response.json();
        
        document.getElementById('totalPatients').textContent = data.totalPatients || 0;
        document.getElementById('todayAppointments').textContent = data.todayAppointments || 0;
        document.getElementById('upcomingAppointments').textContent = data.upcomingAppointments || 0;
    } catch (error) {
        console.error('Error loading dashboard:', error);
        showError('Failed to load dashboard data. Make sure backend is running on ' + API_URL);
    }
}

// ==================== PATIENTS ====================
let allPatients = [];

async function loadPatients() {
    try {
        const response = await fetch(`${API_URL}/patients`);
        allPatients = await response.json();
        displayPatients(allPatients);
    } catch (error) {
        console.error('Error loading patients:', error);
        document.getElementById('patientsList').innerHTML = '<p class="no-data">Failed to load patients. Make sure backend is running.</p>';
    }
}

function displayPatients(patients) {
    const container = document.getElementById('patientsList');
    
    if (patients.length === 0) {
        container.innerHTML = '<p class="no-data">No patients found</p>';
        return;
    }
    
    container.innerHTML = patients.map(patient => `
        <div class="patient-card">
            <div class="patient-header">
                <h3>${patient.animalName}</h3>
                <span class="species-badge">${patient.species}</span>
            </div>
            <div class="patient-info">
                <p><strong>Breed:</strong> ${patient.breed || 'N/A'}</p>
                <p><strong>Age:</strong> ${patient.age ? patient.age + ' months' : 'N/A'}</p>
                <p><strong>Gender:</strong> ${patient.gender || 'N/A'}</p>
                <p><strong>Owner:</strong> ${patient.ownerName}</p>
                <p><strong>Phone:</strong> ${patient.ownerPhone}</p>
            </div>
            <div class="patient-actions">
                <button class="btn-danger" onclick="deletePatient(${patient.id})">Delete</button>
            </div>
        </div>
    `).join('');
}

function searchPatients() {
    const searchTerm = document.getElementById('patientSearch').value.toLowerCase();
    const filtered = allPatients.filter(patient => 
        patient.animalName.toLowerCase().includes(searchTerm) ||
        patient.ownerName.toLowerCase().includes(searchTerm)
    );
    displayPatients(filtered);
}

function togglePatientForm() {
    const form = document.getElementById('patientForm');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
    
    if (form.style.display === 'none') {
        document.getElementById('animalName').value = '';
        document.getElementById('species').value = '';
        document.getElementById('breed').value = '';
        document.getElementById('age').value = '';
        document.getElementById('gender').value = '';
        document.getElementById('ownerName').value = '';
        document.getElementById('ownerPhone').value = '';
        document.getElementById('ownerAddress').value = '';
        document.getElementById('medicalHistory').value = '';
    }
}

async function savePatient(event) {
    event.preventDefault();
    
    const patient = {
        animalName: document.getElementById('animalName').value,
        species: document.getElementById('species').value,
        breed: document.getElementById('breed').value || null,
        age: document.getElementById('age').value || null,
        gender: document.getElementById('gender').value || null,
        ownerName: document.getElementById('ownerName').value,
        ownerPhone: document.getElementById('ownerPhone').value,
        ownerAddress: document.getElementById('ownerAddress').value || null,
        medicalHistory: document.getElementById('medicalHistory').value || null
    };
    
    try {
        const response = await fetch(`${API_URL}/patients`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(patient)
        });
        
        if (response.ok) {
            alert('Patient registered successfully!');
            togglePatientForm();
            loadPatients();
        } else {
            alert('Failed to register patient');
        }
    } catch (error) {
        console.error('Error saving patient:', error);
        alert('Error registering patient. Make sure backend is running.');
    }
}

async function deletePatient(id) {
    if (!confirm('Are you sure you want to delete this patient?')) {
        return;
    }
    
    try {
        const response = await fetch(`${API_URL}/patients/${id}`, {
            method: 'DELETE'
        });
        
        if (response.ok) {
            alert('Patient deleted successfully!');
            loadPatients();
        } else {
            alert('Failed to delete patient');
        }
    } catch (error) {
        console.error('Error deleting patient:', error);
        alert('Error deleting patient');
    }
}

// ==================== APPOINTMENTS ====================
let allAppointments = [];

async function loadAppointments() {
    try {
        const response = await fetch(`${API_URL}/appointments`);
        allAppointments = await response.json();
        displayAppointments(allAppointments);
    } catch (error) {
        console.error('Error loading appointments:', error);
        document.getElementById('appointmentsList').innerHTML = '<p class="no-data">Failed to load appointments. Make sure backend is running.</p>';
    }
}

function displayAppointments(appointments) {
    const container = document.getElementById('appointmentsList');
    
    if (appointments.length === 0) {
        container.innerHTML = '<p class="no-data">No appointments found</p>';
        return;
    }
    
    const tableHTML = `
        <table>
            <thead>
                <tr>
                    <th>Patient</th>
                    <th>Owner</th>
                    <th>Date & Time</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                ${appointments.map(apt => `
                    <tr>
                        <td>${apt.patient.animalName}</td>
                        <td>${apt.patient.ownerName}</td>
                        <td>${formatDate(apt.appointmentDate)}</td>
                        <td>${apt.reason || 'N/A'}</td>
                        <td><span class="status-badge status-${apt.status.toLowerCase()}">${apt.status}</span></td>
                        <td>
                            ${apt.status === 'SCHEDULED' ? 
                                `<button class="btn-success" onclick="updateAppointmentStatus(${apt.id}, 'COMPLETED')">Complete</button>` : 
                                ''}
                            <button class="btn-danger" onclick="deleteAppointment(${apt.id})" style="margin-left: 0.5rem;">Delete</button>
                        </td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;
    
    container.innerHTML = tableHTML;
}

async function loadPatientsForAppointment() {
    try {
        const response = await fetch(`${API_URL}/patients`);
        const patients = await response.json();
        
        const select = document.getElementById('appointmentPatient');
        select.innerHTML = '<option value="">Select Patient *</option>' +
            patients.map(p => `<option value="${p.id}">${p.animalName} - ${p.ownerName}</option>`).join('');
    } catch (error) {
        console.error('Error loading patients for appointment:', error);
    }
}

function toggleAppointmentForm() {
    const form = document.getElementById('appointmentForm');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
    
    if (form.style.display === 'none') {
        document.getElementById('appointmentPatient').value = '';
        document.getElementById('appointmentDate').value = '';
        document.getElementById('appointmentReason').value = '';
        document.getElementById('appointmentNotes').value = '';
    }
}

async function saveAppointment(event) {
    event.preventDefault();
    
    const appointment = {
        patient: {
            id: document.getElementById('appointmentPatient').value
        },
        appointmentDate: document.getElementById('appointmentDate').value,
        reason: document.getElementById('appointmentReason').value || null,
        notes: document.getElementById('appointmentNotes').value || null,
        status: 'SCHEDULED'
    };
    
    try {
        const response = await fetch(`${API_URL}/appointments`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(appointment)
        });
        
        if (response.ok) {
            alert('Appointment scheduled successfully!');
            toggleAppointmentForm();
            loadAppointments();
        } else {
            alert('Failed to schedule appointment');
        }
    } catch (error) {
        console.error('Error saving appointment:', error);
        alert('Error scheduling appointment. Make sure backend is running.');
    }
}

async function updateAppointmentStatus(id, status) {
    try {
        const appointment = allAppointments.find(a => a.id === id);
        const updated = { ...appointment, status: status };
        
        const response = await fetch(`${API_URL}/appointments/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(updated)
        });
        
        if (response.ok) {
            alert('Appointment status updated!');
            loadAppointments();
        } else {
            alert('Failed to update appointment');
        }
    } catch (error) {
        console.error('Error updating appointment:', error);
        alert('Error updating appointment');
    }
}

async function deleteAppointment(id) {
    if (!confirm('Are you sure you want to delete this appointment?')) {
        return;
    }
    
    try {
        const response = await fetch(`${API_URL}/appointments/${id}`, {
            method: 'DELETE'
        });
        
        if (response.ok) {
            alert('Appointment deleted successfully!');
            loadAppointments();
        } else {
            alert('Failed to delete appointment');
        }
    } catch (error) {
        console.error('Error deleting appointment:', error);
        alert('Error deleting appointment');
    }
}

// ==================== UTILITIES ====================
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function showError(message) {
    console.error(message);
    // You can add a toast notification here if needed
}

// ==================== INITIALIZATION ====================
// Load dashboard on page load
window.addEventListener('DOMContentLoaded', () => {
    loadDashboard();
});
