(function(){
  // 1. Mobile Navigation Toggle Logic
  const navBtn = document.querySelector('.nav-toggle');
  const navMenu = document.querySelector('.nav-list');
  if(navBtn && navMenu){
    navBtn.addEventListener('click', function(){
      const expanded = this.getAttribute('aria-expanded') === 'true';
      this.setAttribute('aria-expanded', String(!expanded));
      navMenu.classList.toggle('show');
    });
  }

  // 2. Booking Function Logic
  const KEY = 'pup_appointments_v1';
  const EDIT_KEY = 'pup_edit_requests_v1';
  const NOTIF = 'pup_notifications_v1';
  const form = document.getElementById('bookingForm');
  const listEl = document.getElementById('appointmentsList');
  const noMsg = document.getElementById('noMsg');
  const editingInput = document.getElementById('editingId');
  const saveBtn = document.getElementById('saveBtn');

  // Helper functions
  function uid(){ return Date.now().toString(36) + Math.random().toString(36).slice(2,8); }
  function getAppointments(){ try{return JSON.parse(localStorage.getItem(KEY) || '[]')}catch(e){return []} }
  function saveAppointments(a){ localStorage.setItem(KEY, JSON.stringify(a)); }
  function escape(s){ return String(s||'').replace(/[&<>"']/g,m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m])); }
  
  // Format YYYY-MM-DD to MM/DD/YYYY
  function formatDate(d){ 
    if(!d) return ''; 
    const p=d.split('-'); 
    return p[1]+'/'+p[2]+'/'+p[0]; 
  }

  // Render the list of appointments in the sidebar
  function render(){
    const items = getAppointments();
    
    // Filter only upcoming or recent items if you preferred, 
    // but here we show all sorted by date
    items.sort((a,b)=> new Date(a.date+' '+a.time) - new Date(b.date+' '+b.time));

    listEl.innerHTML = '';
    if(items.length === 0){ 
      noMsg.style.display='block'; 
    } else { 
      noMsg.style.display='none'; 
    }

    items.forEach(item=>{
      const card = document.createElement('div');
      card.className = 'apt-card';
      card.innerHTML = `
        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px">
          <div>
            <strong style="color:var(--accent)">${escape(item.service)}</strong>
            <div class="small" style="margin-top:2px">${escape(item.name)}</div>
          </div>
          <div style="text-align:right">
            <div class="apt-meta" style="font-weight:700;color:#222">
              <span>${formatDate(item.date)}</span>
            </div>
            <div class="small">${item.time}</div>
          </div>
        </div>
        <div style="color:#555;font-size:13px;margin-top:6px;line-height:1.4">
          ${escape(item.notes || 'No notes provided.')}
        </div>
        <div class="apt-actions">
          <button data-action="view" data-id="${item.id}" class="btn ghost" style="padding:6px 10px;font-size:12px">View</button>
          <button data-action="request-edit" data-id="${item.id}" class="btn ghost" style="padding:6px 10px;font-size:12px">Request Edit</button>
          <button data-action="delete" data-id="${item.id}" class="btn ghost" style="padding:6px 10px;font-size:12px;border-color:#eee;color:#777">Cancel</button>
        </div>
      `;
      listEl.appendChild(card);
    });
  }

  // Handle Form Submit
  form.addEventListener('submit', function(e){
    e.preventDefault();
    
    // determine service value (service field removed from form)
    const serviceVal = (form.service && form.service.value) || (form.querySelector('#service_display') && form.querySelector('#service_display').value) || 'General Consultation';
    const data = {
      name: form.name.value.trim(),
      studentId: form.studentId.value.trim(),
      email: form.email.value.trim(),
      service: serviceVal,
      date: form.date.value,
      time: form.time.value,
      notes: form.notes.value.trim()
    };

    // Basic Validation
    if(!data.name||!data.studentId||!data.email||!data.service||!data.date||!data.time){
      alert('Please fill in all required fields.');
      return;
    }

    // --- NEW: Time Range Validation (8:00 AM - 7:00 PM) ---
    if(data.time < '08:00' || data.time > '19:00'){
      alert('Clinic hours are only from 8:00 AM to 7:00 PM. Please choose a valid time.');
      return;
    }
    // -----------------------------------------------------

    const items = getAppointments();
    const editingId = editingInput.value;

    if(editingId){
      // Update existing
      const idx = items.findIndex(x=>x.id===editingId);
      if(idx!==-1) {
        items[idx] = Object.assign({id:editingId, createdAt: items[idx].createdAt}, data);
        alert('Appointment updated successfully!');
      }
      editingInput.value = '';
      saveBtn.textContent = 'Save Appointment';
    } else {
      // Create new
      items.push(Object.assign({id: uid(), createdAt:new Date().toISOString()}, data));
      alert('Appointment saved successfully!');
    }

    saveAppointments(items);
    form.reset();
    render();
  });

  // Handle Edit / Delete Buttons
  listEl.addEventListener('click', function(e){
    const btn = e.target.closest('button');
    if(!btn) return;
    
    const id = btn.getAttribute('data-id');
    const action = btn.getAttribute('data-action');
    let items = getAppointments();
    const item = items.find(x=>x.id===id);
    
    if(!item) return;

    if(action === 'delete'){
      if(!confirm('Are you sure you want to cancel this appointment?')) return;
      items = items.filter(x=>x.id!==id);
      saveAppointments(items);
      
      // If we were editing this specific item, clear the form
      if(editingInput.value === id) {
        form.reset();
        editingInput.value = '';
        saveBtn.textContent = 'Save Appointment';
      }
      render();
    }
    else if(action === 'request-edit'){
      // Student requests an edit — create a request for admin to review
      const suggestion = prompt('Describe what you want changed (date/time/notes etc.)');
      if(!suggestion) return;
      const reqs = JSON.parse(localStorage.getItem(EDIT_KEY) || '[]');
      reqs.push({ id: uid(), appointmentId: id, studentId: item.studentId, studentName: item.name, suggested: suggestion, createdAt: new Date().toISOString() });
      localStorage.setItem(EDIT_KEY, JSON.stringify(reqs));
      // also add a notification for admin
      const nots = JSON.parse(localStorage.getItem(NOTIF) || '[]');
      nots.unshift('Edit request submitted for appointment on '+(item.date||'unknown date'));
      localStorage.setItem(NOTIF, JSON.stringify(nots));
      alert('Edit request submitted. Clinic staff will review and respond.');
    }
  });

  // Handle Reset Button to clear 'Edit' state
  document.getElementById('resetBtn').addEventListener('click', function(){
    editingInput.value = '';
    saveBtn.textContent = 'Save Appointment';
  });

  // Initial render on page load
  render();
})();