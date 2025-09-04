<div class="space-y-6 p-6">
    <!-- Welcome Header -->
    <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl p-6 text-white text-center">
        <h1 class="text-2xl font-bold">Welcome, {{ auth()->user()->name }}!</h1>
        <p class="text-blue-100 mt-2">Interior Designer QR Scanner</p>
    </div>

    <!-- Progress Steps -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between text-sm text-gray-500 mb-6">
            <div id="step-1-indicator" class="flex items-center space-x-2">
                <div class="flex items-center justify-center w-8 h-8 bg-blue-500 text-white rounded-full">1</div>
                <span class="font-medium text-blue-600">Scan QR</span>
            </div>
            <div class="flex-1 h-0.5 bg-gray-200 mx-4"></div>
            <div id="step-2-indicator" class="flex items-center space-x-2">
                <div class="flex items-center justify-center w-8 h-8 bg-gray-200 text-gray-500 rounded-full">2</div>
                <span>Verify Sponsor</span>
            </div>
            <div class="flex-1 h-0.5 bg-gray-200 mx-4"></div>
            <div id="step-3-indicator" class="flex items-center space-x-2">
                <div class="flex items-center justify-center w-8 h-8 bg-gray-200 text-gray-500 rounded-full">3</div>
                <span>Take Photo</span>
            </div>
            <div class="flex-1 h-0.5 bg-gray-200 mx-4"></div>
            <div id="step-4-indicator" class="flex items-center space-x-2">
                <div class="flex items-center justify-center w-8 h-8 bg-gray-200 text-gray-500 rounded-full">4</div>
                <span>Submit</span>
            </div>
        </div>
    </div>

    <!-- Step 1: QR Scanner -->
    <div id="step-1" class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-800">Step 1: Scan QR Code</h2>
            <div class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">Active</div>
        </div>
        
        <div class="flex justify-center mb-4">
            <div class="relative">
                <div id="qr-reader" style="width: 300px; height: 300px;" class="border-2 border-gray-300 rounded-lg"></div>
                <div id="loading-overlay" class="absolute inset-0 bg-gray-100 flex items-center justify-center rounded-lg">
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto mb-2"></div>
                        <p class="text-sm text-gray-600">Starting camera...</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center space-x-2">
            <button id="start-scanner" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg font-medium">
                <i class="fas fa-qrcode mr-2"></i>Start Scanner
            </button>
            <button id="stop-scanner" class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-lg font-medium hidden">
                <i class="fas fa-stop mr-2"></i>Stop Scanner
            </button>
        </div>
        
        <div class="mt-4 text-center text-sm text-gray-600">
            <p>💡 <strong>Testing:</strong> Use QR codes with sponsor IDs: 1, 2, 3, 4, 5, 6, 7, or 8</p>
            <p>QR codes can contain: "3", "sponsor=3", "SPONSOR-3", or full URLs</p>
        </div>
    </div>

    <!-- Status Messages -->
    <div id="status-message" class="hidden">
        <div id="status-content" class="p-4 rounded-lg font-medium"></div>
    </div>

    <!-- Step 2: Sponsor Information -->
    <div id="step-2" class="hidden bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-800">Step 2: Verify Sponsor Information</h2>
            <div class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                <i class="fas fa-check mr-1"></i>Verified
            </div>
        </div>
        
        <div id="sponsor-details" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Sponsor details will be populated here -->
        </div>
        
        <div class="mt-6 text-center space-x-4">
            <button id="confirm-sponsor" class="bg-green-500 hover:bg-green-600 text-white px-8 py-3 rounded-lg font-medium">
                <i class="fas fa-check mr-2"></i>Confirm & Continue
            </button>
            <button id="rescan-qr" class="bg-gray-500 hover:bg-gray-600 text-white px-8 py-3 rounded-lg font-medium">
                <i class="fas fa-redo mr-2"></i>Scan Again
            </button>
        </div>
    </div>

    <!-- Step 3: Photo Capture -->
    <div id="step-3" class="hidden bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-800">Step 3: Take Site Photo</h2>
            <div class="bg-orange-100 text-orange-800 px-3 py-1 rounded-full text-sm font-medium">
                <i class="fas fa-camera mr-1"></i>Photo Required
            </div>
        </div>
        
        <div class="text-center space-y-4">
            <!-- Mobile Camera Input -->
            <div class="flex justify-center">
                <label for="site-photo" class="cursor-pointer bg-blue-500 hover:bg-blue-600 text-white px-8 py-3 rounded-lg font-medium inline-flex items-center">
                    <i class="fas fa-camera mr-2"></i>Take Photo
                    <input type="file" id="site-photo" accept="image/*" capture="environment" class="hidden">
                </label>
            </div>
            
            <!-- Photo Preview -->
            <div id="photo-preview" class="hidden">
                <div class="flex justify-center mb-4">
                    <img id="preview-image" src="" alt="Site photo preview" class="max-w-xs max-h-64 rounded-lg shadow-lg">
                </div>
                <div class="flex justify-center space-x-4">
                    <button id="confirm-photo" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg font-medium">
                        <i class="fas fa-check mr-2"></i>Use This Photo
                    </button>
                    <button id="retake-photo" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium">
                        <i class="fas fa-redo mr-2"></i>Retake
                    </button>
                </div>
            </div>
            
            <p class="text-sm text-gray-600">
                📸 Take a clear photo of the site or sponsor location
            </p>
        </div>
    </div>

    <!-- Step 4: Submit Visit -->
    <div id="step-4" class="hidden bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-800">Step 4: Submit Visit Log</h2>
            <div class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-medium">
                <i class="fas fa-upload mr-1"></i>Ready to Submit
            </div>
        </div>
        
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <h3 class="font-semibold text-gray-800 mb-2">Visit Summary</h3>
            <div id="visit-summary">
                <!-- Summary will be populated here -->
            </div>
        </div>
        
        <div class="text-center space-x-4">
            <button id="submit-visit" class="bg-green-500 hover:bg-green-600 text-white px-8 py-3 rounded-lg font-medium">
                <i class="fas fa-check-circle mr-2"></i>Submit Visit Log
            </button>
            <button id="start-over" class="bg-gray-500 hover:bg-gray-600 text-white px-8 py-3 rounded-lg font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Start Over
            </button>
        </div>
        
        <div id="submit-progress" class="hidden mt-4 text-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-500 mx-auto mb-2"></div>
            <p class="text-sm text-gray-600">Submitting visit log...</p>
        </div>
    </div>

    <!-- Success Message -->
    <div id="success-message" class="hidden bg-green-50 border border-green-200 rounded-xl p-6">
        <div class="text-center">
            <div class="text-green-500 text-4xl mb-2">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2 class="text-xl font-semibold text-green-800 mb-2">Visit Logged Successfully!</h2>
            <p class="text-green-700 mb-4">Your site visit has been recorded.</p>
            <button id="log-another" class="bg-green-500 hover:bg-green-600 text-white px-8 py-3 rounded-lg font-medium">
                <i class="fas fa-plus mr-2"></i>Log Another Visit
            </button>
        </div>
    </div>
