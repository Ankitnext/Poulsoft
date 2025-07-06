//handle_ebtn_as_tbtn1.js
$(document).ready(function () {
    $(document).on('keydown', function (event) {
        if(event.key === 'Enter'){
            if(event.shiftKey){
                event.preventDefault();
                if(document.getElementById('sub_pt')){
                    if(document.getElementById("ebtncount")){
                        let ebtn = document.getElementById("ebtncount").value.trim(); if (ebtn === "") ebtn = 0;
                        if(parseFloat(ebtn) == 0){ document.getElementById('sub_pt').click(); }
                    }
                }
                else if(document.getElementById('submit')){
                    if(document.getElementById("ebtncount")){
                        let ebtn = document.getElementById("ebtncount").value.trim(); if (ebtn === "") ebtn = 0;
                        if(parseFloat(ebtn) == 0){ document.getElementById('submit').click(); }
                    }
                }
            }
            else if(event.ctrlKey){
                event.preventDefault();
                if(document.getElementById('sub_pt')){ document.getElementById('sub_pt').click(); }
                else if(document.getElementById('submit')){ document.getElementById('submit').click(); }
            }
            else{
                event.preventDefault();
                if(document.getElementById("popup_jalibox") && document.getElementById("popup_jalibox").style.display === "block"){
                    const input = document.getElementById("popup_jalino");
                    if(input && input.value.trim() !== ""){
                        document.getElementById("popup_updbtn").click();
                        if(document.getElementById("bookinvoice")){ document.getElementById("bookinvoice").focus(); }
                    }
                    else{ document.getElementById("popup_jalino").focus(); }
                }
                else{
                    let focusedElement = document.activeElement;
                    if(focusedElement && focusedElement.tagName === "A" && typeof check_nrow !== "function"){
                        const id = focusedElement.id.trim(); if(!id) return; const match = id.toLowerCase().match(/^([a-z0-9_]+)\[/);
                        if(match){ const baseId = match[1]; if(["addrow", "addrow1", "addrow2", "addrow3"].includes(baseId)){ focusedElement.click(); } }
                    }
                    let nextElement = getNextFocusableElement(focusedElement);
                    if(nextElement){
                        if($(focusedElement).hasClass('select2-hidden-accessible')){ $(focusedElement).select2('close'); }
                        if($(nextElement).hasClass('select2-hidden-accessible')){ $(nextElement).select2('open'); } else { nextElement.focus(); }
                        if(nextElement.tagName === "INPUT" && nextElement.type === "text"){ nextElement.select(); }
                    }
                }
            }
        }
        else if(event.key === 'ArrowUp' || event.key === 'ArrowRight' || event.key === 'ArrowDown' || event.key === 'ArrowLeft'){
            const active = document.activeElement;
            if(!active || (!active.hasAttribute('data-row') || !active.hasAttribute('data-col'))) return;
            const row = parseInt(active.getAttribute('data-row'));
            const col = parseInt(active.getAttribute('data-col'));
            let nextInput;

            switch (event.key) {
                case 'ArrowUp': nextInput = document.querySelector(`[data-row="${row - 1}"][data-col="${col}"]`); break;
                case 'ArrowDown': nextInput = document.querySelector(`[data-row="${row + 1}"][data-col="${col}"]`); break;
                case 'ArrowLeft': nextInput = document.querySelector(`[data-row="${row}"][data-col="${col - 1}"]`); break;
                case 'ArrowRight': nextInput = document.querySelector(`[data-row="${row}"][data-col="${col + 1}"]`); break;
                default: return;
            }
            if(nextInput){ event.preventDefault(); nextInput.focus(); }
        }
    });
    $('.select2').on('select2:open', function(){ setTimeout(() => { const searchBox = document.querySelector('.select2-search__field'); if(searchBox){ searchBox.focus(); } }, 0); });
    $('.select2').on('select2:close', function(){ $(this).trigger('blur'); });
});

function getNextFocusableElement(currentElement) {
    const focusableElements = Array.from(document.querySelectorAll(
        'input, select, textarea, button, a, [tabindex]:not([tabindex="-1"])'
    )).filter(el => !el.disabled);

    let index = focusableElements.indexOf(currentElement);
    if (index === -1 || index >= focusableElements.length - 1) return null;

    const tryNext = (idx) => {
        if (idx >= focusableElements.length) return null;
        const el = focusableElements[idx];
        const isVisible = el.offsetParent !== null && window.getComputedStyle(el).visibility !== 'hidden';
        if(el && el.id && isVisible){ return el; } else{ return tryNext(idx + 1); }
    };
    return tryNext(index + 1);
}
