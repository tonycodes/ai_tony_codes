{{-- Bulletproof GitFlow Reporter Widget --}}
<style>
#gitflow-reporter-bug {
    position: fixed !important;
    {{ config('gitflow-reporter.ui.position') === 'top-left' ? 'top: 16px !important; left: 16px !important;' : '' }}
    {{ config('gitflow-reporter.ui.position') === 'top-right' ? 'top: 16px !important; right: 16px !important;' : '' }}
    {{ config('gitflow-reporter.ui.position') === 'bottom-left' ? 'bottom: 16px !important; left: 16px !important;' : '' }}
    {{ config('gitflow-reporter.ui.position', 'bottom-right') === 'bottom-right' ? 'bottom: 16px !important; right: 16px !important;' : '' }}
    z-index: 999999 !important;
    width: 48px !important;
    height: 48px !important;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    border-radius: 12px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 24px !important;
    color: white !important;
    cursor: pointer !important;
    box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4) !important;
    border: 2px solid rgba(255, 255, 255, 0.2) !important;
    backdrop-filter: blur(10px) !important;
    opacity: 0 !important;
    transform: scale(0.8) !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
}

#gitflow-reporter-bug.show {
    opacity: 1 !important;
    transform: scale(1) !important;
}

#gitflow-reporter-bug:hover {
    background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%) !important;
    transform: scale(1.05) !important;
    box-shadow: 0 6px 25px rgba(102, 126, 234, 0.6) !important;
    border-color: rgba(255, 255, 255, 0.3) !important;
}


.gitflow-modal {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    background: rgba(0, 0, 0, 0.9) !important;
    z-index: 999998 !important;
    display: none !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 20px !important;
    backdrop-filter: blur(8px) !important;
    -webkit-backdrop-filter: blur(8px) !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    opacity: 0 !important;
}

.gitflow-modal.show {
    display: flex !important;
    opacity: 1 !important;
    animation: modalFadeIn 0.3s ease-out !important;
}

.gitflow-modal-content {
    background: #1f2937 !important;
    border-radius: 16px !important;
    padding: 0 !important;
    max-width: 512px !important;
    width: 100% !important;
    max-height: 90vh !important;
    overflow-y: auto !important;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.8) !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    backdrop-filter: blur(20px) !important;
    -webkit-backdrop-filter: blur(20px) !important;
    transform: scale(0.95) !important;
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
}

.gitflow-modal.show .gitflow-modal-content {
    transform: scale(1) !important;
}

.gitflow-header {
    padding: 24px !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%) !important;
}

.gitflow-header h3 {
    margin: 0 0 4px 0 !important;
    font-size: 20px !important;
    font-weight: 600 !important;
    color: #f9fafb !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3) !important;
}

.gitflow-header p {
    margin: 0 !important;
    font-size: 14px !important;
    color: #d1d5db !important;
}

.gitflow-close {
    background: rgba(255, 255, 255, 0.1) !important;
    border: none !important;
    color: #d1d5db !important;
    cursor: pointer !important;
    padding: 8px !important;
    width: 32px !important;
    height: 32px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    border-radius: 8px !important;
    transition: all 0.2s !important;
}

.gitflow-close:hover {
    background: rgba(255, 255, 255, 0.2) !important;
    color: #f9fafb !important;
    transform: scale(1.05) !important;
}

.gitflow-form {
    padding: 24px !important;
    background: #1f2937 !important;
}

.gitflow-form-group {
    margin-bottom: 16px !important;
}

.gitflow-form-group label {
    display: block !important;
    margin-bottom: 8px !important;
    font-size: 14px !important;
    font-weight: 500 !important;
    color: #f3f4f6 !important;
}

.gitflow-form-group input,
.gitflow-form-group select,
.gitflow-form-group textarea {
    width: 100% !important;
    padding: 12px 16px !important;
    border: 1px solid rgba(255, 255, 255, 0.2) !important;
    border-radius: 8px !important;
    font-size: 14px !important;
    line-height: 1.5 !important;
    box-sizing: border-box !important;
    background: rgba(0, 0, 0, 0.3) !important;
    color: #f9fafb !important;
    backdrop-filter: blur(10px) !important;
    transition: all 0.2s !important;
}

.gitflow-form-group input::placeholder,
.gitflow-form-group textarea::placeholder {
    color: #9ca3af !important;
}

.gitflow-form-group input:focus,
.gitflow-form-group select:focus,
.gitflow-form-group textarea:focus {
    outline: none !important;
    border-color: #667eea !important;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.3) !important;
    background: rgba(0, 0, 0, 0.4) !important;
}

.gitflow-form-group textarea {
    min-height: 100px !important;
    resize: vertical !important;
}

.gitflow-context {
    background: rgba(0, 0, 0, 0.4) !important;
    border-radius: 8px !important;
    padding: 16px !important;
    margin-bottom: 16px !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    backdrop-filter: blur(10px) !important;
}

.gitflow-context h4 {
    margin: 0 0 12px 0 !important;
    font-size: 14px !important;
    font-weight: 500 !important;
    color: #f3f4f6 !important;
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
}