</div>

<!-- Meta tag for CSRF token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Include QR Code Scanner Library -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>

<script>
let html5QrcodeScanner;
let currentSponsor = null;
let capturedPhoto = null;

// Initialize the page
document.addEventListener('DOMContentLoaded', function() {
    console.log('Page loaded, initializing QR scanner...');
    initializeQRScanner();
    setupEventListeners();
});

function initializeQRScanner() {
    console.log('Setting up QR scanner...');
    
    // Auto-start the scanner
    setTimeout(() => {
        startQRScanner();
    }, 1000);
}

function setupEventListeners() {
    // QR Scanner buttons
    document.getElementById('start-scanner').addEventListener('click', startQRScanner);
    document.getElementById('stop-scanner').addEventListener('click', stopQRScanner);
    
    // Sponsor verification
    document.getElementById('confirm-sponsor')?.addEventListener('click', proceedToPhotoStep);
    document.getElementById('rescan-qr')?.addEventListener('click', restartScanning);
    
    // Photo capture
    document.getElementById('site-photo').addEventListener('change', handlePhotoSelection);
    document.getElementById('confirm-photo')?.addEventListener('click', proceedToSubmitStep);
    document.getElementById('retake-photo')?.addEventListener('click', retakePhoto);
    
    // Visit submission
    document.getElementById('submit-visit')?.addEventListener('click', submitVisit);
    document.getElementById('start-over')?.addEventListener('click', startOver);
    document.getElementById('log-another')?.addEventListener('click', startOver);
}

