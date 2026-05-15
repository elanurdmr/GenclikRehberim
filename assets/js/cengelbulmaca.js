/* ===========================================================
   ÇENGEL BULMACA — JavaScript
   Kesişimli kare bulmaca motoru
   =========================================================== */

(function () {
    const CFG = window.GAME_CONFIG;

    function trKey(ch) {
        if (!ch || ch.length < 1) return '';
        return ch.slice(-1).toLocaleUpperCase('tr-TR');
    }

    const acrossByN = CFG.across;
    const downByN = CFG.down;

    let activeAcross = true;
    let earned = 0;
    const completedWords = new Set();

    const elEarned = document.querySelector('.js-cw-earned');
    const elDirLabel = document.querySelector('.js-cw-dir-label');
    const elActiveDir = document.querySelector('.js-cw-active-dir');
    const elActiveNum = document.querySelector('.js-cw-active-num');
    const elActiveText = document.querySelector('.js-cw-active-text');

    let seconds = 0;
    let timerInterval = setInterval(function () {
        seconds++;
        const m = Math.floor(seconds / 60);
        const s = seconds % 60;
        document.querySelector('.js-cw-timer').textContent =
            (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
    }, 1000);

    function cellInput(r, c) {
        return document.querySelector('.crossword-cell-input[data-r="' + r + '"][data-c="' + c + '"]');
    }

    function solAt(r, c) {
        return CFG.sol[r][c];
    }

    function updateDirLabel() {
        elDirLabel.textContent = activeAcross ? 'Yön: soldan sağa' : 'Yön: yukarıdan aşağıya';
    }

    function clueNumAt(r, c, across) {
        if (across) {
            let cc = c;
            while (cc >= 0 && CFG.sol[r][cc] !== '#') {
                if (CFG.nums[r][cc]) return CFG.nums[r][cc];
                cc--;
            }
        } else {
            let rr = r;
            while (rr >= 0 && CFG.sol[rr][c] !== '#') {
                if (CFG.nums[rr][c]) return CFG.nums[rr][c];
                rr--;
            }
        }
        return 0;
    }

    function activeClueMeta(r, c) {
        const n = clueNumAt(r, c, activeAcross);
        const dict = activeAcross ? acrossByN : downByN;
        const cl = n ? dict[String(n)] || dict[n] : null;
        return { n: n, cl: cl };
    }

    function highlightClue(n, dirAcross) {
        document.querySelectorAll('.cengel-clue-item.is-active').forEach(function (x) {
            x.classList.remove('is-active');
        });
        if (!n) return;
        const d = dirAcross ? 'across' : 'down';
        document.querySelectorAll('.js-cw-clue[data-dir="' + d + '"][data-num="' + n + '"]').forEach(function (x) {
            x.classList.add('is-active');
        });
    }

    function focusMeta(r, c) {
        const meta = activeClueMeta(r, c);
        if (meta.cl) {
            elActiveDir.textContent = activeAcross ? 'Soldan sağa' : 'Yukarıdan aşağıya';
            elActiveNum.textContent = ' · ' + meta.n;
            elActiveText.textContent = meta.cl.clue;
            highlightClue(meta.n, activeAcross);
        } else {
            elActiveDir.textContent = 'İpucu';
            elActiveNum.textContent = '';
            elActiveText.textContent = 'Geçerli hücre seç.';
        }
    }

    function readWordAcross(cl) {
        let s = '';
        for (let i = 0; i < cl.word.length; i++) {
            const inp = cellInput(cl.r, cl.c + i);
            s += inp ? trKey(inp.value || '') : '';
        }
        return s;
    }

    function readWordDown(cl) {
        let s = '';
        for (let i = 0; i < cl.word.length; i++) {
            const inp = cellInput(cl.r + i, cl.c);
            s += inp ? trKey(inp.value || '') : '';
        }
        return s;
    }

    function markCellsComplete(cl, across) {
        for (let i = 0; i < cl.word.length; i++) {
            const r = across ? cl.r : cl.r + i;
            const c = across ? cl.c + i : cl.c;
            const inp = cellInput(r, c);
            if (!inp) continue;
            const wrap = inp.closest('.crossword-cell');
            wrap.classList.add('crossword-word-solved');
        }
    }

    function saveWordToServer(direction, clueNum, word) {
        const key = direction + ':' + clueNum;
        fetch('/genclik-rehberim/games/crossword_save_word.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                shuffle_key: CFG.shuffleKey,
                direction: direction,
                clue_number: clueNum,
                word: word
            })
        })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.success && !data.already_awarded) {
                    earned += data.points || CFG.pointsPerWord;
                    elEarned.textContent = String(earned);
                }
                if (data.success) {
                    const icon = document.querySelector(
                        direction === 'across'
                            ? '.js-cw-done-across-' + clueNum
                            : '.js-cw-done-down-' + clueNum
                    );
                    if (icon) icon.hidden = false;
                } else {
                    completedWords.delete(key);
                    const cl = direction === 'across' ? acrossByN[String(clueNum)] : downByN[String(clueNum)];
                    if (cl) {
                        for (let i = 0; i < cl.word.length; i++) {
                            const r = direction === 'across' ? cl.r : cl.r + i;
                            const c = direction === 'across' ? cl.c + i : cl.c;
                            const cell = document.querySelector('.crossword-cell-letter[data-r="' + r + '"][data-c="' + c + '"]');
                            if (cell) cell.classList.remove('crossword-word-solved');
                        }
                    }
                }
            })
            .catch(function () { completedWords.delete(key); });
    }

    function checkClueCompletion(cl, across) {
        if (!cl) return;
        const dir = across ? 'across' : 'down';
        const key = dir + ':' + cl.n;
        if (completedWords.has(key)) return;
        const full = across ? readWordAcross(cl) : readWordDown(cl);
        if (full !== cl.word) return;
        completedWords.add(key);
        markCellsComplete(cl, across);
        saveWordToServer(dir, cl.n, full);
    }

    function moveInDirection(r, c, dr, dc) {
        let nr = r + dr;
        let nc = c + dc;
        while (nr >= 0 && nr < CFG.h && nc >= 0 && nc < CFG.w) {
            if (CFG.sol[nr][nc] !== '#') {
                const t = cellInput(nr, nc);
                if (t) { t.focus(); return; }
            }
            nr += dr;
            nc += dc;
        }
    }

    function moveInReverse(r, c, dr, dc) {
        let nr = r - dr;
        let nc = c - dc;
        while (nr >= 0 && nr < CFG.h && nc >= 0 && nc < CFG.w) {
            if (CFG.sol[nr][nc] !== '#') {
                const t = cellInput(nr, nc);
                if (t) { t.focus(); return; }
            }
            nr -= dr;
            nc -= dc;
        }
    }

    function onInput(inp) {
        let v = inp.value;
        if (v) {
            v = trKey(v);
            inp.value = v || '';
        }
        const r = parseInt(inp.getAttribute('data-r'), 10);
        const c = parseInt(inp.getAttribute('data-c'), 10);
        const sol = solAt(r, c);
        const wrap = inp.closest('.crossword-cell');
        wrap.classList.remove('crossword-letter-wrong');
        if (v && v !== sol) wrap.classList.add('crossword-letter-wrong');
        if (v && v === sol) wrap.classList.remove('crossword-letter-wrong');

        const ca = acrossByN[String(clueNumAt(r, c, true))];
        if (ca) checkClueCompletion(ca, true);
        const cd = downByN[String(clueNumAt(r, c, false))];
        if (cd) checkClueCompletion(cd, false);

        if (v && v.length === 1) {
            window.requestAnimationFrame(function () {
                if (activeAcross) moveInDirection(r, c, 0, 1);
                else moveInDirection(r, c, 1, 0);
            });
        }
    }

    document.querySelectorAll('.crossword-cell-input').forEach(function (inp) {
        inp.addEventListener('focus', function () {
            const r = parseInt(inp.getAttribute('data-r'), 10);
            const c = parseInt(inp.getAttribute('data-c'), 10);
            focusMeta(r, c);
        });
        inp.addEventListener('input', function () { onInput(inp); });
    });

    document.addEventListener('keydown', function (e) {
        const el = document.activeElement;
        if (!el || !el.classList.contains('crossword-cell-input')) return;
        const r = parseInt(el.getAttribute('data-r'), 10);
        const c = parseInt(el.getAttribute('data-c'), 10);
        if (e.key === ' ') {
            e.preventDefault();
            activeAcross = !activeAcross;
            updateDirLabel();
            focusMeta(r, c);
            return;
        }
        if (e.key === 'Backspace' && el.value === '') {
            e.preventDefault();
            if (activeAcross) moveInReverse(r, c, 0, 1);
            else moveInReverse(r, c, 1, 0);
            return;
        }
        if (e.key === 'ArrowRight') { e.preventDefault(); moveInDirection(r, c, 0, 1); }
        else if (e.key === 'ArrowLeft') { e.preventDefault(); moveInDirection(r, c, 0, -1); }
        else if (e.key === 'ArrowDown') { e.preventDefault(); moveInDirection(r, c, 1, 0); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); moveInDirection(r, c, -1, 0); }
    });

    document.querySelector('.js-cw-toggle-dir').addEventListener('click', function () {
        activeAcross = !activeAcross;
        updateDirLabel();
        const el = document.activeElement;
        if (el && el.classList.contains('crossword-cell-input')) {
            const r = parseInt(el.getAttribute('data-r'), 10);
            const c = parseInt(el.getAttribute('data-c'), 10);
            focusMeta(r, c);
        }
    });

    document.querySelectorAll('.js-cw-tab').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const tab = btn.getAttribute('data-tab');
            document.querySelectorAll('.js-cw-tab').forEach(function (b) {
                b.classList.toggle('is-active', b === btn);
            });
            document.querySelector('.js-cw-panel-across').classList.toggle('is-visible', tab === 'across');
            document.querySelector('.js-cw-panel-down').classList.toggle('is-visible', tab === 'down');
        });
    });

    document.querySelectorAll('.js-cw-clue').forEach(function (li) {
        li.addEventListener('click', function () {
            const dir = li.getAttribute('data-dir');
            const n = parseInt(li.getAttribute('data-num'), 10);
            activeAcross = dir === 'across';
            updateDirLabel();
            const cl = dir === 'across' ? acrossByN[String(n)] : downByN[String(n)];
            if (cl) {
                const inp = cellInput(cl.r, cl.c);
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
        completedWords.clear();
        earned = 0;
        elEarned.textContent = '0';
    });

    updateDirLabel();

    Object.keys(acrossByN).forEach(function (k) {
        if (!acrossByN[k].n) acrossByN[k].n = parseInt(k, 10);
    });
    Object.keys(downByN).forEach(function (k) {
        if (!downByN[k].n) downByN[k].n = parseInt(k, 10);
    });
})();
