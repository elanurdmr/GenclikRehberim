/* ===========================================================
   ÇENGEL BULMACA — JavaScript
   =========================================================== */

(function () {
    var CFG = window.GAME_CONFIG;

    var acrossByN = CFG.across;
    var downByN   = CFG.down;

    var activeAcross    = true;
    var earned          = 0;
    var scoreSaved      = false;
    var completedWords  = {};
    var focusedR        = -1;
    var focusedC        = -1;

    var elEarned    = document.querySelector('.js-cw-earned');
    var elActiveDir  = document.querySelector('.js-cw-active-dir');
    var elActiveNum  = document.querySelector('.js-cw-active-num');
    var elActiveText = document.querySelector('.js-cw-active-text');
    var elHintBtn    = document.querySelector('.js-cw-hint-btn');

    /* Tüm .js-cw-final-score ve .js-cw-max-score elementlerini güncelle */
    function setFinalScoreEls(pts) {
        document.querySelectorAll('.js-cw-final-score').forEach(function (el) { el.textContent = String(pts); });
    }
    function setMaxScoreEls() {
        document.querySelectorAll('.js-cw-max-score').forEach(function (el) { el.textContent = String(CFG.maxScore || 100); });
    }

    /* Timer */
    var seconds = 0;
    var timerInterval = setInterval(function () {
        seconds++;
        var m = Math.floor(seconds / 60);
        var s = seconds % 60;
        var timerEl = document.querySelector('.js-cw-timer');
        if (timerEl) {
            timerEl.textContent = (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
        }
    }, 1000);

    /* -------- Yardımcı fonksiyonlar -------- */

    function trKey(ch) {
        if (!ch || ch.length < 1) return '';
        return ch.slice(-1).toLocaleUpperCase('tr-TR');
    }

    function cellInput(r, c) {
        return document.querySelector('.crossword-cell-input[data-r="' + r + '"][data-c="' + c + '"]');
    }

    function solAt(r, c) {
        return CFG.sol[r][c];
    }

    function clueNumAt(r, c, across) {
        if (across) {
            var cc = c;
            while (cc >= 0 && CFG.sol[r][cc] !== '#') {
                if (CFG.nums[r][cc]) return CFG.nums[r][cc];
                cc--;
            }
        } else {
            var rr = r;
            while (rr >= 0 && CFG.sol[rr][c] !== '#') {
                if (CFG.nums[rr][c]) return CFG.nums[rr][c];
                rr--;
            }
        }
        return 0;
    }

    function activeClueMeta(r, c) {
        var n    = clueNumAt(r, c, activeAcross);
        var dict = activeAcross ? acrossByN : downByN;
        var cl   = n ? (dict[String(n)] || dict[n]) : null;
        return { n: n, cl: cl };
    }

    function highlightClue(n, dirAcross) {
        document.querySelectorAll('.cengel-clue-item.is-active').forEach(function (x) {
            x.classList.remove('is-active');
        });
        if (!n) return;
        var d = dirAcross ? 'across' : 'down';
        document.querySelectorAll('.js-cw-clue[data-dir="' + d + '"][data-num="' + n + '"]').forEach(function (x) {
            x.classList.add('is-active');
        });
    }

    function focusMeta(r, c) {
        focusedR = r; focusedC = c;
        var meta = activeClueMeta(r, c);
        if (meta.cl) {
            elActiveDir.textContent  = activeAcross ? 'Soldan sağa' : 'Yukarıdan aşağıya';
            elActiveNum.textContent  = ' · ' + meta.n;
            elActiveText.textContent = meta.cl.clue;
            highlightClue(meta.n, activeAcross);
            if (elHintBtn) elHintBtn.style.display = 'block';
        } else {
            elActiveDir.textContent  = 'İpucu';
            elActiveNum.textContent  = '';
            elActiveText.textContent = 'Geçerli hücre seç.';
            if (elHintBtn) elHintBtn.style.display = 'none';
        }
    }

    /* Aktif hücrenin ipucusunu döndürür (hem yatay hem dikey dener) */
    function getActiveClue() {
        var el = document.activeElement;
        if (!el || !el.classList.contains('crossword-cell-input')) return null;
        var r = parseInt(el.getAttribute('data-r'), 10);
        var c = parseInt(el.getAttribute('data-c'), 10);
        var meta = activeClueMeta(r, c);
        if (meta.cl) return { cl: meta.cl, across: activeAcross };
        /* Karşı yönde dene */
        var wasAcross = activeAcross;
        activeAcross = !activeAcross;
        meta = activeClueMeta(r, c);
        activeAcross = wasAcross;
        if (meta.cl) return { cl: meta.cl, across: !wasAcross };
        return null;
    }

    /* -------- Kelime okuma -------- */

    function readWordAcross(cl) {
        var s = '';
        for (var i = 0; i < cl.word.length; i++) {
            var inp = cellInput(cl.r, cl.c + i);
            s += inp ? trKey(inp.value || '') : '';
        }
        return s;
    }

    function readWordDown(cl) {
        var s = '';
        for (var i = 0; i < cl.word.length; i++) {
            var inp = cellInput(cl.r + i, cl.c);
            s += inp ? trKey(inp.value || '') : '';
        }
        return s;
    }

    /* -------- Kelime tamamlama -------- */

    function markCellsComplete(cl, across) {
        for (var i = 0; i < cl.word.length; i++) {
            var r   = across ? cl.r : cl.r + i;
            var c   = across ? cl.c + i : cl.c;
            var inp = cellInput(r, c);
            if (!inp) continue;
            inp.closest('.crossword-cell').classList.add('crossword-word-solved');
        }
    }

    function saveWordToServer(direction, clueNum, word) {
        var key = direction + ':' + clueNum;
        fetch('/genclik-rehberim/games/crossword_save_word.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                shuffle_key:  CFG.shuffleKey,
                direction:    direction,
                clue_number:  clueNum,
                word:         word
            })
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success && !data.already_awarded) {
                earned += data.points || CFG.pointsPerWord;
                elEarned.textContent = String(earned);
            }
            if (data.success) {
                var icon = document.querySelector(
                    direction === 'across'
                        ? '.js-cw-done-across-' + clueNum
                        : '.js-cw-done-down-'   + clueNum
                );
                if (icon) icon.hidden = false;
            } else {
                delete completedWords[key];
                var cl2 = direction === 'across' ? acrossByN[String(clueNum)] : downByN[String(clueNum)];
                if (cl2) {
                    for (var i = 0; i < cl2.word.length; i++) {
                        var rr = direction === 'across' ? cl2.r : cl2.r + i;
                        var cc = direction === 'across' ? cl2.c + i : cl2.c;
                        var cell = document.querySelector('.crossword-cell-letter[data-r="' + rr + '"][data-c="' + cc + '"]');
                        if (cell) cell.classList.remove('crossword-word-solved');
                    }
                }
            }
        })
        .catch(function () { delete completedWords[key]; });
    }

    function checkClueCompletion(cl, across) {
        if (!cl) return;
        var dir = across ? 'across' : 'down';
        var key = dir + ':' + cl.n;
        if (completedWords[key]) return;
        var full = across ? readWordAcross(cl) : readWordDown(cl);
        if (full !== cl.word) return;
        completedWords[key] = true;
        markCellsComplete(cl, across);
        saveWordToServer(dir, cl.n, full);
    }

    /* -------- Navigasyon -------- */

    function moveInDirection(r, c, dr, dc) {
        var nr = r + dr, nc = c + dc;
        while (nr >= 0 && nr < CFG.h && nc >= 0 && nc < CFG.w) {
            if (CFG.sol[nr][nc] !== '#') {
                var t = cellInput(nr, nc);
                if (t) {
                    if (t.value && trKey(t.value) === CFG.sol[nr][nc]) {
                        nr += dr; nc += dc; continue;
                    }
                    t.focus(); return;
                }
            }
            nr += dr; nc += dc;
        }
    }

    function moveInReverse(r, c, dr, dc) {
        var nr = r - dr, nc = c - dc;
        while (nr >= 0 && nr < CFG.h && nc >= 0 && nc < CFG.w) {
            if (CFG.sol[nr][nc] !== '#') {
                var t = cellInput(nr, nc);
                if (t) { t.focus(); return; }
            }
            nr -= dr; nc -= dc;
        }
    }

    /* -------- Girdi -------- */

    function onInput(inp) {
        var v = inp.value;
        if (v) {
            v = trKey(v);
            inp.value = v || '';
        }
        var r   = parseInt(inp.getAttribute('data-r'), 10);
        var c   = parseInt(inp.getAttribute('data-c'), 10);
        var sol = solAt(r, c);
        var wrap = inp.closest('.crossword-cell');
        wrap.classList.remove('crossword-letter-wrong');
        if (v && v !== sol) wrap.classList.add('crossword-letter-wrong');

        var ca = acrossByN[String(clueNumAt(r, c, true))];
        if (ca) checkClueCompletion(ca, true);
        var cd = downByN[String(clueNumAt(r, c, false))];
        if (cd) checkClueCompletion(cd, false);

        if (v && v.length === 1) {
            window.requestAnimationFrame(function () {
                if (activeAcross) moveInDirection(r, c, 0, 1);
                else              moveInDirection(r, c, 1, 0);
            });
        }
    }

    /* -------- Oyunu Bitir -------- */

    function finishGame() {
        clearInterval(timerInterval);
        setFinalScoreEls(earned);
        setMaxScoreEls();

        var total = Object.keys(completedWords).length;
        var pct   = CFG.maxScore > 0 ? (earned / CFG.maxScore) * 100 : 0;
        document.getElementById('resultEmoji').textContent = pct >= 80 ? '🏆' : pct >= 50 ? '🎉' : '🎯';
        document.getElementById('resultTitle').textContent = 'Bulmaca Tamamlandı!';
        document.getElementById('resultMsg').textContent   = total + ' kelime çözdün.';

        var ss = document.getElementById('finalSaveStatus');
        ss.textContent = '';
        if (!scoreSaved && typeof saveScore === 'function') {
            scoreSaved = true;
            saveScore(5, earned, CFG.maxScore, function (data) {
                if (data && data.success) {
                    ss.innerHTML = '<span class="material-symbols-outlined" style="font-variation-settings:\'FILL\' 1;color:var(--secondary);font-size:16px">check_circle</span> Puan kaydedildi!';
                } else if (data && data.login_required) {
                    ss.innerHTML = '<a href="/genclik-rehberim/girisyap.php" style="color:var(--primary);font-weight:700">Puanı kaydetmek için giriş yap</a>';
                }
            });
        }
        document.getElementById('resultOverlay').classList.add('show');
    }

    /* -------- Olay dinleyicileri -------- */

    document.querySelectorAll('.crossword-cell-input').forEach(function (inp) {
        /* Otomatik yön tespiti: tıklandığında mevcut hücrenin
           hangi yönlerde ipucu içerdiğine bakarak yönü belirler.
           Aynı hücreye ikinci kez tıklanırsa yön değişir. */
        inp.addEventListener('mousedown', function () {
            var r = parseInt(inp.getAttribute('data-r'), 10);
            var c = parseInt(inp.getAttribute('data-c'), 10);
            var key = r + '_' + c;
            var acrossN = clueNumAt(r, c, true);
            var downN   = clueNumAt(r, c, false);
            var hasA = acrossN && acrossByN[String(acrossN)];
            var hasD = downN   && downByN[String(downN)];

            if (hasA && !hasD) {
                activeAcross = true;
            } else if (!hasA && hasD) {
                activeAcross = false;
            } else if (hasA && hasD) {
                if (window._cwLastKey === key) {
                    activeAcross = !activeAcross;
                }
            }
            window._cwLastKey = key;
        });

        inp.addEventListener('focus', function () {
            focusMeta(
                parseInt(inp.getAttribute('data-r'), 10),
                parseInt(inp.getAttribute('data-c'), 10)
            );
        });
        inp.addEventListener('input', function () { onInput(inp); });
    });

    document.addEventListener('keydown', function (e) {
        var el = document.activeElement;
        if (!el || !el.classList.contains('crossword-cell-input')) return;
        var r = parseInt(el.getAttribute('data-r'), 10);
        var c = parseInt(el.getAttribute('data-c'), 10);

        if (e.key === 'Backspace' && el.value === '') {
            e.preventDefault();
            if (activeAcross) moveInReverse(r, c, 0, 1);
            else              moveInReverse(r, c, 1, 0);
            return;
        }
        if      (e.key === 'ArrowRight') { e.preventDefault(); moveInDirection(r, c, 0,  1); }
        else if (e.key === 'ArrowLeft')  { e.preventDefault(); moveInDirection(r, c, 0, -1); }
        else if (e.key === 'ArrowDown')  { e.preventDefault(); moveInDirection(r, c, 1,  0); }
        else if (e.key === 'ArrowUp')    { e.preventDefault(); moveInDirection(r, c, -1, 0); }
    });

    document.querySelectorAll('.js-cw-tab').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var tab = btn.getAttribute('data-tab');
            document.querySelectorAll('.js-cw-tab').forEach(function (b) {
                b.classList.toggle('is-active', b === btn);
            });
            document.querySelector('.js-cw-panel-across').classList.toggle('is-visible', tab === 'across');
            document.querySelector('.js-cw-panel-down').classList.toggle('is-visible', tab === 'down');
        });
    });

    document.querySelectorAll('.js-cw-clue').forEach(function (li) {
        li.addEventListener('click', function () {
            var dir = li.getAttribute('data-dir');
            var n   = parseInt(li.getAttribute('data-num'), 10);
            activeAcross = dir === 'across';
            var cl = dir === 'across' ? acrossByN[String(n)] : downByN[String(n)];
            if (cl) {
                var inp = cellInput(cl.r, cl.c);
                if (inp) inp.focus();
            }
        });
    });

    document.querySelector('.js-cw-reset').addEventListener('click', function () {
        document.querySelectorAll('.crossword-cell-input').forEach(function (inp) {
            inp.value = '';
        });
        document.querySelectorAll('.crossword-cell-letter').forEach(function (w) {
            w.classList.remove('crossword-word-solved', 'crossword-letter-wrong');
        });
        document.querySelectorAll('.cengel-clue-done').forEach(function (ic) { ic.hidden = true; });
        completedWords = {};
        scoreSaved     = false;
        earned         = 0;
        elEarned.textContent = '0';
    });

    document.querySelector('.js-cw-finish').addEventListener('click', finishGame);

    if (elHintBtn) {
        elHintBtn.addEventListener('click', function () {
            var active = getActiveClue();
            if (!active) return;
            var cl     = active.cl;
            var across = active.across;

            /* Boş veya yanlış hücreleri topla */
            var candidates = [];
            for (var i = 0; i < cl.word.length; i++) {
                var hr  = across ? cl.r     : cl.r + i;
                var hc  = across ? cl.c + i : cl.c;
                var inp = cellInput(hr, hc);
                if (!inp) continue;
                var correct = CFG.sol[hr][hc];
                if (!inp.value || trKey(inp.value) !== correct) {
                    candidates.push({ r: hr, c: hc, inp: inp, correct: correct });
                }
            }
            if (candidates.length === 0) return;

            /* Rastgele bir hücre seç */
            var pick = candidates[Math.floor(Math.random() * candidates.length)];
            pick.inp.value = pick.correct;
            var cell = pick.inp.closest('.crossword-cell');
            cell.classList.remove('crossword-letter-wrong');

            /* Altın flaş animasyonu */
            cell.style.transition = 'background 0.15s';
            cell.style.background = 'rgba(255,190,11,0.45)';
            setTimeout(function () { cell.style.background = ''; }, 600);

            earned = Math.max(0, earned - 5);
            elEarned.textContent = String(earned);

            var ca = acrossByN[String(clueNumAt(pick.r, pick.c, true))];
            if (ca) checkClueCompletion(ca, true);
            var cd = downByN[String(clueNumAt(pick.r, pick.c, false))];
            if (cd) checkClueCompletion(cd, false);
        });
    }

    /* -------- init -------- */

    setMaxScoreEls();

    Object.keys(acrossByN).forEach(function (k) {
        if (!acrossByN[k].n) acrossByN[k].n = parseInt(k, 10);
    });
    Object.keys(downByN).forEach(function (k) {
        if (!downByN[k].n) downByN[k].n = parseInt(k, 10);
    });
})();
