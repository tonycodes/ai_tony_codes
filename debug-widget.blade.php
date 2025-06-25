{{-- DEBUG: GitFlow Reporter Widget Visibility --}}
<div style="position: fixed; top: 10px; left: 10px; background: red; color: white; padding: 10px; z-index: 99999;">
    <h4>GitFlow Debug Info:</h4>
    
    <p><strong>Environment:</strong> {{ app()->environment() }}</p>
    <p><strong>Auth Status:</strong> {{ auth()->check() ? 'Logged In' : 'Not Logged In' }}</p>
    <p><strong>User ID:</strong> {{ auth()->id() ?? 'None' }}</p>
    
    <p><strong>Config Values:</strong></p>
    <ul>
        <li>show_in_development: {{ config('gitflow-reporter.ui.show_in_development') ? 'true' : 'false' }}</li>
        <li>position: {{ config('gitflow-reporter.ui.position') }}</li>
        <li>show_to_guests: {{ config('gitflow-reporter.ui.show_to_guests') ? 'true' : 'false' }}</li>
    </ul>
    
    <p><strong>Should Show Widget:</strong> 
        {{ (config('gitflow-reporter.ui.show_in_development') || !app()->environment('local')) && auth()->check() ? 'YES' : 'NO' }}
    </p>
    
    <p><strong>CSS File Exists:</strong> 
        {{ file_exists(public_path('vendor/gitflow-reporter/gitflow-reporter.css')) ? 'YES' : 'NO' }}
    </p>
    
    <p><strong>Widget Conditions:</strong></p>
    <ul>
        <li>Environment check: {{ config('gitflow-reporter.ui.show_in_development') || !app()->environment(['production', 'prod']) ? 'PASS (Show outside production)' : 'FAIL' }}</li>
        <li>Auth check: {{ auth()->check() ? 'PASS' : 'FAIL' }}</li>
        <li>Combined: {{ (config('gitflow-reporter.ui.show_in_development') || !app()->environment(['production', 'prod'])) && auth()->check() ? 'SHOULD SHOW' : 'SHOULD HIDE' }}</li>
    </ul>
</div>

{{-- Include the actual widget --}}
<div style="background: blue; color: white; padding: 5px;">
    <p>About to include widget...</p>
</div>
@include('gitflow-reporter::components.widget')
<div style="background: green; color: white; padding: 5px;">
    <p>Widget included!</p>
</div>