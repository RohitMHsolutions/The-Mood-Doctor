<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>THE MOOD DOCTOR</title>
<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;500;700&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
<link href="https://cdn.quilljs.com/1.3.6/quill.bubble.css" rel="stylesheet">

<style>
/* ================= VARIABLES ================= */
:root {
  --bg-dark: #030712;       /* Deepest Black/Blue */
  --bg-grid: #111827;       /* Grid Lines */
  
  --glass-bg: rgba(17, 24, 39, 0.7);
  --glass-border: rgba(255, 255, 255, 0.08);
  --glass-highlight: rgba(255, 255, 255, 0.03);

  --primary: #6366f1;       /* Indigo Neon */
  --primary-glow: rgba(99, 102, 241, 0.4);
  --accent: #06b6d4;        /* Cyan Neon */
  
  --text-main: #f9fafb;
  --text-muted: #9ca3af;

  --rage-low: #10b981;
  --rage-mid: #f59e0b;
  --rage-high: #ef4444;
}

/* ================= RESET & BASE ================= */
* { box-sizing: border-box; }

body {
  margin: 0;
  min-height: 100vh;
  font-family: 'Inter', sans-serif;
  background-color: var(--bg-dark);
  background-image: 
      linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
      linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
  background-size: 40px 40px;
  color: var(--text-main);
  display: flex;
  justify-content: center;
  padding: 40px 20px;
}

.container { width: 100%; max-width: 950px; position: relative; }

/* ================= TYPOGRAPHY ================= */
h1 {
  font-family: 'Space Grotesk', sans-serif;
  font-size: 2.5rem;
  margin: 0 0 10px 0;
  background: linear-gradient(to right, #fff, #a5b4fc);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  text-align: center;
  letter-spacing: -1px;
}

.subtitle {
  text-align: center;
  color: var(--text-muted);
  font-size: 1rem;
  margin-bottom: 40px;
  font-weight: 300;
}

/* ================= GLASS CARDS ================= */
.glass-panel {
  background: var(--glass-bg);
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  border: 1px solid var(--glass-border);
  border-radius: 24px;
  padding: 30px;
  margin-bottom: 30px;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
  position: relative;
  overflow: hidden;
}

/* Subtle shine effect on cards */
.glass-panel::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0; height: 1px;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
}

/* ================= INPUT AREA ================= */
.input-label {
  font-family: 'Space Grotesk', sans-serif;
  color: var(--accent);
  font-size: 0.85rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 1px;
  margin-bottom: 15px;
  display: block;
}

textarea {
  width: 100%;
  min-height: 150px;
  background: rgba(0, 0, 0, 0.3);
  border: 1px solid var(--glass-border);
  border-radius: 16px;
  padding: 20px;
  color: var(--text-main);
  font-size: 1rem;
  line-height: 1.6;
  resize: vertical;
  transition: all 0.2s ease;
  font-family: 'Inter', sans-serif;
}

textarea:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
  background: rgba(0, 0, 0, 0.5);
}

/* ================= AI BUTTONS ================= */
.btn-main {
  width: 100%;
  margin-top: 20px;
  padding: 16px;
  border: none;
  border-radius: 16px;
  background: linear-gradient(135deg, var(--primary) 0%, #4338ca 100%);
  color: white;
  font-family: 'Space Grotesk', sans-serif;
  font-weight: 700;
  font-size: 1.1rem;
  cursor: pointer;
  position: relative;
  overflow: hidden;
  transition: all 0.3s;
  box-shadow: 0 0 25px var(--primary-glow);
  text-transform: uppercase;
  letter-spacing: 1px;
}

.btn-main:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 40px var(--primary-glow);
}

.btn-main:disabled { opacity: 0.6; cursor: wait; }

/* Shine animation inside button */
.btn-shine {
  position: absolute;
  top: 0; left: -100%;
  width: 50%; height: 100%;
  background: linear-gradient(120deg, transparent, rgba(255,255,255,0.4), transparent);
  animation: shine 3s infinite;
}
@keyframes shine { 0% { left: -100%; } 20% { left: 200%; } 100% { left: 200%; } }

/* ================= RESULTS AREA ================= */
.results-container { display: none; animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1); }
@keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }

/* Emotion Grid */
#emotionGrid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
  gap: 15px;
  margin-bottom: 30px;
}

.emotion-card {
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid var(--glass-border);
  border-radius: 12px;
  padding: 12px 15px;
  transition: transform 0.2s;
}

.emotion-card:hover { background: rgba(255, 255, 255, 0.06); transform: translateY(-2px); }

.emo-header {
  display: flex;
  justify-content: space-between;
  margin-bottom: 8px;
  font-size: 0.85rem;
  font-weight: 600;
  color: var(--text-muted);
  text-transform: capitalize;
}

.emo-bar-bg {
  height: 6px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 10px;
  overflow: hidden;
}

