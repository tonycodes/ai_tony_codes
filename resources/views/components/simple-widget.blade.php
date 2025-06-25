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
    padding: 20px !important;
    max-width: 500px !important;
    width: 100% !important;
    max-height: 80vh !important;
    overflow-y: auto !important;
}

.gitflow-form-group {
    margin-bottom: 15px !important;
}

.gitflow-form-group label {
    display: block !important;
    margin-bottom: 5px !important;
    font-weight: bold !important;
    color: #333 !important;
}

.gitflow-form-group input,
.gitflow-form-group select,
.gitflow-form-group textarea {
    width: 100% !important;
    padding: 8px !important;
    border: 1px solid #ccc !important;
    border-radius: 4px !important;
    font-size: 14px !important;
}

.gitflow-form-group textarea {
    min-height: 80px !important;
    resize: vertical !important;
}

.gitflow-buttons {
    display: flex !important;
    gap: 10px !important;
    justify-content: flex-end !important;
    margin-top: 20px !important;
}

.gitflow-btn {
    padding: 10px 20px !important;
    border: none !important;
    border-radius: 4px !important;
    cursor: pointer !important;
    font-size: 14px !important;
}

.gitflow-btn-primary {
    background: #2563eb !important;
    color: white !important;
}

.gitflow-btn-secondary {
    background: #6b7280 !important;
    color: white !important;
}

.gitflow-btn:hover {
    opacity: 0.9 !important;
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
        <h3 style="margin: 0 0 20px 0; color: #333;">Report an Issue</h3>
        
        <form id="gitflow-form" onsubmit="gitflowSubmitReport(event)">
            <div class="gitflow-form-group">
                <label>Issue Type</label>
                <select name="type" required>
                    <option value="">Select type</option>
                    <option value="bug">🐛 Bug Report</option>
                    <option value="feature">✨ Feature Request</option>
                    <option value="improvement">🔧 Improvement</option>
                    <option value="question">❓ Question</option>
                </select>
            </div>
            
            <div class="gitflow-form-group">
                <label>Title</label>
                <input type="text" name="title" placeholder="Brief description" required maxlength="100">
            </div>
            
            <div class="gitflow-form-group">
                <label>Description</label>
                <textarea name="description" placeholder="Detailed description..." required></textarea>
            </div>
            
            <div class="gitflow-buttons">
                <button type="button" class="gitflow-btn gitflow-btn-secondary" onclick="gitflowCloseModal()">
                    Cancel
                </button>
                <button type="submit" class="gitflow-btn gitflow-btn-primary">
                    Submit Report
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function gitflowOpenModal() {
    document.getElementById('gitflow-modal').classList.add('show');
}

function gitflowCloseModal() {
    document.getElementById('gitflow-modal').classList.remove('show');
    document.getElementById('gitflow-form').reset();
}

async function gitflowSubmitReport(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    
    // Add context data
    formData.append('context', JSON.stringify({
        url: window.location.href,
        userAgent: navigator.userAgent,
        timestamp: new Date().toISOString()
    }));
    
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
        
        if (response.ok) {
            alert('Report submitted successfully!');
            gitflowCloseModal();
        } else {
            throw new Error('Failed to submit report');
        }
    } catch (error) {
        console.error('Error submitting report:', error);
        alert('Failed to submit report. Please try again.');
    }
}

// ESC key to close modal
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        gitflowCloseModal();
    }
});
</script>

@endif
@endauth