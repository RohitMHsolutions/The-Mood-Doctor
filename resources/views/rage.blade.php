<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Customer De-escalator AI</title>
<meta name="csrf-token" content="{{ csrf_token() }}">

<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<style>
/* ================= THEME ================= */
:root{
  --bg:#F3F4F6;
  --card:#FFFFFF;
  --text:#111827;
  --muted:#6B7280;
  --border:#E5E7EB;
  --primary:#4F46E5;
  --primary-strong:#4338CA;

  --low:#10B981;
  --mid:#F59E0B;
  --high:#EF4444;
}

body.dark{
  --bg:#0F172A;
  --card:#020617;
  --text:#E5E7EB;
  --muted:#9CA3AF;
  --border:#1F2937;
  --primary:#6366F1;
  --primary-strong:#4F46E5;
}

/* ================= BASE ================= */
*{box-sizing:border-box;font-family:Inter,system-ui}
body{
  margin:0;
  background:var(--bg);
  color:var(--text);
  min-height:100vh;
  padding:24px;
  display:flex;
  justify-content:center;
  transition:.3s;
}
.container{width:100%;max-width:960px}

/* ================= DARK TOGGLE ================= */
.theme-toggle{
  position:fixed;
  top:18px;
  right:18px;
  width:44px;
  height:44px;
  border-radius:50%;
  background:var(--card);
  border:1px solid var(--border);
  display:flex;
  align-items:center;
  justify-content:center;
  cursor:pointer;
  font-size:20px;
  box-shadow:0 6px 16px rgba(0,0,0,.15);
}

/* ================= CARD ================= */
.card{
  background:var(--card);
  border:1px solid var(--border);
  border-radius:16px;
  padding:22px;
  margin-bottom:20px;
  box-shadow:0 12px 24px rgba(0,0,0,.08);
}

/* ================= HEADER ================= */
.header{text-align:center;margin-bottom:20px}
.header p{color:var(--muted)}

/* ================= INPUT ================= */
textarea{
  width:100%;
  min-height:140px;
  padding:14px;
  border-radius:10px;
  border:1px solid var(--border);
  background:transparent;
  color:var(--text);
  font-size:15px;
}
textarea:focus{outline:none;border-color:var(--primary)}

button{
  width:100%;
  margin-top:14px;
  padding:14px;
  border-radius:10px;
  border:none;
  background:var(--primary);
  color:white;
  font-weight:700;
  cursor:pointer;
  transition:.15s;
}
button:disabled{opacity:.7;cursor:not-allowed}
button:hover:not(:disabled){background:var(--primary-strong)}

/* ================= RESULTS ================= */
.results{display:none}

/* ================= RAGE ================= */
.rage-head{
  display:flex;
  justify-content:space-between;
  align-items:center;
  margin-bottom:8px;
}
.rage-value{
  display:flex;
  align-items:center;
  gap:8px;
  font-size:22px;
  font-weight:900;
}
.rage-emoji{font-size:26px}

/* RAGE BAR */
.track{
  height:14px;
  background:#E5E7EB;
  border-radius:999px;
  overflow:hidden;
  margin-bottom:16px;
}
.fill{
  height:100%;
  width:0%;
  border-radius:999px;
  position:relative;
  transform-origin:left;
}

/* Sweep shine */
.fill::after{
  content:"";
  position:absolute;
  top:0;
  left:-30%;
  width:30%;
  height:100%;
  background:linear-gradient(120deg,transparent,rgba(255,255,255,.6),transparent);
  animation:sweep 1.2s ease forwards;
}

/* Animations */
.bounce{animation:bounceFill .9s cubic-bezier(.34,1.56,.64,1)}
.emoji-pop{animation:emojiPop .6s ease}
.emoji-shake{animation:emojiShake .4s linear}

@keyframes sweep{
  from{left:-30%}
  to{left:110%}
}
@keyframes bounceFill{
  0%{transform:scaleX(0)}
  80%{transform:scaleX(1.06)}
  100%{transform:scaleX(1)}
}
@keyframes emojiPop{
  0%{transform:scale(0)}
  70%{transform:scale(1.3)}
  100%{transform:scale(1)}
}
@keyframes emojiShake{
  0%,100%{transform:translateX(0)}
  25%{transform:translateX(-4px)}
  75%{transform:translateX(4px)}
}

