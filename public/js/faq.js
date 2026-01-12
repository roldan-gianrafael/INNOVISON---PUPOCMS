const FAQ_DATA = [
  {
    id: 'status',
    title: 'Clinic Status & Records',
    icon: '🏥',
    items: [
      { q: 'What does my clinic status mean?', a: 'Your clinic status shows your current relationship with the clinic (e.g. Active, Pending, Suspended). "Active" means you can request appointments and access services. "Pending" usually indicates awaiting verification or approval.' },
      { q: 'How often is my clinic status updated?', a: 'Status updates are applied when clinic staff processes requests or when system events occur. Typical updates happen within 24–48 hours of a staff action.' },
      { q: 'Can I see my past clinic visits?', a: 'Yes — the Appointment History section on your account page lists past consultations and outcomes. This view is read-only for students.' }
    ]
  },
  {
    id: 'documents',
    title: 'Medical Documents',
    icon: '📁',
    items: [
      { q: 'Where can I upload my medical documents?', a: 'Upload medical documents through the Profile / Documents area of your student dashboard. Look for the "Upload" control under Medical Documents.' },
      { q: 'What file types are allowed?', a: 'Common safe types are PDF, JPG, PNG. Keep each file under 5MB. Do not upload sensitive files unless necessary — contact clinic staff for guidance.' }
    ]
  },
  {
    id: 'appointments',
    title: 'Appointments & Requests',
    icon: '📅',
    items: [
      { q: 'How do I request a clinic appointment?', a: 'Use the "Book Appointment" form in the Appointments page. Provide your name, student ID, preferred date/time, and brief reason. Submissions create a request that staff will approve or decline.' },
      { q: 'Can I reschedule or cancel an appointment?', a: 'You may cancel a pending or approved request from your account. Rescheduling requires a new request or a staff action — submit a request-edit from your appointment page and staff will process it.' }
    ]
  },
  {
    id: 'privacy',
    title: 'Account & Privacy',
    icon: '🔒',
    items: [
      { q: 'Who can see my medical information?', a: 'Only authorized clinic staff and you can see your medical details. The system restricts access to protect your privacy. If you are unsure about who sees what, contact clinic staff.' },
      { q: 'My clinic status is incorrect. What should I do?', a: 'If your status appears incorrect, use the Contact or Support option in the dashboard to notify the clinic. Include your student ID and a brief description so staff can investigate.' }
    ]
  },
  {
    id: 'support',
    title: 'Issues & Support',
    icon: '⚙️',
    items: [
      { q: 'How do I reset my password?', a: 'Click "Change Password" in Account Actions or use the Forgot Password link on the login screen. If you cannot reset, contact the helpdesk with your student ID.' }
    ]
  }
];

// FAQ renderer
class FAQ {
  constructor(rootSelector){
    this.root = document.querySelector(rootSelector);
    this.search = document.getElementById('faqSearch');
    this.container = document.getElementById('faqCards');
    this.openId = null; // which question is open (unique id)
    this.init();
  }

  init(){
    this.render();
    this.bind();
  }

  makeId(catId, index){ return `${catId}::${index}` }

  render(){
    this.container.innerHTML = '';
    FAQ_DATA.forEach(category => {
      const card = document.createElement('section');
      card.className = 'card';
      card.setAttribute('data-cat', category.id);

      const head = document.createElement('div'); head.className = 'card-heading';
      head.innerHTML = `<div class="cat-icon" aria-hidden="true">${category.icon}</div><div><div class="cat-title">${category.title}</div><div class="card-sub">Frequently asked questions</div></div>`;
      card.appendChild(head);

      const acc = document.createElement('div'); acc.className = 'accordion';

      category.items.forEach((it, idx)=>{
        const itemId = this.makeId(category.id, idx);
        const item = document.createElement('div'); item.className='accordion-item';

        const btn = document.createElement('button'); btn.className='accordion-button'; btn.type='button';
        btn.setAttribute('aria-expanded','false'); btn.setAttribute('aria-controls', itemId);
        btn.dataset.qid = itemId;
        btn.innerHTML = `<span class="accordion-question"><span class="q-dot" aria-hidden="true"></span><span>${it.q}</span></span><span class="accordion-icon">▸</span>`;

        const panel = document.createElement('div'); panel.className='accordion-panel'; panel.id = itemId; panel.setAttribute('role','region');
        panel.innerHTML = `<div class="accordion-body"><div class="answer">${it.a}</div></div>`;

        item.appendChild(btn); item.appendChild(panel); acc.appendChild(item);
      });

      card.appendChild(acc); this.container.appendChild(card);
    });
  }

