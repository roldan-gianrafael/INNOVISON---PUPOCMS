<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PUP Taguig - Online Clinic</title>
    <style>
        /* --- 1. GLOBAL RESET & VARIABLES --- */
        :root {
            --accent: #8B0000;      /* PUP Maroon */
            --accent-dark: #600000;
            --white: #ffffff;
            --glass-bg: rgba(255, 255, 255, 0.95);
            --text-dark: #20343a;
            --text-light: #64748b;
            --error-bg: #fee2e2;
            --error-text: #b91c1c;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; }
        
        body {
            background: linear-gradient(rgba(15, 27, 38, 0.75), rgba(15, 27, 38, 0.85)), 
                        url('{{ asset("images/PUPBG.jpg") }}') no-repeat center center fixed;
            background-size: cover;
            color: var(--white);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* --- 2. LOGO HEADER --- */
        .logo-header {
            background-color: var(--accent);
            padding: 16px 24px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            display: flex;
            justify-content: center;
            position: relative;
            z-index: 10;
        }
        .logo-brand { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .logo-icon { width: 48px; height: 48px; border-radius: 50%; border: 2px solid rgba(255,255,255,0.2); object-fit: cover; }
        .logo-text { color: var(--white); line-height: 1.1; }
        .logo-title { font-weight: 800; font-size: 18px; letter-spacing: 0.5px; }
        .logo-subtitle { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; opacity: 0.9; }

        /* --- 3. MAIN CONTAINER --- */
        .lp-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            text-align: center;
            width: 100%;
        }

        .login-box {
            background: var(--glass-bg);
            padding: 40px;
            border-radius: 20px;
            width: 100%;
            max-width: 420px;
            color: var(--text-dark);
            box-shadow: 0 20px 50px rgba(0,0,0,0.4);
            animation: slideUp 0.8s ease-out;
        }

        .login-box h2 { color: var(--accent); font-weight: 800; margin-bottom: 8px; }
        .login-box p { color: var(--text-light); font-size: 14px; margin-bottom: 24px; }

        .alert-error {
            background-color: var(--error-bg);
            color: var(--error-text);
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 13px;
            text-align: left;
            border: 1px solid #fecaca;
        }
        .alert-error ul { list-style: none; margin: 0; padding: 0; }

        .form-group { margin-bottom: 15px; text-align: left; }
        .form-group label { display: block; font-size: 12px; font-weight: 700; color: var(--accent); margin-bottom: 6px; letter-spacing: 0.5px; }
        .form-group input, .form-group select {
            width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 14px; transition: border 0.3s;
        }
        .form-group input:focus { outline: none; border-color: var(--accent); }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        
        .error-message { color: var(--error-text); font-size: 11px; font-weight: 600; margin-top: 4px; display: block; }

        .btn-submit {
            width: 100%; padding: 14px; background: var(--accent); color: white; border: none;
            border-radius: 10px; font-weight: 700; cursor: pointer; margin-top: 10px; transition: 0.3s;
        }
        .btn-submit:hover { background: var(--accent-dark); transform: translateY(-2px); }

        /* --- 4. MODAL STYLES --- */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            backdrop-filter: blur(8px);
        }
        .modal-content {
            background: var(--white);
            color: var(--text-dark);
            width: 95%;
            max-width: 600px;
            padding: 40px;
            border-radius: 24px;
            position: relative;
            max-height: 90vh;
            overflow-y: auto;
        }
        .modal-close { position: absolute; top: 20px; right: 20px; cursor: pointer; font-size: 28px; color: var(--text-light); }

        /* Terms Pop-up Styling */
        .terms-popup-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.9);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 2000;
            backdrop-filter: blur(10px);
        }
        .terms-popup-box {
            background: white;
            padding: 30px;
            border-radius: 20px;
            max-width: 450px;
            width: 90%;
            text-align: center;
            color: var(--text-dark);
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
        }

        .switch-form { margin-top: 20px; font-size: 14px; color: var(--text-light); }
        .switch-form span { color: var(--accent); cursor: pointer; font-weight: 700; text-decoration: underline; }

        .lp-foot { text-align: center; padding: 24px; font-size: 13px; color: rgba(255, 255, 255, 0.6); }

        @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

  <div class="logo-header">
    <div class="logo-brand">
      <img src="{{ asset('images/pup_logo.png') }}" alt="PUP Logo" class="logo-icon">
      <div class="logo-text">
        <div class="logo-title">PUP TAGUIG</div>
        <div class="logo-subtitle">ONLINE CLINIC</div>
      </div>
    </div>
  </div>

  <main class="lp-container">
    <div class="login-box">
        <h2>Clinic Portal</h2>
        <p>Login to your account to continue</p>

        @if ($errors->any())
            <div class="alert-error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>⚠️ {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ url('/login-action') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>EMAIL ADDRESS</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="e.g. name@pup.edu.ph" required>
                @error('email') <span class="error-message">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label>PASSWORD</label>
                <input type="password" name="password" placeholder="••••••••" required>
                @error('password') <span class="error-message">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="btn-submit">Login to Portal</button>
        </form>

        <div class="switch-form">
            Don't have an account? <span onclick="openModal('registerModal')">Create Account</span>
        </div>
    </div>
  </main>

  <div id="registerModal" class="modal-overlay">
      <div class="modal-content">
          <span class="modal-close" onclick="closeModal('registerModal')">&times;</span>
          <h2 style="color: var(--accent); font-weight: 800; margin-bottom: 5px;">User Registration</h2>
          <p style="color: var(--text-light); margin-bottom: 25px; font-size: 14px;">Fill out the form to create your medical profile.</p>
          
          <form id="regForm" action="{{ url('/register-action') }}" method="POST">
              @csrf
              <input type="hidden" name="terms_agreed" id="hiddenTerms" value="">

              <div class="form-row">
                  <div class="form-group"><label>FIRST NAME</label><input type="text" name="first_name" required></div>
                  <div class="form-group"><label>LAST NAME</label><input type="text" name="last_name" required></div>
              </div>

              <div class="form-row">
                  <div class="form-group"><label>STUDENT/FACULTY ID</label><input type="text" name="student_id" placeholder="202X-XXXXX-TG-0" required></div>
                  <div class="form-group"><label>DATE OF BIRTH</label><input type="date" name="DOB" required></div>
              </div>

              <div class="form-group"><label>EMAIL ADDRESS</label><input type="email" name="email" required></div>

              <div class="form-row">
                  <div class="form-group">
                      <label>COURSE / DEPARTMENT</label>
                      <select name="course" required>
                          <option value="" disabled selected>Select Course</option>
                          <option value="BSIT">BSIT</option>
                          <option value="BSCS">BSCS</option>
                          <option value="BSCpE">BSCpE</option>
                          <option value="BSA">BSA</option>
                          <option value="FACULTY">FACULTY DEPT</option>
                      </select>
                  </div>
                  <div class="form-group">
                      <label>YEAR & SECTION</label>
                      <div class="form-row" style="grid-template-columns: 1fr 1fr;">
                          <select name="year" required>
                              <option value="" disabled selected>Year</option>
                              <option value="1">1st Year</option>
                              <option value="2">2nd Year</option>
                              <option value="3">3rd Year</option>
                              <option value="4">4th Year</option>
                          </select>
                          <select name="section" required>
                              <option value="" disabled selected>Sec</option>
                              <option value="1">1</option>
                              <option value="2">2</option>
                              <option value="3">3</option>
                              <option value="4">4</option>
                              <option value="5">5</option>
                          </select>
                      </div>
                  </div>
              </div>

              <div class="form-row">
                  <div class="form-group"><label>PASSWORD</label><input type="password" name="password" required></div>
                  <div class="form-group"><label>CONFIRM PASSWORD</label><input type="password" name="password_confirmation" required></div>
              </div>

              <button type="button" onclick="validateAndShowTerms()" class="btn-submit" style="margin-top: 10px;">Register Account</button>
          </form>
      </div>
  </div>

  <div id="termsPopUp" class="terms-popup-overlay">
      <div class="terms-popup-box">
          <h3 style="color: var(--accent); margin-bottom: 15px;">License & Terms of Agreement</h3>
          <div style="text-align: justify; font-size: 13px; line-height: 1.5; color: #444; margin-bottom: 20px; max-height: 250px; overflow-y: auto; padding-right: 10px;">
              <p><strong>1. Data Privacy Act:</strong> By clicking "I Agree", you voluntarily provide your personal and medical information to the PUP Taguig Clinic in compliance with RA 10173.</p><br>
              <p><strong>2. Accuracy:</strong> You certify that all data entered is true. Any falsification of medical records is a violation of University policy.</p><br>
              <p><strong>3. Use of Data:</strong> Your data will only be used for clinic appointments, medical history tracking, and university health reports.</p>
          </div>
          <div style="display: flex; gap: 10px;">
              <button type="button" onclick="closeTerms()" style="flex: 1; padding: 12px; border-radius: 10px; border: 1px solid #ccc; cursor: pointer; background: #eee;">Cancel</button>
              <button type="button" onclick="submitFinalForm()" style="flex: 2; padding: 12px; border-radius: 10px; background: var(--accent); color: white; border: none; cursor: pointer; font-weight: bold;">I Agree and Register</button>
          </div>
      </div>
  </div>

  <footer class="lp-foot">© 2026 PUP Taguig — Online Clinic System</footer>

  <script>
      function openModal(id) { document.getElementById(id).style.display = 'flex'; }
      function closeModal(id) { document.getElementById(id).style.display = 'none'; }

      function validateAndShowTerms() {
          const form = document.getElementById('regForm');
          if (form.checkValidity()) {
              document.getElementById('termsPopUp').style.display = 'flex';
          } else {
              form.reportValidity(); 
          }
      }

      function closeTerms() { document.getElementById('termsPopUp').style.display = 'none'; }

      function submitFinalForm() {
          // Set the hidden terms field to '1' so Laravel validation passes
          document.getElementById('hiddenTerms').value = '1';
          document.getElementById('regForm').submit();
      }

      window.onclick = function(event) {
          if (event.target.className === 'modal-overlay') { closeModal(event.target.id); }
          if (event.target.className === 'terms-popup-overlay') { closeTerms(); }
      }
  </script>

</body>
</html>