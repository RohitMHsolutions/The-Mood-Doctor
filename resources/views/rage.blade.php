<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer De-escalator AI</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        :root {
            --primary: #4F46E5;
            --primary-hover: #4338ca;
            --secondary: #10B981;
            --bg-color: #F3F4F6;
            --card-bg: #ffffff;
            --text-main: #111827;
            --text-muted: #6B7280;
            --border-color: #E5E7EB;
            --radius: 12px;
            --low-rage: #10B981;
            --med-rage: #F59E0B;
            --high-rage: #EF4444;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        body { background-color: var(--bg-color); color: var(--text-main); display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 40px 20px; }
        .container { width: 100%; max-width: 1100px; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 2rem; }
        .header h1 { font-size: 1.8rem; font-weight: 700; margin-bottom: 0.5rem; }
        .header p { color: var(--text-muted); }
        .card { background: var(--card-bg); border-radius: var(--radius); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); padding: 2rem; margin-bottom: 1.5rem; border: 1px solid var(--border-color); }
        .input-group label { display: block; font-weight: 600; margin-bottom: 0.5rem; }
        textarea.raw-input { width: 100%; min-height: 140px; padding: 1rem; border: 1px solid var(--border-color); border-radius: 8px; font-size: 1rem; resize: vertical; background: #FAFAFA; }
        textarea.raw-input:focus { outline: none; border-color: var(--primary); background: #FFF; }
        #editor-container { height: 260px; background: #fff; font-size: 16px; }
        .ql-toolbar.ql-snow { border-top-left-radius: 8px; border-top-right-radius: 8px; border-color: var(--border-color); background: #F9FAFB; }
        .ql-container.ql-snow { border-bottom-left-radius: 8px; border-bottom-right-radius: 8px; border-color: var(--border-color); }
        .btn { border: none; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 1rem; transition: 0.2s; display: inline-flex; justify-content: center; align-items: center; gap: 8px; }
        .btn-primary { background-color: var(--primary); color: white; width: 100%; }
        .btn-primary:hover { background-color: var(--primary-hover); }
        .btn-primary:disabled { opacity: 0.7; cursor: not-allowed; }
        .btn-success { background-color: var(--secondary); color: white; }
        .btn-success:hover { background-color: #059669; }
        .btn-outline { background-color: transparent; border: 1px solid var(--border-color); color: var(--text-muted); }
        .btn-outline:hover { background-color: #F9FAFB; border-color: #9CA3AF; color: var(--text-main); }
        .action-bar { display: flex; gap: 10px; margin-top: 1rem; justify-content: flex-end; flex-wrap: wrap; }
        .results-container { display: none; animation: slideUp 0.5s ease-out; }
        .rage-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
        .rage-title { font-weight: 600; color: var(--text-muted); text-transform: uppercase; font-size: 0.85rem; }
        .rage-value { font-size: 1.5rem; font-weight: 800; }
        .progress-track { width: 100%; height: 12px; background: #E5E7EB; border-radius: 100px; overflow: hidden; margin-bottom: 1.5rem; }
        .progress-fill { height: 100%; width: 0%; border-radius: 100px; transition: width 1.2s ease, background-color 0.4s ease; }
        #toast-container { position: fixed; top: 20px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 10px; }
        .toast-message { background: #333; color: #fff; padding: 12px 24px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); font-size: 0.95rem; display: flex; align-items: center; gap: 10px; animation: toastIn 0.3s ease-out forwards; min-width: 280px; border-left: 4px solid var(--secondary); }
        .toast-message.fade-out { animation: toastOut 0.3s ease-in forwards; }
        .spinner { width: 18px; height: 18px; border: 2px solid #FFF; border-bottom-color: transparent; border-radius: 50%; display: none; animation: rotation 1s linear infinite; }
        .history { margin-top: 1rem; }
        .history h3 { margin-bottom: 0.75rem; }
        .history-list { list-style: none; display: grid; gap: 0.75rem; padding: 0; }
        .history-item { border: 1px solid var(--border-color); border-radius: 8px; padding: 0.75rem 1rem; background: #fff; }
        .history-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem; }
        .history-badge { padding: 2px 8px; border-radius: 999px; font-size: 0.8rem; font-weight: 700; color: #fff; }
        .history-snippet { color: var(--text-muted); font-size: 0.95rem; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes toastIn { from { opacity: 0; transform: translateX(50px); } to { opacity: 1; transform: translateX(0); } }
        @keyframes toastOut { from { opacity: 1; transform: translateX(0); } to { opacity: 0; transform: translateX(50px); } }
        @keyframes rotation { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div id="toast-container"></div>

    <div class="container">
        <header class="header">
            <h1>Customer De-escalator AI</h1>
            <p>Paste the angry email, get rage level and an empathetic reply.</p>
        </header>

        <div class="card">
            <div class="input-group">
                <label for="emailInput">Customer Email</label>
                <textarea id="emailInput" class="raw-input" placeholder="Paste the email here..."></textarea>
            </div>
            <button id="analyzeBtn" class="btn btn-primary" onclick="analyzeEmail()">
                <span class="spinner" id="btnSpinner"></span>
                <span id="btnText">Analyze Tone & Generate Draft</span>
            </button>
        </div>

        <div class="card results-container" id="resultsArea">
            <div class="rage-header">
                <span class="rage-title">Detected Rage Level</span>
                <span class="rage-value" id="rageValue">0/100</span>
            </div>
            <div class="progress-track">
                <div class="progress-fill" id="rageBar"></div>
            </div>

            <hr style="border: 0; border-top: 1px solid #E5E7EB; margin: 1.5rem 0;">

            <div class="input-group">
                <label style="margin-bottom: 10px;">Suggested Response (Editable)</label>
                <div id="editor-container"></div>
            </div>

            <div class="action-bar">
                <button class="btn btn-outline" onclick="copyToClipboard()">Copy HTML</button>
                <button class="btn btn-success" onclick="saveResponse()">Save Response</button>
            </div>
        </div>

        <div class="card history" id="historyCard" style="display:none;">
            <div class="history-head">
                <h3>Recent History</h3>
                <button class="btn btn-outline" onclick="loadHistory()">Refresh</button>
            </div>
            <ul class="history-list" id="historyList"></ul>
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

        const quill = new Quill('#editor-container', {
            theme: 'snow',
            placeholder: 'AI response will appear here...',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'header': [1, 2, 3, false] }],
                    ['clean']
                ]
            }
        });

        async function analyzeEmail() {
            const btn = document.getElementById('analyzeBtn');
            const btnText = document.getElementById('btnText');
            const spinner = document.getElementById('btnSpinner');
            const results = document.getElementById('resultsArea');
            const rageBar = document.getElementById('rageBar');
            const rageValue = document.getElementById('rageValue');
            const emailInput = document.getElementById('emailInput');

            if (!emailInput.value.trim()) {
                showToast('Please paste an email to analyze.');
                return;
            }

            setLoading(btn, btnText, spinner, true, 'Analyzing...');
            results.style.display = 'none';

            try {
                const res = await fetch(api.analyze, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ customer_message: emailInput.value })
                });

                if (!res.ok) throw new Error('Analyze request failed');
                const data = await res.json();

                const rageScore = data.rage_level ?? 0;
                const replyHtml = data.rewritten_reply ?? '';

                results.style.display = 'block';
                rageValue.textContent = rageScore + '/100';
                quill.clipboard.dangerouslyPasteHTML(replyHtml);

                setTimeout(() => { rageBar.style.width = rageScore + '%'; }, 50);
                if (rageScore < 40) rageBar.style.backgroundColor = "var(--low-rage)";
                else if (rageScore < 75) rageBar.style.backgroundColor = "var(--med-rage)";
                else rageBar.style.backgroundColor = "var(--high-rage)";

                showToast('Analysis complete');
                results.scrollIntoView({ behavior: 'smooth' });
            } catch (err) {
                console.error(err);
                showToast('Error analyzing email.');
            } finally {
                setLoading(btn, btnText, spinner, false, 'Analyze Tone & Generate Draft');
            }
        }

        async function saveResponse() {
            const emailInput = document.getElementById('emailInput');
            const rageValue = document.getElementById('rageValue').textContent;
            const rageLevel = parseInt(rageValue, 10) || 0;
            const html = quill.root.innerHTML;

            if (!emailInput.value.trim()) {
                showToast('Please analyze an email first.');
                return;
            }

            try {
                const res = await fetch(api.save, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        customer_message: emailInput.value,
                        rage_level: rageLevel,
                        rewritten_reply: html,
                        support_draft: null
                    })
                });

                if (!res.ok) throw new Error('Save failed');
                await res.json();
                showToast('Draft saved successfully!');
                loadHistory();
            } catch (err) {
                console.error(err);
                showToast('Error saving draft.');
            }
        }

        async function loadHistory() {
            const historyList = document.getElementById('historyList');
            const historyCard = document.getElementById('historyCard');
            try {
                const res = await fetch(api.history, { headers: { 'Accept': 'application/json' } });
                if (!res.ok) throw new Error('History failed');
                const items = await res.json();

                historyList.innerHTML = '';
                if (items.length === 0) {
                    historyList.innerHTML = '<li class="history-item">No history yet.</li>';
                } else {
                    items.forEach(item => {
                        const li = document.createElement('li');
                        li.className = 'history-item';
                        const badgeColor = item.rage_level < 40 ? 'var(--low-rage)' : (item.rage_level < 75 ? 'var(--med-rage)' : 'var(--high-rage)');
                        li.innerHTML = `
                            <div class="history-top">
                                <span class="history-badge" style="background:${badgeColor}">${item.rage_level}/100</span>
                                <span class="history-date">${new Date(item.created_at).toLocaleString()}</span>
                            </div>
                            <div class="history-snippet">${escapeHtml(item.customer_message ?? '').slice(0, 180)}${(item.customer_message ?? '').length > 180 ? '...' : ''}</div>
                        `;
                        historyList.appendChild(li);
                    });
                }
                historyCard.style.display = 'block';
            } catch (err) {
                console.error(err);
                showToast('Error loading history.');
            }
        }

        function copyToClipboard() {
            const html = quill.root.innerHTML;
            const tempInput = document.createElement("input");
            tempInput.value = html;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand("copy");
            document.body.removeChild(tempInput);
            showToast("HTML copied to clipboard!");
        }

        function setLoading(btn, btnText, spinner, state, label) {
            btn.disabled = state;
            btnText.textContent = label;
            spinner.style.display = state ? 'inline-block' : 'none';
        }

        function showToast(message) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = 'toast-message';
            toast.innerText = message;
            container.appendChild(toast);
            setTimeout(() => {
                toast.classList.add('fade-out');
                toast.addEventListener('animationend', () => toast.remove());
            }, 3000);
        }

        function escapeHtml(str) {
            return str.replace(/[&<>"']/g, function (m) {
                return ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#39;'
                })[m];
            });
        }

        // Initial history load
        loadHistory();
    </script>
</body>
</html>