function startQRScanner() {
    console.log('Starting QR scanner...');
    
    html5QrcodeScanner = new Html5Qrcode("qr-reader");
    
    const config = {
        fps: 10,
        qrbox: { width: 250, height: 250 },
        aspectRatio: 1.0
    };

    html5QrcodeScanner.start(
        { facingMode: "environment" },
        config,
        onScanSuccess,
        onScanError
    ).then(() => {
        console.log('QR scanner started successfully');
        document.getElementById('loading-overlay').style.display = 'none';
        document.getElementById('start-scanner').classList.add('hidden');
        document.getElementById('stop-scanner').classList.remove('hidden');
    }).catch((error) => {
        console.error('Failed to start QR scanner:', error);
        document.getElementById('loading-overlay').style.display = 'none';
        showStatus('error', 'Failed to start camera. Please check permissions.');
    });
}

function stopQRScanner() {
    if (html5QrcodeScanner) {
        html5QrcodeScanner.stop().then(() => {
            console.log('QR scanner stopped');
            document.getElementById('start-scanner').classList.remove('hidden');
            document.getElementById('stop-scanner').classList.add('hidden');
            document.getElementById('loading-overlay').style.display = 'flex';
        }).catch((error) => {
            console.error('Error stopping scanner:', error);
        });
    }
}

function onScanSuccess(decodedText, decodedResult) {
    console.log('QR Code detected:', decodedText);
    
    // Parse sponsor ID from QR code
    const sponsorId = parseSponsorId(decodedText);
    
    if (sponsorId) {
        console.log('Parsed sponsor ID:', sponsorId);
        stopQRScanner();
        fetchSponsorData(sponsorId);
    } else {
        console.warn('Could not parse sponsor ID from QR code:', decodedText);
        showStatus('error', 'Invalid QR code format. Please scan a valid sponsor QR code.');
    }
}

function onScanError(error) {
    // Suppress frequent scan errors
    if (error.includes('NotFoundException')) {
        return;
    }
    console.log('QR Scan error:', error);
}

function parseSponsorId(qrText) {
    console.log('Parsing QR text:', qrText);
    
    // Try different parsing methods
    const patterns = [
        /sponsor[=_-](\d+)/i,  // sponsor=123, sponsor_123, sponsor-123
        /SPONSOR[=_-](\d+)/,   // SPONSOR=123, SPONSOR_123, SPONSOR-123
        /(\d+)$/,              // Just a number at the end
        /^(\d+)$/              // Just a number
    ];
    
    for (const pattern of patterns) {
        const match = qrText.match(pattern);
        if (match) {
            const id = parseInt(match[1]);
            console.log('Found sponsor ID:', id);
            return id;
        }
    }
    
    // Check if URL contains sponsor parameter
    try {
        const url = new URL(qrText);
        const sponsorParam = url.searchParams.get('sponsor');
        if (sponsorParam) {
            const id = parseInt(sponsorParam);
            console.log('Found sponsor ID in URL parameter:', id);
            return id;
        }
    } catch (e) {
        // Not a valid URL, ignore
    }
    
    console.warn('Could not parse sponsor ID from:', qrText);
    return null;
}

