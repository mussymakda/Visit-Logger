@php
// Clear PHP output buffer and disable output buffering
if (ob_get_level()) ob_end_clean();
@endphp

<x-filament-panels::page>
    <div class="max-w-md mx-auto space-y-6">
        <!-- Mobile QR Link Banner -->
        <div id="qr-deep-link" style="display: none;">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
                <div class="text-blue-800 font-semibold mb-2">Processing QR Code</div>
                <div class="text-blue-600 text-sm">Loading sponsor information...</div>
                <div class="mt-3">
                    <div class="loading-spinner mx-auto"></div>
                </div>
            </div>
        </div>
        
        <!-- QR Scanner Section -->
        <x-filament::section id="qr-scanner-section">
            <x-slot name="heading">
                <div class="flex items-center justify-between">
                    <span>QR Scanner</span>
                    <span id="scanner-status" class="text-sm text-gray-500">Ready</span>
                </div>
            </x-slot>
            
            <div class="space-y-4">
                <div class="flex justify-center">
                    <div id="qr-reader" class="w-full max-w-lg h-80 border-2 border-gray-200 rounded-lg overflow-hidden"></div>
                </div>
                <div id="status-container" class="text-center space-y-2"></div>
            </div>
        </x-filament::section>

        <!-- Sponsor Information Section -->
        <x-filament::section id="sponsor-info-section" style="display: none;">
            <x-slot name="heading">Sponsor Information</x-slot>
            
            <div id="sponsor-details" class="space-y-4">
                <!-- Sponsor details will be populated by JavaScript -->
            </div>
            
            <div class="flex justify-center space-x-3 mt-4">
                <x-filament::button 
                    id="confirm-sponsor" 
                    color="success"
                    onclick="showPhotoCapture()"
                >
                    Confirm & Take Photo
                </x-filament::button>
                <x-filament::button 
                    id="scan-again" 
                    color="gray"
                    onclick="startOver()"
                >
                    Scan Again
                </x-filament::button>
            </div>
        </x-filament::section>

        <!-- Photo Capture Section -->
        <x-filament::section id="photo-capture-section" style="display: none;">
            <x-slot name="heading">Capture Photo</x-slot>
            
            <div class="space-y-4">
                <div class="text-center text-gray-600 text-sm">
                    Take a photo to complete the visit log
                </div>
                
                <div class="flex justify-center">
                    <video id="camera-preview" class="w-full max-w-sm h-64 bg-gray-100 rounded-lg" autoplay></video>
                </div>
                
                <div class="flex justify-center space-x-3">
                    <x-filament::button 
                        id="capture-photo" 
                        color="primary"
                        onclick="capturePhoto()"
                    >
                        Take Photo
                    </x-filament::button>
                    <x-filament::button 
                        id="back-to-sponsor" 
                        color="gray"
                        onclick="showSponsorInfo()"
                    >
                        Back
                    </x-filament::button>
                </div>
                
                <div id="photo-preview-container" class="flex justify-center" style="display: none;">
                    <div class="space-y-4">
                        <img id="photo-preview" class="w-full max-w-sm h-64 object-cover rounded-lg" />
                        <div class="flex justify-center space-x-3">
                            <x-filament::button 
                                id="retake-photo" 
                                color="gray"
                                onclick="retakePhoto()"
                            >
                                Retake
                            </x-filament::button>
                            <x-filament::button 
                                id="submit-visit" 
                                color="success"
                                onclick="submitVisit()"
                            >
                                Submit Visit
                            </x-filament::button>
                        </div>
                    </div>
                </div>
            </div>
        </x-filament::section>

        <!-- Success Section -->
        <x-filament::section id="success-section" style="display: none;">
            <x-slot name="heading">Visit Logged</x-slot>
            
            <div class="space-y-4">
                <div class="text-center space-y-4">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto">
                        <span class="text-2xl text-green-600">✓</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-green-800">Success!</h3>
                        <p class="text-green-600">Visit logged successfully</p>
                    </div>
                    <x-filament::button 
                        id="log-another-visit" 
                        color="success"
                        onclick="startOver()"
                    >
                        Log Another Visit
                    </x-filament::button>
                </div>
            </div>
        </x-filament::section>
    </div>

    <script>
    let html5QrcodeScanner;
    let cameraStream;
    let currentSponsor = null;
    let capturedPhotoBlob = null;

    document.addEventListener('DOMContentLoaded', function() {
        console.log('Dashboard initialized');
        startQRScanner();
    });

    function startQRScanner() {
        document.getElementById('qr-deep-link').style.display = 'none';
        document.getElementById('qr-scanner-section').style.display = 'block';
        
        try {
            if (html5QrcodeScanner) {
                html5QrcodeScanner.clear();
            }
            
            html5QrcodeScanner = new Html5Qrcode("qr-reader");
            
            html5QrcodeScanner.start(
                { facingMode: "environment" },
                {
                    fps: 10,
                    qrbox: { width: 300, height: 300 }
                },
                qrCodeSuccessCallback,
                qrCodeErrorCallback
            ).then(() => {
                console.log('QR Scanner started');
                updateScannerStatus('Scanning...');
            }).catch(err => {
                console.error('Scanner failed:', err);
                updateScannerStatus('Scanner failed');
                showStatus('Failed to start camera. Please check permissions.', 'error');
            });
            
        } catch (error) {
            console.error('Scanner error:', error);
            showStatus('Failed to initialize scanner.', 'error');
        }
    }

    function qrCodeSuccessCallback(decodedText, decodedResult) {
        console.log('QR scanned:', decodedText);
        
        if (html5QrcodeScanner) {
            html5QrcodeScanner.stop();
        }
        
        const sponsorId = parseQRCode(decodedText);
        
        if (sponsorId) {
            updateScannerStatus('QR Code detected');
            fetchSponsorInfo(sponsorId);
        } else {
            showStatus('Invalid QR code format.', 'error');
            setTimeout(() => {
                startQRScanner();
            }, 2000);
        }
    }

    function qrCodeErrorCallback(error) {
        if (error.includes('NotFoundException')) {
            return;
        }
    }

    function parseQRCode(qrText) {
        const urlMatch = qrText.match(/[?&]sponsor[=](\d+)/i);
        if (urlMatch) {
            return urlMatch[1];
        }
        
        const sponsorMatch = qrText.match(/SPONSOR[_-](\d+)/i);
        if (sponsorMatch) {
            return sponsorMatch[1];
        }
        
        const numberMatch = qrText.match(/^(\d+)$/);
        if (numberMatch) {
            return numberMatch[1];
        }
        
        return null;
    }

    function fetchSponsorInfo(sponsorId) {
        showStatus('Loading sponsor information...', 'info');
        
        fetch(`/api/sponsors/${sponsorId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            currentSponsor = data;
            hideDeepLink();
            showSponsorInfo();
            clearStatus();
        })
        .catch(error => {
            console.error('Error fetching sponsor:', error);
            showStatus('Failed to load sponsor information.', 'error');
            setTimeout(() => {
                startQRScanner();
            }, 3000);
        });
    }

    function showSponsorInfo() {
        if (!currentSponsor) {
            return;
        }
        
        hideAllSections();
        document.getElementById('sponsor-info-section').style.display = 'block';
        
        const sponsorDetails = document.getElementById('sponsor-details');
        sponsorDetails.innerHTML = `
            <div class="space-y-3">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="grid grid-cols-1 gap-2 text-sm">
                        <div><strong>Name:</strong> ${currentSponsor.name || 'N/A'}</div>
                        <div><strong>Company:</strong> ${currentSponsor.company || 'N/A'}</div>
                        <div><strong>Contact:</strong> ${currentSponsor.contact_person || 'N/A'}</div>
                        <div><strong>Phone:</strong> ${currentSponsor.phone || 'N/A'}</div>
                        <div><strong>Email:</strong> ${currentSponsor.email || 'N/A'}</div>
                    </div>
                </div>
            </div>
        `;
    }

    function showPhotoCapture() {
        hideAllSections();
        document.getElementById('photo-capture-section').style.display = 'block';
        startCamera();
    }

    function startCamera() {
        navigator.mediaDevices.getUserMedia({ 
            video: { 
                facingMode: "environment",
                width: { ideal: 1280 },
                height: { ideal: 720 }
            } 
        })
        .then(function(stream) {
            cameraStream = stream;
            const video = document.getElementById('camera-preview');
            video.srcObject = stream;
            video.play();
        })
        .catch(function(error) {
            console.error('Camera error:', error);
            showStatus('Failed to access camera.', 'error');
        });
    }

    function capturePhoto() {
        const video = document.getElementById('camera-preview');
        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');
        
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        canvas.toBlob(function(blob) {
            capturedPhotoBlob = blob;
            
            const photoPreview = document.getElementById('photo-preview');
            photoPreview.src = URL.createObjectURL(blob);
            
            video.style.display = 'none';
            document.getElementById('capture-photo').style.display = 'none';
            document.getElementById('photo-preview-container').style.display = 'block';
        }, 'image/jpeg', 0.8);
    }

    function retakePhoto() {
        capturedPhotoBlob = null;
        
        const video = document.getElementById('camera-preview');
        video.style.display = 'block';
        document.getElementById('capture-photo').style.display = 'inline-flex';
        document.getElementById('photo-preview-container').style.display = 'none';
        
        if (!cameraStream) {
            startCamera();
        }
    }

    function submitVisit() {
        if (!currentSponsor || !capturedPhotoBlob) {
            showStatus('Missing required data.', 'error');
            return;
        }
        
        showStatus('Submitting visit...', 'info');
        
        const formData = new FormData();
        formData.append('sponsor_id', currentSponsor.id);
        formData.append('photo', capturedPhotoBlob, 'visit-photo.jpg');
        
        fetch('/api/visits', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Visit submitted:', data);
            stopCamera();
            showSuccess();
            clearStatus();
        })
        .catch(error => {
            console.error('Submit error:', error);
            showStatus('Failed to submit visit.', 'error');
        });
    }

    function showSuccess() {
        hideAllSections();
        document.getElementById('success-section').style.display = 'block';
    }

    function startOver() {
        console.log('Starting over...');
        
        currentSponsor = null;
        capturedPhotoBlob = null;
        
        stopCamera();
        hideAllSections();
        
        if (html5QrcodeScanner) {
            html5QrcodeScanner.stop().catch(() => {});
        }
        
        clearStatus();
        setTimeout(() => {
            startQRScanner();
        }, 100);
    }

    function hideAllSections() {
        document.getElementById('qr-deep-link').style.display = 'none';
        document.getElementById('qr-scanner-section').style.display = 'none';
        document.getElementById('sponsor-info-section').style.display = 'none';
        document.getElementById('photo-capture-section').style.display = 'none';
        document.getElementById('success-section').style.display = 'none';
    }

    function hideDeepLink() {
        document.getElementById('qr-deep-link').style.display = 'none';
    }

    function stopCamera() {
        if (cameraStream) {
            cameraStream.getTracks().forEach(track => track.stop());
            cameraStream = null;
        }
    }

    function updateScannerStatus(status) {
        const statusElement = document.getElementById('scanner-status');
        if (statusElement) {
            statusElement.textContent = status;
        }
    }

    function showStatus(message, type = 'info') {
        const container = document.getElementById('status-container');
        if (!container) return;
        
        const colors = {
            'info': 'bg-blue-50 border-blue-200 text-blue-800',
            'error': 'bg-red-50 border-red-200 text-red-800',
            'success': 'bg-green-50 border-green-200 text-green-800'
        };
        
        container.innerHTML = `
            <div class="p-3 rounded-lg border ${colors[type] || colors.info}">
                ${message}
            </div>
        `;
    }

    function clearStatus() {
        document.getElementById('status-container').innerHTML = '';
    }
    </script>

    <style>
        .loading-spinner {
            border: 2px solid #f3f3f3;
            border-top: 2px solid #3498db;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
</x-filament-panels::page>
