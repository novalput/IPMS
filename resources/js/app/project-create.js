// Hapus section-frame yang berisi dropdown proyek/periode/date picker di bawah, agar tidak dobel

document.addEventListener('DOMContentLoaded', function() {
    let globalDocIndex = 1000;
    
    // --- Helper functions for letter/numbering ---
    function getLetter(idx) {
        return String.fromCharCode(65 + idx); // 0 -> A, 1 -> B, ...
    }
    function getSubLabel(subCatLetter, subIdx) {
        return `${subCatLetter}.${subIdx}`;
    }

    // --- Engineering Section (flat columns for 6 doc number columns) ---
    // Sembunyikan/tampilkan semua tombol aksi di tabel engineering
    document.getElementById('engToggleButtons').onclick = function(){
        let isHidden = this.dataset.hidden === "1";
        // Semua tombol aksi di tabel engineering
        let btnSelectors = [
            '#engAddCategory',
            '#engAddDoc',
            '#engDelDoc',
            '.btn-master-add-sub',
            '.btn-master-remove-sub',
            '.btn-eng-add-subsub',
            '.btn-eng-remove-subsub',
            '.btn-sub-add-doc',
            '.btn-remove-sub'
        ];
        btnSelectors.forEach(sel => {
            document.querySelectorAll(sel).forEach(btn => {
                btn.style.display = isHidden ? '' : 'none';
            });
        });
        this.textContent = isHidden ? 'Sembunyikan Tombol' : 'Tampilkan Tombol';
        this.dataset.hidden = isHidden ? "0" : "1";
    };

    // Pastikan tombol-tombol baru juga ikut disembunyikan jika mode sembunyi aktif
    function hideEngineeringButtonsIfNeeded() {
        let toggleBtn = document.getElementById('engToggleButtons');
        if(toggleBtn.dataset.hidden === "1") {
            let btnSelectors = [
                '.btn-master-add-sub',
                '.btn-master-remove-sub',
                '.btn-eng-add-subsub',
                '.btn-eng-remove-subsub',
                '.btn-sub-add-doc',
                '.btn-remove-sub'
            ];
            btnSelectors.forEach(sel => {
                document.querySelectorAll(sel).forEach(btn => {
                    btn.style.display = 'none';
                });
            });
        }
    }

    // --- Add Kategori (e.g. Master List Drawing) ---
    document.getElementById('engAddCategory').onclick = function(){
        let body = document.getElementById('engineeringBody');
        let tr = document.createElement('tr');
        tr.className = 'kategori-row';
        tr.innerHTML = `
            <td></td>
            <td colspan="6" class="font-bold text-left bg-blue-50">
                <input type="text" class="form-control form-control-sm kategori-input border border-blue-300 rounded px-2 py-1" name="engineering_kategori[]" value="Master List Drawing" style="width:220px; display:inline-block;">
                <span class="ml-2 text-xs text-gray-400">Kategori</span>
            </td>
            <td colspan="2" class="text-right">
                <button type="button" class="btn-action-primary btn-master-add-sub mr-2">+ Tambah Subkategori</button>
                <button type="button" class="btn-action-danger btn-master-remove-sub">Hapus Kategori</button>
            </td>
        `;
        let lastRow = body.lastElementChild;
        if (lastRow) lastRow.after(tr);
        else body.appendChild(tr);
        hideEngineeringButtonsIfNeeded();
    };

    document.getElementById('engineeringBody').addEventListener('click', function(e){
        // Tambah Subkategori (A, B, C, ...)
        if(e.target.closest('.btn-master-add-sub')){
            let kategoriRow = e.target.closest('tr.kategori-row');
            if(!kategoriRow) return;
            // Cari baris terakhir dalam kategori ini (subkategori/sub/doc terakhir)
            let insertAfter = kategoriRow;
            let idx = 0;
            let next = kategoriRow.nextElementSibling;
            while(next && !next.classList.contains('kategori-row')){
                if(next.classList.contains('subkategori-row')) idx++;
                insertAfter = next;
                next = next.nextElementSibling;
            }
            let subLetter = getLetter(idx);
            let tr = document.createElement('tr');
            tr.className = 'subkategori-row';
            tr.innerHTML = `
                <td class="font-bold text-blue-700 text-center">${subLetter}</td>
                <td colspan="6" class="font-bold text-blue-700 text-left bg-blue-100">
                    <input type="text" class="form-control form-control-sm subkategori-input border border-blue-300 rounded px-2 py-1" name="engineering_subkategori[]" value="Subkategori Baru" style="width:220px; display:inline-block;">
                    <span class="ml-2 text-xs text-gray-400">Subkategori</span>
                </td>
                <td colspan="2" class="text-right">
                    <button type="button" class="btn-action-primary btn-eng-add-subsub mr-2">+ Tambah Sub</button>
                    <button type="button" class="btn-action-danger btn-eng-remove-subsub">Hapus Subkategori</button>
                </td>
            `;
            insertAfter.after(tr);
            hideEngineeringButtonsIfNeeded();
        }
        // Hapus Kategori
        if(e.target.closest('.btn-master-remove-sub')){
            let kategoriRow = e.target.closest('tr.kategori-row');
            if(!kategoriRow) return;
            let next = kategoriRow.nextElementSibling;
            while(next && !next.classList.contains('kategori-row')){
                let toRemove = next;
                next = next.nextElementSibling;
                toRemove.remove();
            }
            kategoriRow.remove();
        }
        // Tambah Sub (A.1, A.2, B.1, ...)
        if(e.target.closest('.btn-eng-add-subsub')){
            let subkategoriRow = e.target.closest('tr.subkategori-row');
            if(!subkategoriRow) return;
            let subCatLetter = subkategoriRow.querySelector('td').textContent.replace('.', '').trim();
            // Cari baris terakhir dalam subkategori ini (sub/doc terakhir)
            let insertAfter = subkategoriRow;
            let idx = 0;
            let next = subkategoriRow.nextElementSibling;
            while(next && !next.classList.contains('subkategori-row') && !next.classList.contains('kategori-row')){
                if(next.classList.contains('sub-row')) idx++;
                insertAfter = next;
                next = next.nextElementSibling;
            }
            let subIdx = idx + 1;
            let subLabel = getSubLabel(subCatLetter, subIdx);
            let tr = document.createElement('tr');
            tr.className = 'sub-row';
            tr.innerHTML = `
                <td class="text-center text-gray-700">${subLabel}</td>
                <td colspan="6" class="bg-blue-50 text-left">
                    <input type="text" class="form-control form-control-sm sub-input border border-blue-300 rounded px-2 py-1" name="engineering_sub[]" value="Sub Baru" style="width:220px; display:inline-block;">
                    <span class="ml-2 text-xs text-gray-400">Sub</span>
                </td>
                <td colspan="2" class="text-right">
                    <button type="button" class="btn-action-primary btn-sub-add-doc mr-2">+ Tambah Dokumen</button>
                    <button type="button" class="btn-action-danger btn-remove-sub">Hapus Sub</button>
                </td>
            `;
            insertAfter.after(tr);
            hideEngineeringButtonsIfNeeded();
        }
        // Hapus Subkategori
        if(e.target.closest('.btn-eng-remove-subsub')){
            let subkategoriRow = e.target.closest('tr.subkategori-row');
            if(!subkategoriRow) return;
            let next = subkategoriRow.nextElementSibling;
            while(next && !next.classList.contains('subkategori-row') && !next.classList.contains('kategori-row')){
                let toRemove = next;
                next = next.nextElementSibling;
                toRemove.remove();
            }
            subkategoriRow.remove();
        }
        // Tambah Dokumen di bawah Sub
        if(e.target.closest('.btn-sub-add-doc')){
            let subRow = e.target.closest('tr.sub-row');
            if(subRow) {
                // Cari dokumen terakhir di sub ini
                let insertAfter = subRow;
                let docIdx = 1;
                let next = subRow.nextElementSibling;
                while(next && next.classList.contains('doc-row')){
                    insertAfter = next;
                    docIdx++;
                    next = next.nextElementSibling;
                }
                // Ambil value kategori, subkategori, sub
                let kategori = '', subkategori = '', sub = '';
                let prev = subRow.previousElementSibling;
                while(prev) {
                    if(prev.classList.contains('kategori-row')) {
                        kategori = prev.querySelector('.kategori-input')?.value || 'Master List Drawing';
                        break;
                    }
                    prev = prev.previousElementSibling;
                }
                prev = subRow.previousElementSibling;
                while(prev) {
                    if(prev.classList.contains('subkategori-row')) {
                        subkategori = prev.querySelector('.subkategori-input')?.value || '';
                        break;
                    }
                    prev = prev.previousElementSibling;
                }
                sub = subRow.querySelector('.sub-input')?.value || subRow.querySelector('td').textContent.trim();

                globalDocIndex++;

                let doc = document.createElement('tr');
                doc.className = 'doc-row';
                doc.innerHTML = `
                    <td class="text-center">${docIdx}</td>
                    <td><input type="text" class="form-control form-control-sm border border-blue-300 rounded px-2 py-1" name="engineering[${docIdx}][doc_part1]" value=""></td>
                    <td><input type="text" class="form-control form-control-sm border border-blue-300 rounded px-2 py-1" name="engineering[${docIdx}][doc_part2]" value=""></td>
                    <td><input type="text" class="form-control form-control-sm border border-blue-300 rounded px-2 py-1" name="engineering[${docIdx}][doc_part3]" value=""></td>
                    <td><input type="text" class="form-control form-control-sm border border-blue-300 rounded px-2 py-1" name="engineering[${docIdx}][doc_part4]" value=""></td>
                    <td><input type="text" class="form-control form-control-sm border border-blue-300 rounded px-2 py-1" name="engineering[${docIdx}][doc_part5]" value=""></td>
                    <td><input type="text" class="form-control form-control-sm border border-blue-300 rounded px-2 py-1" name="engineering[${docIdx}][doc_part6]" value=""></td>
                    <td>
                        <input type="text" class="form-control form-control-sm border border-blue-300 rounded px-2 py-1" name="engineering[${docIdx}][document_name]" value="">
                    </td>
                    <td>
                        <select class="form-control form-control-sm border border-blue-300 rounded px-2 py-1" name="engineering[${docIdx}][approval_status]">
                            <option>On Going</option><option>Done</option><option>Delayed</option>
                        </select>
                        <input type="hidden" name="engineering[${docIdx}][kategori]" value="${kategori}">
                        <input type="hidden" name="engineering[${docIdx}][subkategori]" value="${subkategori}">
                        <input type="hidden" name="engineering[${docIdx}][sub]" value="${sub}">
                    </td>
                `;
                insertAfter.after(doc);
                reindexEngineering();
                updateHiddenFields();
                hideEngineeringButtonsIfNeeded();
            }
        }
        // Hapus Sub
        if(e.target.closest('.btn-remove-sub')){
            let subRow = e.target.closest('tr.sub-row');
            if(!subRow) return;
            let next = subRow.nextElementSibling;
            while(next && next.classList.contains('doc-row')){
                let toRemove = next;
                next = next.nextElementSibling;
                toRemove.remove();
            }
            subRow.remove();
        }
    });

    // Tambah dokumen di bawah sub tertentu
    function addDocumentRow(subRow) {
        let docIdx = 1;
        let next = subRow.nextElementSibling;
        while(next && next.classList.contains('doc-row')){
            docIdx++;
            next = next.nextElementSibling;
        }
        let insertAfter = subRow;
        next = subRow.nextElementSibling;
        while(next && next.classList.contains('doc-row')){
            insertAfter = next;
            next = next.nextElementSibling;
        }
        // Ambil value kategori, subkategori, sub
        let kategori = '', subkategori = '', sub = '';
        let prev = subRow.previousElementSibling;
        while(prev) {
            if(prev.classList.contains('kategori-row')) {
                kategori = prev.querySelector('.kategori-input')?.value || 'Master List Drawing';
                break;
            }
            prev = prev.previousElementSibling;
        }
        prev = subRow.previousElementSibling;
        while(prev) {
            if(prev.classList.contains('subkategori-row')) {
                subkategori = prev.querySelector('.subkategori-input')?.value || '';
                break;
            }
            prev = prev.previousElementSibling;
        }
        sub = subRow.querySelector('.sub-input')?.value || subRow.querySelector('td').textContent.trim();
        
        // PERBAIKAN: Increment globalDocIndex untuk setiap dokumen baru
        globalDocIndex++;
        
        let doc = document.createElement('tr');
        doc.className = 'doc-row';
        doc.innerHTML = `
            <td class="text-center">${docIdx}</td>
            <td><input type="text" class="form-control form-control-sm border border-blue-300 rounded px-2 py-1" name="engineering[${globalDocIndex}][doc_part1]" value=""></td>
            <td><input type="text" class="form-control form-control-sm border border-blue-300 rounded px-2 py-1" name="engineering[${globalDocIndex}][doc_part2]" value=""></td>
            <td><input type="text" class="form-control form-control-sm border border-blue-300 rounded px-2 py-1" name="engineering[${globalDocIndex}][doc_part3]" value=""></td>
            <td><input type="text" class="form-control form-control-sm border border-blue-300 rounded px-2 py-1" name="engineering[${globalDocIndex}][doc_part4]" value=""></td>
            <td><input type="text" class="form-control form-control-sm border border-blue-300 rounded px-2 py-1" name="engineering[${globalDocIndex}][doc_part5]" value=""></td>
            <td><input type="text" class="form-control form-control-sm border border-blue-300 rounded px-2 py-1" name="engineering[${globalDocIndex}][doc_part6]" value=""></td>
            <td>
                <input type="text" class="form-control form-control-sm border border-blue-300 rounded px-2 py-1" name="engineering[${globalDocIndex}][document_name]" value="">
            </td>
            <td>
                <select class="form-control form-control-sm border border-blue-300 rounded px-2 py-1" name="engineering[${globalDocIndex}][approval_status]">
                    <option value="ongoing">On Going</option><option value="done">Done</option><option value="delayed">Delayed</option>
                </select>
                <input type="hidden" name="engineering[${globalDocIndex}][kategori]" value="${kategori}">
                <input type="hidden" name="engineering[${globalDocIndex}][subkategori]" value="${subkategori}">
                <input type="hidden" name="engineering[${globalDocIndex}][sub]" value="${sub}">
            </td>
        `;
        insertAfter.after(doc);
        reindexEngineering();
        // PERBAIKAN: Tambahkan update hidden fields setelah menambah dokumen
        updateHiddenFields();
    }

    // Reindex nomor dokumen di setiap sub
    function reindexEngineering() {
        let body = document.getElementById('engineeringBody');
        let allSubs = body.querySelectorAll('tr.sub-row');
        allSubs.forEach(function(subRow){
            let idx = 1;
            let next = subRow.nextElementSibling;
            while(next && next.classList.contains('doc-row')){
                next.querySelector('td').textContent = idx++;
                next = next.nextElementSibling;
            }
        });
    }

        // PERBAIKAN: Tambahkan function untuk update hidden fields kategori/subkategori/sub
    function updateHiddenFields() {
        let body = document.getElementById('engineeringBody');
        let currentKategori = '', currentSubkategori = '', currentSub = '';
        
        Array.from(body.children).forEach(row => {
            if(row.classList.contains('kategori-row')) {
                currentKategori = row.querySelector('.kategori-input')?.value || '';
            } else if(row.classList.contains('subkategori-row')) {
                currentSubkategori = row.querySelector('.subkategori-input')?.value || '';
            } else if(row.classList.contains('sub-row')) {
                currentSub = row.querySelector('.sub-input')?.value || '';
            } else if(row.classList.contains('doc-row')) {
                // Update hidden fields untuk dokumen
                let hiddenKategori = row.querySelector('input[name*="[kategori]"]');
                let hiddenSubkategori = row.querySelector('input[name*="[subkategori]"]');
                let hiddenSub = row.querySelector('input[name*="[sub]"]');
                
                if(hiddenKategori) hiddenKategori.value = currentKategori;
                if(hiddenSubkategori) hiddenSubkategori.value = currentSubkategori;
                if(hiddenSub) hiddenSub.value = currentSub;
            }
        });
    }

    // PERBAIKAN: Event listener untuk perubahan input kategori/subkategori/sub
    document.getElementById('engineeringBody').addEventListener('input', function(e) {
        if(e.target.classList.contains('kategori-input') || 
           e.target.classList.contains('subkategori-input') || 
           e.target.classList.contains('sub-input')) {
            updateHiddenFields();
        }
    });

    // --- Tombol global tambah/hapus dokumen (fallback, menambah ke sub terakhir) ---
    document.getElementById('engAddDoc').onclick = function() {
        let body = document.getElementById('engineeringBody');
        let subs = body.querySelectorAll('tr.sub-row');
        if(subs.length === 0){
            alert('Buat dulu sub di bawah subkategori sebelum menambah dokumen');
            return;
        }
        let lastSub = subs[subs.length - 1];
        addDocumentRow(lastSub);
    };
    document.getElementById('engDelDoc').onclick = function() {
        let body = document.getElementById('engineeringBody');
        let docs = body.querySelectorAll('tr.doc-row');
        if(docs.length > 0){
            docs[docs.length - 1].remove();
            reindexEngineering();
        }
    };

    // --- Scope Table Dinamis ---
    let scopesContainer = document.getElementById('scopesContainer');
    let scopeCount = 0; // Scope A sudah ada di atas

    // Handler tambah/hapus baris untuk Scope A
    document.querySelector('.scope-add-row[data-scope="A"]').onclick = function() {
        let tbody = document.querySelector('#scopeTableA tbody');
        let idx = tbody.querySelectorAll('tr').length + 1;
        let tr = document.createElement('tr');
        tr.className = 'act-row';
        tr.innerHTML = `
            <td>${idx}</td>
            <td><input type="text" class="form-control form-control-sm" name="activity[A][${idx}][location]"></td>
            <td><textarea class="form-control form-control-sm" name="activity[A][${idx}][achievement_this_week]"></textarea></td>
            <td><input type="number" step="0.01" class="form-control form-control-sm" name="activity[A][${idx}][progress]"></td>
            <td><textarea class="form-control form-control-sm" name="activity[A][${idx}][plan_next_week]"></textarea></td>
            <td>
                <select class="form-control form-control-sm" name="activity[A][${idx}][status]">
                    <option value="ongoing">On Going</option>
                    <option value="done">Done</option>
                    <option value="delayed">Delayed</option>
                </select>
            </td>
        `;
        tbody.appendChild(tr);
    };
    document.querySelector('.scope-remove-row[data-scope="A"]').onclick = function() {
        let tbody = document.querySelector('#scopeTableA tbody');
        if (tbody.children.length > 1) tbody.removeChild(tbody.lastElementChild);
    };

    // Helper for Scope Letter
    function getScopeLetter(idx) {
        return String.fromCharCode(66 + idx); // 0->B, 1->C, ...
    }

    // Tambah Scope baru (mulai dari Scope B)
    document.getElementById('addScopeBtn').onclick = function() {
        let letter = getScopeLetter(scopeCount);
        let tableId = `scopeTable${letter}`;
        let div = document.createElement('div');
        div.className = 'scope-table-wrap';
        div.innerHTML = `
            <div class="section-header" style="margin-top:12px;">Scope ${letter}</div>
            <div class="table-responsive">
                <table class="table table-bordered activity-table" id="${tableId}">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Lokasi</th>
                            <th>Capaian Minggu Ini</th>
                            <th>Progress</th>
                            <th>Rencana Minggu Depan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="act-row">
                            <td>1</td>
                            <td><input type="text" class="form-control form-control-sm" name="activity[${letter}][1][location]"></td>
                            <td><textarea class="form-control form-control-sm" name="activity[${letter}][1][achievement_this_week]"></textarea></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="activity[${letter}][1][progress]"></td>
                            <td><textarea class="form-control form-control-sm" name="activity[${letter}][1][plan_next_week]"></textarea></td>
                            <td>
                                <select class="form-control form-control-sm" name="activity[${letter}][1][status]">
                                    <option value="ongoing">On Going</option>
                                    <option value="done">Done</option>
                                    <option value="delayed">Delayed</option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="table-actions-bottom">
                    <button type="button" class="btn-action-primary scope-add-row" data-scope="${letter}">+ Tambah Baris</button>
                    <button type="button" class="btn-action-danger scope-remove-row" data-scope="${letter}">Hapus Baris</button>
                </div>
            </div>
        `;
        scopesContainer.appendChild(div);

        // Handler tambah/hapus baris untuk scope baru
        div.querySelector(`.scope-add-row[data-scope="${letter}"]`).onclick = function() {
            let tbody = div.querySelector('tbody');
            let idx = tbody.querySelectorAll('tr').length + 1;
            let tr = document.createElement('tr');
            tr.className = 'act-row';
            tr.innerHTML = `
                <td>${idx}</td>
                <td><input type="text" class="form-control form-control-sm" name="activity[${letter}][${idx}][location]"></td>
                <td><textarea class="form-control form-control-sm" name="activity[${letter}][${idx}][achievement_this_week]"></textarea></td>
                <td><input type="number" step="0.01" class="form-control form-control-sm" name="activity[${letter}][${idx}][progress]"></td>
                <td><textarea class="form-control form-control-sm" name="activity[${letter}][${idx}][plan_next_week]"></textarea></td>
                <td>
                    <select class="form-control form-control-sm" name="activity[${letter}][${idx}][status]">
                        <option value="ongoing">On Going</option>
                        <option value="done">Done</option>
                        <option value="delayed">Delayed</option>
                    </select>
                </td>
            `;
            tbody.appendChild(tr);
        };
        div.querySelector(`.scope-remove-row[data-scope="${letter}"]`).onclick = function() {
            let tbody = div.querySelector('tbody');
            if (tbody.children.length > 1) tbody.removeChild(tbody.lastElementChild);
        };

        scopeCount++;
    };

    // Hapus Scope terakhir (kecuali Scope A)
    document.getElementById('removeScopeBtn').onclick = function() {
        if (scopesContainer.children.length > 0) {
            scopesContainer.removeChild(scopesContainer.lastElementChild);
            scopeCount--;
        }
    };

    // --- Tabel Isu Dinamis ---
    // Tambahkan tabel isu di bawah scopesContainer
    let isuTableHtml = `
        <div class="section-header" style="background:#009FDF;">Isu</div>
        <div class="section-frame">
            <div class="table-responsive">
                <table class="table table-bordered" id="issueTable">
                    <thead>
                        <tr>
                            <th style="width:40px">No</th>
                            <th>Isu</th>
                            <th>Lokasi</th>
                            <th>Equipment/Part</th>
                            <th>Deskripsi</th>
                            <th style="width:120px">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="issue-row">
                            <td>1</td>
                            <td><input type="text" class="form-control form-control-sm" name="issue[1][name]"></td>
                            <td><input type="text" class="form-control form-control-sm" name="issue[1][location]"></td>
                            <td><input type="text" class="form-control form-control-sm" name="issue[1][equipment]"></td>
                            <td><input type="text" class="form-control form-control-sm" name="issue[1][description]"></td>
                            <td>
                                <select class="form-control form-control-sm" name="issue[1][status]">
                                    <option value="ongoing">On Going</option>
                                    <option value="done">Done</option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="table-actions-bottom">
                    <button type="button" class="btn-action-primary" id="issueAddRow">+ Tambah Baris</button>
                    <button type="button" class="btn-action-danger" id="issueRemoveRow">Hapus Baris</button>
                </div>
            </div>
        </div>
    `;
    // Sisipkan setelah scopesContainer
    scopesContainer.insertAdjacentHTML('afterend', isuTableHtml);

    // Fungsi tambah/hapus baris isu
    document.getElementById('issueAddRow').onclick = function() {
        let tbody = document.querySelector('#issueTable tbody');
        let idx = tbody.querySelectorAll('tr').length + 1;
        let tr = document.createElement('tr');
        tr.className = 'issue-row';
        tr.innerHTML = `
            <td>${idx}</td>
            <td><input type="text" class="form-control form-control-sm" name="issue[${idx}][name]"></td>
            <td><input type="text" class="form-control form-control-sm" name="issue[${idx}][location]"></td>
            <td><input type="text" class="form-control form-control-sm" name="issue[${idx}][equipment]"></td>
            <td><input type="text" class="form-control form-control-sm" name="issue[${idx}][description]"></td>
            <td>
                <select class="form-control form-control-sm" name="issue[${idx}][status]">
                    <option value="ongoing">On Going</option>
                    <option value="done">Done</option>
                </select>
            </td>
        `;
        tbody.appendChild(tr);
    };
    document.getElementById('issueRemoveRow').onclick = function() {
        let tbody = document.querySelector('#issueTable tbody');
        if (tbody.children.length > 1) tbody.removeChild(tbody.lastElementChild);
    };

    // Material
    document.getElementById('matAddRow').onclick = function() {
        let tbody = document.querySelector('#materialTable tbody');
        let idx = tbody.querySelectorAll('tr').length + 1;
        let tr = document.createElement('tr');
        tr.className = 'mat-row';
        tr.innerHTML = `
            <td><input type="text" class="form-control form-control-sm" name="material[${idx}][material_name]"></td>
            <td><input type="text" class="form-control form-control-sm" name="material[${idx}][po_number]"></td>
            <td><input type="date" class="form-control form-control-sm" name="material[${idx}][po_issued]"></td>
            <td><input type="date" class="form-control form-control-sm" name="material[${idx}][submit_tpg]"></td>
            <td><input type="date" class="form-control form-control-sm" name="material[${idx}][approve_tpg]"></td>
            <td><input type="date" class="form-control form-control-sm" name="material[${idx}][manufacture]"></td>
            <td><input type="date" class="form-control form-control-sm" name="material[${idx}][fat]"></td>
            <td><input type="date" class="form-control form-control-sm" name="material[${idx}][delivery]"></td>
            <td><input type="date" class="form-control form-control-sm" name="material[${idx}][material_on_site]"></td>
        `;
        tbody.appendChild(tr);
    };
    document.getElementById('matRemoveRow').onclick = function(){
        let tbody = document.querySelector('#materialTable tbody');
        if(tbody.children.length > 1) tbody.removeChild(tbody.lastElementChild);
    };

    // Service
    document.getElementById('svcAddRow').onclick = function() {
        let tbody = document.querySelector('#serviceTable tbody');
        let idx = tbody.querySelectorAll('tr').length + 1;
        let tr = document.createElement('tr');
        tr.className = 'svc-row';
        tr.innerHTML = `
            <td><input type="text" class="form-control form-control-sm" name="service[${idx}][service_name]"></td>
            <td><input type="text" class="form-control form-control-sm" name="service[${idx}][po_number]"></td>
            <td><input type="date" class="form-control form-control-sm" name="service[${idx}][po_issued]"></td>
            <td><input type="date" class="form-control form-control-sm" name="service[${idx}][mobilization]"></td>
            <td><input type="date" class="form-control form-control-sm" name="service[${idx}][field_work]"></td>
            <td><input type="date" class="form-control form-control-sm" name="service[${idx}][reporting]"></td>
        `;
        tbody.appendChild(tr);
    };
    document.getElementById('svcRemoveRow').onclick = function(){
        let tbody = document.querySelector('#serviceTable tbody');
        if(tbody.children.length > 1) tbody.removeChild(tbody.lastElementChild);
    };

    // Construction
    document.getElementById('conAddRow').onclick = function() {
        let tbody = document.querySelector('#constructionTable tbody');
        let idx = tbody.querySelectorAll('tr').length + 1;
        let tr = document.createElement('tr');
        tr.className = 'con-row';
        tr.innerHTML = `
            <td>${idx}</td>
            <td><input type="text" class="form-control form-control-sm" name="construction[${idx}][description]"></td>
            <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[${idx}][weight_factor]"></td>
            <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[${idx}][cum_last_plan]"></td>
            <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[${idx}][cum_last_actual]"></td>
            <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[${idx}][cum_last_variance]"></td>
            <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[${idx}][this_plan]"></td>
            <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[${idx}][this_actual]"></td>
            <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[${idx}][cum_now_plan]"></td>
            <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[${idx}][cum_now_actual]"></td>
            <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[${idx}][cum_now_variance]"></td>
            <td>
                <select class="form-control form-control-sm" name="construction[${idx}][status]">
                    <option value="on schedule">On Schedule</option>
                    <option value="delayed">Delayed</option>
                </select>
            </td>
        `;
        tbody.appendChild(tr);
    };

    // Dropdown proyek
    fetch('/dropdown/projects')
      .then(res => res.json())
      .then(data => {
        let select = document.getElementById('projectSelect');
        select.innerHTML = '<option value="">-- Pilih Proyek --</option>';
        data.forEach(p => {
          let opt = document.createElement('option');
          opt.value = p.id;
          opt.textContent = p.name;
          select.appendChild(opt);
        });
      });

    // Dropdown periode berdasarkan project
    document.getElementById('projectSelect').onchange = function() {
        let projectId = this.value;
        let periodSelect = document.getElementById('periodSelect');
        let projectInput = document.getElementById('projectInput');
        
        // Clear dropdown periode
        periodSelect.innerHTML = '<option value="">-- Pilih Periode --</option>';
        
        if (!projectId) return;
        
        // Auto-fill nama proyek dari dropdown selection
        let selectedProject = this.options[this.selectedIndex];
        if (selectedProject.value) {
            projectInput.value = selectedProject.textContent;
            projectInput.setAttribute('readonly', true);
            projectInput.style.backgroundColor = '#f8f9fa';
            projectInput.style.color = '#6c757d';
        }
        
        // Load periode untuk proyek yang dipilih
        fetch('/dropdown/periods/' + projectId)
            .then(res => res.json())
            .then(data => {
                data.forEach(p => {
                    let opt = document.createElement('option');
                    opt.value = p.id;
                    opt.textContent = `${p.name} (${p.start_date} - ${p.end_date})`;
                    periodSelect.appendChild(opt);
                });
            });
    };

    // Reset input nama proyek ketika dropdown dikosongkan
    document.getElementById('projectSelect').addEventListener('change', function() {
        let projectInput = document.getElementById('projectInput');
        
        if (!this.value) {
            // Jika dropdown kosong, enable input manual
            projectInput.removeAttribute('readonly');
            projectInput.style.backgroundColor = '';
            projectInput.style.color = '';
            projectInput.value = '';
            projectInput.placeholder = 'Masukkan nama proyek baru';
        }
    });

    // Clear dropdown proyek ketika user mulai mengetik nama proyek baru
    document.getElementById('projectInput').addEventListener('input', function() {
        if (this.value && !this.hasAttribute('readonly')) {
            // Reset dropdown jika user mengetik nama proyek baru
            document.getElementById('projectSelect').value = '';
            document.getElementById('periodSelect').innerHTML = '<option value="">-- Pilih Periode --</option>';
            
            // Reset date picker
            document.getElementById('startDate').value = '';
            document.getElementById('endDate').value = '';
        }
    });

    // Copy Template
    document.getElementById('copyProjectTemplateBtn').onclick = function() {
        let projectId = document.getElementById('projectSelect').value;
        if (!projectId) {
            alert('Pilih proyek terlebih dahulu!');
            return;
        }
        fetch('/project/copy-template/' + projectId)
            .then(res => res.json())
            .then(data => {
                // Tampilkan data template proyek (preview sederhana)
                let preview = document.getElementById('templatePreview');
                preview.innerHTML = `
                    <div class="p-3 border rounded bg-gray-50">
                        <b>Nama Proyek:</b> ${data.name}<br>
                        <b>Deskripsi:</b> ${data.description || '-'}<br>
                        <b>Jumlah Periode:</b> ${data.periods.length}<br>
                        <b>Jumlah Engineering:</b> ${data.engineerings.length}<br>
                        <b>Jumlah EPC:</b> ${data.epc.length}<br>
                        <b>Jumlah Procurement Material:</b> ${data.procurement_materials.length}<br>
                        <b>Jumlah Procurement Service:</b> ${data.procurement_services.length}<br>
                        <b>Jumlah Construction:</b> ${data.constructions.length}<br>
                        <b>Jumlah Aktivitas:</b> ${data.activities.length}<br>
                        <b>Jumlah Isu:</b> ${data.issues.length}<br>
                        <b>Jumlah Dokumentasi:</b> ${data.documentations.length}
                    </div>
                `;
            });
    };

    // Copy Template Periode - Updated to autofill all forms INCLUDING engineering hierarchy
    document.getElementById('copyPeriodTemplateBtn').onclick = function() {
        let periodId = document.getElementById('periodSelect').value;
        if (!periodId) {
            alert('Pilih periode terlebih dahulu!');
            return;
        }
        
        // Show loading indicator
        let btn = this;
        let originalText = btn.textContent;
        btn.textContent = 'Loading...';
        btn.disabled = true;
        
        fetch('/period/copy-template/' + periodId)
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    alert(data.message || 'Gagal memuat template');
                    return;
                }
                
                // Autofill periode info
                let periodData = data.period;
                document.getElementById('periodInput').value = periodData.name;
                document.getElementById('startDate').value = periodData.start_date;
                document.getElementById('endDate').value = periodData.end_date;
                
                // Autofill EPC Table
                if (data.epc && data.epc.length > 0) {
                    let epcTbody = document.querySelector('#epcTable tbody');
                    epcTbody.innerHTML = ''; // Clear existing rows
                    
                    let regularEpcData = data.epc.filter(epc => !epc.is_total);
                    let totalEpcData = data.epc.find(epc => epc.is_total);
                    
                    regularEpcData.forEach((epc, index) => {
                        let idx = index + 1;
                        let roman = ['','I','II','III','IV','V','VI','VII','VIII','IX','X'][idx] || idx;
                        let tr = document.createElement('tr');
                        tr.className = 'epc-row';
                        tr.innerHTML = `
                            <td>${roman}</td>
                            <td><input type="text" class="form-control form-control-sm" name="epc[${idx}][description]" value="${epc.description || ''}"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="epc[${idx}][weight_factor]" value="${epc.weight_factor || ''}"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="epc[${idx}][cum_last_plan]" value="${epc.cum_last_plan || ''}"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="epc[${idx}][cum_last_actual]" value="${epc.cum_last_actual || ''}"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="epc[${idx}][cum_last_variance]" value="${epc.cum_last_variance || ''}" readonly style="background:#f8f9fa;"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="epc[${idx}][this_plan]" value="${epc.this_plan || ''}" readonly style="background:#f8f9fa;"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="epc[${idx}][this_actual]" value="${epc.this_actual || ''}" readonly style="background:#f8f9fa;"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="epc[${idx}][cum_now_plan]" value="${epc.cum_now_plan || ''}"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="epc[${idx}][cum_now_actual]" value="${epc.cum_now_actual || ''}"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="epc[${idx}][cum_now_variance]" value="${epc.cum_now_variance || ''}" readonly style="background:#f8f9fa;"></td>
                            <td>
                                <select class="form-control form-control-sm" name="epc[${idx}][status]">
                                    <option value="on schedule" ${epc.status === 'on schedule' ? 'selected' : ''}>On Schedule</option>
                                    <option value="delayed" ${epc.status === 'delayed' ? 'selected' : ''}>Delayed</option>
                                </select>
                            </td>
                        `;
                        epcTbody.appendChild(tr);
                    });

                    // total row
                    let totalRow = document.createElement('tr');
                    totalRow.className = 'epc-total-row';
                    totalRow.style.background = '#f8f9fa';
                    totalRow.style.fontWeight = 'bold';
                    totalRow.innerHTML = `
                        <td></td>
                        <td><input type="text" class="form-control form-control-sm" name="epc[total][description]" value="Total" readonly style="background:#e9ecef; font-weight:bold;"></td>
                        <td><input type="number" step="0.01" class="form-control form-control-sm epc-total-weight" name="epc[total][weight_factor]" value="${totalEpcData ? totalEpcData.weight_factor : '0.00'}" readonly style="background:#e9ecef;"></td>
                        <td><input type="number" step="0.01" class="form-control form-control-sm epc-total-cum-last-plan" name="epc[total][cum_last_plan]" value="${totalEpcData ? totalEpcData.cum_last_plan : '0.00'}" readonly style="background:#e9ecef;"></td>
                        <td><input type="number" step="0.01" class="form-control form-control-sm epc-total-cum-last-actual" name="epc[total][cum_last_actual]" value="${totalEpcData ? totalEpcData.cum_last_actual : '0.00'}" readonly style="background:#e9ecef;"></td>
                        <td><input type="number" step="0.01" class="form-control form-control-sm epc-total-cum-last-variance" name="epc[total][cum_last_variance]" value="${totalEpcData ? totalEpcData.cum_last_variance : '0.00'}" readonly style="background:#e9ecef;"></td>
                        <td><input type="number" step="0.01" class="form-control form-control-sm epc-total-this-plan" name="epc[total][this_plan]" value="${totalEpcData ? totalEpcData.this_plan : '0.00'}" readonly style="background:#e9ecef;"></td>
                        <td><input type="number" step="0.01" class="form-control form-control-sm epc-total-this-actual" name="epc[total][this_actual]" value="${totalEpcData ? totalEpcData.this_actual : '0.00'}" readonly style="background:#e9ecef;"></td>
                        <td><input type="number" step="0.01" class="form-control form-control-sm epc-total-cum-now-plan" name="epc[total][cum_now_plan]" value="${totalEpcData ? totalEpcData.cum_now_plan : '0.00'}" readonly style="background:#e9ecef;"></td>
                        <td><input type="number" step="0.01" class="form-control form-control-sm epc-total-cum-now-actual" name="epc[total][cum_now_actual]" value="${totalEpcData ? totalEpcData.cum_now_actual : '0.00'}" readonly style="background:#e9ecef;"></td>
                        <td><input type="number" step="0.01" class="form-control form-control-sm epc-total-cum-now-variance" name="epc[total][cum_now_variance]" value="${totalEpcData ? totalEpcData.cum_now_variance : '0.00'}" readonly style="background:#e9ecef;"></td>
                        <td>
                            <select class="form-control form-control-sm epc-total-status" disabled style="background:#e9ecef;">
                                <option value="on schedule" ${(totalEpcData && totalEpcData.status === 'on schedule') ? 'selected' : ''}>On Schedule</option>
                                <option value="delayed" ${(totalEpcData && totalEpcData.status === 'delayed') ? 'selected' : ''}>Delayed</option>
                            </select>
                            <input type="hidden" name="epc[total][status]" value="${totalEpcData ? totalEpcData.status : 'on schedule'}">
                            <input type="hidden" name="epc[total][is_total]" value="1">
                        </td>
                    `;
                    epcTbody.appendChild(totalRow);
                    
                    // Recalculate totals setelah load template
                    calculateTotalRow('epcTable');
                }
                
                // NEW: Autofill Engineering Table with hierarchy structure
                if (data.engineering && data.engineering.length > 0) {
                    let engBody = document.getElementById('engineeringBody');
                    engBody.innerHTML = ''; // Clear existing rows
                    let globalDocIndex = 1000; // Start dari index tinggi untuk avoid conflict
                    
                    data.engineering.forEach((kategoriData, katIndex) => {
                        // Add kategori row
                        let kategoriRow = document.createElement('tr');
                        kategoriRow.className = 'kategori-row';
                        kategoriRow.innerHTML = `
                            <td></td>
                            <td colspan="6" class="font-bold text-left bg-blue-50">
                                <input type="text" class="form-control form-control-sm kategori-input border border-blue-300 rounded px-2 py-1" name="engineering_kategori[]" value="${kategoriData.name}" style="width:220px; display:inline-block;">
                                <span class="ml-2 text-xs text-gray-400">Kategori</span>
                            </td>
                            <td colspan="2" class="text-right">
                                <button type="button" class="btn-action-primary btn-master-add-sub mr-2">+ Tambah Subkategori</button>
                                <button type="button" class="btn-action-danger btn-master-remove-sub">Hapus Kategori</button>
                            </td>
                        `;
                        engBody.appendChild(kategoriRow);
                        
                        // Add subkategori rows
                        kategoriData.subkategories.forEach((subkategoriData, subkatIndex) => {
                            let subLetter = String.fromCharCode(65 + subkatIndex); // A, B, C...
                            let subkategoriRow = document.createElement('tr');
                            subkategoriRow.className = 'subkategori-row';
                            subkategoriRow.innerHTML = `
                                <td class="font-bold text-blue-700 text-center">${subLetter}</td>
                                <td colspan="6" class="font-bold text-blue-700 text-left bg-blue-100">
                                    <input type="text" class="form-control form-control-sm subkategori-input border border-blue-300 rounded px-2 py-1" name="engineering_subkategori[]" value="${subkategoriData.name}" style="width:220px; display:inline-block;">
                                    <span class="ml-2 text-xs text-gray-400">Subkategori</span>
                                </td>
                                <td colspan="2" class="text-right">
                                    <button type="button" class="btn-action-primary btn-eng-add-subsub mr-2">+ Tambah Sub</button>
                                    <button type="button" class="btn-action-danger btn-eng-remove-subsub">Hapus Subkategori</button>
                                </td>
                            `;
                            engBody.appendChild(subkategoriRow);
                            
                            // Add sub rows
                            subkategoriData.subs.forEach((subData, subIndex) => {
                                let subLabel = `${subLetter}.${subIndex + 1}`;
                                let subRow = document.createElement('tr');
                                subRow.className = 'sub-row';
                                subRow.innerHTML = `
                                    <td class="text-center text-gray-700">${subLabel}</td>
                                    <td colspan="6" class="bg-blue-50 text-left">
                                        <input type="text" class="form-control form-control-sm sub-input border border-blue-300 rounded px-2 py-1" name="engineering_sub[]" value="${subData.name}" style="width:220px; display:inline-block;">
                                        <span class="ml-2 text-xs text-gray-400">Sub</span>
                                    </td>
                                    <td colspan="2" class="text-right">
                                        <button type="button" class="btn-action-primary btn-sub-add-doc mr-2">+ Tambah Dokumen</button>
                                        <button type="button" class="btn-action-danger btn-remove-sub">Hapus Sub</button>
                                    </td>
                                `;
                                engBody.appendChild(subRow);
                                
                                // Add document rows
                                subData.documents.forEach((doc, docIndex) => {
                                    globalDocIndex++;
                                    let docRow = document.createElement('tr');
                                    docRow.className = 'doc-row';
                                    docRow.innerHTML = `
                                        <td class="text-center">${docIndex + 1}</td>
                                        <td><input type="text" class="form-control form-control-sm border border-blue-300 rounded px-2 py-1" name="engineering[${globalDocIndex}][doc_part1]" value="${doc.doc_part1 || ''}"></td>
                                        <td><input type="text" class="form-control form-control-sm border border-blue-300 rounded px-2 py-1" name="engineering[${globalDocIndex}][doc_part2]" value="${doc.doc_part2 || ''}"></td>
                                        <td><input type="text" class="form-control form-control-sm border border-blue-300 rounded px-2 py-1" name="engineering[${globalDocIndex}][doc_part3]" value="${doc.doc_part3 || ''}"></td>
                                        <td><input type="text" class="form-control form-control-sm border border-blue-300 rounded px-2 py-1" name="engineering[${globalDocIndex}][doc_part4]" value="${doc.doc_part4 || ''}"></td>
                                        <td><input type="text" class="form-control form-control-sm border border-blue-300 rounded px-2 py-1" name="engineering[${globalDocIndex}][doc_part5]" value="${doc.doc_part5 || ''}"></td>
                                        <td><input type="text" class="form-control form-control-sm border border-blue-300 rounded px-2 py-1" name="engineering[${globalDocIndex}][doc_part6]" value="${doc.doc_part6 || ''}"></td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm border border-blue-300 rounded px-2 py-1" name="engineering[${globalDocIndex}][document_name]" value="${doc.document_name || ''}">
                                        </td>
                                        <td>
                                            <select class="form-control form-control-sm border border-blue-300 rounded px-2 py-1" name="engineering[${globalDocIndex}][approval_status]">
                                                <option value="ongoing" ${doc.approval_status === 'ongoing' ? 'selected' : ''}>On Going</option>
                                                <option value="done" ${doc.approval_status === 'done' ? 'selected' : ''}>Done</option>
                                                <option value="delayed" ${doc.approval_status === 'delayed' ? 'selected' : ''}>Delayed</option>
                                            </select>
                                            <input type="hidden" name="engineering[${globalDocIndex}][kategori]" value="${doc.kategori}">
                                            <input type="hidden" name="engineering[${globalDocIndex}][subkategori]" value="${doc.subkategori}">
                                            <input type="hidden" name="engineering[${globalDocIndex}][sub]" value="${doc.sub}">
                                        </td>
                                    `;
                                    engBody.appendChild(docRow);
                                });
                            });
                        });
                    });
                    hideEngineeringButtonsIfNeeded();
                }
                
                // Autofill Procurement Material Table
                if (data.materials && data.materials.length > 0) {
                    let matTbody = document.querySelector('#materialTable tbody');
                    matTbody.innerHTML = ''; // Clear existing rows
                    
                    data.materials.forEach((mat, index) => {
                        let idx = index + 1;
                        let tr = document.createElement('tr');
                        tr.className = 'mat-row';
                        tr.innerHTML = `
                            <td><input type="text" class="form-control form-control-sm" name="material[${idx}][material_name]" value="${mat.material_name || ''}"></td>
                            <td><input type="text" class="form-control form-control-sm" name="material[${idx}][po_number]" value="${mat.po_number || ''}"></td>
                            <td><input type="date" class="form-control form-control-sm" name="material[${idx}][po_issued]" value="${mat.po_issued || ''}"></td>
                            <td><input type="date" class="form-control form-control-sm" name="material[${idx}][submit_tpg]" value="${mat.submit_tpg || ''}"></td>
                            <td><input type="date" class="form-control form-control-sm" name="material[${idx}][approve_tpg]" value="${mat.approve_tpg || ''}"></td>
                            <td><input type="date" class="form-control form-control-sm" name="material[${idx}][manufacture]" value="${mat.manufacture || ''}"></td>
                            <td><input type="date" class="form-control form-control-sm" name="material[${idx}][fat]" value="${mat.fat || ''}"></td>
                            <td><input type="date" class="form-control form-control-sm" name="material[${idx}][delivery]" value="${mat.delivery || ''}"></td>
                            <td><input type="date" class="form-control form-control-sm" name="material[${idx}][material_on_site]" value="${mat.material_on_site || ''}"></td>
                        `;
                        matTbody.appendChild(tr);
                    });
                }
                
                // Autofill Procurement Service Table
                if (data.services && data.services.length > 0) {
                    let svcTbody = document.querySelector('#serviceTable tbody');
                    svcTbody.innerHTML = ''; // Clear existing rows
                    
                    data.services.forEach((svc, index) => {
                        let idx = index + 1;
                        let tr = document.createElement('tr');
                        tr.className = 'svc-row';
                        tr.innerHTML = `
                            <td><input type="text" class="form-control form-control-sm" name="service[${idx}][service_name]" value="${svc.service_name || ''}"></td>
                            <td><input type="text" class="form-control form-control-sm" name="service[${idx}][po_number]" value="${svc.po_number || ''}"></td>
                            <td><input type="date" class="form-control form-control-sm" name="service[${idx}][po_issued]" value="${svc.po_issued || ''}"></td>
                            <td><input type="date" class="form-control form-control-sm" name="service[${idx}][mobilization]" value="${svc.mobilization || ''}"></td>
                            <td><input type="date" class="form-control form-control-sm" name="service[${idx}][field_work]" value="${svc.field_work || ''}"></td>
                            <td><input type="date" class="form-control form-control-sm" name="service[${idx}][reporting]" value="${svc.reporting || ''}"></td>
                        `;
                        svcTbody.appendChild(tr);
                    });
                }
                
                // Autofill Construction Table
                if (data.constructions && data.constructions.length > 0) {
                    let conTbody = document.querySelector('#constructionTable tbody');
                    conTbody.innerHTML = ''; // Clear existing rows
                    
                    let regularConData = data.constructions.filter(con => !con.is_total);
                    let totalConData = data.constructions.find(con => con.is_total);

                    regularConData.forEach((con, index) => {
                        let idx = index + 1;
                        let tr = document.createElement('tr');
                        tr.className = 'con-row';
                        tr.innerHTML = `
                            <td>${idx}</td>
                            <td><input type="text" class="form-control form-control-sm" name="construction[${idx}][description]" value="${con.description || ''}"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[${idx}][weight_factor]" value="${con.weight_factor || ''}"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[${idx}][cum_last_plan]" value="${con.cum_last_plan || ''}"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[${idx}][cum_last_actual]" value="${con.cum_last_actual || ''}"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[${idx}][cum_last_variance]" value="${con.cum_last_variance || ''}" readonly style="background:#f8f9fa;"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[${idx}][this_plan]" value="${con.this_plan || ''}" readonly style="background:#f8f9fa;"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[${idx}][this_actual]" value="${con.this_actual || ''}" readonly style="background:#f8f9fa;"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[${idx}][cum_now_plan]" value="${con.cum_now_plan || ''}"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[${idx}][cum_now_actual]" value="${con.cum_now_actual || ''}" ></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[${idx}][cum_now_variance]" value="${con.cum_now_variance || ''}" readonly style="background:#f8f9fa;"></td>
                            <td>
                                <select class="form-control form-control-sm" name="construction[${idx}][status]">
                                    <option value="on schedule" ${con.status === 'on schedule' ? 'selected' : ''}>On Schedule</option>
                                    <option value="delayed" ${con.status === 'delayed' ? 'selected' : ''}>Delayed</option>
                                </select>
                            </td>
                        `;
                        conTbody.appendChild(tr);
                    });

                    // Autofill Construction Table
                    let totalRow = document.createElement('tr');
                    totalRow.className = 'con-total-row';
                    totalRow.style.background = '#f8f9fa';
                    totalRow.style.fontWeight = 'bold';
                    totalRow.innerHTML = `
                        <td style="background:#e9ecef;"></td>
                        <td><input type="text" class="form-control form-control-sm" name="construction[total][description]" value="Total" readonly style="background:#e9ecef; font-weight:bold;"></td>
                        <td><input type="number" step="0.01" class="form-control form-control-sm con-total-weight" name="construction[total][weight_factor]" value="${totalConData ? totalConData.weight_factor : '0.00'}" readonly style="background:#e9ecef;"></td>
                        <td><input type="number" step="0.01" class="form-control form-control-sm con-total-cum-last-plan" name="construction[total][cum_last_plan]" value="${totalConData ? totalConData.cum_last_plan : '0.00'}" readonly style="background:#e9ecef;"></td>
                        <td><input type="number" step="0.01" class="form-control form-control-sm con-total-cum-last-actual" name="construction[total][cum_last_actual]" value="${totalConData ? totalConData.cum_last_actual : '0.00'}" readonly style="background:#e9ecef;"></td>
                        <td><input type="number" step="0.01" class="form-control form-control-sm con-total-cum-last-variance" name="construction[total][cum_last_variance]" value="${totalConData ? totalConData.cum_last_variance : '0.00'}" readonly style="background:#e9ecef;"></td>
                        <td><input type="number" step="0.01" class="form-control form-control-sm con-total-this-plan" name="construction[total][this_plan]" value="${totalConData ? totalConData.this_plan : '0.00'}" readonly style="background:#e9ecef;"></td>
                        <td><input type="number" step="0.01" class="form-control form-control-sm con-total-this-actual" name="construction[total][this_actual]" value="${totalConData ? totalConData.this_actual : '0.00'}" readonly style="background:#e9ecef;"></td>
                        <td><input type="number" step="0.01" class="form-control form-control-sm con-total-cum-now-plan" name="construction[total][cum_now_plan]" value="${totalConData ? totalConData.cum_now_plan : '0.00'}" readonly style="background:#e9ecef;"></td>
                        <td><input type="number" step="0.01" class="form-control form-control-sm con-total-cum-now-actual" name="construction[total][cum_now_actual]" value="${totalConData ? totalConData.cum_now_actual : '0.00'}" readonly style="background:#e9ecef;"></td>
                        <td><input type="number" step="0.01" class="form-control form-control-sm con-total-cum-now-variance" name="construction[total][cum_now_variance]" value="${totalConData ? totalConData.cum_now_variance : '0.00'}" readonly style="background:#e9ecef;"></td>
                        <td>
                            <select class="form-control form-control-sm con-total-status" disabled style="background:#e9ecef;">
                                <option value="on schedule" ${(totalConData && totalConData.status === 'on schedule') ? 'selected' : ''}>On Schedule</option>
                                <option value="delayed" ${(totalConData && totalConData.status === 'delayed') ? 'selected' : ''}>Delayed</option>
                            </select>
                            <input type="hidden" name="construction[total][status]" value="${totalConData ? totalConData.status : 'on schedule'}">
                            <input type="hidden" name="construction[total][is_total]" value="1">
                        </td>
                    `;
                    conTbody.appendChild(totalRow);
                    
                    // Recalculate totals setelah load template
                    calculateTotalRow('constructionTable');
                }
                
                // Autofill Activities Tables
                if (data.activities) {
                    // Clear existing scope tables (except scope A)
                    scopesContainer.innerHTML = '';
                    scopeCount = 0;
                    
                    // Clear scope A table first
                    let scopeATbody = document.querySelector('#scopeTableA tbody');
                    scopeATbody.innerHTML = '';
                    
                    Object.keys(data.activities).forEach(scope => {
                        let activities = data.activities[scope];
                        
                        if (scope === 'A') {
                            // Fill scope A table
                            activities.forEach((act, index) => {
                                let idx = index + 1;
                                let tr = document.createElement('tr');
                                tr.className = 'act-row';
                                tr.innerHTML = `
                                    <td>${idx}</td>
                                    <td><input type="text" class="form-control form-control-sm" name="activity[A][${idx}][location]" value="${act.location || ''}"></td>
                                    <td><textarea class="form-control form-control-sm" name="activity[A][${idx}][achievement_this_week]">${act.achievement_this_week || ''}</textarea></td>
                                    <td><input type="number" step="0.01" class="form-control form-control-sm" name="activity[A][${idx}][progress]" value="${act.progress || ''}"></td>
                                    <td><textarea class="form-control form-control-sm" name="activity[A][${idx}][plan_next_week]">${act.plan_next_week || ''}</textarea></td>
                                    <td>
                                        <select class="form-control form-control-sm" name="activity[A][${idx}][status]">
                                            <option value="ongoing" ${act.status === 'ongoing' ? 'selected' : ''}>On Going</option>
                                            <option value="done" ${act.status === 'done' ? 'selected' : ''}>Done</option>
                                            <option value="delayed" ${act.status === 'delayed' ? 'selected' : ''}>Delayed</option>
                                        </select>
                                    </td>
                                `;
                                scopeATbody.appendChild(tr);
                            });
                        } else {
                            // Create new scope tables for B, C, etc.
                            let tableId = `scopeTable${scope}`;
                            let div = document.createElement('div');
                            div.className = 'scope-table-wrap';
                            div.innerHTML = `
                                <div class="section-header" style="margin-top:12px;">Scope ${scope}</div>
                                <div class="table-responsive">
                                    <table class="table table-bordered activity-table" id="${tableId}">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Lokasi</th>
                                                <th>Capaian Minggu Ini</th>
                                                <th>Progress</th>
                                                <th>Rencana Minggu Depan</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                    <div class="table-actions-bottom">
                                        <button type="button" class="btn-action-primary scope-add-row" data-scope="${scope}">+ Tambah Baris</button>
                                        <button type="button" class="btn-action-danger scope-remove-row" data-scope="${scope}">Hapus Baris</button>
                                    </div>
                                </div>
                            `;
                            scopesContainer.appendChild(div);
                            
                            // Fill activities for this scope
                            let tbody = div.querySelector('tbody');
                            activities.forEach((act, index) => {
                                let idx = index + 1;
                                let tr = document.createElement('tr');
                                tr.className = 'act-row';
                                tr.innerHTML = `
                                    <td>${idx}</td>
                                    <td><input type="text" class="form-control form-control-sm" name="activity[${scope}][${idx}][location]" value="${act.location || ''}"></td>
                                    <td><textarea class="form-control form-control-sm" name="activity[${scope}][${idx}][achievement_this_week]">${act.achievement_this_week || ''}</textarea></td>
                                    <td><input type="number" step="0.01" class="form-control form-control-sm" name="activity[${scope}][${idx}][progress]" value="${act.progress || ''}"></td>
                                    <td><textarea class="form-control form-control-sm" name="activity[${scope}][${idx}][plan_next_week]">${act.plan_next_week || ''}</textarea></td>
                                    <td>
                                        <select class="form-control form-control-sm" name="activity[${scope}][${idx}][status]">
                                            <option value="ongoing" ${act.status === 'ongoing' ? 'selected' : ''}>On Going</option>
                                            <option value="done" ${act.status === 'done' ? 'selected' : ''}>Done</option>
                                            <option value="delayed" ${act.status === 'delayed' ? 'selected' : ''}>Delayed</option>
                                        </select>
                                    </td>
                                `;
                                tbody.appendChild(tr);
                            });
                            
                            // Add event handlers for new scope buttons
                            div.querySelector(`.scope-add-row[data-scope="${scope}"]`).onclick = function() {
                                let tbody = div.querySelector('tbody');
                                let idx = tbody.querySelectorAll('tr').length + 1;
                                let tr = document.createElement('tr');
                                tr.className = 'act-row';
                                tr.innerHTML = `
                                    <td>${idx}</td>
                                    <td><input type="text" class="form-control form-control-sm" name="activity[${scope}][${idx}][location]"></td>
                                    <td><textarea class="form-control form-control-sm" name="activity[${scope}][${idx}][achievement_this_week]"></textarea></td>
                                    <td><input type="number" step="0.01" class="form-control form-control-sm" name="activity[${scope}][${idx}][progress]"></td>
                                    <td><textarea class="form-control form-control-sm" name="activity[${scope}][${idx}][plan_next_week]"></textarea></td>
                                    <td>
                                        <select class="form-control form-control-sm" name="activity[${scope}][${idx}][status]">
                                            <option value="ongoing">On Going</option>
                                            <option value="done">Done</option>
                                            <option value="delayed">Delayed</option>
                                        </select>
                                    </td>
                                `;
                                tbody.appendChild(tr);
                            };
                            div.querySelector(`.scope-remove-row[data-scope="${scope}"]`).onclick = function() {
                                let tbody = div.querySelector('tbody');
                                if (tbody.children.length > 1) tbody.removeChild(tbody.lastElementChild);
                            };
                            
                            // Update scope count
                            let scopeLetter = scope.charCodeAt(0) - 65; // A=0, B=1, C=2...
                            if (scopeLetter > scopeCount) scopeCount = scopeLetter;
                        }
                    });
                }
                
                // Autofill Issues Table
                if (data.issues && data.issues.length > 0) {
                    let issueTbody = document.querySelector('#issueTable tbody');
                    issueTbody.innerHTML = ''; // Clear existing rows
                    
                    data.issues.forEach((issue, index) => {
                        let idx = index + 1;
                        let tr = document.createElement('tr');
                        tr.className = 'issue-row';
                        tr.innerHTML = `
                            <td>${idx}</td>
                            <td><input type="text" class="form-control form-control-sm" name="issue[${idx}][name]" value="${issue.name || ''}"></td>
                            <td><input type="text" class="form-control form-control-sm" name="issue[${idx}][location]" value="${issue.location || ''}"></td>
                            <td><input type="text" class="form-control form-control-sm" name="issue[${idx}][equipment]" value="${issue.equipment || ''}"></td>
                            <td><input type="text" class="form-control form-control-sm" name="issue[${idx}][description]" value="${issue.description || ''}"></td>
                            <td>
                                <select class="form-control form-control-sm" name="issue[${idx}][status]">
                                    <option value="ongoing" ${issue.status === 'ongoing' ? 'selected' : ''}>On Going</option>
                                    <option value="done" ${issue.status === 'done' ? 'selected' : ''}>Done</option>
                                </select>
                            </td>
                        `;
                        issueTbody.appendChild(tr);
                    });
                }
                
                // Show success message
                let preview = document.getElementById('templatePreview');
                preview.innerHTML = `
                    <div class="p-3 border rounded bg-green-50 border-green-300">
                        <div class="text-green-700 font-semibold mb-2">✓ Template berhasil dimuat!</div>
                        <div class="text-sm text-green-600">
                            Semua form termasuk struktur Engineering (kategori, subkategori, sub, dan dokumen) telah diisi dengan data dari periode "<strong>${periodData.name}</strong>". 
                            Anda dapat memodifikasi data sebelum menyimpan.
                        </div>
                    </div>
                `;
                
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal memuat template periode');
            })
            .finally(() => {
                // Reset button
                btn.textContent = originalText;
                btn.disabled = false;
            });
    };

    const btnSaveDraft = document.getElementById('btnSaveDraft');
    if (btnSaveDraft) {
        btnSaveDraft.addEventListener('click', function(){
            let actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'save';
            document.getElementById('projectForm').appendChild(actionInput);
        
            document.getElementById('projectForm').submit();
        });
    }

    const btnSubmit = document.getElementById('btnSubmit');
    if (btnSubmit) {
        btnSubmit.addEventListener('click', function(e){
            e.preventDefault();
            openSubmitModal();
        });
    }

    window.openSubmitModal = function() {
        document.getElementById('submitModal').style.display = 'block';
        document.body.style.overflow = 'hidden'; // Prevent body scroll
    };

    window.closeSubmitModal = function() {
        document.getElementById('submitModal').style.display = 'none';
        document.body.style.overflow = 'auto'; // Restore body scroll
    };

    window.confirmSubmit = function() {

        const confirmBtn = document.getElementById('confirmSubmitBtn');
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Submitting...';

        let actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'submit';
        document.getElementById('projectForm').appendChild(actionInput);
    
        document.getElementById('projectForm').submit();
    };

    window.addEventListener('click', function(e) {
        const modal = document.getElementById('submitModal');
        if (e.target === modal) {
            closeSubmitModal();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('submitModal');
            if (modal.style.display === 'block') {
                closeSubmitModal();
            }
        }
    });

    function calculateRow(row) {
        const inputs = row.querySelectorAll('input[type="number"]');
        const statusSelect = row.querySelector('select');
        
        let values = {};
        inputs.forEach(input => {
            const name = input.name;
            const value = parseFloat(input.value) || 0;
            
            if (name.includes('cum_last_plan')) values.cum_last_plan = value;
            else if (name.includes('cum_last_actual')) values.cum_last_actual = value;
            else if (name.includes('cum_last_variance')) values.cum_last_variance_input = input;
            else if (name.includes('this_plan')) values.this_plan_input = input;
            else if (name.includes('this_actual')) values.this_actual_input = input;
            else if (name.includes('cum_now_plan')) values.cum_now_plan = value;
            else if (name.includes('cum_now_actual')) values.cum_now_actual = value;
            else if (name.includes('cum_now_variance')) values.cum_now_variance_input = input;
        });

        const cum_last_variance = values.cum_last_actual - values.cum_last_plan;
        if (values.cum_last_variance_input) {
            values.cum_last_variance_input.value = cum_last_variance.toFixed(2);
        }

        const this_plan = values.cum_now_plan - values.cum_last_plan;
        if (values.this_plan_input) {
            values.this_plan_input.value = this_plan.toFixed(2);
        }

        const this_actual = values.cum_now_actual - values.cum_last_actual;
        if (values.this_actual_input) {
            values.this_actual_input.value = this_actual.toFixed(2);
        }

        const cum_now_variance = values.cum_now_actual - values.cum_now_plan;
        if (values.cum_now_variance_input) {
            values.cum_now_variance_input.value = cum_now_variance.toFixed(2);
        }

        // Auto-set status based on variance (untuk row individual, bukan total)
        if (statusSelect && !statusSelect.disabled) {
            if (cum_last_variance < 0 || cum_now_variance < 0) {
                statusSelect.value = 'delayed';
            } else {
                statusSelect.value = 'on schedule';
            }
        }
    }

    function calculateTotalRow(tableId) {
        const table = document.getElementById(tableId);
        if (!table) return;

        const prefix = tableId === 'epcTable' ? 'epc' : 'con';
        const regularRows = table.querySelectorAll(`.${prefix}-row:not(.${prefix}-total-row)`);
        
        let totals = {
            weight_factor: 0,
            cum_last_plan: 0,
            cum_last_actual: 0,
            cum_last_variance: 0,
            this_plan: 0,
            this_actual: 0,
            cum_now_plan: 0,
            cum_now_actual: 0,
            cum_now_variance: 0
        };

        // Sum all regular rows
        regularRows.forEach(row => {
            const inputs = row.querySelectorAll('input[type="number"]');
            inputs.forEach(input => {
                const name = input.name;
                const value = parseFloat(input.value) || 0;
                
                if (name.includes('weight_factor')) totals.weight_factor += value;
                else if (name.includes('cum_last_plan')) totals.cum_last_plan += value;
                else if (name.includes('cum_last_actual')) totals.cum_last_actual += value;
                else if (name.includes('cum_last_variance')) totals.cum_last_variance += value;
                else if (name.includes('this_plan')) totals.this_plan += value;
                else if (name.includes('this_actual')) totals.this_actual += value;
                else if (name.includes('cum_now_plan')) totals.cum_now_plan += value;
                else if (name.includes('cum_now_actual')) totals.cum_now_actual += value;
                else if (name.includes('cum_now_variance')) totals.cum_now_variance += value;
            });
        });

        // Update total row inputs
        const totalWeightInput = table.querySelector(`.${prefix}-total-weight`);
        const totalCumLastPlanInput = table.querySelector(`.${prefix}-total-cum-last-plan`);
        const totalCumLastActualInput = table.querySelector(`.${prefix}-total-cum-last-actual`);
        const totalCumLastVarianceInput = table.querySelector(`.${prefix}-total-cum-last-variance`);
        const totalThisPlanInput = table.querySelector(`.${prefix}-total-this-plan`);
        const totalThisActualInput = table.querySelector(`.${prefix}-total-this-actual`);
        const totalCumNowPlanInput = table.querySelector(`.${prefix}-total-cum-now-plan`);
        const totalCumNowActualInput = table.querySelector(`.${prefix}-total-cum-now-actual`);
        const totalCumNowVarianceInput = table.querySelector(`.${prefix}-total-cum-now-variance`);
        const totalStatusSelect = table.querySelector(`.${prefix}-total-status`);

        if (totalWeightInput) totalWeightInput.value = totals.weight_factor.toFixed(2);
        if (totalCumLastPlanInput) totalCumLastPlanInput.value = totals.cum_last_plan.toFixed(2);
        if (totalCumLastActualInput) totalCumLastActualInput.value = totals.cum_last_actual.toFixed(2);
        if (totalCumLastVarianceInput) totalCumLastVarianceInput.value = totals.cum_last_variance.toFixed(2);
        if (totalThisPlanInput) totalThisPlanInput.value = totals.this_plan.toFixed(2);
        if (totalThisActualInput) totalThisActualInput.value = totals.this_actual.toFixed(2);
        if (totalCumNowPlanInput) totalCumNowPlanInput.value = totals.cum_now_plan.toFixed(2);
        if (totalCumNowActualInput) totalCumNowActualInput.value = totals.cum_now_actual.toFixed(2);
        if (totalCumNowVarianceInput) totalCumNowVarianceInput.value = totals.cum_now_variance.toFixed(2);

        // Set status based on total variances dengan hidden input
        if (totalStatusSelect) {
            // Tentukan status berdasarkan variance
            let statusValue = 'on schedule';
            if (totals.cum_last_variance < 0 || totals.cum_now_variance < 0) {
                statusValue = 'delayed';
            }
            
            // Update visual select (disabled)
            totalStatusSelect.value = statusValue;
            
            // Update hidden input untuk mengirim ke server
            const totalRow = totalStatusSelect.closest('tr');
            let hiddenStatusInput = totalRow.querySelector(`input[name="${prefix}[total][status]"]`);
            
            if (hiddenStatusInput) {
                hiddenStatusInput.value = statusValue;
            }
            
            console.log(`Total ${prefix} status set to: ${statusValue}`); // Debug log
        }
    }

    // Enhanced setup for auto-calculation with total calculation
    function setupAutoCalculation(tableId) {
        const table = document.getElementById(tableId);
        if (!table) return;

        table.addEventListener('input', function(e) {
            const input = e.target;
            if (input.type === 'number') {
                const row = input.closest('tr');
                const prefix = tableId === 'epcTable' ? 'epc' : 'con';
                
                // Skip if this is the total row
                if (row && row.classList.contains(`${prefix}-total-row`)) return;
                
                if (row && (row.classList.contains('epc-row') || row.classList.contains('con-row'))) {
                    calculateRow(row);
                    // Calculate total after individual row calculation
                    setTimeout(() => calculateTotalRow(tableId), 10);
                }
            }
        });

        // Calculate existing rows and totals on page load
        const prefix = tableId === 'epcTable' ? 'epc' : 'con';
        const rows = table.querySelectorAll(`.${prefix}-row:not(.${prefix}-total-row)`);
        rows.forEach(row => calculateRow(row));
        calculateTotalRow(tableId);
    }

    // Setup auto-calculation untuk kedua tabel dengan total calculation
    setupAutoCalculation('epcTable');
    setupAutoCalculation('constructionTable');

    // Update add row functions to trigger total calculation
    document.getElementById('epcAddRow').onclick = function() {
        let tbody = document.querySelector('#epcTable tbody');
        let rows = tbody.querySelectorAll('tr.epc-row:not(.epc-total-row)');
        let idx = rows.length + 1;
        let roman = ['','I','II','III','IV','V','VI','VII','VIII','IX','X'][idx] || idx;
        
        let tr = document.createElement('tr');
        tr.className = 'epc-row';
        tr.innerHTML = `
            <td>${roman}</td>
            <td><input type="text" class="form-control form-control-sm" name="epc[${idx}][description]"></td>
            <td><input type="number" step="0.01" class="form-control form-control-sm" name="epc[${idx}][weight_factor]"></td>
            <td><input type="number" step="0.01" class="form-control form-control-sm" name="epc[${idx}][cum_last_plan]"></td>
            <td><input type="number" step="0.01" class="form-control form-control-sm" name="epc[${idx}][cum_last_actual]"></td>
            <td><input type="number" step="0.01" class="form-control form-control-sm" name="epc[${idx}][cum_last_variance]" readonly style="background:#f8f9fa;"></td>
            <td><input type="number" step="0.01" class="form-control form-control-sm" name="epc[${idx}][this_plan]" readonly style="background:#f8f9fa;"></td>
            <td><input type="number" step="0.01" class="form-control form-control-sm" name="epc[${idx}][this_actual]" readonly style="background:#f8f9fa;"></td>
            <td><input type="number" step="0.01" class="form-control form-control-sm" name="epc[${idx}][cum_now_plan]"></td>
            <td><input type="number" step="0.01" class="form-control form-control-sm" name="epc[${idx}][cum_now_actual]"></td>
            <td><input type="number" step="0.01" class="form-control form-control-sm" name="epc[${idx}][cum_now_variance]" readonly style="background:#f8f9fa;"></td>
            <td>
                <select class="form-control form-control-sm" name="epc[${idx}][status]">
                    <option value="on schedule">On Schedule</option>
                    <option value="delayed">Delayed</option>
                </select>
            </td>
        `;
        
        // Insert before total row
        const totalRow = tbody.querySelector('.epc-total-row');
        if (totalRow) {
            totalRow.parentNode.insertBefore(tr, totalRow);
        } else {
            tbody.appendChild(tr);
        }
        
        calculateTotalRow('epcTable');
    };

    document.getElementById('conAddRow').onclick = function() {
        let tbody = document.querySelector('#constructionTable tbody');
        let rows = tbody.querySelectorAll('tr.con-row:not(.con-total-row)');
        let idx = rows.length + 1;
        
        let tr = document.createElement('tr');
        tr.className = 'con-row';
        tr.innerHTML = `
            <td>${idx}</td>
            <td><input type="text" class="form-control form-control-sm" name="construction[${idx}][description]"></td>
            <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[${idx}][weight_factor]"></td>
            <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[${idx}][cum_last_plan]"></td>
            <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[${idx}][cum_last_actual]"></td>
            <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[${idx}][cum_last_variance]" readonly style="background:#f8f9fa;"></td>
            <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[${idx}][this_plan]" readonly style="background:#f8f9fa;"></td>
            <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[${idx}][this_actual]" readonly style="background:#f8f9fa;"></td>
            <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[${idx}][cum_now_plan]"></td>
            <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[${idx}][cum_now_actual]"></td>
            <td><input type="number" step="0.01" class="form-control form-control-sm" name="construction[${idx}][cum_now_variance]" readonly style="background:#f8f9fa;"></td>
            <td>
                <select class="form-control form-control-sm" name="construction[${idx}][status]">
                    <option value="on schedule">On Schedule</option>
                    <option value="delayed">Delayed</option>
                </select>
            </td>
        `;
        
        // Insert before total row
        const totalRow = tbody.querySelector('.con-total-row');
        if (totalRow) {
            totalRow.parentNode.insertBefore(tr, totalRow);
        } else {
            tbody.appendChild(tr);
        }
        
        calculateTotalRow('constructionTable');
    };

    // Update remove row functions
    document.getElementById('epcRemoveRow').onclick = function(){
        let tbody = document.querySelector('#epcTable tbody');
        let regularRows = tbody.querySelectorAll('tr.epc-row:not(.epc-total-row)');
        if(regularRows.length > 1) {
            regularRows[regularRows.length - 1].remove();
            calculateTotalRow('epcTable');
        }
    };

    document.getElementById('conRemoveRow').onclick = function(){
        let tbody = document.querySelector('#constructionTable tbody');
        let regularRows = tbody.querySelectorAll('tr.con-row:not(.con-total-row)');
        if(regularRows.length > 1) {
            regularRows[regularRows.length - 1].remove();
            calculateTotalRow('constructionTable');
        }
    };

    const progressInput = document.getElementById('progress_percentage');
    const weightInput = document.getElementById('weight');
    const calculatedInput = document.getElementById('calculated_progress');
    const previewPercent = document.getElementById('preview_percentage');
    const previewBar = document.getElementById('preview_bar');
    const calcFormula = document.getElementById('calc_formula');

    function runCalculateProgress() {
        const progress = parseFloat(progressInput.value) || 0;
        const weight = parseFloat(weightInput.value) || 0;
        
        // Rumus: Progress * Weight
        // Contoh: 100% * 0.5 = 50%
        // Jika input progress user adalah 100, dan weight 0.5.
        // Hasil yang diinginkan biasanya (Value * Weight).
        // Pastikan apakah weight anda 0-1 (decimal) atau 0-100 (persen).
        // Di blade anda min=0 max=1, jadi asumsi decimal.
        
        const calculated = (progress * weight).toFixed(2);
        
        // Update calculated field
        if(calculatedInput) calculatedInput.value = calculated + '%';
        
        // Update preview
        if(previewPercent) previewPercent.textContent = calculated + '%';
        if(previewBar) previewBar.style.width = calculated + '%';
        if(calcFormula) calcFormula.textContent = `${progress.toFixed(2)}% × ${weight.toFixed(2)} = ${calculated}%`;
    }

    // Pasang Event Listener (Pengganti oninput di HTML)
    if (progressInput) {
        progressInput.addEventListener('input', runCalculateProgress);
    }
    if (weightInput) {
        weightInput.addEventListener('input', runCalculateProgress);
    }

    // Jalankan sekali saat load
    if (progressInput && weightInput) {
        runCalculateProgress();
    }
});

