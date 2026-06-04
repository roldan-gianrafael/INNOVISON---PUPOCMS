# INNOVISON PUPOCMS - Complete System Documentation

## Table of Contents
1. [System Overview](#system-overview)
2. [Technology Stack](#technology-stack)
3. [Architecture & Database](#architecture--database)
4. [Complete User Flow: Login to Logout](#complete-user-flow-login-to-logout)
5. [Core Processes & Features](#core-processes--features)
6. [UI/UX Components & Styling](#uiux-components--styling)
7. [External Integrations](#external-integrations)
8. [Security & Authentication](#security--authentication)

---

## System Overview

**INNOVISON PUPOCMS** (PUP Clinic Management System) is a comprehensive web-based clinic management platform designed for PUP (Philippine University of the Philippines) Taguig Clinic. The system facilitates patient intake, health records management, appointment scheduling, inventory management, and staff workflows.

### Key Objectives:
- Streamline patient intake and medical assessments
- Manage health records and digital documentation
- Schedule and track appointments
- Manage clinic inventory and supplies
- Generate reports for clinic operations
- Support student assistants and admin staff with role-based access

---

## Technology Stack

### **Backend**
- **Framework**: Laravel 8+ (PHP)
- **Language**: PHP 8.0+
- **ORM**: Eloquent
- **Database**: MySQL 8.0
- **Authentication**: OAuth2 with PKCE (One Portal Integration)
- **Logging**: Laravel Log Facades
- **Task Scheduling**: Laravel Scheduler (Cron)

### **Frontend**
- **Template Engine**: Blade (Laravel Templating)
- **JavaScript**: Vanilla ES6+ (No framework, pure JS)
- **CSS**: Custom CSS with Tailwind-like utility classes
- **HTTP Client**: Fetch API, Axios
- **Build Tool**: Laravel Mix (Webpack wrapper)

### **Key Libraries & Services**
- **Spatie Laravel Activities** - Activity logging
- **Guzzle HTTP** - HTTP requests to external APIs
- **Carbon** - Date/time handling
- **Stripe** (optional) - Payment processing
- **OpenAI API** - AI-powered ID verification and OCR
- **PUPTAS Webhook Service** - Medical clearance integration
- **Maatwebsite Excel** (optional) - Report generation

### **External Services**
- **One Portal (IDP)** - Identity Provider for OAuth2 authentication
- **PUPTAS** - Medical records and applicant management system
- **OpenAI** - AI-powered document processing and ID verification

---

## Architecture & Database

### **Directory Structure**

```
app/
├── Http/
│   ├── Controllers/          # Request handlers
│   │   ├── WalkInController.php      # Patient intake workflows
│   │   ├── AppointmentController.php # Appointment management
│   │   ├── Auth/LoginController.php  # Authentication flows
│   │   ├── AdminController.php       # Admin dashboard
│   │   └── StudentAssistantController.php
│   ├── Middleware/           # Request processing
│   │   └── AuditTrailMiddleware.php
│   └── Requests/             # Form validation
├── Models/                   # Database models
│   ├── User.php             # Users (students, admins, assistants)
│   ├── HealthProfile.php    # Health records
│   ├── Appointment.php      # Appointment data
│   ├── ActivityLog.php      # Audit trail
│   ├── Item.php             # Inventory items
│   └── Consultation.php     # Consultation records
├── Services/                # Business logic
│   ├── PuptasWebhookService.php
│   └── Custom services
└── Events/                  # Event-driven tasks

resources/
├── views/                   # Blade templates
│   ├── landing.blade.php           # Public landing page
│   ├── auth/                       # Authentication pages
│   ├── student/                    # Student-facing pages
│   ├── admin/                      # Admin pages
│   └── partials/                   # Reusable components
├── css/                     # Custom stylesheets
└── js/                      # JavaScript bundles

database/
├── migrations/              # 55+ schema definitions
├── seeders/                 # Sample data
└── factories/               # Test data generation

routes/
├── web.php                  # All routes (public & protected)
└── api.php                  # API endpoints (if applicable)
```

### **Core Database Tables**

| Table | Purpose | Key Fields |
|-------|---------|-----------|
| `users` | User accounts (students, admins, assistants) | id, name, email, user_role, student_number, status |
| `health_profiles` | Patient health records | id, user_id, reference_number, clearance_status |
| `appointments` | Appointment scheduling | id, user_id, appointment_date, status, notes |
| `consultations` | Visit records | id, user_id, consultation_date, medical_condition_id, temperature, bp |
| `activity_logs` | Audit trail | id, user_id, action, module, event_type, metadata |
| `items` | Inventory items | id, name, unit, quantity, min_stock, category |
| `inventory_movements` | Stock transactions | id, item_id, type, quantity, stock_before, stock_after |
| `medical_conditions` | Disease/condition reference | id, name, classification |
| `medical_assessments` | Assessment forms | id, health_profile_id, section_data (JSON) |

### **Entity Relationships**

```
User
├── HealthProfiles (1:many)
├── Appointments (1:many)
├── Consultations (1:many)
├── ActivityLogs (1:many)
└── Medical Assessments (1:many)

HealthProfile
├── User (1:1)
├── Consultations (1:many)
└── Medical Assessments (1:many)

Item
└── Inventory Movements (1:many)
```

---

## Complete User Flow: Login to Logout

### **Phase 1: Authentication (Landing → Dashboard)**

#### Step 1.1: User Arrives at Landing Page
**Route**: `GET /` → `landing` route
**File**: `resources/views/landing.blade.php`
**Process**:
```php
// routes/web.php (lines 22-47)
Route::get('/', function () {
    $user = Auth::guard('admin')->user() ?? Auth::guard('student')->user();
    if ($user instanceof User) {
        // User already authenticated - redirect to dashboard
        return redirect based on role;
    }
    // Show landing page
    return view('landing');
});
```

**Frontend Components**:
- Two-column layout with PUP branding
- "Login via One Portal" CTA button (navigates to `/login/portal`)
- Help panel with FAQs
- Responsive design with gradient backgrounds

**CSS/Styling**:
- Custom CSS with maroon (#70131B) theme
- Gradient backgrounds: `linear-gradient(135deg, rgba(255, 253, 246, 0.76) ...)`
- Rounded corners (14-32px border-radius)
- Shadow effects for depth
- Dark mode support with CSS variables

**HTML/SVG**:
- Custom SVG icons for UI elements
- Structured semantic HTML5
- Accessibility attributes (aria-label, role)

---

#### Step 1.2: Click "Login via One Portal"
**Route**: `GET /login/portal`
**File**: `routes/web.php` (lines 49-104)
**Process**:
```php
Route::get('/login/portal', function () {
    // Check if already authenticated
    $existingUser = Auth::guard('admin')->user() ?? Auth::guard('student')->user();
    if ($existingUser instanceof User) {
        // Redirect to dashboard based on role
        return redirect(resolveRedirectPathForUser($user));
    }

    // Redirect to One Portal OAuth2
    $authorizeUrl = buildOAuthAuthorizationUrl(
        client_id: env('IDP_CLIENT_ID'),
        redirect_uri: env('IDP_REDIRECT_URI'),
        response_type: 'code',
        scope: env('IDP_AUTHORIZE_SCOPE')
    );
    
    return redirect()->away($authorizeUrl);
});
```

**OAuth2 Flow (PKCE)**:
- Generate PKCE challenge (S256 method)
- Create authorization URL with:
  - `client_id`: Clinic system's identifier
  - `redirect_uri`: `https://clinic-ms.inaebsit2027.com/auth/callback`
  - `response_type`: 'code'
  - `scope`: User profile, email, roles
  - `code_challenge`: PKCE challenge (SHA256)
- User redirected to One Portal

---

#### Step 1.3: One Portal Authentication
**External Service**: One Portal (IDP)
**Process**:
1. User sees One Portal login screen
2. Enters credentials (email + password)
3. One Portal verifies credentials
4. User grants permission to clinic app
5. One Portal redirects back with authorization code: `https://clinic-ms.inaebsit2027.com/auth/callback?code=xxx&state=yyy`

---

#### Step 1.4: Handle OAuth2 Callback
**Route**: `GET /auth/callback`
**File**: `app/Http/Controllers/Auth/LoginController.php` (lines 1272-1407)
**Controller Method**: `handleIdpCallback(Request $request)`

**Process**:
```php
1. Validate Authorization Code
   - Check if $request->code exists
   - Validate state parameter (CSRF protection)

2. Exchange Code for Tokens
   POST /api/v1/auth/token
   Headers: Authorization: Basic {base64(client_id:client_secret)}
   Body: {
       "grant_type": "authorization_code",
       "code": "xxx",
       "redirect_uri": "https://clinic-ms.inaebsit2027.com/auth/callback",
       "code_verifier": "pkce_verifier" // PKCE verification
   }
   
   Response: {
       "access_token": "jwt_token",
       "refresh_token": "refresh_jwt",
       "expires_in": 3600,
       "token_type": "Bearer"
   }

3. Cache Tokens
   - Store in Redis/File cache (1 hour expiry)
   - Also store in secure HttpOnly cookies

4. Fetch User Profile
   GET /me (with Bearer token)
   Response: {
       "idp_user_id": "uuid",
       "email": "student@pup.edu.ph",
       "name": "Juan Dela Cruz",
       "user_roles": ["student"]
   }

5. Upsert User to Local Database
   - Check if user exists (by idp_user_id, email, student_number)
   - Create or update user record
   - Set user_role from IDP profile

6. Authenticate User
   - Log user into Laravel guard (admin or student)
   - Regenerate session ID
   - Set 'show_terms_modal' session flag

7. Attach OAuth Cookies
   - access_token cookie (HttpOnly, 60 min)
   - refresh_token cookie (HttpOnly, 10,080 min / 7 days)
   - Cookie flags: Secure=true, SameSite=Lax

8. Redirect to Dashboard
   return redirect($redirectPath);
   // Path determined by resolveRedirectPathForUser()
```

**Security Measures**:
- PKCE flow prevents authorization code interception
- State parameter prevents CSRF attacks
- HttpOnly cookies prevent XSS token theft
- Tokens refreshed automatically before expiry
- Session regeneration after login
- IP validation and user agent checking

---

#### Step 1.5: Role-Based Redirect
**Method**: `resolveRedirectPathForUser($user)` (lines 351-379)

```php
// Determine destination based on user role
if ($user->user_role === 'superadmin') {
    return '/admin/dashboard';
}

if ($user->user_role === 'admin') {
    if ($isStudentAssistant) {
        return '/assistant/choose-portal';  // Role selector
    }
    return '/assistant/dashboard';
}

// Default for students
return '/student/home';
```

**Three User Paths**:

**Path A: Superadmin**
→ Redirects to `/admin/dashboard`
→ Full system access

**Path B: Admin/Student Assistant**
→ Redirects to `/assistant/choose-portal`
→ Shows role selector page (student side vs admin side)
→ Can switch between student and admin workspaces

**Path C: Regular Student**
→ Redirects to `/student/home`
→ Student portal with health records, appointments

---

### **Phase 2: Dashboard & Navigation**

#### Admin Dashboard (Superadmin/Admin)
**Route**: `GET /admin/dashboard` or `/assistant/dashboard`
**File**: `resources/views/admin/dashboard.blade.php`

**Sections**:
- **Header**: Welcome message, quick stats, user profile
- **Sidebar Navigation**: 
  - Dashboard
  - Patient Intake (Walk-in)
  - Health Records
  - Appointments
  - Inventory Management
  - User Management
  - Reports
  - Settings
- **Main Content**: Dashboard cards with metrics
  - Total patients
  - Pending appointments
  - Low stock items
  - Recent activities

**Tech**:
- Blade templating with data injection from controller
- JavaScript for interactive charts (if any)
- CSS Grid/Flexbox for responsive layout
- Custom JavaScript for menu toggle, mobile responsiveness

---

#### Student Dashboard
**Route**: `GET /student/home`
**File**: `resources/views/student/home.blade.php`

**Sections**:
- Health record status
- Upcoming appointments
- Medical clearance status
- Available actions (book appointment, view results)

---

### **Phase 3: Core Features (Patient Intake Example)**

#### Access Patient Intake Module
**Route**: `GET /admin/walkin`
**File**: `resources/views/admin/walkin.blade.php` (4,000+ lines)
**Controller**: `WalkInController::index()`

**Landing Page Components**:

**1. Top Navigation**
- "Patient Intake" title with icon
- Current date/time
- User profile dropdown
- Search bar

**2. Intake Options Panel** (Hero Section)
```html
<div class="intake-option-grid">
    <!-- Option 1: Scan ID -->
    <button class="intake-option-card" id="btnScanId">
        <svg><!-- Barcode icon --></svg>
        <h3>Scan Student ID</h3>
        <p>Use OCR to extract ID information</p>
    </button>

    <!-- Option 2: Manual Entry -->
    <button class="intake-option-card" id="btnManualEntry">
        <svg><!-- Edit icon --></svg>
        <h3>Manual Entry</h3>
        <p>Type student details manually</p>
    </button>

    <!-- Option 3: Reference Lookup -->
    <button class="intake-option-card" id="btnReferenceNumber">
        <svg><!-- Search icon --></svg>
        <h3>Reference Number</h3>
        <p>Find applicant by reference</p>
    </button>
</div>
```

**CSS Styling**:
```css
.intake-option-card {
    /* Base style */
    display: flex;
    flex-direction: column;
    gap: 12px;
    padding: 24px;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    cursor: pointer;
    transition: all .18s ease;
    position: relative;
    overflow: hidden;
    
    /* Sweep animation on hover */
    &::after {
        content: "";
        position: absolute;
        top: -42%;
        left: -130%;
        width: 120%;
        height: 185%;
        background: linear-gradient(115deg, 
            rgba(250, 204, 21, 0) 0%, 
            rgba(250, 204, 21, 0.5) 45%, 
            rgba(250, 204, 21, 0) 100%);
        transform: skewX(-20deg);
        opacity: 0;
        transition: left .8s ease, opacity .18s ease;
        pointer-events: none;
    }
    
    &:hover::after {
        opacity: 1;
        left: 125%;
    }
}
```

**JavaScript Interaction**:
```javascript
const scanIdBtn = document.getElementById('btnScanId');
const manualEntryBtn = document.getElementById('btnManualEntry');
const refNumberBtn = document.getElementById('btnReferenceNumber');

scanIdBtn.addEventListener('click', function() {
    // Open image capture modal
    showModal('ocr-capture-modal');
    captureStudentId();
});

manualEntryBtn.addEventListener('click', function() {
    // Show manual entry form
    showModal('manual-entry-modal');
});

refNumberBtn.addEventListener('click', function() {
    // Show reference lookup modal
    showModal('applicant-modal');
    setEntryMode(true);
});
```

---

#### Feature: Scan Student ID (OCR Process)

**Step 1: Capture Image**
```javascript
function captureStudentId() {
    const videoElement = document.getElementById('video');
    const canvasElement = document.getElementById('canvas');
    const captureBtn = document.getElementById('captureBtn');
    
    // Request camera access
    navigator.mediaDevices.getUserMedia({
        video: { facingMode: 'environment' },
        audio: false
    }).then(stream => {
        videoElement.srcObject = stream;
        videoElement.play();
    }).catch(err => {
        showError('Camera access denied');
    });
    
    // Capture when user clicks
    captureBtn.addEventListener('click', function() {
        const context = canvasElement.getContext('2d');
        context.drawImage(videoElement, 0, 0);
        const imageData = canvasElement.toDataURL('image/png');
        
        // Send to backend
        verifyIdWithAI(imageData);
    });
}
```

**Step 2: AI Verification (OpenAI)**
**Route**: `POST /admin/walkin/verify-id-ai`
**Controller**: `WalkInController::verifyStudentIdWithAi()`

```php
public function verifyStudentIdWithAi(Request $request)
{
    $imageData = $request->input('image_data');
    
    // Call OpenAI Vision API
    $response = Http::withToken(env('OPENAI_API_KEY'))
        ->timeout(30)
        ->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4-vision-preview',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => $imageData
                            ]
                        ],
                        [
                            'type' => 'text',
                            'text' => 'Extract: student_number, first_name, surname, full_name, confidence_note'
                        ]
                    ]
                ]
            ]
        ]);
    
    $extractedData = parseAIResponse($response->json());
    
    return response()->json([
        'status' => 'verified',
        'student_number' => $extractedData['student_number'],
        'first_name' => $extractedData['first_name'],
        'surname' => $extractedData['surname'],
        'confidence' => $extractedData['confidence_note']
    ]);
}
```

**AI Prompt**:
```
"You are a student ID card extraction specialist. Analyze this image and extract:
1. Student Number (ID)
2. First Name
3. Last Name / Surname
4. Full Name
5. Confidence Level

Return as JSON with these exact fields.
If you cannot read a field, set it to null."
```

**Step 3: Populate Form with Extracted Data**
```javascript
function verifyIdWithAI(imageData) {
    fetch('/admin/walkin/verify-id-ai', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ image_data: imageData })
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'verified') {
            // Fill form fields
            document.getElementById('studentNumber').value = data.student_number;
            document.getElementById('firstName').value = data.first_name;
            document.getElementById('lastName').value = data.surname;
            
            // Fetch student from database
            fetchStudentData(data.student_number);
        }
    });
}
```

---

#### Feature: Reference Number Lookup

**Step 1: User Enters Reference Number**
```html
<div class="applicant-modal">
    <div class="applicant-ref-entry">
        <input type="text" id="refInput" placeholder="Enter reference number">
        <button id="btnFindApplicant">Find</button>
    </div>
</div>
```

**Step 2: Lookup via PUPTAS API**
**Route**: `GET /admin/walkin/get-student?student_id=REF123`
**Service**: `PuptasWebhookService::fetchApplicantByStudentNumber()`

```php
public function getStudent(Request $request, PuptasWebhookService $puptasWebhookService)
{
    $lookup = trim($request->student_id);
    
    // Try local database first
    $student = User::where('student_number', $lookup)
        ->orWhere('student_id', $lookup)
        ->first();
    
    // If not found locally, check PUPTAS
    if (!$student && $lookup !== '') {
        $lookupResult = $puptasWebhookService
            ->fetchApplicantByStudentNumberDetailed($lookup);
        
        if ($lookupResult['success']) {
            $applicant = $lookupResult['data'];
            // Create local user from PUPTAS data
            $student = $this->resolveLocalUserFromApplicant($applicant);
        }
    }
    
    // Log to audit trail
    $this->logReferenceLookup(
        $request, 
        $lookup, 
        $student ? true : false,
        $student?->name,
        $lookupResult['message'] ?? null
    );
    
    return response()->json([
        'status' => 'found',
        'student_number' => $student->student_number,
        'student_name' => $student->name,
        'course' => $student->course,
        'year' => $student->year,
        // ... more fields
    ]);
}
```

**PUPTAS API Call** (in PuptasWebhookService):
```php
$response = Http::timeout(20)
    ->withToken($this->getAccessToken())  // OAuth2 access token
    ->acceptJson()
    ->get(rtrim($applicantsBaseUrl, '/') . '/' . urlencode($studentNumber));

$data = $response->json('data');
return [
    'success' => is_array($data),
    'data' => $data,  // Contains: student_number, name, course, year, email, dob, etc.
];
```

**Step 3: Frontend Processing**
```javascript
function doLookup() {
    const ref = document.getElementById('refInput').value.trim();
    
    fetch('/admin/walkin/get-student?student_id=' + encodeURIComponent(ref))
        .then(r => r.json())
        .then(data => {
            if (data.status === 'found') {
                // Extract name from multiple possible fields
                let applicantName = data.student_name || data.name || data.full_name;
                
                // Display found message
                setStatus('success', 'Applicant found: ' + applicantName);
                
                // Show found card
                document.getElementById('foundName').textContent = applicantName;
                document.getElementById('foundCard').style.display = 'block';
                
                // Display details
                showLookupDetails(data);
                
                // Change button to Approve
                document.getElementById('btnFindApplicant').textContent = 'Approve';
                document.getElementById('btnFindApplicant').removeEventListener('click', doLookup);
                document.getElementById('btnFindApplicant').addEventListener('click', doApprove);
            } else {
                setStatus('error', data.message || 'Not found');
            }
        });
}

function doApprove() {
    // Send approval to backend
    fetch('/admin/walkin/approve-applicant', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            reference_number: currentLookupRef
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            setStatus('success', 'Applicant approved successfully');
            // Reset form after 1.5 seconds
            setTimeout(() => resetForm(), 1500);
        } else {
            setStatus('error', data.message);
        }
    });
}
```

**Step 4: Approval & Webhook**
**Route**: `POST /admin/walkin/approve-applicant`
**Controller**: `WalkInController::approveApplicant()`

```php
public function approveApplicant(Request $request, PuptasWebhookService $webhookService)
{
    $referenceNumber = $request->input('reference_number');
    
    // Fetch applicant for student ID
    $applicant = $webhookService->fetchApplicantByStudentNumber($referenceNumber);
    
    if (!$applicant) {
        return response()->json([
            'success' => false,
            'message' => 'Applicant not found'
        ], 404);
    }
    
    // Send medical clearance webhook to PUPTAS (approval)
    $webhookResult = $webhookService->sendMedicalClearance(
        $referenceNumber,
        $applicant['idp_user_id'],
        true  // isCleared = true (approval)
    );
    
    // Log to audit trail
    ActivityLog::create([
        'user_id' => auth()->id(),
        'action' => 'Applicant Approval',
        'module' => 'Patient Intake',
        'event_type' => 'applicant_approval',
        'description' => "Applicant approved: {$referenceNumber}",
        'metadata' => [
            'reference_number' => $referenceNumber,
            'webhook_status' => $webhookResult['success'] ? 'success' : 'failed',
        ],
        'status_code' => $webhookResult['success'] ? 200 : 422,
    ]);
    
    return response()->json($webhookResult);
}
```

**PUPTAS Webhook** (sends medical clearance):
```php
public function sendMedicalClearance($referenceNumber, $studentId, $isCleared = true)
{
    $timestamp = now()->timestamp;
    $nonce = Str::uuid();
    
    $payload = json_encode([
        'reference_number' => $referenceNumber,
        'student_id' => $studentId,
        'medical_status' => $isCleared ? 'cleared' : 'failed',
        'is_health_profile_completed' => $isCleared ? 1 : 0,
        'timestamp' => $timestamp,
        'nonce' => $nonce,
    ]);
    
    // Sign with HMAC
    $signature = hash_hmac('sha256', $payload, env('PUPTAS_WEBHOOK_SECRET'));
    
    // POST to PUPTAS
    $response = Http::withToken($this->getAccessToken())
        ->withHeaders([
            'X-HMAC-Signature' => $signature,
            'X-HMAC-Timestamp' => $timestamp,
            'X-HMAC-Nonce' => $nonce,
        ])
        ->post(env('PUPTAS_API_URL'), json_decode($payload, true));
    
    return [
        'success' => $response->successful(),
        'message' => $response->successful() ? 'Synced successfully' : $response->body(),
    ];
}
```

---

#### Feature: Medical Assessment Form

**Modal Structure**:
```html
<div class="hr-modal-backdrop" id="hrModalBackdrop">
    <div class="hr-modal-shell">
        <!-- Fixed Header -->
        <div class="hr-modal-head">
            <div>
                <h2>Medical Assessment</h2>
                <p>Complete the 5-section assessment form</p>
            </div>
            <button class="hr-modal-close" id="closeBtn">×</button>
        </div>
        
        <!-- Scrollable Content -->
        <div class="hr-modal-body">
            <!-- Section 00: Applicant Info -->
            <fieldset class="ma-section">
                <legend>Applicant Information</legend>
                <div class="ma-grid">
                    <div class="ma-control">
                        <label for="refNum">Reference Number</label>
                        <input type="text" id="refNum" class="form-control" readonly>
                    </div>
                    <div class="ma-control">
                        <label for="studentId">Student ID</label>
                        <input type="text" id="studentId" class="form-control" readonly>
                    </div>
                </div>
            </fieldset>
            
            <!-- Section 01: Assessment Context -->
            <fieldset class="ma-section">
                <legend>Assessment Context</legend>
                <!-- Date, Type of Assessment, etc. -->
            </fieldset>
            
            <!-- Section 02: Vitals -->
            <fieldset class="ma-section">
                <legend>Vital Signs</legend>
                <div class="ma-grid">
                    <div class="ma-control">
                        <label for="temp">Temperature (°C)</label>
                        <input type="number" id="temp" step="0.1" class="form-control">
                    </div>
                    <div class="ma-control">
                        <label for="bp">Blood Pressure (mmHg)</label>
                        <input type="text" id="bp" class="form-control">
                    </div>
                </div>
            </fieldset>
            
            <!-- Section 03: Document Review -->
            <fieldset class="ma-section">
                <legend>Medical Documents</legend>
                <!-- Document upload -->
            </fieldset>
            
            <!-- Section 04: Clinical Remarks -->
            <fieldset class="ma-section">
                <legend>Clinical Remarks</legend>
                <textarea class="form-control" rows="6"></textarea>
            </fieldset>
        </div>
        
        <!-- Sticky Footer -->
        <div class="hr-modal-footer">
            <button class="btn btn-secondary" id="cancelBtn">Cancel</button>
            <button class="btn btn-primary" id="submitBtn">Submit Assessment</button>
        </div>
    </div>
</div>
```

**CSS Styling**:
```css
.hr-modal-backdrop {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 1300;
    align-items: center;
    justify-content: center;
    padding: 28px;
    background: rgba(15, 23, 42, 0.52);
    backdrop-filter: blur(10px);
}

.hr-modal-backdrop.show {
    display: flex;
}

.hr-modal-shell {
    width: min(520px, 100%);
    max-height: calc(100dvh - 56px);
    border-radius: 24px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    background: rgba(255,255,255,0.96);
    border-left: 1px solid rgba(112,19,27,0.12);
    border-right: 1px solid rgba(112,19,27,0.12);
    border-top: 4px solid #facc15;      /* Yellow top border */
    border-bottom: 4px solid #70131B;   /* Maroon bottom border */
    box-shadow: 0 26px 60px rgba(15,23,42,0.22);
}

/* Fixed Header */
.hr-modal-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    padding: 22px;
    border-bottom: 1px solid rgba(112,19,27,0.12);
    background: linear-gradient(135deg, #fef3c7 0%, #fffbeb 100%);
    flex-shrink: 0;
}

/* Scrollable Body */
.hr-modal-body {
    flex: 1;
    overflow-y: auto;
    padding: 22px;
    
    /* Hide scrollbar */
    scrollbar-width: none;
    &::-webkit-scrollbar {
        display: none;
    }
}

/* Sticky Footer */
.hr-modal-footer {
    display: flex;
    align-items: center;
    gap: 12px;
    justify-content: flex-end;
    padding: 16px 22px;
    border-top: 1px solid rgba(112,19,27,0.12);
    background: #f8fafc;
    flex-shrink: 0;
}

/* Form Sections */
.ma-section {
    margin-bottom: 28px;
    padding-bottom: 24px;
    border-bottom: 1px dashed rgba(112,19,27,0.12);
}

.ma-section legend {
    font-size: 14px;
    font-weight: 800;
    color: #70131B;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 16px;
}

/* Form Grid */
.ma-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 16px;
}

/* Form Controls */
.ma-control {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.ma-control label {
    font-size: 13px;
    font-weight: 700;
    color: #374151;
}

.ma-control input,
.ma-control textarea,
.ma-control select {
    padding: 10px 14px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 13px;
    transition: all .15s ease;
    
    &:focus {
        outline: none;
        border-color: #facc15;
        box-shadow: 0 0 0 3px rgba(250, 204, 21, 0.1);
        background: rgba(254, 243, 199, 0.3);
    }
}
```

**JavaScript Interactions**:
```javascript
const backdrop = document.getElementById('hrModalBackdrop');
const submitBtn = document.getElementById('submitBtn');
const cancelBtn = document.getElementById('cancelBtn');

cancelBtn.addEventListener('click', () => {
    backdrop.classList.remove('show');
});

submitBtn.addEventListener('click', function(e) {
    e.preventDefault();
    
    // Collect form data
    const formData = new FormData();
    formData.append('reference_number', document.getElementById('refNum').value);
    formData.append('temperature', document.getElementById('temp').value);
    formData.append('blood_pressure', document.getElementById('bp').value);
    // ... collect other fields
    
    // Submit
    fetch('/admin/walkin/store', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
        },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showSuccess('Assessment saved successfully');
            setTimeout(() => backdrop.classList.remove('show'), 1500);
        } else {
            showError(data.message);
        }
    });
});
```

---

### **Phase 4: Health Records & Management**

#### Browse Health Records
**Route**: `GET /admin/health-records`
**File**: `resources/views/admin/health_records.blade.php`

**Features**:
- **Toolbar**:
  - Filter button (Filter Health Forms)
  - Search toggle with magnifying glass icon
  - Medical Assessment button (Opens modal)

- **Summary Cards**:
  - Total Submissions
  - Pending Approvals
  - Clearance Status breakdown

- **Records Table**:
  - Student Name
  - Course
  - Health Status
  - Clearance Status
  - Actions (View, Edit, Approve, Download)

**Filter Modal**:
```html
<div class="health-filter-modal" id="healthFilterModal">
    <div class="health-filter-modal-card">
        <div class="health-filter-modal-head">
            <h3>Filter Health Forms</h3>
            <button class="health-filter-modal-close">×</button>
        </div>
        <form class="health-filter-form" id="healthFilterForm">
            <div class="health-filter-field">
                <label>Course</label>
                <select name="course">
                    <option>All Courses</option>
                    @foreach($courses as $course)
                        <option value="{{ $course }}">{{ $course }}</option>
                    @endforeach
                </select>
            </div>
            <div class="health-filter-field">
                <label>Month</label>
                <select name="month">
                    <option>All Months</option>
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}">{{ date('F', mktime(0,0,0,$m)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="health-filter-field">
                <label>Year Level</label>
                <select name="year">
                    <option>All Levels</option>
                    <option>1st Year</option>
                    <option>2nd Year</option>
                    <option>3rd Year</option>
                    <option>4th Year</option>
                </select>
            </div>
            <div class="health-filter-actions">
                <button type="reset" class="btn btn-secondary">Reset</button>
                <button type="submit" class="btn btn-primary">Apply Filters</button>
            </div>
        </form>
    </div>
</div>
```

**JavaScript**:
```javascript
const filterToggle = document.getElementById('healthFilterToggle');
const filterModal = document.getElementById('healthFilterModal');
const filterForm = document.getElementById('healthFilterForm');

// Toggle filter modal with sweep animation
filterToggle.addEventListener('click', function() {
    const isOpen = filterModal.classList.contains('is-open');
    if (isOpen) {
        filterModal.classList.remove('is-open');
    } else {
        filterModal.classList.add('is-open');
    }
});

// Submit filters
filterForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const filters = new FormData(filterForm);
    const queryString = new URLSearchParams(filters).toString();
    
    window.location.href = '/admin/health-records?' + queryString;
});

// Search functionality
const searchToggle = document.getElementById('healthRecordsSearchToggle');
const searchShell = document.getElementById('healthRecordsSearchShell');
const searchInput = document.getElementById('recordSearch');

searchToggle.addEventListener('click', function() {
    const isOpen = searchShell.classList.contains('is-open');
    searchShell.classList.toggle('is-open', !isOpen);
    
    if (!isOpen) {
        searchInput.focus();
    }
});

searchInput.addEventListener('input', function() {
    const searchTerm = this.value.trim().toLowerCase();
    
    // Filter table rows
    document.querySelectorAll('#healthTable tbody tr').forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});
```

---

### **Phase 5: Appointments Management**

#### View Appointments
**Route**: `GET /admin/appointments`
**File**: `resources/views/admin/appointments.blade.php`

**Features**:
- Calendar or list view
- Schedule new appointment
- View appointment details
- Cancel/Reschedule
- Send reminders

**Calendar View** (if implemented):
```javascript
// Using a calendar library or custom implementation
const calendarElement = document.getElementById('calendar');
const appointments = @json($appointments);

// Render calendar with appointment indicators
renderCalendar(appointments);

// Color coding:
// - Green: Confirmed
// - Yellow: Pending
// - Red: Cancelled
// - Blue: Completed
```

---

### **Phase 6: Inventory Management**

#### Manage Inventory
**Route**: `GET /admin/inventory`
**File**: `resources/views/admin/inventory.blade.php`

**Features**:
- **Add New Item Modal**:
  - Item name
  - Category (Medicines, Supplies, Equipment)
  - Unit (pcs, box, bottles)
  - Min stock level
  - Current quantity

- **Item Cards** with actions:
  - Edit
  - Delete
  - Restock
  - View History

**Restock Modal**:
```html
<div class="inventory-modal" id="restockModal">
    <div class="modal-card">
        <div class="modal-head">
            <h2>Restock: {{ $item->name }}</h2>
            <button class="modal-close">×</button>
        </div>
        <div class="modal-body">
            <div class="info-box">
                <strong>Current Stock:</strong> 50 box <br>
                <strong>After Restock:</strong> <span id="afterRestock">-</span>
            </div>
            <div class="form-group">
                <label>Quantity to Add</label>
                <input type="number" id="restockQty" min="1" value="10">
            </div>
            <div class="form-group">
                <label>Unit</label>
                <select id="restockUnit">
                    <option>pcs</option>
                    <option>box</option>
                </select>
            </div>
            <div class="form-group">
                <label>Notes</label>
                <textarea placeholder="Add notes..."></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary">Cancel</button>
            <button class="btn btn-primary" id="submitRestock">Confirm Restock</button>
        </div>
    </div>
</div>
```

**JavaScript**:
```javascript
document.getElementById('restockQty').addEventListener('input', function() {
    const current = parseInt(document.querySelector('[data-current-stock]').dataset.currentStock);
    const added = parseInt(this.value) || 0;
    document.getElementById('afterRestock').textContent = (current + added) + ' box';
});

document.getElementById('submitRestock').addEventListener('click', function() {
    const qty = document.getElementById('restockQty').value;
    const notes = document.querySelector('[name="notes"]').value;
    
    fetch('/admin/inventory/restock', {
        method: 'POST',
        body: JSON.stringify({
            item_id: itemId,
            quantity: qty,
            notes: notes
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showSuccess('Item restocked successfully');
            // Refresh inventory list
            location.reload();
        }
    });
});
```

**Backend** (WalkInController::store):
```php
// Handles inventory consumption during consultation
foreach ($request->issued_medicines as $medicine) {
    $item = Item::find($medicine['item_id']);
    
    if ($item) {
        $stockBefore = $item->quantity;
        $item->quantity -= $medicine['quantity'];
        $item->save();
        
        // Log inventory movement
        InventoryMovement::create([
            'item_id' => $item->id,
            'user_id' => auth()->id(),
            'type' => 'consumed',
            'quantity' => -$medicine['quantity'],
            'stock_before' => $stockBefore,
            'stock_after' => $item->quantity,
            'unit' => $item->unit,
            'notes' => 'Issued during consultation.',
        ]);
    }
}
```

---

### **Phase 7: User Settings**

#### Access Settings
**Route**: `GET /admin/settings`
**File**: `resources/views/admin/settings.blade.php`

**Settings Modals**:

**1. Clinic Information Modal**:
```html
<div class="modal" id="clinicInfoModal">
    <div class="modal-head" style="background: linear-gradient(135deg, #70131B, #8f2230);">
        <div class="modal-head-content">
            <h2 class="modal-title">Clinic Information</h2>
            <p class="modal-subtitle">Update clinic details and contact info</p>
        </div>
        <button class="modal-head-close">×</button>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <label>Clinic Name</label>
            <input type="text" class="form-control" value="{{ $clinic->name }}">
        </div>
        <div class="form-group">
            <label>Address</label>
            <textarea class="form-control" rows="3">{{ $clinic->address }}</textarea>
        </div>
        <div class="form-group">
            <label>Phone</label>
            <input type="tel" class="form-control" value="{{ $clinic->phone }}">
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-secondary">Cancel</button>
        <button class="btn btn-primary">Save Changes</button>
    </div>
</div>
```

**CSS for Modal**:
```css
.modal-head {
    position: relative;
    padding: 20px;
    background: linear-gradient(135deg, #70131B, #8f2230);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
}

.modal-title {
    margin: 0;
    font-size: 18px;
    font-weight: 800;
    color: #ffffff;
}

.modal-subtitle {
    margin: 6px 0 0;
    font-size: 13px;
    color: rgba(255,255,255,0.85);
}

.modal-body {
    max-height: calc(100vh - 280px);
    overflow-y: auto;
    padding: 24px;
}

.modal-footer {
    position: sticky;
    bottom: 0;
    padding: 16px 24px;
    border-top: 1px solid #e5e7eb;
    background: #f8fafc;
    display: flex;
    gap: 12px;
    justify-content: flex-end;
}

.form-control {
    padding: 10px 14px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 13px;
    transition: all .15s ease;
}

.form-control:focus {
    outline: none;
    border-color: #facc15;
    box-shadow: 0 0 0 3px rgba(250, 204, 21, 0.1);
    background: rgba(254, 243, 199, 0.3);
}
```

---

### **Phase 8: Logout Process**

#### Click Logout
**Route**: `GET /admin/logout` or `POST /admin/logout`
**File**: `app/Http/Controllers/Auth/LoginController.php` (lines 1409-1452)
**Controller Method**: `logout(Request $request)`

```php
public function logout(Request $request)
{
    $user = $this->authenticatedUser();
    
    // Get access token from cookie for IDP logout
    $accessToken = $request->cookie(config('services.idp.access_cookie_name'));
    $idpLogoutUrl = config('services.idp.logout_url');
    
    // Notify IDP to logout
    if ($idpLogoutUrl && $accessToken) {
        try {
            Http::withToken($accessToken)
                ->post($idpLogoutUrl);
        } catch (\Exception $e) {
            Log::error('IDP logout failed', ['error' => $e->getMessage()]);
        }
    }
    
    // Log logout event to audit trail
    $this->recordAuthEvent(
        $request,
        'Logout',
        'User logged out from the system.',
        $user
    );
    
    // Clear Laravel session
    auth()->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    
    // Clear OAuth cookies
    return redirect('/')->withCookie(
        Cookie::forget(config('services.idp.access_cookie_name'))
    )->withCookie(
        Cookie::forget(config('services.idp.refresh_cookie_name'))
    );
}
```

**Process**:
1. Get user's access token from HttpOnly cookie
2. Make POST request to One Portal logout endpoint
3. Log logout event to ActivityLog table
4. Invalidate Laravel session
5. Clear OAuth cookies (access_token, refresh_token)
6. Redirect to landing page

**AuditTrail Entry**:
```json
{
    "user_id": 123,
    "user_name": "Juan Dela Cruz",
    "user_role": "admin",
    "action": "Logout",
    "module": "Authentication",
    "event_type": "auth",
    "description": "User logged out from the system.",
    "status_code": 200,
    "ip_address": "192.168.1.100",
    "created_at": "2026-06-04T15:30:00Z"
}
```

#### Land on Landing Page
```html
<!-- resources/views/landing.blade.php -->
<div class="landing-shell">
    <h1>PUP Taguig Clinic Management System</h1>
    <p>Welcome. Please log in via One Portal to continue.</p>
    <a href="{{ route('login.portal') }}" class="portal-btn">
        Login via One Portal
    </a>
</div>
```

---

## Core Processes & Features

### **1. Patient Intake Workflow**

**Entry Points**:
- Scan Student ID (OCR)
- Manual Entry
- Reference Number Lookup

**Outcomes**:
- Create health profile
- Schedule consultation
- Complete medical assessment
- Issue clearance or referral

**Audit Trail**: Every lookup, approval, and assessment is logged

---

### **2. Reference Lookup with Approval**

**Flow**:
1. User enters reference number
2. System queries:
   - Local database (users table)
   - PUPTAS API (external system)
3. If found:
   - Display applicant details
   - Change button to "Approve"
4. On approval:
   - Send medical_status='cleared' webhook to PUPTAS
   - Create audit log entry
   - Show success/error message

**Audit Trail Captures**:
- Reference number searched
- Applicant found/not found
- Error messages
- Approval status and webhook response
- User, IP, timestamp

---

### **3. Medical Assessment Form**

**5-Section Structure**:
1. **Applicant Information**
   - Reference number (read-only)
   - Student ID (read-only)
   - Name (read-only)

2. **Assessment Context**
   - Assessment date
   - Type of assessment
   - Examiner name

3. **Vital Signs**
   - Temperature
   - Blood Pressure
   - Pulse Rate
   - Respiratory Rate
   - COVID Status

4. **Document Review**
   - Upload medical documents
   - OCR extracted data
   - Reference lookups

5. **Clinical Remarks**
   - Assessment notes
   - Clinical findings
   - Recommendations

**Styling**:
- Fixed header with maroon gradient
- Scrollable body
- Sticky footer with action buttons
- Yellow top border, maroon bottom border
- Input focus states with shadow effects

---

### **4. Inventory Management**

**Operations**:
- Add new items
- Update quantities
- Track consumption during consultations
- Restock items
- View history of movements

**Stock Tracking**:
- Unit types: pcs (pieces), box, bottles
- Minimum stock levels
- Automatic deduction on item issue
- Inventory movement logs

---

### **5. Appointments System**

**Features**:
- Schedule appointments
- View appointment calendar
- Track appointment status
- Send appointment reminders
- Cancel/reschedule

---

### **6. Health Records Management**

**Features**:
- Browse all health records
- Filter by course, month, year level
- Search by student name or ID
- View health profile status
- Download clearance documents
- Approve records

---

### **7. Audit Trail System**

**Logged Events**:
- Login/Logout
- Reference lookups (success, not found, errors)
- Applicant approvals (success, webhook failures)
- Data modifications
- Report generation
- Inventory movements

**Metadata Captured**:
- User ID, name, role
- IP address, user agent
- HTTP method, route
- Event type, status code
- Custom metadata (reference numbers, errors, etc.)

---

## UI/UX Components & Styling

### **Design System**

**Color Palette**:
- Primary: Maroon (#70131B)
- Secondary: Gold/Yellow (#facc15)
- Background: Light Cream (#fffbeb, #fef3c7)
- Text: Dark Gray (#111827)
- Borders: Light Gray (#d1d5db, #e5e7eb)

**Typography**:
- Headers: 800-900 font-weight
- Body: 400-500 font-weight
- Size: 13px-18px (mobile-first approach)

**Spacing**:
- Padding: 12px, 16px, 18px, 22px, 24px, 28px, 34px
- Gap/Margin: 8px, 10px, 12px, 14px, 16px, 18px, 20px, 24px
- Border-radius: 6px, 8px, 14px, 16px, 22px, 24px, 28px, 32px, 999px

**Shadows**:
- Small: `0 4px 12px rgba(0,0,0,0.05)`
- Medium: `0 12px 24px rgba(112, 19, 27, 0.12)`
- Large: `0 24px 60px rgba(15, 23, 42, 0.18)`

### **Interactive Components**

**Buttons**:
```css
/* Primary Button */
.btn-primary {
    background: linear-gradient(135deg, #70131B, #8f2230);
    color: #ffffff;
    padding: 10px 18px;
    border-radius: 8px;
    border: none;
    font-weight: 700;
    cursor: pointer;
    transition: all .18s ease;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 12px 24px rgba(112, 19, 27, 0.24);
}

.btn-primary:active {
    transform: translateY(0);
}

/* Secondary Button */
.btn-secondary {
    background: #f3f4f6;
    color: #374151;
    border: 1px solid #d1d5db;
    padding: 10px 18px;
    border-radius: 8px;
    font-weight: 700;
    cursor: pointer;
    transition: all .15s ease;
}

.btn-secondary:hover {
    background: #e5e7eb;
    border-color: #9ca3af;
}
```

**Form Inputs**:
```css
.form-control {
    padding: 10px 14px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 13px;
    background: #ffffff;
    transition: all .15s ease;
}

.form-control:focus {
    outline: none;
    border-color: #facc15;
    box-shadow: 0 0 0 3px rgba(250, 204, 21, 0.1);
    background: rgba(254, 243, 199, 0.3);
}

.form-control:disabled,
.form-control[readonly] {
    background: #f3f4f6;
    cursor: not-allowed;
    opacity: 0.7;
}
```

**Cards**:
```css
.card {
    background: #ffffff;
    border-radius: 12px;
    padding: 24px;
    border: 1px solid #f0f0f0;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    transition: all .18s ease;
}

.card:hover {
    box-shadow: 0 12px 24px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}
```

### **Animations**

**Sweep Animation** (Hero Cards, Buttons):
```css
@keyframes sweep {
    0% {
        left: -130%;
        opacity: 0;
    }
    100% {
        left: 125%;
        opacity: 1;
    }
}

.intake-option-card::after {
    content: "";
    position: absolute;
    top: -42%;
    left: -130%;
    width: 120%;
    height: 185%;
    background: linear-gradient(115deg, 
        rgba(250, 204, 21, 0) 0%, 
        rgba(250, 204, 21, 0.5) 45%, 
        rgba(250, 204, 21, 0) 100%);
    transform: skewX(-20deg);
    opacity: 0;
    transition: left .8s ease, opacity .18s ease;
    pointer-events: none;
    z-index: 0;
}

.intake-option-card:hover::after {
    left: 125%;
    opacity: 1;
}
```

**Float Animation** (Icons):
```css
@keyframes floatCard {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-5px);
    }
}

.icon {
    animation: floatCard 3.8s ease-in-out infinite;
}
```

**Transition Effects**:
- All `.18s ease` (buttons, states)
- Modal opens `.32s cubic-bezier(.22, 1, .36, 1)`
- Form inputs `.15s ease`
- Hover effects `.22s ease`

### **Responsive Design**

**Mobile Breakpoints**:
```css
/* Default: Desktop (1024px+) */

@media (max-width: 900px) {
    .grid-2col {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 760px) {
    /* Reduce padding */
    /* Stack vertical layouts */
    /* Increase touch targets */
    .btn { min-height: 48px; }
}

@media (max-width: 480px) {
    /* Full-width modals */
    /* Single column layouts */
    /* Larger text for readability */
}
```

---

## External Integrations

### **1. One Portal (OAuth2 IDP)**

**Purpose**: User authentication and authorization

**Flow**:
```
Clinic App → One Portal (OAuth2)
   ↓
User logs in at One Portal
   ↓
One Portal returns auth code
   ↓
Clinic app exchanges code for tokens
   ↓
Clinic app fetches user profile
   ↓
User authenticated in Clinic app
```

**Configuration**:
```env
IDP_ENABLED=true
IDP_BASE_URL=https://oneportal.pup.edu.ph
IDP_CLIENT_ID=clinic-ms-client
IDP_CLIENT_SECRET=secret_key
IDP_REDIRECT_URI=https://clinic-ms.inaebsit2027.com/auth/callback
IDP_AUTHORIZE_PATH=/api/v1/auth/authorize
IDP_TOKEN_PATH=/api/v1/auth/token
IDP_USE_PKCE=true
IDP_PKCE_CHALLENGE_METHOD=S256
```

**Token Management**:
- Access Token (1 hour): Used for API calls
- Refresh Token (7 days): Used to get new access tokens
- Both stored in HttpOnly secure cookies

---

### **2. PUPTAS (Medical Records)**

**Purpose**: Applicant lookup and medical clearance

**Endpoints**:
- `GET /applicants/{reference_number}` - Fetch applicant data
- `POST /medical-clearance` - Send medical status (cleared/failed)
- `GET /api/validate-token` - Validate access token

**API Call Example**:
```php
$response = Http::withToken($accessToken)
    ->acceptJson()
    ->get('https://puptas.edu.ph/api/applicants/' . $referenceNumber);

$applicantData = $response->json('data');
// Returns: {
//   "idp_user_id": "uuid",
//   "student_number": "2027-1234",
//   "email": "student@pup.edu.ph",
//   "name": "Juan Dela Cruz",
//   "course": "BS Computer Science",
//   "dob": "2005-01-15",
//   ...
// }
```

**Webhook (Approval)**:
```php
$payload = json_encode([
    'reference_number' => '2027-1234',
    'student_id' => 'idp-uuid',
    'medical_status' => 'cleared',  // or 'failed'
    'is_health_profile_completed' => 1,
    'timestamp' => 1717500000,
    'nonce' => 'uuid-nonce',
]);

$signature = hash_hmac('sha256', $payload, env('PUPTAS_WEBHOOK_SECRET'));

$response = Http::withToken($accessToken)
    ->withHeaders([
        'X-HMAC-Signature' => $signature,
        'X-HMAC-Timestamp' => $timestamp,
        'X-HMAC-Nonce' => $nonce,
    ])
    ->post('https://puptas.edu.ph/api/medical-clearance', json_decode($payload, true));
```

---

### **3. OpenAI API**

**Purpose**: AI-powered ID verification and OCR

**Model**: `gpt-4-vision-preview`

**Request**:
```php
$response = Http::withToken(env('OPENAI_API_KEY'))
    ->timeout(30)
    ->post('https://api.openai.com/v1/chat/completions', [
        'model' => 'gpt-4-vision-preview',
        'messages' => [
            [
                'role' => 'user',
                'content' => [
                    [
                        'type' => 'image_url',
                        'image_url' => ['url' => $base64Image]
                    ],
                    [
                        'type' => 'text',
                        'text' => 'Extract: student_number, first_name, last_name, confidence_note'
                    ]
                ]
            ]
        ]
    ]);
```

**Response**:
```json
{
    "choices": [
        {
            "message": {
                "content": "{\"student_number\": \"2027-1234\", \"first_name\": \"Juan\", \"last_name\": \"Dela Cruz\", \"confidence_note\": \"High confidence\"}"
            }
        }
    ]
}
```

---

## Security & Authentication

### **OAuth2 with PKCE Flow**

**Why PKCE?**
- Prevents authorization code interception in mobile/SPA apps
- Uses code challenge + code verifier pairs

**Flow**:
1. Generate random code_verifier (43-128 chars)
2. Create code_challenge = BASE64URL(SHA256(code_verifier))
3. Redirect to IDP with code_challenge
4. User logs in, IDP returns auth code
5. Exchange code + code_verifier for tokens
6. Tokens valid only with matching verifier

---

### **Session Management**

**Cookies**:
- `access_token` (HttpOnly, Secure, SameSite=Lax, 60 min)
- `refresh_token` (HttpOnly, Secure, SameSite=Lax, 7 days)
- Laravel session ID (HttpOnly, Secure)

**Token Refresh**:
```php
if ($tokenExpiry < now()->addMinutes(5)) {
    $newTokens = $webhookService->refreshTokens();
    // Store new tokens in cookies
}
```

---

### **CSRF Protection**

**Laravel CSRF Token**:
```html
<meta name="csrf-token" content="{{ csrf_token() }}">
```

**Fetch Requests**:
```javascript
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

fetch('/admin/action', {
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': csrfToken,
        'Content-Type': 'application/json'
    },
    body: JSON.stringify(data)
});
```

---

### **Input Validation**

**Backend**:
```php
$request->validate([
    'reference_number' => 'required|string|max:50',
    'temperature' => 'numeric|between:35,42',
    'blood_pressure' => 'required|regex:/^\d+\/\d+$/',
]);
```

**Frontend**:
```javascript
function validateForm(data) {
    if (!data.referenceNumber || data.referenceNumber.trim() === '') {
        showError('Reference number is required');
        return false;
    }
    
    if (data.temperature && (data.temperature < 35 || data.temperature > 42)) {
        showError('Temperature out of valid range');
        return false;
    }
    
    return true;
}
```

---

### **Data Encryption**

**Sensitive Data**:
- Passwords: Hashed with bcrypt (Laravel's Hash::make())
- Tokens: Stored in HttpOnly secure cookies
- API Keys: Stored in .env (not in version control)
- Personal Data: Encrypted at rest (if implemented)

---

## Conclusion

The INNOVISON PUPOCMS is a comprehensive, well-structured clinic management system that:

✅ Uses modern security practices (OAuth2, PKCE, CSRF protection)
✅ Integrates with external services (One Portal, PUPTAS, OpenAI)
✅ Maintains complete audit trails for compliance
✅ Provides intuitive user interfaces with animations
✅ Handles complex workflows (patient intake, assessments, approvals)
✅ Manages inventory and resources effectively
✅ Supports multiple user roles with appropriate access controls

The system is built on solid architectural principles with clear separation of concerns, making it maintainable and extensible for future enhancements.