  bind(){
    // delegate clicks for accordion buttons
    this.container.addEventListener('click', e=>{
      const btn = e.target.closest('.accordion-button'); if(!btn) return;
      this.toggle(btn.dataset.qid);
    });

    // keyboard support
    this.container.addEventListener('keydown', e=>{
      const btn = e.target.closest('.accordion-button'); if(!btn) return;
      if(e.key === ' ' || e.key === 'Enter') { e.preventDefault(); this.toggle(btn.dataset.qid); }
    });

    // search
    this.search && this.search.addEventListener('input', ()=>{ this.filter(this.search.value.trim().toLowerCase()); });
  }

  toggle(qid){
    const button = this.container.querySelector(`[data-qid="${qid}"]`);
    if(!button) return;
    const panel = document.getElementById(qid);
    const expanded = button.getAttribute('aria-expanded') === 'true';
    // close currently open if different
    if(this.openId && this.openId !== qid){ this.close(this.openId); }
    if(expanded){ this.close(qid); this.openId = null; }
    else{ this.open(qid); this.openId = qid; }
  }

  open(qid){
    const button = this.container.querySelector(`[data-qid="${qid}"]`);
    const panel = document.getElementById(qid);
    if(!panel || !button) return;
    button.setAttribute('aria-expanded','true');
    const body = panel.querySelector('.accordion-body');
    // set max-height to scrollHeight for smooth transition
    panel.style.maxHeight = body.scrollHeight + 'px';
    // rotate icon
    const icon = button.querySelector('.accordion-icon'); if(icon) icon.style.transform = 'rotate(90deg)';
    panel.setAttribute('aria-hidden','false');
  }

  close(qid){
    const button = this.container.querySelector(`[data-qid="${qid}"]`);
    const panel = document.getElementById(qid);
    if(!panel || !button) return;
    button.setAttribute('aria-expanded','false');
    panel.style.maxHeight = '0px';
    const icon = button.querySelector('.accordion-icon'); if(icon) icon.style.transform = 'rotate(0deg)';
    panel.setAttribute('aria-hidden','true');
  }

  filter(term){
    // show categories and items matching term
    const cards = Array.from(this.container.querySelectorAll('.card'));
    cards.forEach(card=>{
      let visible = false;
      const items = Array.from(card.querySelectorAll('.accordion-item'));
      items.forEach((it, idx)=>{
        const qText = it.querySelector('.accordion-question span:last-child').textContent.toLowerCase();
        const match = qText.includes(term);
        it.style.display = match ? '' : 'none';
        if(match) visible = true;
      });
      card.style.display = visible ? '' : 'none';
    });
  }
}

// init on DOM
function readAppointments(){ try{return JSON.parse(localStorage.getItem('pup_appointments_v1')||'[]')}catch(e){return []} }

function renderAppointmentSummary(){
  const el = document.getElementById('appointmentSummary'); if(!el) return;
  const items = readAppointments();
  // compute counts
  const now = new Date(); const today = now.toISOString().slice(0,10);
  let pending=0, upcoming=0, completed=0, cancelled=0;
  items.forEach(it=>{
    const status = (it.status||'').toString().toLowerCase();
    if(status.includes('cancel')) cancelled++;
    else if(status.includes('complete')) completed++;
    else if(status.includes('approve')){
      // approved and date in future => upcoming
      if(it.date && it.date >= today) upcoming++; else completed++;
    }
    else { // treat as pending
      pending++;
    }
  });

  el.innerHTML = `
    <div class="appointment-summary">
      <div style="font-weight:800">My Appointments</div>
      <div class="summary-row"><div class="small">Pending</div><div class="summary-count">${pending}</div></div>
      <div class="summary-row"><div class="small">Upcoming</div><div class="summary-count">${upcoming}</div></div>
      <div class="summary-row"><div class="small">Completed</div><div class="summary-count">${completed}</div></div>
      <div class="summary-row"><div class="small">Cancelled</div><div class="summary-count">${cancelled}</div></div>
      <div style="margin-top:8px"><a href="myaccount.html" class="btn">View Appointments</a></div>
    </div>
  `;
}

window.addEventListener('DOMContentLoaded', ()=>{ new FAQ('#faqApp'); renderAppointmentSummary(); });

export default FAQ;