.gitflow-context h4::before {
    content: '🔍' !important;
    font-size: 12px !important;
}

.gitflow-context-item {
    font-size: 12px !important;
    color: #d1d5db !important;
    margin-bottom: 6px !important;
    padding: 4px 0 !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05) !important;
}

.gitflow-context-item:last-child {
    margin-bottom: 0 !important;
    border-bottom: none !important;
}

.gitflow-buttons {
    display: flex !important;
    gap: 12px !important;
    justify-content: flex-end !important;
    padding-top: 20px !important;
    border-top: 1px solid rgba(255, 255, 255, 0.1) !important;
    background: rgba(0, 0, 0, 0.2) !important;
    margin: 20px -24px -24px -24px !important;
    padding: 20px 24px !important;
    border-radius: 0 0 16px 16px !important;
}

.gitflow-btn {
    padding: 8px 16px !important;
    border: 1px solid transparent !important;
    border-radius: 6px !important;
    cursor: pointer !important;
    font-size: 14px !important;
    font-weight: 500 !important;
    transition: all 0.2s !important;
}

.gitflow-btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    color: white !important;
    border-color: transparent !important;
    box-shadow: 0 4px 14px 0 rgba(102, 126, 234, 0.39) !important;
}

.gitflow-btn-primary:hover {
    background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%) !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 6px 20px 0 rgba(102, 126, 234, 0.5) !important;
}

.gitflow-btn-primary:disabled {
    background: rgba(156, 163, 175, 0.5) !important;
    border-color: transparent !important;
    cursor: not-allowed !important;
    transform: none !important;
    box-shadow: none !important;
}

.gitflow-btn-secondary {
    background: rgba(255, 255, 255, 0.1) !important;
    color: #f3f4f6 !important;
    border-color: rgba(255, 255, 255, 0.2) !important;
    backdrop-filter: blur(10px) !important;
}

.gitflow-btn-secondary:hover {
    background: rgba(255, 255, 255, 0.2) !important;
    border-color: rgba(255, 255, 255, 0.3) !important;
    transform: translateY(-1px) !important;
}
</style>

{{-- Always show if authenticated and not in production --}}
@auth
@if(!app()->environment(['production', 'prod']) || config('gitflow-reporter.ui.show_in_development'))

<!-- Bug button -->
<div id="gitflow-reporter-bug" onclick="gitflowOpenModal()">🐛</div>

<div id="gitflow-modal" class="gitflow-modal" onclick="gitflowCloseModal()">
    <div class="gitflow-modal-content" onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="gitflow-header">
            <h3>
                Report an Issue
                <button type="button" class="gitflow-close" onclick="gitflowCloseModal()">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </h3>
            <p>Help us improve by reporting bugs, requesting features, or asking questions.</p>
        </div>
        
        <!-- Form -->
        <form id="gitflow-form" onsubmit="gitflowSubmitReport(event)" class="gitflow-form">
            <div class="gitflow-form-group">
                <label>Issue Type</label>
                <select name="type" required>
                    <option value="">Select issue type</option>
                    <option value="bug">🐛 Bug Report</option>
                    <option value="feature">✨ Feature Request</option>
                    <option value="improvement">🔧 Improvement</option>
                    <option value="question">❓ Question</option>
                </select>
            </div>
            
            <div class="gitflow-form-group">
                <label>Title</label>
                <input type="text" name="title" placeholder="Brief description of the issue" required maxlength="100">
            </div>
            
            <div class="gitflow-form-group">
                <label>Description</label>
                <textarea name="description" placeholder="Detailed description. For bugs, please include steps to reproduce." required></textarea>
            </div>
            
            
            <!-- Context Preview -->
            <div class="gitflow-context">
                <h4>Context (automatically included)</h4>
                <div class="gitflow-context-item">User: {{ auth()->user()->name }}</div>
                <div class="gitflow-context-item">Page: <span id="current-path"></span></div>
                <div class="gitflow-context-item">Browser: <span id="browser-info"></span></div>
                <div class="gitflow-context-item">Screenshot: Current page capture</div>
                <div class="gitflow-context-item">Timestamp: <span id="timestamp"></span></div>
            </div>
            
            <div class="gitflow-buttons">
                <button type="button" class="gitflow-btn gitflow-btn-secondary" onclick="gitflowCloseModal()">
                    Cancel
                </button>
                <button type="submit" class="gitflow-btn gitflow-btn-primary" id="submit-btn">
                    Submit Report
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function gitflowOpenModal() {
    document.getElementById('gitflow-modal').classList.add('show');
    
    // Update context info
    document.getElementById('current-path').textContent = window.location.pathname;
    document.getElementById('browser-info').textContent = navigator.userAgent.split(' ')[0];
    document.getElementById('timestamp').textContent = new Date().toLocaleString();
}

function gitflowCloseModal() {
    document.getElementById('gitflow-modal').classList.remove('show');
    document.getElementById('gitflow-form').reset();
    document.getElementById('submit-btn').disabled = false;
    document.getElementById('submit-btn').textContent = 'Submit Report';
}

