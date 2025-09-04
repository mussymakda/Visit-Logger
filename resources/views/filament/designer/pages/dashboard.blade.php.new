<x-filament-panels::page>
    <div class="space-y-6">
        
        <!-- QR Scanner Section -->
        <x-filament::section>
            <x-slot name="heading">QR Code Scanner</x-slot>
            <x-slot name="description">Scan sponsor QR codes to log your visits</x-slot>
            
            <div class="text-center space-y-4">
                <div class="flex justify-center">
                    <div id="qr-reader" style="width: 300px; height: 300px; border: 2px solid #e5e7eb; border-radius: 8px;"></div>
                </div>
                
                <div class="space-x-3">
                    <x-filament::button id="start-scanner" color="primary">
                        Start Scanner
                    </x-filament::button>
                    <x-filament::button id="stop-scanner" color="danger" style="display: none;">
                        Stop Scanner
                    </x-filament::button>
                </div>
                
                <div>
                    <x-filament::button id="test-sponsor-3" color="warning" size="sm">
                        Test Sponsor #3
                    </x-filament::button>
                    <p class="text-xs text-gray-500 mt-2">For testing: sponsor IDs 1-8</p>
                </div>
            </div>
        </x-filament::section>

        <!-- Status Messages -->
        <div id="status-container"></div>

        <!-- Sponsor Information -->
        <div id="sponsor-section" style="display: none;">
            <x-filament::section>
                <x-slot name="heading">Sponsor Information</x-slot>
                <div id="sponsor-details"></div>
                <div class="mt-4 space-x-3 text-center">
                    <x-filament::button id="take-photo" color="primary">Take Photo</x-filament::button>
                    <x-filament::button id="rescan-qr" color="gray">Scan Again</x-filament::button>
                </div>
            </x-filament::section>
        </div>

        <!-- Photo Section -->
        <div id="photo-section" style="display: none;">
            <x-filament::section>
                <x-slot name="heading">Take Site Photo</x-slot>
                <div class="text-center space-y-4">
                    <input type="file" id="site-photo" accept="image/*" capture="environment" style="display: none;">
                    <x-filament::button onclick="document.getElementById('site-photo').click()" color="primary">
                        Choose Photo
                    </x-filament::button>
                    
                    <div id="photo-preview" style="display: none;">
                        <img id="preview-image" src="" alt="Preview" style="max-width: 300px; max-height: 200px; margin: 0 auto; border-radius: 8px;">
                        <div class="mt-3 space-x-3">
                            <x-filament::button id="confirm-photo" color="success">Use Photo</x-filament::button>
                            <x-filament::button id="retake-photo" color="gray">Try Again</x-filament::button>
                        </div>
                    </div>
                </div>
            </x-filament::section>
        </div>

        <!-- Submit Section -->
        <div id="submit-section" style="display: none;">
            <x-filament::section>
                <x-slot name="heading">Submit Visit</x-slot>
                <div id="visit-summary" class="mb-4 p-3 bg-gray-50 rounded"></div>
                <div class="text-center space-x-3">
                    <x-filament::button id="confirm-submit" color="success">Submit Visit</x-filament::button>
                    <x-filament::button id="start-over" color="gray">Start Over</x-filament::button>
                </div>
                <div id="submit-progress" style="display: none;" class="text-center mt-4">
                    <x-filament::loading-indicator />
                    <p class="text-sm text-gray-600 mt-2">Submitting...</p>
                </div>
            </x-filament::section>
        </div>

        <!-- Success Section -->
        <div id="success-section" style="display: none;">
            <x-filament::section>
                <div class="text-center space-y-4">
                    <div class="text-green-600 text-4xl">✓</div>
                    <h3 class="text-lg font-medium">Visit Logged Successfully!</h3>
                    <x-filament::button id="log-another-visit" color="success">Log Another Visit</x-filament::button>
                </div>
            </x-filament::section>
        </div>
    </div>

    @push('scripts')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    
    <script>
    let html5QrcodeScanner;
    let currentSponsor = null;
    let capturedPhoto = null;

    document.addEventListener('DOMContentLoaded', function() {
        setupEventListeners();
    });

    function setupEventListeners() {
        document.getElementById('start-scanner').addEventListener('click', startQRScanner);
        document.getElementById('stop-scanner').addEventListener('click', stopQRScanner);
        document.getElementById('test-sponsor-3').addEventListener('click', () => fetchSponsorData(3));
        document.getElementById('take-photo').addEventListener('click', showPhotoSection);
        document.getElementById('rescan-qr').addEventListener('click', restartScanning);
        document.getElementById('site-photo').addEventListener('change', handlePhotoSelection);
        document.getElementById('confirm-photo').addEventListener('click', showSubmitSection);
        document.getElementById('retake-photo').addEventListener('click', retakePhoto);
        document.getElementById('confirm-submit').addEventListener('click', submitVisit);
        document.getElementById('start-over').addEventListener('click', startOver);
        document.getElementById('log-another-visit').addEventListener('click', startOver);
    }

    function startQRScanner() {
        html5QrcodeScanner = new Html5Qrcode("qr-reader");
        
        html5QrcodeScanner.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: { width: 250, height: 250 } },
            onScanSuccess,
            () => {} // Ignore scan errors
        ).then(() => {
            document.getElementById('start-scanner').style.display = 'none';
            document.getElementById('stop-scanner').style.display = 'inline-block';
        }).catch(() => {
            showStatus('error', 'Failed to start camera');
        });
    }

    function stopQRScanner() {
        if (html5QrcodeScanner) {
            html5QrcodeScanner.stop().then(() => {
                document.getElementById('start-scanner').style.display = 'inline-block';
                document.getElementById('stop-scanner').style.display = 'none';
            });
        }
    }

    function onScanSuccess(decodedText) {
        const sponsorId = parseSponsorId(decodedText);
        if (sponsorId) {
            stopQRScanner();
            fetchSponsorData(sponsorId);
        }
    }

    function parseSponsorId(qrText) {
        if (/^\d+$/.test(qrText)) return parseInt(qrText);
        const match = qrText.match(/sponsor[=_-](\d+)/i);
        return match ? parseInt(match[1]) : null;
    }

    function fetchSponsorData(sponsorId) {
        showStatus('info', 'Looking up sponsor...');
        
        fetch(`/api/sponsors/${sponsorId}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.id) {
                currentSponsor = data;
                displaySponsorInfo(data);
                showSponsorSection();
                showStatus('success', 'Sponsor verified!');
            } else {
                showStatus('error', 'Sponsor not found');
            }
        })
        .catch(() => showStatus('error', 'Error fetching sponsor'));
    }

    function displaySponsorInfo(sponsor) {
        document.getElementById('sponsor-details').innerHTML = `
            <div class="space-y-3">
                <div class="bg-blue-50 p-3 rounded">
                    <h4 class="font-medium">${sponsor.name}</h4>
                    <p class="text-sm text-gray-600">${sponsor.company_name}</p>
                </div>
                <div class="text-sm space-y-1">
                    <div><strong>Contact:</strong> ${sponsor.contact || 'N/A'}</div>
                    <div><strong>Location:</strong> ${sponsor.location || 'N/A'}</div>
                </div>
            </div>
        `;
    }

    function showSponsorSection() {
        hideAllSections();
        document.getElementById('sponsor-section').style.display = 'block';
    }

    function showPhotoSection() {
        hideAllSections();
        document.getElementById('photo-section').style.display = 'block';
    }

    function showSubmitSection() {
        if (!capturedPhoto) {
            showStatus('error', 'Please select a photo');
            return;
        }
        hideAllSections();
        document.getElementById('submit-section').style.display = 'block';
        displayVisitSummary();
    }

    function handlePhotoSelection(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById('preview-image').src = e.target.result;
                document.getElementById('photo-preview').style.display = 'block';
                capturedPhoto = file;
            };
            reader.readAsDataURL(file);
        }
    }

    function retakePhoto() {
        document.getElementById('site-photo').value = '';
        document.getElementById('photo-preview').style.display = 'none';
        capturedPhoto = null;
    }

    function displayVisitSummary() {
        document.getElementById('visit-summary').innerHTML = `
            <h4 class="font-medium mb-2">Visit Summary</h4>
            <div class="text-sm space-y-1">
                <div><strong>Sponsor:</strong> ${currentSponsor.name}</div>
                <div><strong>Company:</strong> ${currentSponsor.company_name}</div>
                <div><strong>Time:</strong> ${new Date().toLocaleString()}</div>
                <div><strong>Photo:</strong> ✓ Ready</div>
            </div>
        `;
    }

    function submitVisit() {
        document.getElementById('submit-progress').style.display = 'block';
        
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
            document.getElementById('submit-progress').style.display = 'none';
            if (data.success) {
                hideAllSections();
                document.getElementById('success-section').style.display = 'block';
                hideStatus();
            } else {
                showStatus('error', 'Submit failed');
            }
        })
        .catch(() => {
            document.getElementById('submit-progress').style.display = 'none';
            showStatus('error', 'Submit error');
        });
    }

    function restartScanning() {
        currentSponsor = null;
        hideAllSections();
        document.getElementById('qr-reader').innerHTML = '';
        document.getElementById('start-scanner').style.display = 'inline-block';
        document.getElementById('stop-scanner').style.display = 'none';
    }

    function startOver() {
        currentSponsor = null;
        capturedPhoto = null;
        document.getElementById('site-photo').value = '';
        document.getElementById('photo-preview').style.display = 'none';
        hideAllSections();
        hideStatus();
        document.getElementById('qr-reader').innerHTML = '';
        document.getElementById('start-scanner').style.display = 'inline-block';
        document.getElementById('stop-scanner').style.display = 'none';
    }

    function hideAllSections() {
        ['sponsor-section', 'photo-section', 'submit-section', 'success-section'].forEach(id => {
            document.getElementById(id).style.display = 'none';
        });
    }

    function showStatus(type, message) {
        const colors = {
            success: 'text-green-800 bg-green-50 border-green-200',
            error: 'text-red-800 bg-red-50 border-red-200',
            info: 'text-blue-800 bg-blue-50 border-blue-200'
        };
        
        document.getElementById('status-container').innerHTML = `
            <div class="p-3 rounded border text-center ${colors[type]}">${message}</div>
        `;
        
        if (type === 'success' || type === 'info') {
            setTimeout(hideStatus, 3000);
        }
    }

    function hideStatus() {
        document.getElementById('status-container').innerHTML = '';
    }
    </script>
    @endpush
</x-filament-panels::page>
