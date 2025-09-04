<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>QR Scanner - Visit Logger</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
</head>
<body class="bg-gray-100 min-h-screen py-8">
    <div class="max-w-md mx-auto space-y-6">
        
        <!-- Header -->
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <h1 class="text-xl font-bold text-gray-800">QR Scanner</h1>
            <p class="text-gray-600">{{ auth()->user()->name ?? 'User' }}</p>
        </div>

        <!-- Status Messages -->
        <div id="status-message" class="hidden rounded-lg p-3 text-center"></div>

        <!-- QR Scanner -->
        <div class="bg-white rounded-lg shadow">
            <div class="border-b p-4">
                <h2 class="font-medium text-gray-800">Scan Sponsor QR Code</h2>
            </div>
            <div class="p-4 space-y-4">
                <div class="flex justify-center">
                    <div class="relative">
                        <div id="qr-reader" class="border-2 border-gray-300 rounded" style="width: 280px; height: 280px;"></div>
                        <div id="loading-overlay" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-90 rounded">
                            <div class="text-center">
                                <div class="animate-spin w-8 h-8 border-4 border-blue-500 border-t-transparent rounded-full mx-auto mb-2"></div>
                                <p class="text-sm text-gray-600">Starting camera...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-center space-x-3">
                    <button id="start-scanner" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">
                        Start Scanner
                    </button>
                    <button id="stop-scanner" class="hidden px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition">
                        Stop Scanner
                    </button>
                </div>

                <div class="text-center">
                    <button id="test-sponsor-3" class="px-3 py-1 bg-yellow-500 text-white rounded text-sm hover:bg-yellow-600">
                        Test Sponsor 3
                    </button>
                    <p class="text-xs text-gray-500 mt-1">For testing: enter numbers 1-8</p>
                </div>
            </div>
        </div>

        <!-- Sponsor Info (Hidden initially) -->
        <div id="sponsor-info" class="hidden bg-white rounded-lg shadow">
            <div class="border-b p-4 bg-green-50">
                <div class="flex justify-between items-center">
                    <h3 class="font-medium text-gray-800">Sponsor Found</h3>
                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">✓ Verified</span>
                </div>
            </div>
            <div class="p-4">
                <div id="sponsor-details" class="space-y-3 mb-4">
                    <!-- Details populated by JS -->
                </div>
                <div class="flex justify-center space-x-3">
                    <button id="take-photo" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Take Photo
                    </button>
                    <button id="rescan-qr" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                        Scan Again
                    </button>
                </div>
            </div>
        </div>

        <!-- Photo Capture (Hidden initially) -->
        <div id="photo-capture" class="hidden bg-white rounded-lg shadow">
            <div class="border-b p-4">
                <h3 class="font-medium text-gray-800">Take Site Photo</h3>
            </div>
            <div class="p-4 space-y-4">
                <div class="text-center">
                    <label for="site-photo" class="cursor-pointer inline-block px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Choose Photo
                        <input type="file" id="site-photo" accept="image/*" capture="environment" class="hidden">
                    </label>
                </div>
                
                <div id="photo-preview" class="hidden text-center">
                    <img id="preview-image" src="" alt="Preview" class="max-w-full max-h-48 rounded border mx-auto mb-3">
                    <div class="space-x-3">
                        <button id="confirm-photo" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                            Use Photo
                        </button>
                        <button id="retake-photo" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                            Try Again
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Visit (Hidden initially) -->
        <div id="submit-visit" class="hidden bg-white rounded-lg shadow">
            <div class="border-b p-4">
                <h3 class="font-medium text-gray-800">Submit Visit</h3>
            </div>
            <div class="p-4">
                <div class="bg-gray-50 rounded p-3 mb-4">
                    <h4 class="font-medium text-gray-700 mb-2">Summary</h4>
                    <div id="visit-summary" class="text-sm text-gray-600">
                        <!-- Summary populated by JS -->
                    </div>
                </div>
                
                <div class="flex justify-center space-x-3">
                    <button id="confirm-submit" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                        Submit Visit
                    </button>
                    <button id="start-over" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                        Start Over
                    </button>
                </div>
                
                <div id="submit-progress" class="hidden mt-4 text-center">
                    <div class="animate-spin w-6 h-6 border-2 border-green-500 border-t-transparent rounded-full mx-auto mb-2"></div>
                    <p class="text-sm text-gray-600">Submitting...</p>
                </div>
            </div>
        </div>

        <!-- Success (Hidden initially) -->
        <div id="success-message" class="hidden bg-green-50 border border-green-200 rounded-lg">
            <div class="p-4 text-center">
                <div class="w-12 h-12 bg-green-500 text-white rounded-full flex items-center justify-center mx-auto mb-3 text-xl">
                    ✓
                </div>
                <h3 class="font-bold text-green-800 mb-2">Success!</h3>
                <p class="text-green-600 text-sm mb-4">Visit logged successfully</p>
                <button id="log-another-visit" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                    Log Another Visit
                </button>
            </div>
        </div>
    </div>

    <script>
    let html5QrcodeScanner;
    let currentSponsor = null;
    let capturedPhoto = null;

    document.addEventListener('DOMContentLoaded', function() {
        setupEventListeners();
        setTimeout(startQRScanner, 1000);
    });

    function setupEventListeners() {
        document.getElementById('start-scanner').addEventListener('click', startQRScanner);
        document.getElementById('stop-scanner').addEventListener('click', stopQRScanner);
        document.getElementById('test-sponsor-3').addEventListener('click', () => fetchSponsorData(3));
        document.getElementById('take-photo').addEventListener('click', showPhotoCapture);
        document.getElementById('rescan-qr').addEventListener('click', restartScanning);
        document.getElementById('site-photo').addEventListener('change', handlePhotoSelection);
        document.getElementById('confirm-photo').addEventListener('click', showSubmitVisit);
        document.getElementById('retake-photo').addEventListener('click', retakePhoto);
        document.getElementById('confirm-submit').addEventListener('click', submitVisit);
        document.getElementById('start-over').addEventListener('click', startOver);
        document.getElementById('log-another-visit').addEventListener('click', startOver);
    }

    function startQRScanner() {
        html5QrcodeScanner = new Html5Qrcode("qr-reader");
        
        const config = {
            fps: 10,
            qrbox: { width: 220, height: 220 }
        };

        html5QrcodeScanner.start(
            { facingMode: "environment" },
            config,
            onScanSuccess,
            onScanError
        ).then(() => {
            document.getElementById('loading-overlay').style.display = 'none';
            document.getElementById('start-scanner').classList.add('hidden');
            document.getElementById('stop-scanner').classList.remove('hidden');
        }).catch((error) => {
            console.error('Scanner failed:', error);
            showStatus('error', 'Failed to start camera. Check permissions.');
        });
    }

    function stopQRScanner() {
        if (html5QrcodeScanner) {
            html5QrcodeScanner.stop().then(() => {
                document.getElementById('start-scanner').classList.remove('hidden');
                document.getElementById('stop-scanner').classList.add('hidden');
                document.getElementById('loading-overlay').style.display = 'flex';
            });
        }
    }

    function onScanSuccess(decodedText) {
        console.log('QR detected:', decodedText);
        const sponsorId = parseSponsorId(decodedText);
        if (sponsorId) {
            stopQRScanner();
            fetchSponsorData(sponsorId);
        } else {
            showStatus('error', 'Invalid QR code format');
        }
    }

    function onScanError(error) {
        // Ignore frequent scan errors
    }

    function parseSponsorId(qrText) {
        // Just a number
        if (/^\d+$/.test(qrText)) {
            return parseInt(qrText);
        }
        
        // sponsor=123 format
        const match = qrText.match(/sponsor[=_-](\d+)/i);
        if (match) {
            return parseInt(match[1]);
        }
        
        return null;
    }

    function fetchSponsorData(sponsorId) {
        showStatus('info', 'Looking up sponsor...');
        
        fetch(`/api/sponsors/${sponsorId}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.id) {
                currentSponsor = data;
                displaySponsorInfo(data);
                showSponsorInfo();
                showStatus('success', 'Sponsor verified!');
            } else {
                showStatus('error', 'Invalid sponsor data');
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            showStatus('error', `Error: ${error.message}`);
        });
    }

    function displaySponsorInfo(sponsor) {
        document.getElementById('sponsor-details').innerHTML = `
            <div class="bg-blue-50 p-3 rounded">
                <h4 class="font-medium text-blue-800">${sponsor.name}</h4>
                <p class="text-blue-600 text-sm">${sponsor.company_name}</p>
            </div>
            <div class="text-sm space-y-1">
                <div><strong>Contact:</strong> ${sponsor.contact || 'N/A'}</div>
                <div><strong>Location:</strong> ${sponsor.location || 'N/A'}</div>
                <div><strong>Description:</strong> ${sponsor.description || 'N/A'}</div>
            </div>
        `;
    }

    function showSponsorInfo() {
        hideAllSections();
        document.getElementById('sponsor-info').classList.remove('hidden');
    }

    function showPhotoCapture() {
        hideAllSections();
        document.getElementById('photo-capture').classList.remove('hidden');
    }

    function handlePhotoSelection(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview-image').src = e.target.result;
                document.getElementById('photo-preview').classList.remove('hidden');
                capturedPhoto = file;
            };
            reader.readAsDataURL(file);
        }
    }

    function retakePhoto() {
        document.getElementById('site-photo').value = '';
        document.getElementById('photo-preview').classList.add('hidden');
        capturedPhoto = null;
    }

    function showSubmitVisit() {
        if (!capturedPhoto) {
            showStatus('error', 'Please select a photo first');
            return;
        }
        
        hideAllSections();
        document.getElementById('submit-visit').classList.remove('hidden');
        displayVisitSummary();
    }

    function displayVisitSummary() {
        const visitTime = new Date().toLocaleString();
        document.getElementById('visit-summary').innerHTML = `
            <div class="space-y-1">
                <div><strong>Sponsor:</strong> ${currentSponsor.name}</div>
                <div><strong>Company:</strong> ${currentSponsor.company_name}</div>
                <div><strong>Location:</strong> ${currentSponsor.location}</div>
                <div><strong>Time:</strong> ${visitTime}</div>
                <div><strong>Photo:</strong> ✓ Ready</div>
            </div>
        `;
    }

    function submitVisit() {
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
        .then(response => response.json())
        .then(data => {
            document.getElementById('submit-progress').classList.add('hidden');
            if (data.success) {
                showSuccessMessage();
            } else {
                showStatus('error', data.message || 'Failed to submit');
            }
        })
        .catch(error => {
            document.getElementById('submit-progress').classList.add('hidden');
            showStatus('error', 'Submit failed: ' + error.message);
        });
    }

    function showSuccessMessage() {
        hideAllSections();
        document.getElementById('success-message').classList.remove('hidden');
        hideStatus();
    }

    function restartScanning() {
        currentSponsor = null;
        hideAllSections();
        document.getElementById('qr-reader').innerHTML = '';
        document.getElementById('loading-overlay').style.display = 'flex';
        setTimeout(startQRScanner, 500);
    }

    function startOver() {
        currentSponsor = null;
        capturedPhoto = null;
        document.getElementById('site-photo').value = '';
        document.getElementById('photo-preview').classList.add('hidden');
        hideStatus();
        hideAllSections();
        document.getElementById('qr-reader').innerHTML = '';
        document.getElementById('loading-overlay').style.display = 'flex';
        document.getElementById('start-scanner').classList.remove('hidden');
        document.getElementById('stop-scanner').classList.add('hidden');
        setTimeout(startQRScanner, 500);
    }

    function hideAllSections() {
        const sections = ['sponsor-info', 'photo-capture', 'submit-visit', 'success-message'];
        sections.forEach(id => {
            document.getElementById(id).classList.add('hidden');
        });
    }

    function showStatus(type, message) {
        const statusDiv = document.getElementById('status-message');
        statusDiv.className = 'rounded-lg p-3 text-center text-sm';
        
        switch (type) {
            case 'success':
                statusDiv.classList.add('bg-green-50', 'text-green-800', 'border', 'border-green-200');
                break;
            case 'error':
                statusDiv.classList.add('bg-red-50', 'text-red-800', 'border', 'border-red-200');
                break;
            case 'info':
                statusDiv.classList.add('bg-blue-50', 'text-blue-800', 'border', 'border-blue-200');
                break;
        }
        
        statusDiv.textContent = message;
        statusDiv.classList.remove('hidden');
        
        if (type === 'success' || type === 'info') {
            setTimeout(hideStatus, 3000);
        }
    }

    function hideStatus() {
        document.getElementById('status-message').classList.add('hidden');
    }
    </script>
</body>
</html>
