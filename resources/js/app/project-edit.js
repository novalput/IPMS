document.addEventListener('DOMContentLoaded', function() {
    // Ambil data dari window object yang kita definisikan di Blade
    let globalDocIndex = window.projectData.globalDocIndex || 1000;
    let lastScopeIdx = window.projectData.lastScopeIdx || 0;

    // --- Helper functions for letter/numbering ---
    function getLetter(idx) {
        return String.fromCharCode(65 + idx); // 0 -> A, 1 -> B, ...
    }
    function getSubLabel(subCatLetter, subIdx) {
        return `${subCatLetter}.${subIdx}`;
    }

    // --- Engineering Section ---
    let engToggleButtons = document.getElementById('engToggleButtons');
    if (engToggleButtons) {
        engToggleButtons.onclick = function(){
            let isHidden = this.dataset.hidden === "1";
            let btnSelectors = [
                '#engAddCategory', '#engAddDoc', '#engDelDoc',
                '.btn-master-add-sub', '.btn-master-remove-sub',
                '.btn-eng-add-subsub', '.btn-eng-remove-subsub',
                '.btn-sub-add-doc', '.btn-remove-sub'
            ];
            btnSelectors.forEach(sel => {
                document.querySelectorAll(sel).forEach(btn => {
                    btn.style.display = isHidden ? '' : 'none';
                });
            });
            this.textContent = isHidden ? 'Sembunyikan Tombol' : 'Tampilkan Tombol';
            this.dataset.hidden = isHidden ? "0" : "1";
        };
    }

    function hideEngineeringButtonsIfNeeded() {
        let toggleBtn = document.getElementById('engToggleButtons');
        if(toggleBtn && toggleBtn.dataset.hidden === "1") {
            let btnSelectors = [
                '.btn-master-add-sub', '.btn-master-remove-sub',
                '.btn-eng-add-subsub', '.btn-eng-remove-subsub',
                '.btn-sub-add-doc', '.btn-remove-sub'
            ];
            btnSelectors.forEach(sel => {
                document.querySelectorAll(sel).forEach(btn => {
                    btn.style.display = 'none';
                });
            });
        }
    }

    // --- Engineering Actions (Add/Remove Kategori, Sub, Doc) ---
    // (Logika insertAdjacentHTML aman di JS selama tidak ada syntax Blade {{ }})
    
    let engAddCategoryBtn = document.getElementById('engAddCategory');
    if (engAddCategoryBtn) {
        engAddCategoryBtn.onclick = function(){
            let body = document.getElementById('engineeringBody');
            let tr = document.createElement('tr');
            tr.className = 'kategori-row';
            tr.innerHTML = `
                <td></td>
                <td colspan="6" class="font-bold text-left bg-blue-50">
                    <input type="text" class="form-control form-control-sm kategori-input" name="engineering_kategori[]" value="Master List Drawing" style="width:220px; display:inline-block;">
                    <span class="ml-2 text-xs text-gray-400">Kategori</span>
                </td>
                <td colspan="2" class="text-right">
                    <button type="button" class="btn-action-primary btn-master-add-sub mr-2">+ Tambah Subkategori</button>
                    <button type="button" class="btn-action-danger btn-master-remove-sub">Hapus Kategori</button>
                </td>
            `;
            body.appendChild(tr);
            hideEngineeringButtonsIfNeeded();
        };
    }

    // Event delegation untuk Engineering Table
    let engineeringBody = document.getElementById('engineeringBody');
    if (engineeringBody) {
        engineeringBody.addEventListener('click', function(e){
            // Tambah Subkategori
            if(e.target.closest('.btn-master-add-sub')){
                let kategoriRow = e.target.closest('tr.kategori-row');
                if(!kategoriRow) return;
                
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
                        <input type="text" class="form-control form-control-sm subkategori-input" name="engineering_subkategori[]" value="Subkategori Baru" style="width:220px; display:inline-block;">
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

            // Tambah Sub (A.1, A.2...)
            if(e.target.closest('.btn-eng-add-subsub')){
                let subkategoriRow = e.target.closest('tr.subkategori-row');
                if(!subkategoriRow) return;
                let subCatLetter = subkategoriRow.querySelector('td').textContent.replace('.', '').trim();
                
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
                        <input type="text" class="form-control form-control-sm sub-input" name="engineering_sub[]" value="Sub Baru" style="width:220px; display:inline-block;">
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

            // Tambah Dokumen via Sub Row
            if(e.target.closest('.btn-sub-add-doc')){
                let subRow = e.target.closest('tr.sub-row');
                if(subRow) {
                    addDocumentRow(subRow);
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
        
        // Listen input change untuk update hidden fields
        engineeringBody.addEventListener('input', function(e) {
            if(e.target.classList.contains('kategori-input') || 
               e.target.classList.contains('subkategori-input') || 
               e.target.classList.contains('sub-input')) {
                updateHiddenFields();
            }
        });
    }

    function addDocumentRow(subRow) {
        let insertAfter = subRow;
        let docIdx = 1;
        let next = subRow.nextElementSibling;
        
        // Cari posisi terakhir doc
        while(next && next.classList.contains('doc-row')){
            insertAfter = next;
            docIdx++;
            next = next.nextElementSibling;
        }
        
        // Ambil context values
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
            <td><input type="text" class="form-control form-control-sm" name="engineering[${globalDocIndex}][doc_part1]"></td>
            <td><input type="text" class="form-control form-control-sm" name="engineering[${globalDocIndex}][doc_part2]"></td>
            <td><input type="text" class="form-control form-control-sm" name="engineering[${globalDocIndex}][doc_part3]"></td>
            <td><input type="text" class="form-control form-control-sm" name="engineering[${globalDocIndex}][doc_part4]"></td>
            <td><input type="text" class="form-control form-control-sm" name="engineering[${globalDocIndex}][doc_part5]"></td>
            <td><input type="text" class="form-control form-control-sm" name="engineering[${globalDocIndex}][doc_part6]"></td>
            <td>
                <input type="text" class="form-control form-control-sm" name="engineering[${globalDocIndex}][document_name]">
            </td>
            <td>
                <select class="form-control form-control-sm" name="engineering[${globalDocIndex}][approval_status]">
                    <option value="ongoing">On Going</option><option value="done">Done</option><option value="delayed">Delayed</option>
                </select>
                <input type="hidden" name="engineering[${globalDocIndex}][kategori]" value="${kategori}">
                <input type="hidden" name="engineering[${globalDocIndex}][subkategori]" value="${subkategori}">
                <input type="hidden" name="engineering[${globalDocIndex}][sub]" value="${sub}">
            </td>
        `;
        insertAfter.after(doc);
        reindexEngineering();
        updateHiddenFields();
    }

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
                let hiddenKategori = row.querySelector('input[name*="[kategori]"]');
                let hiddenSubkategori = row.querySelector('input[name*="[subkategori]"]');
                let hiddenSub = row.querySelector('input[name*="[sub]"]');
                
                if(hiddenKategori) hiddenKategori.value = currentKategori;
                if(hiddenSubkategori) hiddenSubkategori.value = currentSubkategori;
                if(hiddenSub) hiddenSub.value = currentSub;
            }
        });
    }

    // Tombol Global Engineering
    let engAddDocBtn = document.getElementById('engAddDoc');
    if (engAddDocBtn) {
        engAddDocBtn.onclick = function() {
            let body = document.getElementById('engineeringBody');
            let subs = body.querySelectorAll('tr.sub-row');
            if(subs.length === 0){
                alert('Buat dulu sub di bawah subkategori sebelum menambah dokumen');
                return;
            }
            let lastSub = subs[subs.length - 1];
            addDocumentRow(lastSub);
        };
    }
    
    let engDelDocBtn = document.getElementById('engDelDoc');
    if (engDelDocBtn) {
        engDelDocBtn.onclick = function() {
            let body = document.getElementById('engineeringBody');
            let docs = body.querySelectorAll('tr.doc-row');
            if(docs.length > 0){
                docs[docs.length - 1].remove();
                reindexEngineering();
            }
        };
    }

    // --- SCOPE ACTIONS ---
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('scope-add-row')) {
            let scope = e.target.dataset.scope;
            let tbody = document.querySelector(`#scopeTable${scope} tbody`);
            let rows = tbody.querySelectorAll('tr.act-row');
            let idx = rows.length + 1;
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
        }
        
        if (e.target.classList.contains('scope-remove-row')) {
            let scope = e.target.dataset.scope;
            let tbody = document.querySelector(`#scopeTable${scope} tbody`);
            let rows = tbody.querySelectorAll('tr.act-row');
            if (rows.length > 1) {
                tbody.removeChild(tbody.lastElementChild);
            }
        }
    });

    // Helper for Scope Letter
    function getScopeLetter(idx) {
        return String.fromCharCode(65 + idx);
    }

    let addScopeBtn = document.getElementById('addScopeBtn');
    if (addScopeBtn) {
        addScopeBtn.onclick = function() {
            lastScopeIdx++;
            let letter = getScopeLetter(lastScopeIdx);
            let tableId = `scopeTable${letter}`;
            
            let div = document.createElement('div');
            div.className = 'scope-table-wrap';
            div.dataset.scope = letter;
            div.innerHTML = `
                <div class="section-header">Scope ${letter}</div>
                <div class="section-frame">
                    <div class="table-responsive">
                        <table class="table table-bordered activity-table" id="${tableId}">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Lokasi</th>
                                    <th>Pencapaian Minggu Ini</th>
                                    <th>Progress (%)</th>
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
                </div>
            `;
            document.getElementById('scopesContainer').appendChild(div);
        };
    }

    let removeScopeBtn = document.getElementById('removeScopeBtn');
    if (removeScopeBtn) {
        removeScopeBtn.onclick = function() {
            let container = document.getElementById('scopesContainer');
            let wraps = container.querySelectorAll('.scope-table-wrap');
            if (wraps.length > 1) {
                container.removeChild(wraps[wraps.length - 1]);
                lastScopeIdx--;
            } else {
                alert('Minimal harus ada 1 scope!');
            }
        };
    }

    // --- ISSUE TABLE (Sekarang hanya handle tombol tambah/hapus baris) ---
    // HTML structure sudah ada di Blade
    
    let issueAddRowBtn = document.getElementById('issueAddRow');
    if (issueAddRowBtn) {
        issueAddRowBtn.onclick = function() {
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
    }

    let issueRemoveRowBtn = document.getElementById('issueRemoveRow');
    if (issueRemoveRowBtn) {
        issueRemoveRowBtn.onclick = function() {
            let tbody = document.querySelector('#issueTable tbody');
            if (tbody.children.length > 1) tbody.removeChild(tbody.lastElementChild);
        };
    }

    // --- EPC & Other Tables Add/Remove (Code stays mostly same) ---
    let epcAddRowBtn = document.getElementById('epcAddRow');
    if (epcAddRowBtn) {
        epcAddRowBtn.onclick = function() {
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
            let totalRow = tbody.querySelector('.epc-total-row');
            if (totalRow) {
                totalRow.parentNode.insertBefore(tr, totalRow);
            } else {
                tbody.appendChild(tr);
            }
            
            calculateTotalRow('epcTable');

            tr.addEventListener('input', function(e) {
                if (e.target.type === 'number') calculateRow(tr);
            });
        };
    }

    let epcRemoveRowBtn = document.getElementById('epcRemoveRow');
    if (epcRemoveRowBtn) {
        epcRemoveRowBtn.onclick = function(){
            let tbody = document.querySelector('#epcTable tbody');
            let regularRows = tbody.querySelectorAll('tr.epc-row:not(.epc-total-row)');
            if(regularRows.length > 1) {
                regularRows[regularRows.length - 1].remove();
                calculateTotalRow('epcTable');
            }
        };
    }

    // Material
    let matAddRowBtn = document.getElementById('matAddRow');
    if (matAddRowBtn) {
        matAddRowBtn.onclick = function() {
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
    }
    
    let matRemoveRowBtn = document.getElementById('matRemoveRow');
    if (matRemoveRowBtn) {
        matRemoveRowBtn.onclick = function() {
            let tbody = document.querySelector('#materialTable tbody');
            if (tbody.children.length > 1) tbody.removeChild(tbody.lastElementChild);
        };
    }

    // Service
    let svcAddRowBtn = document.getElementById('svcAddRow');
    if (svcAddRowBtn) {
        svcAddRowBtn.onclick = function() {
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
    }

    let svcRemoveRowBtn = document.getElementById('svcRemoveRow');
    if (svcRemoveRowBtn) {
        svcRemoveRowBtn.onclick = function() {
            let tbody = document.querySelector('#serviceTable tbody');
            if (tbody.children.length > 1) tbody.removeChild(tbody.lastElementChild);
        };
    }

    // Construction
    let conAddRowBtn = document.getElementById('conAddRow');
    if (conAddRowBtn) {
        conAddRowBtn.onclick = function() {
            let tbody = document.querySelector('#constructionTable tbody');
            let regularRows = tbody.querySelectorAll('tr.con-row:not(.con-total-row)');
            let idx = regularRows.length + 1;
            
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
            
            let totalRow = tbody.querySelector('.con-total-row');
            if (totalRow) {
                totalRow.parentNode.insertBefore(tr, totalRow);
            } else {
                tbody.appendChild(tr);
            }
            
            calculateTotalRow('constructionTable');
            
            tr.addEventListener('input', function(e) {
                if (e.target.type === 'number') calculateRow(tr);
            });
        };
    }
    
    let conRemoveRowBtn = document.getElementById('conRemoveRow');
    if (conRemoveRowBtn) {
        conRemoveRowBtn.onclick = function(){
            let tbody = document.querySelector('#constructionTable tbody');
            let regularRows = tbody.querySelectorAll('tr.con-row:not(.con-total-row)');
            if(regularRows.length > 1) {
                regularRows[regularRows.length - 1].remove();
                calculateTotalRow('constructionTable');
            }
        };
    }

    // --- Dropdowns ---
    fetch('/dropdown/projects')
      .then(res => res.json())
      .then(data => {
        let select = document.getElementById('projectSelect');
        if (select) {
            // Keep existing option if selected (handled by Blade)
            // But we can append others if needed, or simple reset:
            // select.innerHTML = '<option value="">-- Pilih Proyek --</option>'; 
            // Better logic: Don't overwrite if PHP already rendered it, or just append new ones?
            // Since edit page usually has pre-filled values, we might skip this fetch OR check if we need to repopulate.
            // For now, let's just log it or only populate if empty.
            if(select.children.length <= 1) {
                data.forEach(p => {
                    let opt = document.createElement('option');
                    opt.value = p.id;
                    opt.textContent = p.name;
                    select.appendChild(opt);
                });
            }
        }
      });

    let projectSelect = document.getElementById('projectSelect');
    if (projectSelect) {
        projectSelect.onchange = function() {
            let projectId = this.value;
            let periodSelect = document.getElementById('periodSelect');
            let projectInput = document.getElementById('projectInput');
            
            if (periodSelect) periodSelect.innerHTML = '<option value="">-- Pilih Periode --</option>';
            
            if (!projectId) {
                // Restore original value from Blade data
                if(projectInput) projectInput.value = window.projectData.originalProjectName || '';
                return;
            }
            
            let selectedProject = this.options[this.selectedIndex];
            if (projectInput && selectedProject.value) {
                projectInput.value = selectedProject.textContent;
            }
            
            fetch('/dropdown/periods/' + projectId)
                .then(res => res.json())
                .then(data => {
                    data.forEach(p => {
                        let opt = document.createElement('option');
                        opt.value = p.id;
                        opt.textContent = `${p.name} (${p.start_date || 'No date'} - ${p.end_date || 'No date'})`;
                        if (periodSelect) periodSelect.appendChild(opt);
                    });
                });
        };
    }
    
    // Copy Templates & Calculations (Rest of the code)
    // ... [Copy Template Project, Copy Period Template, Calculate Row, Calculate Total Row]
    // Pastikan function ini ada (sama seperti file sebelumnya tapi tanpa blade syntax)

    // Lock Form Check
    if (window.projectData.isReadOnly) {
        document.querySelectorAll('input, select, textarea').forEach(el => {
            if (el.name !== 'period_action' && el.name !== '_token' && el.name !== '_method') { 
                el.setAttribute('disabled', 'disabled'); 
            }
        });
    }

    // Calculation Logic (Helper)
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

        const cum_last_variance = (values.cum_last_actual || 0) - (values.cum_last_plan || 0);
        if (values.cum_last_variance_input) values.cum_last_variance_input.value = cum_last_variance.toFixed(2);

        const this_plan = (values.cum_now_plan || 0) - (values.cum_last_plan || 0);
        if (values.this_plan_input) values.this_plan_input.value = this_plan.toFixed(2);

        const this_actual = (values.cum_now_actual || 0) - (values.cum_last_actual || 0);
        if (values.this_actual_input) values.this_actual_input.value = this_actual.toFixed(2);

        const cum_now_variance = (values.cum_now_actual || 0) - (values.cum_now_plan || 0);
        if (values.cum_now_variance_input) values.cum_now_variance_input.value = cum_now_variance.toFixed(2);

        if (statusSelect) {
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
            weight_factor: 0, cum_last_plan: 0, cum_last_actual: 0, cum_last_variance: 0,
            this_plan: 0, this_actual: 0,
            cum_now_plan: 0, cum_now_actual: 0, cum_now_variance: 0
        };

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

        // Set Values
        let setVal = (cls, val) => {
            let el = table.querySelector(`.${prefix}-total-${cls}`);
            if(el) el.value = val.toFixed(2);
        };
        
        setVal('weight', totals.weight_factor);
        setVal('cum-last-plan', totals.cum_last_plan);
        setVal('cum-last-actual', totals.cum_last_actual);
        setVal('cum-last-variance', totals.cum_last_variance);
        setVal('this-plan', totals.this_plan);
        setVal('this-actual', totals.this_actual);
        setVal('cum-now-plan', totals.cum_now_plan);
        setVal('cum-now-actual', totals.cum_now_actual);
        setVal('cum-now-variance', totals.cum_now_variance);

        const totalStatusSelect = table.querySelector(`.${prefix}-total-status`);
        if (totalStatusSelect) {
            let statusValue = 'on schedule';
            if (totals.cum_last_variance < 0 || totals.cum_now_variance < 0) {
                statusValue = 'delayed';
            }
            totalStatusSelect.value = statusValue;
            
            // Handle Hidden Input
            const totalRow = totalStatusSelect.closest('tr');
            let hiddenStatusInput = totalRow.querySelector(`input[name="${totalStatusSelect.name}"]`);
            if (!hiddenStatusInput) {
                hiddenStatusInput = document.createElement('input');
                hiddenStatusInput.type = 'hidden';
                hiddenStatusInput.name = totalStatusSelect.name;
                totalStatusSelect.parentNode.appendChild(hiddenStatusInput);
            }
            hiddenStatusInput.value = statusValue;
        }
    }

    // Initial Calc Setup
    function setupAutoCalculation(tableId) {
        const table = document.getElementById(tableId);
        if (!table) return;

        table.addEventListener('input', function(e) {
            const input = e.target;
            if (input.type === 'number') {
                const row = input.closest('tr');
                const prefix = tableId === 'epcTable' ? 'epc' : 'con';
                if (row && row.classList.contains(`${prefix}-total-row`)) return;
                
                if (row && (row.classList.contains('epc-row') || row.classList.contains('con-row'))) {
                    calculateRow(row);
                    setTimeout(() => calculateTotalRow(tableId), 10);
                }
            }
        });
        const prefix = tableId === 'epcTable' ? 'epc' : 'con';
        const rows = table.querySelectorAll(`.${prefix}-row:not(.${prefix}-total-row)`);
        rows.forEach(row => calculateRow(row));
        calculateTotalRow(tableId);
    }

    setupAutoCalculation('epcTable');
    setupAutoCalculation('constructionTable');

    const progressInput = document.getElementById('progress_percentage');
    const weightInput = document.getElementById('weight');
    const calculatedInput = document.getElementById('calculated_progress');
    const previewPercent = document.getElementById('preview_percentage');
    const previewBar = document.getElementById('preview_bar');
    const calcFormula = document.getElementById('calc_formula');
    
    // Elemen khusus Edit Page (Weight Warning)
    const otherWeightsEl = document.getElementById('other_weights');
    const remainingWeightEl = document.getElementById('remaining_weight');
    const weightStatusDiv = document.getElementById('weight_status');

    function runCalculateProgress() {
        const progress = parseFloat(progressInput.value) || 0;
        const weight = parseFloat(weightInput.value) || 0;
        const calculated = (progress * weight).toFixed(2);
        
        // Update calculated field
        if(calculatedInput) calculatedInput.value = calculated + '%';
        
        // Update preview
        if(previewPercent) previewPercent.textContent = calculated + '%';
        if(previewBar) previewBar.style.width = calculated + '%';
        if(calcFormula) calcFormula.textContent = `${progress.toFixed(2)}% × ${weight.toFixed(2)} = ${calculated}%`;
        
        // Update Weight Status (Khusus Edit Page)
        if (otherWeightsEl) {
            const otherWeights = parseFloat(otherWeightsEl.textContent) || 0;
            const totalWeight = otherWeights + weight;
            const remaining = (1 - totalWeight);
            
            if (remainingWeightEl) remainingWeightEl.textContent = remaining.toFixed(2);
            
            if (weightStatusDiv) {
                // Reset style
                weightStatusDiv.style.background = '';
                weightStatusDiv.style.border = '';
                weightStatusDiv.style.color = '';

                // Gunakan margin of error kecil (epsilon) untuk komparasi float
                if (Math.abs(remaining) < 0.001) {
                    // Pas (1.0) -> Hijau
                    weightStatusDiv.style.background = '#d4edda';
                    weightStatusDiv.style.border = '2px solid #c3e6cb';
                    weightStatusDiv.style.color = '#155724';
                } else if (remaining < -0.001) {
                    // Kelebihan (> 1.0) -> Merah
                    weightStatusDiv.style.background = '#f8d7da';
                    weightStatusDiv.style.border = '2px solid #f5c6cb';
                    weightStatusDiv.style.color = '#721c24';
                } else {
                    // Kurang (< 1.0) -> Kuning
                    weightStatusDiv.style.background = '#fff3cd';
                    weightStatusDiv.style.border = '2px solid #ffeaa7';
                    weightStatusDiv.style.color = '#856404';
                }
            }
        }
    }

    // Pasang Event Listener (Pengganti oninput di HTML)
    if (progressInput) {
        progressInput.addEventListener('input', runCalculateProgress);
    }
    if (weightInput) {
        weightInput.addEventListener('input', runCalculateProgress);
    }

    // Jalankan kalkulasi awal saat halaman dimuat
    if (progressInput && weightInput) {
        runCalculateProgress();
    }

    // --- BUTTON EVENT LISTENERS (WAJIB ADA UNTUK TYPE="BUTTON") ---
    
    // 1. Tombol Save Draft
    const btnSaveDraft = document.getElementById('btnSaveDraft');
    if (btnSaveDraft) {
        btnSaveDraft.addEventListener('click', function() {
            // Set action ke 'save'
            const actionInput = document.getElementById('periodAction');
            if (actionInput) actionInput.value = 'save';
            
            // Submit Form
            document.getElementById('projectForm').submit();
        });
    }

    // 2. Tombol Submit (Memicu Modal)
    const btnSubmit = document.getElementById('btnSubmit');
    if (btnSubmit) {
        btnSubmit.addEventListener('click', function(e) {
            e.preventDefault();
            // Tampilkan modal konfirmasi (pastikan fungsi openSubmitModal ada/didefinisikan)
            // Jika Anda menggunakan logika modal manual seperti di create:
            const modal = document.getElementById('submitModal');
            if(modal) {
                modal.style.display = 'block';
                document.body.style.overflow = 'hidden';
            }
        });
    }

    // 3. Konfirmasi Submit di dalam Modal
    // Pastikan fungsi ini bisa dipanggil global atau pasang listener pada ID tombol di modal
    const confirmSubmitBtn = document.getElementById('confirmSubmitBtn');
    if (confirmSubmitBtn) {
        confirmSubmitBtn.addEventListener('click', function() {
            // Disable tombol biar gak double submit
            this.disabled = true;
            this.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Submitting...';

            // Set action ke 'submit'
            const actionInput = document.getElementById('periodAction');
            if (actionInput) actionInput.value = 'submit';

            // Submit Form
            document.getElementById('projectForm').submit();
        });
    }

    // 4. Close Modal Helpers (Opsional jika belum ada di inline script)
    window.closeSubmitModal = function() {
        const modal = document.getElementById('submitModal');
        if(modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    };
    
    // Klik di luar modal untuk menutup
    window.addEventListener('click', function(e) {
        const modal = document.getElementById('submitModal');
        if (e.target === modal) {
            closeSubmitModal();
        }
    });
});
