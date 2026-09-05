<?php
require_once "config/database.php";
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>MISA — Intelligent Service Assistant</title>
<link rel="stylesheet" href="assets/css/chat.css">
</head>
<body>
<div class="app">
  <aside class="sidebar" id="sidebar">
    <div class="brand">
      <div class="brand-mark"><img src="assets/logo.png" alt="BG Mesob logo"></div>
      <div><b>BG Mesob</b><small>Intelligent Assistant</small></div>
      <button class="close mobile" id="closeSidebar">×</button>
    </div>

    <button class="new-chat" id="newChat"><span>＋</span> New chat</button>

    <div class="side-block">
      <div class="label">Explore</div>
      <a href="chat.php">💬 <span>Assistant</span></a>
      <a href="services.php">▣ <span>Services</span></a>
    </div>

    <div class="side-block">
      <div class="label">Try asking</div>
      <button class="suggest" data-question="What documents are required for passport renewal?">Passport renewal</button>
      <button class="suggest" data-question="How do I renew my driver's license?">Driver's license renewal</button>
      <button class="suggest" data-question="What are the steps for business registration?">Business registration</button>
      <button class="suggest" data-question="How long does passport renewal take?">Processing time</button>
    </div>

    <div class="side-footer">
      <div class="ready"><i></i> MISA knowledge base ready</div>
      <div class="disclaimer">Prototype information. Verify official requirements before real-world use.</div>
    </div>
  </aside>

  <main class="main">
    <header class="topbar">
      <button class="icon mobile" id="openSidebar">☰</button>
      <div class="title">
        <div class="mini-mark"><img src="assets/logo.png" alt="BG Mesob logo"></div>
        <div><b>BG Mesob</b><small>Intelligent Assistant</small></div>
      </div>
      <button class="icon" id="clearChat" title="Clear conversation">⌫</button>
    </header>

    <section class="messages" id="messages">
      <div class="welcome" id="welcome">
        <div class="welcome-mark"><img src="assets/logo.png" alt="BG Mesob logo"></div>
        <h1>How can I help you?</h1>
        <p>Ask about MESOB services, documents, procedures, fees, or processing times.</p>
        <div class="cards">
          <button class="card" data-question="What documents are required for passport renewal?">
            <span>📄</span><b>Required documents</b><small>Find documents for a service</small>
          </button>
          <button class="card" data-question="How do I renew my driver's license?">
            <span>🧭</span><b>Service procedure</b><small>See the steps to follow</small>
          </button>
          <button class="card" data-question="How long does business registration take?">
            <span>⏱</span><b>Processing time</b><small>Find expected processing time</small>
          </button>
          <button class="card" data-question="What is the fee for passport renewal?">
            <span>💳</span><b>Service fees</b><small>Find fee information</small>
          </button>
        </div>
      </div>
    </section>

    <div class="composer-area">
      <form class="composer" id="form">
        <textarea id="input" rows="1" placeholder="Message MISA..." autocomplete="off"></textarea>
        <button id="send" type="submit" aria-label="Send">↑</button>
      </form>
      <div class="note">MISA can only answer from the current service knowledge base.</div>
    </div>
  </main>
</div>

<script>
const messages=document.getElementById('messages');
const welcome=document.getElementById('welcome');
const input=document.getElementById('input');
const form=document.getElementById('form');
const sidebar=document.getElementById('sidebar');

function esc(s){return s.replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));}
function bottom(){messages.scrollTop=messages.scrollHeight;}
function user(text){
  welcome.style.display='none';
  messages.insertAdjacentHTML('beforeend',`<div class="row user"><div class="bubble">${esc(text)}</div></div>`);
  bottom();
}
function typing(){
  const id='typing-'+Date.now();
  messages.insertAdjacentHTML('beforeend',`<div class="row bot" id="${id}"><div class="avatar"><img src="assets/logo.png" alt="MISA"></div><div class="botbubble"><span class="dots"><i></i><i></i><i></i></span></div></div>`);
  bottom(); return id;
}
function bot(html){
  messages.insertAdjacentHTML('beforeend',`<div class="row bot"><div class="avatar"><img src="assets/logo.png" alt="MISA"></div><div class="botbubble">${html}</div></div>`);
  bottom();
}
async function ask(q){
  q=q.trim(); if(!q)return;
  user(q); input.value=''; input.style.height='auto';
  const id=typing();
  try{
    const fd=new FormData(); fd.append('question',q);
    const r=await fetch('api_chat.php',{method:'POST',body:fd});
    const d=await r.json(); document.getElementById(id)?.remove();
    bot(d.ok ? d.answer : '<p>Sorry, I could not process that question.</p>');
  }catch(e){
    document.getElementById(id)?.remove();
    bot('<p>I could not connect to the MISA database. Make sure Apache and MySQL are running.</p>');
  }
}
form.addEventListener('submit',e=>{e.preventDefault();ask(input.value);});
input.addEventListener('keydown',e=>{if(e.key==='Enter'&&!e.shiftKey){e.preventDefault();form.requestSubmit();}});
input.addEventListener('input',()=>{input.style.height='auto';input.style.height=Math.min(input.scrollHeight,150)+'px';});
document.querySelectorAll('[data-question]').forEach(x=>x.addEventListener('click',()=>ask(x.dataset.question)));
document.getElementById('newChat').onclick=()=>{messages.innerHTML='';messages.appendChild(welcome);welcome.style.display='';input.focus();};
document.getElementById('clearChat').onclick=()=>{messages.innerHTML='';messages.appendChild(welcome);welcome.style.display='';};
document.getElementById('openSidebar').onclick=()=>sidebar.classList.add('open');
document.getElementById('closeSidebar').onclick=()=>sidebar.classList.remove('open');
</script>
</body>
</html>
