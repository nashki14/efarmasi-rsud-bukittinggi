// Consultation Process Management
let currentStep = 1;
let consultationData = {
    symptoms: [],
    details: {},
    patientInfo: {}
};

// Initialize consultation
document.addEventListener('DOMContentLoaded', function() {
    loadSymptoms();
    updateStepDisplay();
});

function loadSymptoms() {
    const symptoms = [
        { id: 1, name: 'Demam', category: 'Umum', icon: 'fas fa-thermometer-half' },
        { id: 2, name: 'Batuk', category: 'Pernapasan', icon: 'fas fa-lungs' },
        { id: 3, name: 'Pilek', category: 'Pernapasan', icon: 'fas fa-wind' },
        { id: 4, name: 'Sakit Kepala', category: 'Neurologi', icon: 'fas fa-head-side-virus' },
        { id: 5, name: 'Diare', category: 'Pencernaan', icon: 'fas fa-poop' },
        { id: 6, name: 'Mual', category: 'Pencernaan', icon: 'fas fa-dizzy' },
        { id: 7, name: 'Muntah', category: 'Pencernaan', icon: 'fas fa-vomit' },
        { id: 8, name: 'Nyeri Otot', category: 'Musculoskeletal', icon: 'fas fa-bone' },
        { id: 9, name: 'Gatal-gatal', category: 'Kulit', icon: 'fas fa-allergies' },
        { id: 10, name: 'Sesak Napas', category: 'Pernapasan', icon: 'fas fa-lungs-virus' }
    ];

    const symptomsGrid = document.getElementById('mainSymptoms');
    symptomsGrid.innerHTML = '';

    symptoms.forEach(symptom => {
        const symptomCard = document.createElement('div');
        symptomCard.className = 'symptom-card';
        symptomCard.innerHTML = `
            <i class="${symptom.icon}"></i>
            <h4>${symptom.name}</h4>
            <span class="symptom-category">${symptom.category}</span>
        `;
        symptomCard.addEventListener('click', () => toggleSymptom(symptom.name, symptomCard));
        symptomsGrid.appendChild(symptomCard);
    });
}

function toggleSymptom(symptomName, element) {
    const index = consultationData.symptoms.indexOf(symptomName);
    
    if (index === -1) {
        consultationData.symptoms.push(symptomName);
        element.classList.add('selected');
    } else {
        consultationData.symptoms.splice(index, 1);
        element.classList.remove('selected');
    }
    
    updateNextButton();
    hideStep1Error();
}

function hideStep1Error() {
    document.getElementById('step1Error').style.display = 'none';
}

function updateNextButton() {
    const nextButton = document.querySelector('.btn-next');
    if (currentStep === 1) {
        nextButton.disabled = consultationData.symptoms.length === 0;
    }
}

function nextStep() {
    if (validateCurrentStep()) {
        currentStep++;
        updateStepDisplay();
        loadStepContent();
    }
}

function prevStep() {
    currentStep--;
    updateStepDisplay();
    loadStepContent();
}

function updateStepDisplay() {
    // Update step indicators
    document.querySelectorAll('.step').forEach((step, index) => {
        const stepNumber = index + 1;
        step.classList.remove('active', 'completed');
        
        if (stepNumber === currentStep) {
            step.classList.add('active');
        } else if (stepNumber < currentStep) {
            step.classList.add('completed');
        }
    });
    
    // Update navigation buttons
    const prevButton = document.querySelector('.btn-prev');
    const nextButton = document.querySelector('.btn-next');
    
    prevButton.style.display = currentStep > 1 ? 'flex' : 'none';
    
    if (currentStep === 4) {
        nextButton.innerHTML = '<i class="fas fa-check"></i> Selesaikan Konsultasi';
        nextButton.onclick = finishConsultation;
    } else {
        nextButton.innerHTML = 'Selanjutnya <i class="fas fa-arrow-right"></i>';
        nextButton.onclick = nextStep;
    }
    
    updateNextButton();
}

function loadStepContent() {
    // Hide all step content
    document.querySelectorAll('.step-content').forEach(content => {
        content.classList.remove('active');
    });
    
    // Show current step content
    const currentContent = document.querySelector(`.step-content[data-step="${currentStep}"]`);
    if (currentContent) {
        currentContent.classList.add('active');
    }
    
    // Load specific content for each step
    switch(currentStep) {
        case 2:
            loadSymptomDetails();
            break;
        case 4:
            loadConfirmationData();
            break;
    }
}

