document.addEventListener('DOMContentLoaded', () => {
    const $ = (s, r = document) => r.querySelector(s);
    const $$ = (s, r = document) => [...r.querySelectorAll(s)];

    // 1. Sidebar
    const sb = $('#sidebar'), scrim = $('.scrim'), main = $('.main');
    $$('[data-sidebar-toggle]').forEach(b => b.addEventListener('click', () => {
        if (!sb) return;
        const open = !sb.classList.contains('open');
        sb.classList.toggle('open', open);
        scrim.classList.toggle('open', open);
        if (main) main.classList.toggle('no-scroll', open);
    }));

    // 2. Modals
    $$('[data-open]').forEach(b => b.addEventListener('click', e => { e.preventDefault(); const t = document.getElementById(b.dataset.open); if (t) t.classList.add('open'); }));
    $$('[data-close]').forEach(b => b.addEventListener('click', () => b.closest('.modal').classList.remove('open')));
    $$('.modal').forEach(m => m.addEventListener('click', e => { if (e.target === m) m.classList.remove('open'); }));
    const fm = $('#flashModal');
    if (fm && fm.dataset.auto) setTimeout(() => fm.classList.remove('open'), 2500);

    // 3. Password eye
    $$('[data-eye]').forEach(b => b.addEventListener('click', () => {
        const i = b.parentElement.querySelector('input');
        i.type = i.type === 'password' ? 'text' : 'password';
    }));

    // 4. Login enable/disable
    const login = $('form[data-auth]');
    if (login) {
        const em = $('#email'), pw = $('#password'), btn = $('#authBtn');
        const chk = () => { btn.disabled = !(em.value.trim() && pw.value.trim()); };
        ['input', 'change', 'keyup', 'blur'].forEach(ev => login.addEventListener(ev, chk));
        setInterval(chk, 400); chk();
    }

    // 5. Signup rules (no recursion)
    const su = $('form[data-signup]');
    if (su) {
        const pw = $('#password'), cf = $('#confirm'), rules = $$('#pwRules li'), btn = $('#authBtn');
        const check = () => {
            const v = pw.value, r = [v.length >= 8, /[A-Za-z]/.test(v), /\d/.test(v)];
            r.forEach((ok, i) => { rules[i].classList.toggle('ok', ok); });
            btn.disabled = !(r.every(Boolean) && $('#name').value.trim() && $('#email').value.trim() && v === cf.value);
        };
        ['input', 'change', 'keyup', 'blur'].forEach(ev => su.addEventListener(ev, check));
setInterval(check, 400); check();
    }

    // 6. Stock forms: live current qty, validation, preview, review step
    const form = $('form[data-review]');
    if (form) {
        const mode = form.dataset.mode, sel = $('[data-item]', form), qty = $('[data-qty]', form);
        const reviewBtn = $('[data-review-btn]', form), err = $('[data-qerr]', form), calc = $('[data-calc]', form);
        const after = $('[data-after]', form), cur = $('[data-cur]', form);
        const st = (q, t) => q <= 0 ? ['bad', 'Out of stock'] : q <= t ? ['warn', 'Low stock'] : ['ok', 'In stock'];
        let valid = true;
        const update = () => {
            if (!sel || !qty) return;
            const o = sel.selectedOptions[0]; if (!o) return;
            const c = +o.dataset.qty, u = o.dataset.unit, t = +o.dataset.th;
            if (cur) cur.value = c.toLocaleString();
            $$('[data-unit-lbl]', form).forEach(s => s.textContent = u);
            const raw = qty.value.trim(), v = parseInt(raw, 10);
            let msg = '';
            if (raw !== '') {
                if (!Number.isInteger(v) || String(v) !== raw) msg = 'Enter a whole number.';
                else if (mode === 'restock' && v < 1) msg = 'Enter a positive whole number (at least 1).';
                else if (mode === 'issue' && (v < 1 || v > c)) msg = c < 1 ? 'This item is out of stock.' : `Only ${c} ${u} are available. Enter a whole number from 1 to ${c}.`;
                else if (mode === 'adjust' && v < 0) msg = 'Enter a whole number of 0 or more.';
                else if (mode === 'adjust' && v === c) msg = 'The corrected count matches the recorded quantity.';
            }
            if (err && !(err.dataset.server && raw === qty.defaultValue)) err.textContent = msg;
            qty.classList.toggle('error', !!msg || (err && err.textContent !== ''));
            valid = raw !== '' && !msg;
            reviewBtn.disabled = !valid;
            if (valid) {
                let r, txt;
                if (mode === 'restock') { r = c + v; txt = `${c} + ${v} = ${r} ${u}`; }
                else if (mode === 'issue') { r = c - v; txt = `${c} − ${v} = ${r} ${u}`; }
                else { r = v; const d = v - c; txt = `${v} − ${c} = ${d > 0 ? '+' : '−'}${Math.abs(d)} ${u}`; }
                const [cl, lb] = st(r, t);
                calc.textContent = txt;
                after.innerHTML = `<span class="badge ${cl}">${lb} after ${mode}</span>`;
            } else { calc.textContent = 'Enter a quantity to preview'; after.innerHTML = ''; }
        };
        if (sel && qty) { sel.addEventListener('change', update); qty.addEventListener('input', () => { if (err) err.dataset.server = ''; update(); }); if (err && err.textContent.trim()) err.dataset.server = '1'; update(); if (err && err.dataset.server) { reviewBtn.disabled = false; } }

        reviewBtn.addEventListener('click', () => {
            if (!form.reportValidity()) return;
            if (sel && qty && !valid) return;
            const body = $('#reviewBody');
            body.innerHTML = '';
            $$('[data-label]', form).forEach(f => {
                let v = f.tagName === 'SELECT' ? f.selectedOptions[0].textContent : f.value;
                if (!v.trim()) return;
                const d = document.createElement('div');
                d.innerHTML = '<dt></dt><dd></dd>';
                d.children[0].textContent = f.dataset.label; d.children[1].textContent = v;
                body.appendChild(d);
            });
            $('#reviewModal').classList.add('open');
        });
        $('#reviewConfirm').addEventListener('click', () => form.submit());
    }
});
