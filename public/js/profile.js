/* profile.js — handles avatar upload/preview, initials fallback, and status badge */
(function(){
  const PROFILE_KEY = 'pup_profile_v1';

  function qs(sel){ return document.querySelector(sel); }

  function loadProfile(){
    try{ return JSON.parse(localStorage.getItem(PROFILE_KEY) || 'null') || {}; }catch(e){ return {}; }
  }
  function saveProfile(p){ localStorage.setItem(PROFILE_KEY, JSON.stringify(p||{})); }

  function initialsFrom(name){
    if(!name) return 'U';
    const parts = name.trim().split(/\s+/);
    if(parts.length===1) return parts[0].slice(0,2).toUpperCase();
    return (parts[0].charAt(0)+parts[parts.length-1].charAt(0)).toUpperCase();
  }

  function setAvatar(profile){
    const img = qs('#avatarImg');
    const initials = qs('#avatarInitials');
    if(profile && profile.photo){
      img.src = profile.photo;
      img.style.display = 'block';
      initials.style.display = 'none';
      img.alt = (profile.fullName || 'Student') + "'s profile photo";
    } else {
      img.removeAttribute('src'); img.style.display = 'none';
      initials.textContent = initialsFrom(profile && profile.fullName ? profile.fullName : 'U');
      initials.style.display = 'flex';
    }
  }

  function updateStatus(profile){
    const badge = qs('#accountStatus');
    const status = (profile && profile.status) ? profile.status : 'Active';
    badge.textContent = status;
    badge.classList.remove('status-active','status-inactive','status-other');
    if(/active/i.test(status)) badge.classList.add('status-active');
    else if(/inactive|suspended/i.test(status)) badge.classList.add('status-inactive');
    else badge.classList.add('status-other');
  }

  function handleFileInput(file){
    if(!file) return;
    if(!/^image\/(png|jpeg|jpg)$/.test(file.type)){
      alert('Please upload a PNG or JPG image.');
      return;
    }
    const reader = new FileReader();
    reader.onload = function(e){
      const data = e.target.result;
      const profile = loadProfile();
      profile.photo = data;
      saveProfile(profile);
      setAvatar(profile);
    };
    reader.readAsDataURL(file);
  }

  document.addEventListener('DOMContentLoaded', ()=>{
    const profile = loadProfile();
    // Ensure minimal defaults
    if(!profile.fullName) profile.fullName = 'Juan Dela Cruz';
    if(!profile.memberSince) profile.memberSince = profile.memberSince || new Date().getFullYear();
    // sync memberSince display if present
    const ms = qs('#memberSince'); if(ms) ms.textContent = profile.memberSince;

    setAvatar(profile);
    updateStatus(profile);

    const uploadBtn = qs('#avatarUploadBtn');
    const fileInput = qs('#avatarFileInput');

    if(uploadBtn && fileInput){
      uploadBtn.addEventListener('click', ()=> fileInput.click());
      // keyboard accessible: Enter/Space on button works automatically
      fileInput.addEventListener('change', (ev)=>{ const f = ev.target.files && ev.target.files[0]; handleFileInput(f); });
    }

    // allow removing photo via long-press (Alt+Click) for keyboard users: Alt+Click on change button
    if(uploadBtn){
      uploadBtn.addEventListener('click', (ev)=>{
        if(ev.altKey){ if(!confirm('Remove current profile photo?')) return; const p = loadProfile(); delete p.photo; saveProfile(p); setAvatar(p); }
      });
    }

    // expose small API for other scripts if needed
    window.profileUI = {
      refresh(){ const p = loadProfile(); setAvatar(p); updateStatus(p); }
    };
  });

})();
