{{-- GitFlow Reporter Widget --}}
@if(config('gitflow-reporter.ui.show_in_development') || !app()->environment('local'))
@auth
<div id="gitflow-reporter-widget" 
     x-data="gitflowReporter()" 
     x-init="init()"
     class="fixed z-50 {{ $position ?? 'bottom-6 right-6' }}">
    
    {{-- Trigger Button --}}
    <button @click="toggleWidget()" 
            class="gitflow-reporter-trigger bg-blue-600 hover:bg-blue-700 text-white rounded-full p-3 shadow-lg transition-all duration-200 hover:scale-110"
            title="Report an issue">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
    </button>

    {{-- Modal --}}
    <div x-show="showModal" 
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4"
         @click.self="closeModal()">
        
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-lg w-full max-h-[90vh] overflow-y-auto"
             @click.stop>
            
            {{-- Header --}}
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Report an Issue
                    </h3>
                    <button @click="closeModal()" 
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Help us improve by reporting bugs, requesting features, or asking questions.
                </p>
            </div>

            {{-- Form --}}
            <form @submit.prevent="submitReport()" class="px-6 py-4 space-y-4">
                
                {{-- Issue Type --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Issue Type
                    </label>
                    <select x-model="form.type" 
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                            required>
                        <option value="">Select issue type</option>
                        <option value="bug">🐛 Bug Report</option>
                        <option value="feature">✨ Feature Request</option>
                        <option value="improvement">🔧 Improvement</option>
                        <option value="question">❓ Question</option>
                    </select>
                </div>

                {{-- Title --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Title
                    </label>
                    <input type="text" 
                           x-model="form.title"
                           placeholder="Brief description of the issue"
                           class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                           required 
                           maxlength="100">
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Description
                    </label>
                    <textarea x-model="form.description"
                              rows="4"
                              placeholder="Detailed description. For bugs, please include steps to reproduce."
                              class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                              required></textarea>
                </div>

                {{-- Priority (if enabled) --}}
                <div x-show="config.features.priority_selection">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Priority
                    </label>
                    <select x-model="form.priority" 
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        <option value="low">🟢 Low</option>
                        <option value="medium">🟡 Medium</option>
                        <option value="high">🟠 High</option>
                        <option value="urgent">🔴 Urgent</option>
                    </select>
                </div>

                {{-- Screenshot Option --}}
                <div x-show="config.features.screenshots">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               x-model="form.includeScreenshot"
                               class="rounded border-gray-300 text-blue-600">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                            Include screenshot of current page
                        </span>
                    </label>
                </div>

                {{-- Context Preview --}}
                <div x-show="config.features.context_collection" 
                     class="bg-gray-50 dark:bg-gray-700 rounded-md p-3">
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Context (automatically included)
                    </h4>
                    <div class="text-xs text-gray-600 dark:text-gray-400 space-y-1">
                        <div>User: {{ auth()->user()->name }}</div>
                        <div>Page: <span x-text="window.location.pathname"></span></div>
                        <div>Browser: <span x-text="navigator.userAgent.split(' ')[0]"></span></div>
                        <div>Timestamp: <span x-text="new Date().toLocaleString()"></span></div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" 
                            @click="closeModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500">
                        Cancel
                    </button>
                    <button type="submit" 
                            :disabled="submitting"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 disabled:opacity-50">
                        <span x-show="!submitting">Submit Report</span>
                        <span x-show="submitting" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Submitting...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Success/Error Notifications --}}
    <div x-show="notification.show" 
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"
         class="fixed top-4 right-4 max-w-sm rounded-md shadow-lg p-4 text-white z-50"
         :class="notification.type === 'success' ? 'bg-green-500' : 'bg-red-500'">
        <div class="flex items-center">
            <span x-text="notification.message"></span>
            <button @click="hideNotification()" class="ml-2 text-white/80 hover:text-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>
</div>

{{-- JavaScript --}}
<script>
function gitflowReporter() {
    return {
        showModal: false,
        submitting: false,
        config: {},
        form: {
            type: '',
            title: '',
            description: '',
            priority: 'medium',
            includeScreenshot: true
        },
        notification: {
            show: false,
            type: 'success',
            message: ''
        },

        async init() {
            // Load configuration
            try {
                const response = await fetch('{{ route("gitflow-reporter.config") }}');
                this.config = await response.json();
            } catch (error) {
                console.warn('Failed to load GitFlow Reporter config:', error);
                this.config = {
                    features: {
                        screenshots: true,
                        context_collection: true,
                        priority_selection: true
                    }
                };
            }
        },

        toggleWidget() {
            this.showModal = !this.showModal;
        },

        closeModal() {
            this.showModal = false;
            this.resetForm();
        },

        resetForm() {
            this.form = {
                type: '',
                title: '',
                description: '',
                priority: 'medium',
                includeScreenshot: true
            };
            this.submitting = false;
        },

        async captureScreenshot() {
            if (!this.form.includeScreenshot || !this.config.features?.screenshots) {
                return null;
            }
            
            try {
                if (typeof html2canvas !== 'undefined') {
                    const canvas = await html2canvas(document.body, {
                        height: window.innerHeight,
                        width: window.innerWidth,
                        scrollX: 0,
                        scrollY: 0
                    });
                    return canvas.toDataURL('image/png');
                }
            } catch (error) {
                console.warn('Screenshot capture failed:', error);
            }
            return null;
        },

        async submitReport() {
            if (this.submitting) return;
            
            this.submitting = true;
            
            try {
                const screenshot = await this.captureScreenshot();
                
                const contextData = {
                    url: window.location.href,
                    userAgent: navigator.userAgent,
                    viewport: {
                        width: window.innerWidth,
                        height: window.innerHeight
                    },
                    timestamp: new Date().toISOString(),
                    referrer: document.referrer
                };

                const formData = new FormData();
                formData.append('type', this.form.type);
                formData.append('title', this.form.title);
                formData.append('description', this.form.description);
                formData.append('priority', this.form.priority);
                formData.append('context', JSON.stringify(contextData));
                
                if (screenshot) {
                    const blob = this.dataURLtoBlob(screenshot);
                    formData.append('screenshot', blob, 'screenshot.png');
                }

                const response = await fetch('{{ route("gitflow-reporter.report") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    this.showNotification(result.message || 'Issue reported successfully!', 'success');
                    this.closeModal();
                } else {
                    throw new Error(result.message || 'Failed to submit report');
                }

            } catch (error) {
                console.error('Error submitting report:', error);
                this.showNotification(error.message || 'Failed to submit report. Please try again.', 'error');
            } finally {
                this.submitting = false;
            }
        },

        dataURLtoBlob(dataURL) {
            const arr = dataURL.split(',');
            const mime = arr[0].match(/:(.*?);/)[1];
            const bstr = atob(arr[1]);
            let n = bstr.length;
            const u8arr = new Uint8Array(n);
            while (n--) {
                u8arr[n] = bstr.charCodeAt(n);
            }
            return new Blob([u8arr], { type: mime });
        },

        showNotification(message, type = 'success') {
            this.notification = {
                show: true,
                type: type,
                message: message
            };
            
            // Auto-hide after configured time
            setTimeout(() => {
                this.hideNotification();
            }, this.config.notifications?.auto_hide_after || 5000);
        },

        hideNotification() {
            this.notification.show = false;
        }
    };
}
</script>
@endauth
@endif