function fetchSponsorData(sponsorId) {
    console.log('Fetching sponsor data for ID:', sponsorId);
    showStatus('info', 'Looking up sponsor information...');
    
    fetch(`/api/sponsors/${sponsorId}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        console.log('API Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Sponsor data received:', data);
        if (data.id) {
            currentSponsor = data;
            displaySponsorInfo(data);
            moveToStep(2);
            showStatus('success', 'Sponsor verified successfully!');
        } else {
            console.error('Invalid sponsor data format:', data);
            showStatus('error', 'Invalid sponsor data received.');
        }
    })
    .catch(error => {
        console.error('Error fetching sponsor:', error);
        showStatus('error', `Sponsor ${sponsorId} not found. Error: ${error.message}`);
    });
}

function displaySponsorInfo(sponsor) {
    const detailsContainer = document.getElementById('sponsor-details');
    detailsContainer.innerHTML = `
        <div class="space-y-3">
            <div class="bg-blue-50 p-4 rounded-lg">
                <h3 class="font-semibold text-blue-800 text-lg">${sponsor.name || 'N/A'}</h3>
                <p class="text-blue-600">${sponsor.company_name || 'N/A'}</p>
            </div>
        </div>
        <div class="space-y-3">
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="grid grid-cols-1 gap-2 text-sm">
                    <div><strong>Contact:</strong> ${sponsor.contact || 'N/A'}</div>
                    <div><strong>Location:</strong> ${sponsor.location || 'N/A'}</div>
                    <div><strong>Description:</strong> ${sponsor.description || 'N/A'}</div>
                </div>
            </div>
        </div>
    `;
}

function moveToStep(stepNumber) {
    console.log('Moving to step:', stepNumber);
    
    // Hide all steps
    document.getElementById('step-1').classList.add('hidden');
    document.getElementById('step-2').classList.add('hidden');
    document.getElementById('step-3').classList.add('hidden');
    document.getElementById('step-4').classList.add('hidden');
    
    // Show current step
    document.getElementById(`step-${stepNumber}`).classList.remove('hidden');
    
    // Update step indicators
    for (let i = 1; i <= 4; i++) {
        const indicator = document.getElementById(`step-${i}-indicator`);
        const circle = indicator.querySelector('div');
        const text = indicator.querySelector('span');
        
        if (i < stepNumber) {
            // Completed step
            circle.className = 'flex items-center justify-center w-8 h-8 bg-green-500 text-white rounded-full';
            text.className = 'font-medium text-green-600';
            circle.innerHTML = '<i class="fas fa-check text-sm"></i>';
        } else if (i === stepNumber) {
            // Current step
            circle.className = 'flex items-center justify-center w-8 h-8 bg-blue-500 text-white rounded-full';
            text.className = 'font-medium text-blue-600';
            circle.textContent = i;
        } else {
            // Future step
            circle.className = 'flex items-center justify-center w-8 h-8 bg-gray-200 text-gray-500 rounded-full';
            text.className = '';
            circle.textContent = i;
        }
    }
}

function proceedToPhotoStep() {
    moveToStep(3);
    showStatus('info', 'Please take a photo of the site.');
}

function restartScanning() {
    currentSponsor = null;
    moveToStep(1);
    setTimeout(() => {
        startQRScanner();
    }, 500);
}

function handlePhotoSelection(event) {
    const file = event.target.files[0];
    if (file) {
        console.log('Photo selected:', file.name, file.size);
        
        if (file.size > 10 * 1024 * 1024) {
            showStatus('error', 'Photo is too large. Please select a file under 10MB.');
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-image').src = e.target.result;
            document.getElementById('photo-preview').classList.remove('hidden');
            capturedPhoto = file;
        };
        reader.readAsDataURL(file);
    }
}

function proceedToSubmitStep() {
    if (!capturedPhoto) {
        showStatus('error', 'Please take a photo first.');
        return;
    }
    
    moveToStep(4);
    displayVisitSummary();
}

function retakePhoto() {
    document.getElementById('site-photo').value = '';
    document.getElementById('photo-preview').classList.add('hidden');
    capturedPhoto = null;
}

function displayVisitSummary() {
    const summaryContainer = document.getElementById('visit-summary');
    const visitTime = new Date().toLocaleString();
    
    summaryContainer.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div><strong>Sponsor:</strong> ${currentSponsor.name}</div>
            <div><strong>Company:</strong> ${currentSponsor.company_name}</div>
            <div><strong>Location:</strong> ${currentSponsor.location}</div>
            <div><strong>Visit Time:</strong> ${visitTime}</div>
            <div><strong>Photo:</strong> ✓ Captured</div>
            <div><strong>Designer:</strong> {{ auth()->user()->name }}</div>
        </div>
    `;
}

function submitVisit() {
    if (!currentSponsor || !capturedPhoto) {
        showStatus('error', 'Missing required information.');
        return;
    }
    
    console.log('Submitting visit...');
    document.getElementById('submit-progress').classList.remove('hidden');
    
    const formData = new FormData();
    formData.append('sponsor_id', currentSponsor.id);
    formData.append('site_photo', capturedPhoto);
    formData.append('visited_at', new Date().toISOString());
    
    fetch('/api/visits', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('Submit response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Submit response:', data);
        document.getElementById('submit-progress').classList.add('hidden');
        
        if (data.success) {
            showSuccessMessage();
        } else {
            showStatus('error', data.message || 'Failed to submit visit.');
        }
    })
    .catch(error => {
        console.error('Submit error:', error);
        document.getElementById('submit-progress').classList.add('hidden');
        showStatus('error', 'Error submitting visit: ' + error.message);
    });
}