async function gitflowSubmitReport(event) {
    event.preventDefault();
    
    const form = event.target;
    const submitBtn = document.getElementById('submit-btn');
    const formData = new FormData(form);
    
    // Disable submit button
    submitBtn.disabled = true;
    submitBtn.innerHTML = `
        <svg style="display: inline-block; width: 16px; height: 16px; margin-right: 8px; animation: spin 1s linear infinite;" fill="none" viewBox="0 0 24 24">
            <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Submitting...
    `;
    
    // Add context data
    formData.append('context', JSON.stringify({
        url: window.location.href,
        userAgent: navigator.userAgent,
        viewport: {
            width: window.innerWidth,
            height: window.innerHeight
        },
        timestamp: new Date().toISOString(),
        referrer: document.referrer
    }));
    
    // Add priority
    formData.append('priority', 'medium');
    
    // Always capture screenshot
    try {
        const screenshot = await captureScreenshot();
        if (screenshot) {
            const blob = dataURLtoBlob(screenshot);
            formData.append('screenshot', blob, 'screenshot.png');
        }
    } catch (error) {
        console.warn('Screenshot capture failed:', error);
    }
    
    try {
        // Get CSRF token
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (token) {
            formData.append('_token', token);
        }
        
        const response = await fetch('/gitflow-reporter/report', {
            method: 'POST',
            body: formData,
            headers: token ? { 'X-CSRF-TOKEN': token } : {}
        });
        
        const result = await response.json();
        
        if (response.ok && result.success) {
            gitflowShowNotification(result.message || 'Issue reported successfully!', 'success');
            gitflowCloseModal();
        } else {
            throw new Error(result.message || 'Failed to submit report');
        }
        
    } catch (error) {
        console.error('Error submitting report:', error);
        gitflowShowNotification(error.message || 'Failed to submit report. Please try again.', 'error');
        submitBtn.disabled = false;
        submitBtn.textContent = 'Submit Report';
    }
}

function gitflowShowNotification(message, type) {
    // Simple notification system
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed !important;
        top: 20px !important;
        right: 20px !important;
        background: ${type === 'success' ? '#10b981' : '#ef4444'} !important;
        color: white !important;
        padding: 12px 20px !important;
        border-radius: 6px !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2) !important;
        z-index: 1000000 !important;
        max-width: 400px !important;
        font-size: 14px !important;
        animation: slideIn 0.3s ease-out !important;
    `;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-in forwards';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 5000);
}

// Screenshot capture functions
async function captureScreenshot() {
    try {
        // Try to use html2canvas if available
        if (typeof html2canvas !== 'undefined') {
            const canvas = await html2canvas(document.body, {
                height: window.innerHeight,
                width: window.innerWidth,
                scrollX: 0,
                scrollY: 0,
                scale: 0.5 // Reduce file size
            });
            return canvas.toDataURL('image/png');
        } else {
            console.warn('html2canvas not available for screenshot capture');
            return null;
        }
    } catch (error) {
        console.error('Screenshot capture failed:', error);
        return null;
    }
}

function dataURLtoBlob(dataURL) {
    const arr = dataURL.split(',');
    const mime = arr[0].match(/:(.*?);/)[1];
    const bstr = atob(arr[1]);
    let n = bstr.length;
    const u8arr = new Uint8Array(n);
    while (n--) {
        u8arr[n] = bstr.charCodeAt(n);
    }
    return new Blob([u8arr], { type: mime });
}

// ESC key to close modal
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        gitflowCloseModal();
    }
});

// Corner hover detection
const bugButton = document.getElementById('gitflow-reporter-bug');
const position = '{{ config('gitflow-reporter.ui.position', 'bottom-right') }}';

let hideTimeout;

document.addEventListener('mousemove', function(e) {
    const threshold = 80;
    let isNearCorner = false;
    
    if (position === 'top-left') {
        isNearCorner = e.clientX < threshold && e.clientY < threshold;
    } else if (position === 'top-right') {
        isNearCorner = e.clientX > window.innerWidth - threshold && e.clientY < threshold;
    } else if (position === 'bottom-left') {
        isNearCorner = e.clientX < threshold && e.clientY > window.innerHeight - threshold;
    } else { // bottom-right
        isNearCorner = e.clientX > window.innerWidth - threshold && e.clientY > window.innerHeight - threshold;
    }
    
    if (isNearCorner) {
        clearTimeout(hideTimeout);
        bugButton.classList.add('show');
    } else {
        clearTimeout(hideTimeout);
        hideTimeout = setTimeout(() => {
            bugButton.classList.remove('show');
        }, 500);
    }
});

// Keep visible when hovering the button itself
bugButton.addEventListener('mouseenter', function() {
    clearTimeout(hideTimeout);
    bugButton.classList.add('show');
});

// Add animations
const style = document.createElement('style');
style.textContent = `
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    @keyframes modalFadeIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
`;
document.head.appendChild(style);
</script>

@endif
@endauth