function loadSymptomDetails() {
    const detailsContainer = document.getElementById('symptomDetails');
    detailsContainer.innerHTML = '';
    
    if (consultationData.symptoms.length === 0) {
        detailsContainer.innerHTML = '<p class="no-symptoms">Tidak ada gejala yang dipilih</p>';
        return;
    }
    
    consultationData.symptoms.forEach(symptom => {
        const symptomSection = document.createElement('div');
        symptomSection.className = 'symptom-detail-section';
        
        let specificQuestions = '';
        
        // Pertanyaan spesifik berdasarkan gejala
        switch(symptom) {
            case 'Demam':
                specificQuestions = `
                    <div class="form-group">
                        <label class="form-label">Berapa suhu tubuh Anda? (°C)</label>
                        <input type="number" name="temperature_${symptom}" class="form-input" step="0.1" min="35" max="42" placeholder="37.5">
                    </div>
                `;
                break;
            case 'Batuk':
                specificQuestions = `
                    <div class="form-group">
                        <label class="form-label">Jenis batuk?</label>
                        <select name="type_${symptom}" class="form-select">
                            <option value="">Pilih jenis batuk</option>
                            <option value="kering">Batuk Kering</option>
                            <option value="berdahak">Batuk Berdahak</option>
                        </select>
                    </div>
                `;
                break;
            case 'Diare':
                specificQuestions = `
                    <div class="form-group">
                        <label class="form-label">Frekuensi BAB per hari?</label>
                        <select name="frequency_${symptom}" class="form-select">
                            <option value="">Pilih frekuensi</option>
                            <option value="<3">Kurang dari 3 kali</option>
                            <option value="3-5">3-5 kali</option>
                            <option value=">5">Lebih dari 5 kali</option>
                        </select>
                    </div>
                `;
                break;
        }
        
        symptomSection.innerHTML = `
            <h4>Detail ${symptom}</h4>
            <div class="form-group">
                <label class="form-label">Berapa lama mengalami ${symptom.toLowerCase()}?</label>
                <select name="duration_${symptom}" class="form-select" required>
                    <option value="">Pilih durasi</option>
                    <option value="<1">Kurang dari 1 hari</option>
                    <option value="1-3">1-3 hari</option>
                    <option value="4-7">4-7 hari</option>
                    <option value=">7">Lebih dari 7 hari</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Seberapa parah ${symptom.toLowerCase()}?</label>
                <select name="severity_${symptom}" class="form-select" required>
                    <option value="">Pilih tingkat keparahan</option>
                    <option value="ringan">Ringan</option>
                    <option value="sedang">Sedang</option>
                    <option value="berat">Berat</option>
                </select>
            </div>
            ${specificQuestions}
        `;
        detailsContainer.appendChild(symptomSection);
    });
}

function loadConfirmationData() {
    const confirmationContainer = document.getElementById('confirmationData');
    
    let html = `
        <div class="confirmation-section">
            <h4>Gejala yang Dipilih:</h4>
            <ul class="symptoms-list">
    `;
    
    consultationData.symptoms.forEach(symptom => {
        html += `<li><i class="fas fa-check-circle"></i> ${symptom}</li>`;
    });
    
    html += `</ul>`;
    
    // Collect form data
    const formData = new FormData(document.getElementById('consultationForm'));
    consultationData.details = {};
    consultationData.patientInfo = {};
    
    for (let [key, value] of formData.entries()) {
        if (value) {
            if (key.startsWith('duration_') || key.startsWith('severity_') || key.startsWith('type_') || key.startsWith('frequency_') || key.startsWith('temperature_')) {
                consultationData.details[key] = value;
            } else {
                consultationData.patientInfo[key] = value;
            }
        }
    }
    
    // Display additional info
    if (Object.keys(consultationData.patientInfo).length > 0) {
        html += '<h4>Informasi Tambahan:</h4>';
        html += '<div class="info-grid">';
        for (let [key, value] of Object.entries(consultationData.patientInfo)) {
            if (value) {
                const label = getFieldLabel(key);
                html += `<div class="info-item"><strong>${label}:</strong> ${value}</div>`;
            }
        }
        html += '</div>';
    }
    
    html += '</div>';
    confirmationContainer.innerHTML = html;
}

function getFieldLabel(fieldName) {
    const labels = {
        'temperature': 'Suhu Tubuh',
        'allergies': 'Alergi Obat',
        'current_meds': 'Obat yang Dikonsumsi',
        'medical_history': 'Riwayat Penyakit',
        'symptom_duration': 'Durasi Gejala Utama'
    };
    return labels[fieldName] || fieldName;
}

function validateCurrentStep() {
    let isValid = true;
    
    if (currentStep === 1) {
        if (consultationData.symptoms.length === 0) {
            document.getElementById('step1Error').style.display = 'block';
            isValid = false;
        }
    } else {
        const currentContent = document.querySelector(`.step-content[data-step="${currentStep}"]`);
        const inputs = currentContent.querySelectorAll('select[required]');
        
        inputs.forEach(input => {
            if (!input.value) {
                input.classList.add('error');
                isValid = false;
            } else {
                input.classList.remove('error');
            }
        });
    }
    
    return isValid;
}

function finishConsultation() {
    if (validateCurrentStep()) {
        // Show loading
        const nextButton = document.querySelector('.btn-next');
        const originalText = nextButton.innerHTML;
        nextButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
        nextButton.disabled = true;

        // Prepare data for API
        const formData = new FormData(document.getElementById('consultationForm'));

        // Collect all data
        const requestData = {
            symptoms: consultationData.symptoms,
            details: consultationData.details,
            patientInfo: consultationData.patientInfo
        };
        
        console.log('Sending consultation data:', requestData);
        
        // Send to server
        fetch('api/consultation.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(requestData)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Response received:', data);
            if (data.success) {
                window.location.href = `result.php?consultation_id=${data.consultation_id}`;
            } else {
                throw new Error(data.message || 'Terjadi kesalahan');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memproses konsultasi: ' + error.message);
            nextButton.innerHTML = originalText;
            nextButton.disabled = false;
        });
    }
}