.emo-bar-fill {
  height: 100%;
  border-radius: 10px;
  transition: width 1s ease;
  box-shadow: 0 0 10px currentColor; /* Glow matches color */
}

/* ================= EDITOR & ACTIONS ================= */
.editor-wrapper {
  background: rgba(0, 0, 0, 0.2);
  border: 1px solid var(--glass-border);
  border-radius: 16px;
  margin-top: 10px;
}

/* Quill Bubble Theme Overrides */
.ql-container { font-family: 'Inter', sans-serif !important; font-size: 1.05rem !important; }
.ql-editor { padding: 25px !important; color: #e4e4e7; line-height: 1.7; }
.ql-editor.ql-blank::before { color: rgba(255,255,255,0.2) !important; font-style: normal !important; }
.ql-bubble .ql-tooltip { background-color: #27272a !important; border-radius: 8px; }
.ql-bubble .ql-tooltip-arrow { border-bottom-color: #27272a !important; }

/* Action Buttons */
.actions-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 15px;
  margin-top: 25px;
}

.btn-secondary {
  padding: 12px;
  border-radius: 12px;
  border: 1px solid var(--glass-border);
  background: rgba(255, 255, 255, 0.03);
  color: var(--text-main);
  font-family: 'Space Grotesk', sans-serif;
  font-weight: 600;
  cursor: pointer;
  transition: 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.btn-secondary:hover { background: rgba(255, 255, 255, 0.08); border-color: rgba(255, 255, 255, 0.2); }

.btn-save { border-color: rgba(16, 185, 129, 0.3); color: #34d399; background: rgba(16, 185, 129, 0.1); }
.btn-save:hover { background: rgba(16, 185, 129, 0.2); box-shadow: 0 0 15px rgba(16, 185, 129, 0.15); }

.btn-link { 
  background: transparent; 
  border: none; 
  color: var(--text-muted); 
  width: 100%; 
  margin-top: 15px; 
  cursor: pointer; 
}
.btn-link:hover { color: var(--text-main); text-decoration: underline; }

/* ================= TOAST ================= */
#toast-container {
  position: fixed; top: 20px; right: 20px; z-index: 9999;
  display: flex; flex-direction: column; gap: 10px; align-items: flex-end;
}
.toast {
  background: rgba(20, 20, 25, 0.95);
  border: 1px solid var(--glass-border);
  color: var(--text-main);
  padding: 12px 20px;
  border-radius: 10px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.5);
  animation: slideInRight 0.3s cubic-bezier(0.16, 1, 0.3, 1);
  display: flex; align-items: center; gap: 10px; min-width: 250px;
}
.toast.fade-out { opacity: 0; transform: translateX(20px); transition: 0.3s; }
@keyframes slideInRight { from { opacity: 0; transform: translateX(50px); } to { opacity: 1; transform: translateX(0); } }

</style>
</head>

<body>

<div id="toast-container"></div>

<div class="container">
  
  <h1>The Mood Doctor</h1>
  {{-- <div class="subtitle">Emotional Intelligence Analysis & De-escalation Protocol</div> --}}

  <div class="glass-panel">
    <span class="input-label">Incoming Message</span>
    <textarea id="emailInput" placeholder="Paste the customer email here..."></textarea>
    
    <button id="analyzeBtn" class="btn-main" onclick="analyze()">
      <div class="btn-shine"></div>
      <span id="btnText">Analyze & Generate Response</span>
    </button>
  </div>

  <div class="glass-panel results-container" id="resultsCard">
    
    <span class="input-label">Detected Emotional Spectrum</span>
    <div id="emotionGrid">
      </div>

    <span class="input-label" style="color: var(--primary);">AI Suggested Response</span>
    <div class="editor-wrapper">
      <div id="editor-container"></div>
    </div>

    <div class="actions-row">
      <button class="btn-secondary" onclick="copyText()">
        <span>📋</span> Copy Text
      </button>
      <button class="btn-secondary btn-save" onclick="saveResponse()">
        <span>💾</span> Save to Database
      </button>
    </div>
    
    <button class="btn-link" onclick="window.location.reload()">Start New Analysis</button>
    <button class="btn-link" onclick="window.location.href='{{ url('/history') }}'">View History Log</button>

  </div>

</div>

<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
// --- Config & Init ---
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const api = {
  analyze: '{{ url('/rage/analyze') }}',
  save: '{{ url('/rage/save') }}'
};

// Use "Bubble" theme for cleaner AI look
const quill = new Quill('#editor-container', {
  theme: 'bubble',
  placeholder: 'Generating response...',
  modules: { toolbar: [['bold', 'italic'], ['link', 'clean']] }
});

// --- UI Helpers ---
function setLoading(isLoading, text) {
  const btn = document.getElementById('analyzeBtn');
  const label = document.getElementById('btnText');
  btn.disabled = isLoading;
  label.innerText = text;
}

function showToast(msg, type='info') {
  const container = document.getElementById('toast-container');
  const t = document.createElement('div');
  t.className = 'toast';
  
  let icon = 'ℹ️';
  if(type === 'success') icon = '✅';
  if(type === 'error') icon = '⚠️';
  
  t.innerHTML = `<span>${icon}</span> <span>${msg}</span>`;
  container.appendChild(t);
  
  setTimeout(() => {
    t.classList.add('fade-out');
    setTimeout(() => t.remove(), 300);
  }, 3000);
}

// --- Typing Effect (Visual Polish) ---
function typeWriter(htmlContent) {
  quill.setText(''); // clear
  // We use a trick: set contents silently, then type it out purely visually? 
  // For HTML preservation, simple typing is hard. 
  // Let's stick to a clean fade-in or character append for plain text.
  // For this demo, we will just paste the HTML but add a slight delay to feel like processing.
  
  // Reset opacity
  const editor = document.querySelector('.ql-editor');
  editor.style.opacity = 0;
  editor.style.transition = 'opacity 1s ease';
  
  setTimeout(() => {
    quill.clipboard.dangerouslyPasteHTML(htmlContent);
    editor.style.opacity = 1;
  }, 300);
}

// --- Emotion Render ---
function renderEmotions(emotions) {
  const grid = document.getElementById('emotionGrid');
  grid.innerHTML = '';
  
  if(!emotions) return;

  Object.entries(emotions).forEach(([name, val]) => {
    const score = Math.max(0, Math.min(100, parseInt(val, 10) || 0));
    
    // Dynamic Color based on Emotion
    let color = 'var(--accent)'; // Default Blue
    if(['rage', 'anger', 'frustration'].includes(name.toLowerCase())) color = 'var(--rage-high)';
    if(['joy', 'happiness'].includes(name.toLowerCase())) color = 'var(--rage-low)';
    if(['sadness', 'disappointment'].includes(name.toLowerCase())) color = '#a855f7'; // Purple

    const div = document.createElement('div');
    div.className = 'emotion-card';
    div.innerHTML = `
      <div class="emo-header">
        <span>${name}</span>
        <span style="color:${color}">${score}%</span>
      </div>
      <div class="emo-bar-bg">
        <div class="emo-bar-fill" style="width: 0%; background-color: ${color}; box-shadow: 0 0 8px ${color};"></div>
      </div>
    `;
    grid.appendChild(div);

    // Animate Bar after append
    setTimeout(() => {
      div.querySelector('.emo-bar-fill').style.width = score + '%';
    }, 100);
  });
}

// --- Main Logic ---
async function analyze() {
  const text = document.getElementById("emailInput").value;
  if(!text.trim()) { showToast("Please enter an email text", "error"); return; }

  setLoading(true, "Processing Neural Engine...");
  document.getElementById('resultsCard').style.display = 'none';

  try {
    const res = await fetch(api.analyze, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json'
      },
      body: JSON.stringify({ customer_message: text })
    });

    if(!res.ok) throw new Error("API Failed");
    const data = await res.json();

    // Store Globals for Saving
    window.__lastData = {
      rage: data.rage_level,
      reply: data.rewritten_reply,
      emotions: data.emotions,
      language: data.language
    };

    // Render
    document.getElementById('resultsCard').style.display = 'block';
    renderEmotions(data.emotions || {});
    typeWriter(data.rewritten_reply || '');
    
    showToast("Analysis Complete", "success");

  } catch(err) {
    console.error(err);
    showToast("Connection to Neural Engine Failed", "error");
    // DEMO DATA FALLBACK (If backend is offline)
    // remove this block in production
    document.getElementById('resultsCard').style.display = 'block';
    renderEmotions({'Rage': 85, 'Frustration': 92, 'Sadness': 12});
    typeWriter('<p>I completely understand your frustration...</p>');
  } finally {
    setLoading(false, "Analyze & Generate Response");
  }
}

async function saveResponse() {
  if(!window.__lastData) { showToast("No analysis to save", "error"); return; }
  
  const userEditedHtml = quill.root.innerHTML;
  const originalText = document.getElementById("emailInput").value;

  try {
    const res = await fetch(api.save, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
      body: JSON.stringify({
        customer_message: originalText,
        rage_level: window.__lastData.rage,
        emotions: window.__lastData.emotions,
        language: window.__lastData.language,
        ai_reply: window.__lastData.reply,
        user_reply: userEditedHtml,
        rewritten_reply: userEditedHtml
      })
    });
    
    if(!res.ok) throw new Error("Save Failed");
    showToast("Response Saved to Database", "success");
  } catch(e) {
    showToast("Error saving data", "error");
  }
}

function copyText() {
  const text = quill.getText();
  navigator.clipboard.writeText(text);
  showToast("Copied to Clipboard", "success");
}
</script>
</body>
</html>