/* ================= EDITOR ================= */
.response-label{
  font-weight:600;
  font-size:14px;
  margin-bottom:6px;
}
.editor-shell{
  border:1px solid var(--border);
  border-radius:14px;
  overflow:hidden;
  background:var(--card);
}
.ql-toolbar{
  border:none!important;
  border-bottom:1px solid var(--border)!important;
  background:rgba(0,0,0,.03);
}
body.dark .ql-toolbar{background:#020617}
.ql-container{border:none!important}
.ql-editor{
  padding:10px 12px!important;
  line-height:1.45;
}
.ql-editor p{margin:0 0 6px 0!important;}
.ql-editor p:last-child{margin-bottom:0!important}

/* ================= ACTIONS ================= */
.actions{
  display:flex;
  gap:12px;
  margin-top:14px;
  flex-wrap:wrap;
}
.actions button{flex:1}
.copy{background:#16A34A}
.reset{background:#DC2626}
.save{background:var(--primary)}
.outline{
  background:transparent;
  border:1px solid var(--border);
  color:var(--text);
  width:auto;
}
.outline:hover{background:rgba(0,0,0,.04)}

/* ================= HISTORY ================= */
.history-list{list-style:none;padding:0;display:grid;gap:10px;margin-top:10px}
.history-item{border:1px solid var(--border);border-radius:10px;padding:10px 12px;background:var(--card)}
.history-top{display:flex;justify-content:space-between;align-items:center;margin-bottom:6px}
.history-badge{padding:2px 8px;border-radius:999px;font-size:0.82rem;font-weight:700;color:#fff}
.history-snippet{color:var(--muted);font-size:0.95rem;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;}

/* ================= TOAST ================= */
#toast-container{position:fixed;top:18px;left:50%;transform:translateX(-50%);z-index:9999;display:flex;flex-direction:column;gap:10px;align-items:center}
.toast{background:var(--card);color:var(--text);padding:10px 16px;border-radius:10px;border:1px solid var(--border);box-shadow:0 8px 18px rgba(0,0,0,.12);animation:toastIn .25s ease;min-width:240px;text-align:center}
.toast.fade-out{animation:toastOut .25s ease forwards}
@keyframes toastIn{from{opacity:0;transform:translate(-50%, -10px)}to{opacity:1;transform:translate(-50%,0)}}
@keyframes toastOut{from{opacity:1;transform:translate(-50%,0)}to{opacity:0;transform:translate(-50%,-8px)}}

/* ================= RESPONSIVE ================= */
@media(max-width:640px){
  body{padding:14px}
  .card{padding:16px}
  .actions{flex-direction:column}
}
</style>
</head>

<body>
<div class="theme-toggle" id="themeIcon" onclick="toggleTheme()">🌙</div>
<div id="toast-container"></div>

<div class="container">
  <div class="header">
    <h1>🧠 Mood Doctor</h1>
    <p>Detect rage & generate calming responses</p>
  </div>

  <div class="card">
    <textarea id="emailInput" placeholder="Paste angry customer email here..."></textarea>
    <button id="analyzeBtn" onclick="analyze()">Analyze & Generate</button>
  </div>

  <div class="card results" id="resultsCard">
    <div class="rage-head">
      <span>Rage Level</span>
      <span class="rage-value">
        <span class="rage-emoji" id="rageEmoji">😌</span>
        <span id="rageNum">0/100</span>
      </span>
    </div>
    <div class="track">
      <div class="fill" id="rageBar"></div>
    </div>

    <div class="response-label">Suggested Response (Editable)</div>
    <div class="editor-shell">
      <div id="editor-container"></div>
    </div>

    <div class="actions">
      <button class="copy" onclick="copyText()">Copy</button>
      <!-- <button class="save" onclick="saveResponse()">Save</button> -->
      <button class="reset" onclick="analyze()">Regenerate</button>
      <button class="outline" onclick="window.location.href='{{ url('/history') }}'">History</button>
    </div>
  </div>
</div>

<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const api = {
  analyze: '{{ url('/rage/analyze') }}',
  save: '{{ url('/rage/save') }}',
  history: '{{ url('/rage/history') }}'
};

const quill = new Quill('#editor-container',{theme:'snow',modules:{toolbar:[['bold','italic','underline'],[{list:'ordered'},{list:'bullet'}],[{header:[1,2,3,false]}],['clean']]}});

function toggleTheme(){
  document.body.classList.toggle("dark");
  document.getElementById("themeIcon").textContent =
    document.body.classList.contains("dark") ? "☀️" : "🌙";
}

function showToast(msg){
  const c=document.getElementById('toast-container');
  const t=document.createElement('div');
  t.className='toast';
  t.textContent=msg;
  c.appendChild(t);
  setTimeout(()=>{
    t.classList.add('fade-out');
    t.addEventListener('animationend',()=>t.remove());
  },2000);
}

function setLoading(state,label){
  const btn=document.getElementById('analyzeBtn');
  btn.disabled=state;
  btn.textContent=label;
}

function colorForRage(rage){
  if(rage<=20) return "var(--low)";
  if(rage<50) return "#FACC15";
  if(rage<80) return "var(--mid)";
  return "var(--high)";
}

function emojiForRage(rage){
  if(rage<=20) return "😌";
  if(rage<50) return "😕";
  if(rage<80) return "😠";
  return "🤬";
}

async function analyze(){
  const text=document.getElementById("emailInput").value;
  if(!text.trim()){
    showToast("Please paste an email first.");
    return;
  }

  setLoading(true,"Analyzing...");

  try{
    const res=await fetch(api.analyze,{
      method:'POST',
      headers:{
        'Content-Type':'application/json',
        'X-CSRF-TOKEN':csrfToken,
        'Accept':'application/json'
      },
      body:JSON.stringify({customer_message:text})
    });
    if(!res.ok) throw new Error("Analyze failed");
    const data=await res.json();

    const rage=Math.max(0,Math.min(100, data.rage_level ?? 0));
    const reply=data.rewritten_reply ?? '';
    window.__aiReply = reply;

    renderResults(rage, reply);
    showToast("Analysis complete");
  }catch(err){
    console.error(err);
    showToast("Error analyzing email");
  }finally{
    setLoading(false,"Analyze & Generate");
  }
}

function renderResults(rage, replyHtml){
  const result=document.getElementById("resultsCard");
  const bar=document.getElementById("rageBar");
  const num=document.getElementById("rageNum");
  const emojiEl=document.getElementById("rageEmoji");

  result.style.display="block";
  bar.className="fill";
  bar.style.width="0%";
  num.textContent="0/100";
  emojiEl.className="rage-emoji";
  emojiEl.textContent="😌";

  quill.clipboard.dangerouslyPasteHTML(replyHtml || '');
  cleanQuillSpacing();

  let c=0;
  const target=rage;
  const timer=setInterval(()=>{
    c++;
    bar.style.width=c+"%";
    num.textContent=c+"/100";
    if(c>=target){
      clearInterval(timer);
      const color=colorForRage(target);
      bar.style.background=color;
      emojiEl.textContent=emojiForRage(target);
      if(target>=50 && target<80) emojiEl.classList.add("emoji-pop");
      if(target>=80) emojiEl.classList.add("emoji-shake");
      bar.classList.add("bounce");
    }
  },8);
}

function cleanQuillSpacing(){
  quill.root.querySelectorAll('p').forEach(p=>{
    if(p.innerHTML === '<br>' || p.innerHTML.trim()===''){
      p.remove();
    }
  });
}

async function saveResponse(){
  const text=document.getElementById("emailInput").value;
  const rageText=document.getElementById("rageNum").textContent;
  const rage=parseInt(rageText,10) || 0;
  const html=quill.root.innerHTML;
  const aiReply = window.__aiReply || html;

  if(!text.trim()){
    showToast("Analyze an email first.");
    return;
  }

  try{
    const res=await fetch(api.save,{
      method:'POST',
      headers:{
        'Content-Type':'application/json',
        'X-CSRF-TOKEN':csrfToken,
        'Accept':'application/json'
      },
      body:JSON.stringify({
        customer_message:text,
        rage_level:rage,
        ai_reply: aiReply,
        user_reply: html,
        rewritten_reply: aiReply,
        support_draft:null
      })
    });
    if(!res.ok) throw new Error("Save failed");
    await res.json();
    showToast("Draft saved");
    loadHistory();
  }catch(err){
    console.error(err);
    showToast("Error saving draft");
  }
}

async function loadHistory(){
  // intentionally left empty; history is now on a separate page
}

function copyText(){
  const plain=quill.getText();
  navigator.clipboard.writeText(plain);
  showToast("Copied to clipboard");
}

function escapeHtml(str){
  return str.replace(/[&<>"']/g, m=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[m]));
}

// no auto history load on main page
</script>
</body>
</html>