function showSuccessMessage() {
    // Hide all steps
    document.getElementById('step-1').classList.add('hidden');
    document.getElementById('step-2').classList.add('hidden');
    document.getElementById('step-3').classList.add('hidden');
    document.getElementById('step-4').classList.add('hidden');
    
    // Show success message
    document.getElementById('success-message').classList.remove('hidden');
    
    // Clear status
    hideStatus();
}

function startOver() {
    currentSponsor = null;
    capturedPhoto = null;
    
    // Hide success message
    document.getElementById('success-message').classList.add('hidden');
    
    // Reset photo input
    document.getElementById('site-photo').value = '';
    document.getElementById('photo-preview').classList.add('hidden');
    
    // Clear status
    hideStatus();
    
    // Move back to step 1
    moveToStep(1);
    
    // Restart scanner after a short delay
    setTimeout(() => {
        startQRScanner();
    }, 500);
}

function showStatus(type, message) {
    const statusMessage = document.getElementById('status-message');
    const statusContent = document.getElementById('status-content');
    
    statusMessage.classList.remove('hidden');
    
    // Reset classes
    statusContent.className = 'p-4 rounded-lg font-medium';
    
    switch (type) {
        case 'success':
            statusContent.classList.add('bg-green-50', 'text-green-800', 'border', 'border-green-200');
            break;
        case 'error':
            statusContent.classList.add('bg-red-50', 'text-red-800', 'border', 'border-red-200');
            break;
        case 'info':
            statusContent.classList.add('bg-blue-50', 'text-blue-800', 'border', 'border-blue-200');
            break;
    }
    
    statusContent.textContent = message;
    
    // Auto-hide success and info messages
    if (type === 'success' || type === 'info') {
        setTimeout(hideStatus, 3000);
    }
}

function hideStatus() {
    document.getElementById('status-message').classList.add('hidden');
}
</script>
