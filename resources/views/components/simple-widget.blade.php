{{-- Bulletproof GitFlow Reporter Widget --}}
<style>
#gitflow-reporter-simple {
    position: fixed !important;
    {{ config('gitflow-reporter.ui.position') === 'top-left' ? 'top: 24px !important; left: 24px !important;' : '' }}
    {{ config('gitflow-reporter.ui.position') === 'top-right' ? 'top: 24px !important; right: 24px !important;' : '' }}
    {{ config('gitflow-reporter.ui.position') === 'bottom-left' ? 'bottom: 24px !important; left: 24px !important;' : '' }}
    {{ config('gitflow-reporter.ui.position', 'bottom-right') === 'bottom-right' ? 'bottom: 24px !important; right: 24px !important;' : '' }}
    z-index: 999999 !important;
    background: #2563eb !important;
    color: white !important;
    border: none !important;
    border-radius: 50px !important;
    width: 60px !important;
    height: 60px !important;
    cursor: pointer !important;
    font-size: 24px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3) !important;
    transition: all 0.2s !important;
}

#gitflow-reporter-simple:hover {
    background: #1d4ed8 !important;
    transform: scale(1.1) !important;
}

.gitflow-modal {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    background: rgba(0,0,0,0.5) !important;
    z-index: 999998 !important;
    display: none !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 20px !important;
}

.gitflow-modal.show {
    display: flex !important;
}

.gitflow-modal-content {
    background: white !important;
    border-radius: 8px !important;
    padding: 0 !important;
    max-width: 512px !important;
    width: 100% !important;
    max-height: 90vh !important;
    overflow-y: auto !important;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
}

.gitflow-header {
    padding: 24px !important;
    border-bottom: 1px solid #e5e7eb !important;
}

.gitflow-header h3 {
    margin: 0 0 4px 0 !important;
    font-size: 18px !important;
    font-weight: 600 !important;
    color: #111827 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
}

.gitflow-header p {
    margin: 0 !important;
    font-size: 14px !important;
    color: #6b7280 !important;
}

.gitflow-close {
    background: none !important;
    border: none !important;
    color: #9ca3af !important;
    cursor: pointer !important;
    padding: 0 !important;
    width: 24px !important;
    height: 24px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    border-radius: 4px !important;
}

.gitflow-close:hover {
    color: #6b7280 !important;
}

.gitflow-form {
    padding: 24px !important;
}

.gitflow-form-group {
    margin-bottom: 16px !important;
}

.gitflow-form-group label {
    display: block !important;
    margin-bottom: 8px !important;
    font-size: 14px !important;
    font-weight: 500 !important;
    color: #374151 !important;
}

.gitflow-form-group input,
.gitflow-form-group select,
.gitflow-form-group textarea {
    width: 100% !important;
    padding: 8px 12px !important;
    border: 1px solid #d1d5db !important;
    border-radius: 6px !important;
    font-size: 14px !important;
    line-height: 1.5 !important;
    box-sizing: border-box !important;
}

.gitflow-form-group input:focus,
.gitflow-form-group select:focus,
.gitflow-form-group textarea:focus {
    outline: none !important;
    border-color: #2563eb !important;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1) !important;
}

.gitflow-form-group textarea {
    min-height: 100px !important;
    resize: vertical !important;
}

.gitflow-context {
    background: #f9fafb !important;
    border-radius: 6px !important;
    padding: 12px !important;
    margin-bottom: 16px !important;
}

.gitflow-context h4 {
    margin: 0 0 8px 0 !important;
    font-size: 14px !important;
    font-weight: 500 !important;
    color: #374151 !important;
}

.gitflow-context-item {
    font-size: 12px !important;
    color: #6b7280 !important;
    margin-bottom: 4px !important;
}

.gitflow-context-item:last-child {
    margin-bottom: 0 !important;
}

.gitflow-buttons {
    display: flex !important;
    gap: 12px !important;
    justify-content: flex-end !important;
    padding-top: 16px !important;
    border-top: 1px solid #e5e7eb !important;
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
    background: #2563eb !important;
    color: white !important;
    border-color: #2563eb !important;
}

.gitflow-btn-primary:hover {
    background: #1d4ed8 !important;
    border-color: #1d4ed8 !important;
}

.gitflow-btn-primary:disabled {
    background: #9ca3af !important;
    border-color: #9ca3af !important;
    cursor: not-allowed !important;
}

.gitflow-btn-secondary {
    background: white !important;
    color: #374151 !important;
    border-color: #d1d5db !important;
}

.gitflow-btn-secondary:hover {
    background: #f9fafb !important;
}
</style>

{{-- Always show if authenticated and not in production --}}
@auth
@if(!app()->environment(['production', 'prod']) || config('gitflow-reporter.ui.show_in_development'))

<button id="gitflow-reporter-simple" onclick="gitflowOpenModal()">
    🐛
</button>

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

// ESC key to close modal
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        gitflowCloseModal();
    }
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
`;
document.head.appendChild(style);
</script>

@endif
@endauth