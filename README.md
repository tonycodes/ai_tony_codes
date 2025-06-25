# GitFlow Reporter 🚀

**Premium Laravel Package for Automated GitHub Issue Reporting**

Transform your application's user feedback into actionable GitHub issues automatically. GitFlow Reporter captures rich context, screenshots, and user information to create detailed bug reports and feature requests.

## 📋 Features

### 🎯 **Core Features**
- **One-Click Reporting** - Floating widget for instant issue reporting
- **Rich Context Collection** - Automatically captures user data, browser info, page state
- **Screenshot Capture** - Optional screenshot inclusion with issues
- **GitHub Integration** - Direct issue creation with formatted content
- **Smart Categorization** - Automatic labeling and priority assignment

### 🔒 **Security & Licensing**
- **License Validation** - Secure server-side license verification
- **Data Sanitization** - Automatic removal of sensitive information
- **Rate Limiting** - Prevents abuse and spam
- **Permission Controls** - Role-based access controls

### 🎨 **Customization**
- **Configurable UI** - Position, theme, and behavior settings
- **Template System** - Customizable issue templates
- **Multi-Theme Support** - Light, dark, and auto themes
- **Responsive Design** - Works on all devices

## 💰 Pricing

| Plan | Price | Features |
|------|-------|----------|
| **Starter** | $29/month | Up to 3 sites, 500 reports/month |
| **Professional** | $79/month | Up to 10 sites, 2,000 reports/month |
| **Enterprise** | $199/month | Unlimited sites, unlimited reports |

*All plans include priority support and updates*

## 🚀 Installation

### 1. Purchase License
Visit [https://tony.codes/gitflow-reporter](https://tony.codes/gitflow-reporter) to purchase your license.

### 2. Install Package
```bash
composer require tonycodes/ai_tony_codes
```

### 3. Publish Configuration
```bash
php artisan vendor:publish --tag=gitflow-reporter-config
php artisan vendor:publish --tag=gitflow-reporter-views
php artisan vendor:publish --tag=gitflow-reporter-assets
```

### 4. Configure Environment
Add to your `.env` file:
```env
# GitFlow Reporter Configuration
GITFLOW_REPORTER_LICENSE_KEY=your-license-key
GITFLOW_REPORTER_GITHUB_TOKEN=your-github-token
GITFLOW_REPORTER_GITHUB_OWNER=your-github-username
GITFLOW_REPORTER_GITHUB_REPO=your-repository-name

# Optional Configuration
GITFLOW_REPORTER_POSITION=bottom-right
GITFLOW_REPORTER_THEME=auto
GITFLOW_REPORTER_SHOW_TO_GUESTS=false
GITFLOW_REPORTER_SCREENSHOTS=true  # Set to false for sensitive sites
```

### 5. Add Widget to Layout
Add to your main layout file (e.g., `app.blade.php`):
```blade
@include('gitflow-reporter::components.widget')

<!-- Required for screenshots (optional) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
```

## 📖 Usage

### Basic Usage
Once installed, the widget automatically appears for authenticated users. Users can:

1. Click the floating report button
2. Select issue type (Technical Issue/Bug, Feature Suggestion)
3. Fill in title and description
4. Optionally include a screenshot
5. Submit the report

### Advanced Configuration

#### Custom Issue Templates
Edit `config/gitflow-reporter.php`:
```php
'templates' => [
    'bug' => [
        'title_prefix' => '🐛',
        'labels' => ['bug', 'auto-generated', 'needs-triage'],
        'assignees' => ['maintainer-username'],
    ],
    // ... more templates
],
```

#### Role-Based Permissions
```php
'ui' => [
    'show_to_guests' => false,
    'show_priority_to' => ['admin', 'manager'],
    'restricted_types' => ['urgent' => ['admin']],
],
```

#### Custom Styling
Publish views and modify:
```bash
php artisan vendor:publish --tag=gitflow-reporter-views
```

## 🔧 GitHub Setup

### 1. Create GitHub Token
1. Go to GitHub → Settings → Developer settings → Personal access tokens
2. Generate new token with `repo` scope
3. Add token to your `.env` file

### 2. Repository Configuration
Ensure your repository has:
- Issues enabled
- Appropriate labels created (bug, enhancement, etc.)
- Team members assigned for notifications

## 📊 Issue Format

GitFlow Reporter creates detailed issues with:

```markdown
## Issue Description
User's description here...

## Reporter Information
- **User**: John Doe (john@example.com)
- **Role**: Admin
- **Organization**: Acme Corp

## Technical Context
- **Page URL**: https://app.example.com/dashboard
- **User Agent**: Chrome/91.0.4472.124
- **Viewport**: 1920x1080
- **Timestamp**: 2023-12-01T10:30:00Z

## Screenshot
![Screenshot](url-to-screenshot)

---
_This issue was automatically created by GitFlow Reporter_
```

## 🛡️ Security Features

### Data Sanitization
Automatically removes:
- Passwords and tokens
- Session data
- API keys
- Personal identifiable information

### Rate Limiting
- 5 reports per hour per user (configurable)
- IP-based limiting for guests
- Exponential backoff for repeated attempts

### License Validation
- Server-side license verification
- Graceful degradation when offline
- Automatic license renewal reminders

## 🎯 Use Cases

### 🐛 **Bug Reporting**
- Users report bugs with full context
- Screenshots show exact issue state
- Developers get actionable information

### ✨ **Feature Requests**
- Structured feature request collection
- Priority assignment by user role
- Centralized feature backlog

### ❓ **Support Tickets**
- Direct integration with support workflow
- Rich context for faster resolution
- Automated categorization

## 🔄 Updates & Support

### Automatic Updates
```bash
composer update tonycodes/ai_tony_codes
```

### Support Channels
- 📧 Email: support@tony.codes
- 📖 Documentation: See this GitHub repository README

## 📝 Changelog

### v1.0.0 (2023-12-01)
- Initial release
- Core reporting functionality
- GitHub integration
- License system
- Screenshot capture
- Context collection

## 📄 License

This is a proprietary commercial package. Each license permits use on a specific number of domains as per your subscription plan.

**License Agreement**: [https://tony.codes/gitflow-reporter/license](https://tony.codes/gitflow-reporter/license)

## 🤝 Contributing

We welcome feedback and suggestions! However, as this is a commercial package, code contributions are limited to our core team.

**Feature Requests**: Use the GitFlow Reporter widget on our demo site!

---

**Made with ❤️ by [Tony Codes](https://tony.codes)**

*Transform your Laravel app's feedback system today with GitFlow Reporter.*