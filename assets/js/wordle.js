/* ===========================================================
   WORDLE OYUNU — JavaScript
   5 harfli kelime tahmini (Türkçe klavye)
   =========================================================== */

(function () {
    const DATE_SEED = window.GAME_CONFIG.dateSeed;
    /* Etkinlik ID'si PHP'den gelir; sabit kodlanmaz. */
    const ACTIVITY_ID = window.GAME_CONFIG.activityId || 4;

    /* Günlük hedef kelimeleri — bunlardan biri çözüm olarak seçilir. */
    const TARGET_WORDS = [
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

    /* Geçerli tahmin kelimeler — hedef listesi + ek Türkçe sözlük.
       Burada olmayan bir kelime girildiğinde satır harcanmadan uyarı verilir. */
    const VALID_WORDS = new Set([
        ...TARGET_WORDS,
        'AÇMAK', 'ALTIN', 'ALMAK', 'ARABA', 'ARAZI', 'ARMUT', 'ASKER', 'ASLAN',
        'BADEM', 'BALIK', 'BANKA', 'BEBEK', 'BEKLE', 'BEYAZ', 'BÖREK', 'BOZMA', 'BÜTÇE', 'BÜYÜT',
        'ÇANTA', 'ÇARŞI', 'ÇOCUK',
        'DENİZ', 'DEVAM', 'DOĞUM', 'DUVAR',
        'ELMAS', 'EMSAL',
        'FİYAT', 'FORUM',
        'GÜMÜŞ',
        'HABER', 'HALKA', 'HAMLE', 'HAMSİ', 'HOROZ',
        'İDEAL', 'İZLEM',
        'KADAR', 'KADIN', 'KABLO', 'KANCA', 'KAPAK', 'KARMA', 'KAVUN', 'KENAR', 'KEMER', 'KESME',
        'KİLİM', 'KİRAZ', 'KIRIK', 'KONUT', 'KÖFTE', 'KÖPEK', 'KUBBE', 'KURUL', 'KUŞAK', 'KÜMES',
        'LIMAN',
        'MEKAN', 'MEYVE', 'MİZAH', 'MÜDÜR',
        'NAMAZ', 'NEHİR', 'NÜFUS',
        'OKUMA', 'ÖNLEM',
        'PAKET', 'PASTA', 'PAZAR', 'PEMBE', 'PERDE',
        'RESİM',
        'SAHTE', 'SALON', 'SARAY', 'SEFER', 'SINAV', 'SOKAK', 'SOYUT', 'SÜPER',
        'ŞAHİN', 'ŞARAP', 'ŞEKER',
        'TABLO', 'TAHTA', 'TAMAM', 'TARAF', 'TAVAN', 'TEKNE', 'TEMEL', 'TEORİ', 'TOPLU', 'TURNA', 'TUTAR', 'TÜNEL', 'TUZAK',
        'ÜZERE',
        'VAGON', 'VATAN', 'VURMA',
        'YARGI', 'YASAL', 'YASAK', 'YATAK', 'YATAY', 'YAZAR', 'YEŞİL', 'YEMEK', 'YİRMİ', 'YOLCU',
        'ZARIF', 'ZİHİN'
    ]);

    const ROWS = 8;
    const COLS = 5;

    /**
     * Verilen string için deterministik bir tam sayı hash üretir.
     * @param {string} str - Hash'lenecek string (tarih seed'i)
     * @returns {number} Negatif olmayan tam sayı hash değeri
     */
    function hashDay(str) {
        let h = 0;
        for (let i = 0; i < str.length; i++) {
            h = ((h << 5) - h) + str.charCodeAt(i);
            h |= 0;
        }
        return Math.abs(h);
    }

    /**
     * Günlük seed'e göre hedef kelimeyi seçer.
     * @returns {string} Büyük harfli 5 karakterli Türkçe kelime
     */
    function pickSolution() {
        const i = hashDay(DATE_SEED) % TARGET_WORDS.length;
        return TARGET_WORDS[i];
    }

    /**
     * Türkçe karakterleri hesaba katarak büyük harfe çevirir.
     * @param {string} s - Dönüştürülecek string
     * @returns {string} Büyük harfli string (i→İ, ı→I düzeltmeleriyle)
     */
    function trUpper(s) {
        return String(s || '')
            .split('i').join('İ')   /* i U+0069 → İ U+0130 */
            .split('ı').join('I')   /* ı U+0131 → I U+0049 */
            .toUpperCase();
    }

    /**
     * Kaç denemede bulunduğuna göre puan hesaplar.
     * @param {number} rowZero - Sıfır tabanlı deneme indeksi
     * @returns {number} 50–100 arasında puan
     */
    function scoreForRow(rowZero) {
        return Math.max(50, 100 - rowZero * 10);
    }

    /**
     * Tahmini çözümle karşılaştırarak her hücrenin durumunu döndürür.
     * @param {string} solution - Doğru kelime
     * @param {string} guess    - Kullanıcı tahmini
     * @returns {Array<'correct'|'present'|'absent'>} Her hücre için durum dizisi
     */
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

    /**
     * Oyun mesajını günceller veya temizler.
     * @param {string} [t] - Gösterilecek mesaj; boş bırakılırsa temizler
     */
    function setMessage(t) {
        msgEl.textContent = t || '';
    }

    /** Oyun tahtasını (8×5 hücre) DOM'da oluşturur. */
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

    /** Türkçe sanal klavyeyi DOM'da oluşturur. */
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

    /** Deneme sayacı metnini (ör. "3/8") günceller. */
    function updateAttemptDisplay() {
        attemptEl.textContent = row + '/' + ROWS;
    }

    /**
     * Klavye tuşlarının rengini tahmin sonuçlarına göre günceller.
     * @param {string[]} guess - Harf dizisi
     * @param {string[]} state - Her harfin durumu ('correct' | 'present' | 'absent')
     */
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

    /**
     * Geçersiz tahmin için belirtilen satırı sallama animasyonuyla işaretler.
     * @param {number} r - Sıfır tabanlı satır indeksi
     */
    function shakeRow(r) {
        for (let c = 0; c < COLS; c++) {
            board[r][c].classList.add('shake');
            setTimeout(() => board[r][c].classList.remove('shake'), 500);
        }
    }

    /**
     * Mevcut satırdaki tahmini değerlendirir; geçersiz veya kısa kelimede uyarır.
     * Doğru tahmin ya da deneme bitmesinde finish() çağrılır.
     */
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

        if (!VALID_WORDS.has(guess)) {
            setMessage('Geçersiz kelime! Farklı bir kelime dene.');
            shakeRow(row);
            return;
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

    /**
     * Oyunu sonlandırır: sonuç modalını gösterir ve puanı sunucuya kaydeder.
     * @param {boolean} won         - Kullanıcı kelimeyi tahmin etti mi?
     * @param {number}  lastRowZero - Sıfır tabanlı son deneme satırı indeksi
     */
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
            saveScore(ACTIVITY_ID, pts, 100, function (data) {
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

    /**
     * Bir tuşa basıldığında oyun mantığını işler.
     * @param {string} k - 'ENTER', 'BACK' veya tek harf
     */
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

    /** Sanal klavye tıklaması ve fiziksel klavye olaylarını bağlar. */
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

    /** Oyunu sıfırlar: yeni kelime seçer, tahtayı ve klavyeyi yeniden oluşturur. */
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
        const idx = Math.floor(Math.random() * TARGET_WORDS.length);
        solution = TARGET_WORDS[idx];
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
