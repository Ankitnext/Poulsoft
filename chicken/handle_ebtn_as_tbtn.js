//handle_ebtn_as_tbtn.js
// Handle Enter as Tab functionality
$(document).ready(function () {
    // Initialize Select2
    $('.select2').select2({
        placeholder: 'Select an option',
        allowClear: true
    });

    // Handle Enter as Tab functionality
    $(document).on('keydown', function (event) {
        if(event.key === 'Enter'){
            if(event.shiftKey){
                event.preventDefault();
                if(document.getElementById('sub_pt')){
                    if(document.getElementById("ebtncount")){
                        var ebtn = document.getElementById("ebtncount").value; if(ebtn == ""){ ebtn = 0; }
                        if(parseFloat(ebtn) == 0){ document.getElementById('sub_pt').click(); }
                    }
                }
                else if(document.getElementById('submit')){
                    if(document.getElementById("ebtncount")){
                        var ebtn = document.getElementById("ebtncount").value; if(ebtn == ""){ ebtn = 0; }
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
                    document.getElementById("popup_jalino").focus();
                }
                else{
                    let focusedElement = document.activeElement;
                    if(focusedElement.tagName.toLowerCase() === "a"){
                        var a = focusedElement.id;
                        var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                        if(["addrow", "addrow1", "addrow2", "addrow3"].includes(b[0])){
                            focusedElement.click();
                        }
                    }
                    
                    let focusableElements = Array.from(document.querySelectorAll(
                        'input, select, button, textarea, a, table, thead, [tabindex]:not([tabindex="-1"])'
                    )).filter(el => !el.disabled);

                    const currentIndex = focusableElements.indexOf(focusedElement);

                    if (currentIndex > -1 && currentIndex < focusableElements.length - 1) {
                        let nextElement = null;
                        let n_fcs = 0;

                        for(let n_key = currentIndex + 1; n_key <= focusableElements.length - 1; n_key++){
                            let n_val = focusableElements[n_key];
                            if(n_fcs === 0 && window.getComputedStyle(n_val).visibility !== "hidden"){
                                n_fcs = 1;
                                nextElement = n_val;
                            }
                        }

                        // **Fix for single to multiple format transition**
                        if (nextElement) {
                            let currentName = focusedElement.getAttribute("name");
                            let nextName = nextElement.getAttribute("name");

                            if (currentName && nextName) {
                                let isCurrentArray = currentName.endsWith("[]");
                                let isNextArray = nextName.endsWith("[]");

                                // If transitioning from single field to array field, ensure correct focus
                                if (!isCurrentArray && isNextArray) {
                                    let matchingElements = focusableElements.filter(el => el.getAttribute("name") === nextName);
                                    if (matchingElements.length > 0) {
                                        nextElement = matchingElements[0]; // Focus first element in the array
                                    }
                                }
                            }

                            // Close Select2 dropdown if necessary
                            if ($(focusedElement).hasClass('select2-hidden-accessible')) {
                                $(focusedElement).select2('close');
                            }

                            // Move focus to the next element
                            if ($(nextElement).hasClass('select2-hidden-accessible')) {
                                $(nextElement).select2('open');
                            } else {
                                nextElement.focus();
                            }

                            // Select text inside input fields
                            if (nextElement.tagName === "INPUT" && nextElement.type === "text") {
                                nextElement.select();
                            }
                        }
                    }
                }
            }
        }
        else if(event.key === 'Escape'){
            if(document.getElementById("popup_updbtn") && document.getElementById("popup_jalibox") && document.getElementById("popup_jalibox").style.display === "block"){
                document.getElementById("popup_updbtn").click();
                if(document.getElementById("bookinvoice")){ document.getElementById("bookinvoice").focus(); }
            }
        }
        else if(['ArrowUp', 'ArrowRight', 'ArrowDown', 'ArrowLeft'].includes(event.key)){
            const active = document.activeElement;
            if(!active || (!active.hasAttribute('data-row') || !active.hasAttribute('data-col'))) return;
            const row = parseInt(active.getAttribute('data-row'));
            const col = parseInt(active.getAttribute('data-col'));
            let nextInput;

            switch (event.key) {
                case 'ArrowUp':
                    nextInput = document.querySelector(`[data-row="${row - 1}"][data-col="${col}"]`);
                    break;
                case 'ArrowDown':
                    nextInput = document.querySelector(`[data-row="${row + 1}"][data-col="${col}"]`);
                    break;
                case 'ArrowLeft':
                    nextInput = document.querySelector(`[data-row="${row}"][data-col="${col - 1}"]`);
                    break;
                case 'ArrowRight':
                    nextInput = document.querySelector(`[data-row="${row}"][data-col="${col + 1}"]`);
                    break;
            }

            if(nextInput){
                event.preventDefault();
                nextInput.focus();
                console.log(row+"@"+col);
            }
        }
    });

    // Focus the search box when Select2 opens
    $('.select2').on('select2:open', function () {
        setTimeout(() => {
            const searchBox = document.querySelector('.select2-search__field');
            if (searchBox) {
                searchBox.focus();
            }
        }, 0);
    });

    // Avoid re-triggering focus on closed dropdown
    $('.select2').on('select2:close', function () {
        $(this).trigger('blur');
    });
});
