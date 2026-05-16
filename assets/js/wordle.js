/* ===========================================================
   WORDLE OYUNU — JavaScript
   5 harfli kelime tahmini (Türkçe klavye)
   =========================================================== */

(function () {
    const DATE_SEED = window.GAME_CONFIG.dateSeed;

    const WORDS = [
        'DURUM', 'CESUR', 'SAYGI', 'OLMAK', 'GEREK', 'FARKI', 'BIRAK', 'İÇSEL', 'ORTAK', 'GÜVEN',
        'KORUN', 'DENGE', 'DOĞRU', 'YALAN', 'SINIR', 'ÖZGÜR', 'SAVUN', 'ÇÖZÜM', 'KONUŞ',
        'DİNLE', 'HELAL', 'NAMUS', 'HAKLI', 'BARIŞ', 'ÖRNEK', 'GÜÇLÜ', 'MERAK', 'ÖZGÜN', 'İLETİ', 'UYGUN',
        'DUYGU', 'SABIR', 'SEVGİ', 'HAYIR', 'KURAL', 'DÜZEN', 'AHLAK', 'HUZUR', 'DEĞER', 'TEMİZ',
        'BÜYÜK', 'KÜÇÜK', 'ZAMAN', 'KİTAP', 'İNSAN', 'KOMŞU', 'NEDEN', 'KİMSE', 'SANAT', 'MÜZİK',
        'SEVEN', 'GÜLER', 'SÖYLE', 'DÜŞÜN', 'KOLAY', 'GÜZEL', 'KALEM', 'HAYAL', 'HEDEF', 'KAZAN',
        'YETKİ', 'FİKİR', 'CANLI', 'HAYAT', 'YAŞAM', 'NEFES', 'TEPKİ', 'DERİN', 'DALGA', 'SORUN',
        'MORAL', 'MUTLU', 'ÜZGÜN', 'SEVİN', 'YÜREK', 'ÇEVRE', 'TAKİP', 'ZARAR', 'KORKU', 'MESAJ',
        'ŞÜKÜR', 'DOĞAL', 'TATLI', 'ŞARKI', 'ÇİÇEK', 'SOĞUK', 'SICAK', 'BÜTÜN', 'PAYDA', 'UZMAN'
    ];

    const ROWS = 8;
    const COLS = 5;

    function hashDay(str) {
        let h = 0;
        for (let i = 0; i < str.length; i++) {
            h = ((h << 5) - h) + str.charCodeAt(i);
            h |= 0;
        }
        return Math.abs(h);
    }

    function pickSolution() {
        const i = hashDay(DATE_SEED) % WORDS.length;
        return WORDS[i];
    }

    function trUpper(s) {
        return String(s).toLocaleUpperCase('tr-TR');
    }

    function scoreForRow(rowZero) {
        return Math.max(50, 100 - rowZero * 10);
    }

    function evaluateGuess(solution, guess) {
        const sol = [...solution];
        const g = [...guess];
        const state = Array(COLS).fill('absent');
        const remaining = {};

        for (let i = 0; i < COLS; i++) {
            const ch = sol[i];
            remaining[ch] = (remaining[ch] || 0) + 1;
        }

        for (let i = 0; i < COLS; i++) {
            if (g[i] === sol[i]) {
                state[i] = 'correct';
                remaining[g[i]]--;
            }
        }

        for (let i = 0; i < COLS; i++) {
            if (state[i] === 'correct') continue;
            const ch = g[i];
            if (remaining[ch] > 0) {
                state[i] = 'present';
                remaining[ch]--;
            }
        }

        return state;
    }

    const KEYBOARD_ROWS = [
        ['E', 'R', 'T', 'Y', 'U', 'O', 'P', 'Ğ', 'Ü'],
        ['A', 'S', 'D', 'F', 'G', 'H', 'J', 'K', 'L', 'Ş', 'İ', 'I'],
        ['ENTER', 'Z', 'C', 'V', 'B', 'N', 'M', 'Ö', 'Ç', 'BACK']
    ];

    let solution = pickSolution();
    let board = [];
    let row = 0;
    let col = 0;
    let locked = false;

    const msgEl = document.getElementById('wordleMessage');
    const boardEl = document.getElementById('wordleBoard');
    const kbEl = document.getElementById('wordleKeyboard');
    const attemptEl = document.getElementById('wordleAttempt');

    function setMessage(t) {
        msgEl.textContent = t || '';
    }

    function buildBoard() {
        boardEl.innerHTML = '';
        board = [];
        for (let r = 0; r < ROWS; r++) {
            const rowEl = document.createElement('div');
            rowEl.className = 'wordle-row';
            const cells = [];
            for (let c = 0; c < COLS; c++) {
                const cell = document.createElement('div');
                cell.className = 'wordle-cell';
                cell.id = 'wc_' + r + '_' + c;
                cell.setAttribute('aria-label', 'Boş');
                rowEl.appendChild(cell);
                cells.push(cell);
            }
            boardEl.appendChild(rowEl);
            board.push(cells);
        }
    }

    function buildKeyboard() {
        kbEl.innerHTML = '';
        KEYBOARD_ROWS.forEach(function (keys) {
            const rowEl = document.createElement('div');
            rowEl.className = 'wordle-key-row';
            keys.forEach(function (k) {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'wordle-key' + (k === 'ENTER' || k === 'BACK' ? ' wide' : '');
                btn.textContent = k === 'BACK' ? '←' : k;
                btn.dataset.key = k;
                btn.setAttribute('aria-label', k === 'BACK' ? 'Sil' : k);
                rowEl.appendChild(btn);
            });
            kbEl.appendChild(rowEl);
        });
    }

    function updateAttemptDisplay() {
        attemptEl.textContent = row + '/' + ROWS;
    }

    function updateKeyState(guess, state) {
        for (let i = 0; i < COLS; i++) {
            const k = guess[i];
            const st = state[i];
            const btn = kbEl.querySelector('.wordle-key[data-key="' + k + '"]');
            if (!btn) continue;

            const rank = { absent: 1, present: 2, correct: 3 };
            const cur = btn.dataset.state ? rank[btn.dataset.state] : 0;
            if (rank[st] >= cur) {
                btn.dataset.state = st;
                btn.classList.remove('state-correct', 'state-present', 'state-absent');
                btn.classList.add('state-' + st);
            }
        }
    }

    function submitRow() {
        if (locked) return;
        if (col < COLS) {
            setMessage('Kelime 5 harf olmalı.');
            return;
        }
        let guess = '';
        for (let c = 0; c < COLS; c++) {
            guess += board[row][c].textContent;
        }
        setMessage('');

        const states = evaluateGuess(solution, guess);
        for (let c = 0; c < COLS; c++) {
            const cell = board[row][c];
            cell.classList.add(states[c]);
            cell.setAttribute('aria-label', guess[c] + ' ' + states[c]);
        }
        updateKeyState(guess, states);

        if (guess === solution) {
            finish(true, row);
            return;
        }

        row++;
        col = 0;
        updateAttemptDisplay();

        if (row >= ROWS) {
            finish(false, ROWS - 1);
            return;
        }
    }

    function finish(won, lastRowZero) {
        locked = true;
        const pts = won ? scoreForRow(lastRowZero) : 0;
        const overlay = document.getElementById('wordleResultOverlay');
        const emojiEl = overlay.querySelector('.result-emoji');
        const titleEl = document.getElementById('wordleResultTitle');
        const msgEl2 = document.getElementById('wordleResultMsg');
        const scoreEl = document.getElementById('wordleFinalScore');
        const revealEl = document.getElementById('wordleReveal');
        const saveEl = document.getElementById('wordleSaveStatus');

        revealEl.textContent = 'Kelime: ' + solution;
        scoreEl.textContent = String(pts);

        if (won) {
            emojiEl.textContent = pts >= 100 ? '🏆' : '🎉';
            titleEl.textContent = 'Tebrikler!';
            msgEl2.textContent = (lastRowZero + 1) + '. denemede bildin.';
        } else {
            emojiEl.textContent = '😔';
            titleEl.textContent = 'Bir dahaki sefere!';
            msgEl2.textContent = 'Denemeler bitti.';
        }

        saveEl.textContent = '';
        overlay.classList.add('show');

        if (typeof saveScore === 'function') {
            saveScore(4, pts, 100, function (data) {
                if (data && data.success) {
                    saveEl.innerHTML = '<span class="material-symbols-outlined" style="font-variation-settings:\'FILL\' 1;color:var(--secondary);font-size:16px">check_circle</span> Puan kaydedildi!';
                } else if (data && data.login_required) {
                    saveEl.innerHTML = '<a href="/genclik-rehberim/girisyap.php" style="color:var(--primary);font-weight:700">Puanı kaydetmek için giriş yap</a>';
                } else {
                    saveEl.textContent = 'Puan kaydı yapılamadı.';
                }
            });
        }
    }

    function onKey(k) {
        if (locked) return;
        if (k === 'ENTER') {
            submitRow();
            return;
        }
        if (k === 'BACK') {
            if (col > 0) {
                col--;
                board[row][col].textContent = '';
                board[row][col].classList.remove('filled');
                board[row][col].setAttribute('aria-label', 'Boş');
            }
            return;
        }
        if (col >= COLS) return;
        board[row][col].textContent = k;
        board[row][col].classList.add('filled');
        board[row][col].setAttribute('aria-label', k);
        col++;
    }

    function bindKeys() {
        kbEl.addEventListener('click', function (e) {
            const b = e.target.closest('.wordle-key');
            if (!b) return;
            onKey(b.dataset.key);
        });

        document.addEventListener('keydown', function (e) {
            if (locked) return;
            const active = document.activeElement;
            if (active && (active.tagName === 'INPUT' || active.tagName === 'TEXTAREA')) return;

            if (e.key === 'Enter') {
                e.preventDefault();
                onKey('ENTER');
                return;
            }
            if (e.key === 'Backspace') {
                e.preventDefault();
                onKey('BACK');
                return;
            }
            const ch = e.key;
            if (ch.length !== 1) return;
            const letter = trUpper(ch);
            const keyBtn = kbEl.querySelector('.wordle-key[data-key="' + letter + '"]');
            if (!keyBtn) return;
            e.preventDefault();
            onKey(letter);
        });
    }

    function init() {
        solution = pickSolution();
        row = 0;
        col = 0;
        locked = false;
        buildBoard();
        buildKeyboard();
        updateAttemptDisplay();
        setMessage('Türkiye Türkçesi harfleriyle yaz. ENTER ile gönder.');
        document.getElementById('wordleResultOverlay').classList.remove('show');
    }

    document.getElementById('wordleRestartBtn').addEventListener('click', function () {
        document.getElementById('wordleResultOverlay').classList.remove('show');
        const letters = WORDS.slice();
        const idx = Math.floor(Math.random() * letters.length);
        solution = letters[idx];
        row = 0;
        col = 0;
        locked = false;
        buildBoard();
        buildKeyboard();
        updateAttemptDisplay();
        setMessage('Yeni tur — kelime değişti. İyi şanslar!');
    });

    bindKeys();
    init();
})();
