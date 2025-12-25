<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Rage History</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
:root{
  --bg:#F3F4F6;
  --card:#FFFFFF;
  --text:#111827;
  --muted:#6B7280;
  --border:#E5E7EB;
  --primary:#4F46E5;
  --low:#10B981;
  --mid:#F59E0B;
  --high:#EF4444;
}
body{margin:0;background:var(--bg);color:var(--text);font-family:Inter,system-ui;min-height:100vh;padding:24px;display:flex;justify-content:center;}
.container{width:100%;max-width:960px;}
.card{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:18px;margin-bottom:16px;box-shadow:0 10px 20px rgba(0,0,0,.08);}
.top{display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;}
.badge{padding:2px 8px;border-radius:999px;font-weight:700;color:#fff;font-size:.9rem;}
.muted{color:var(--muted);font-size:.95rem;}
.entry{border-top:1px solid var(--border);padding-top:12px;margin-top:10px;}
.entry h3{margin:0 0 6px 0;font-size:1rem;}
.entry pre{margin:4px 0;white-space:pre-wrap;word-break:break-word;background:#F9FAFB;padding:8px;border-radius:8px;border:1px solid var(--border);}
.cta{margin:8px 0 16px 0;display:flex;gap:10px;flex-wrap:wrap;}
.btn{padding:10px 14px;border-radius:10px;border:none;cursor:pointer;font-weight:700;}
.primary{background:var(--primary);color:#fff;}
.outline{background:transparent;border:1px solid var(--border);color:var(--text);}
.grid{display:grid;gap:12px;}
</style>
</head>
<body>
<div class="container">
  <div class="cta">
    <button class="btn outline" onclick="window.location.href='{{ url('/') }}'">← Back</button>
    <button class="btn primary" onclick="loadHistory()">Refresh</button>
  </div>
  <div id="list" class="grid"></div>
</div>

<script>
const list=document.getElementById('list');
const api='{{ url('/rage/history') }}';

function color(r){
  if(r<=20) return "var(--low)";
  if(r<50) return "#FACC15";
  if(r<80) return "var(--mid)";
  return "var(--high)";
}

function escapeHtml(str){
  return str.replace(/[&<>"']/g, m=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[m]));
}

async function loadHistory(){
  list.innerHTML = '<div class="card">Loading...</div>';
  try{
    const res=await fetch(api,{headers:{'Accept':'application/json'}});
    if(!res.ok) throw new Error('fail');
    const items=await res.json();
    if(!items.length){
      list.innerHTML='<div class="card">No history yet.</div>';
      return;
    }
    list.innerHTML='';
    items.forEach(item=>{
      const card=document.createElement('div');
      card.className='card';
      const rageColor=color(item.rage_level);
      card.innerHTML=`
        <div class="top">
          <span class="badge" style="background:${rageColor}">${item.rage_level}/100</span>
          <span class="muted">${new Date(item.created_at).toLocaleString()}</span>
        </div>
        <div class="entry">
          <h3>User Email</h3>
          <pre>${escapeHtml(item.customer_message || '')}</pre>
        </div>
        <div class="entry">
          <h3>AI Reply</h3>
          <pre>${escapeHtml(item.ai_reply || item.rewritten_reply || '')}</pre>
        </div>
        <div class="entry">
          <h3>User Final</h3>
          <pre>${escapeHtml(item.user_reply || '')}</pre>
        </div>
      `;
      list.appendChild(card);
    });
  }catch(err){
    console.error(err);
    list.innerHTML='<div class="card">Error loading history.</div>';
  }
}

loadHistory();
</script>
</body>
</html>

