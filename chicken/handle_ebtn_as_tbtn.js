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
        if (event.key === 'Enter') {
            event.preventDefault(); // Prevent default Enter behavior
            
            let focusedElement = document.activeElement;

            if(focusedElement.tagName == "A" || focusedElement.tagName == "a"){
                var a = focusedElement.id;
                
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                if(b[0] == "addrow" || b[0] == "addrow1" || b[0] == "addrow2" || b[0] == "addrow3"){
                    focusedElement.click();
                }
            }
            
            let focusableElements = Array.from(document.querySelectorAll(
                'input, select, button, textarea, a,table,thead, [tabindex]:not([tabindex="-1"])'
            )).filter(el => !el.disabled);

            const currentIndex = focusableElements.indexOf(focusedElement);

            if (currentIndex > -1 && currentIndex < focusableElements.length - 1) {
                //const nextElement = focusableElements[currentIndex + 1];
                let n_fcs = 0;
                for(let n_key = currentIndex + 1;n_key <= focusableElements.length - 1;n_key++){
                    let n_val = focusableElements[n_key];
                    if(parseInt(n_fcs) == 0){
                        if(window.getComputedStyle(n_val).visibility == "hidden"){ }
                        else{
                            n_fcs = 1;
                            nextElement = n_val;
                        }
                    }
                }
                /* Skip hidden elements
                while(nextIndex < focusableElements.length && isHidden(focusableElements[nextIndex])){ nextIndex++; }
                const nextElement = focusableElements[nextIndex];*/
                /*Element Properties
                let e_prt = '';
                e_prt += '1. Tag Name: '+nextElement.tagName;
                e_prt += '2. Type:'+nextElement.type || 'N/A';
                e_prt += '3. Classes:'+nextElement.className || 'No classes';
                e_prt += '4. ID:'+nextElement.id || 'No ID';
                e_prt += '5. Styles:'+{visibility: window.getComputedStyle(element).visibility,display: window.getComputedStyle(element).display,};
                e_prt += '6. Tabindex:'+nextElement.tabIndex || 'Default';
                e_prt += '7. Disabled:'+nextElement.disabled || false;
                alert(e_prt);
                logElementProperties(nextElement);
                */
                
                // If the focused element is Select2, close it before moving
                if ($(focusedElement).hasClass('select2-hidden-accessible')) { $(focusedElement).select2('close'); }

                // Move focus to the next element
                if ($(nextElement).hasClass('select2-hidden-accessible')) { $(nextElement).select2('open'); } else { nextElement.focus(); }

                
                if (nextElement.tagName === "INPUT" && nextElement.type === "text") {
                    nextElement.select(); // Select all text inside the input field
                }
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
        // Move the focus to the parent select to enable normal navigation
        $(this).trigger('blur');
    });
});

/*function logElementProperties(element) {
    console.log('Next Focusable Element Properties:');
    console.log('Tag Name:', element.tagName);
    console.log('Type:', element.type || 'N/A');
    console.log('Classes:', element.className || 'No classes');
    console.log('ID:', element.id || 'No ID');
    console.log('Styles:', {
        visibility: window.getComputedStyle(element).visibility,
        display: window.getComputedStyle(element).display,
    });
    console.log('Tabindex:', element.tabIndex || 'Default');
    console.log('Disabled:', element.disabled || false);
    if(window.getComputedStyle(element).visibility == "hidden"){

    }
}*/