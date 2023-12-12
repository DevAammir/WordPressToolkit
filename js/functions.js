// console.log('HELLO & WELCOME TO functions.js from wpt');
/**************************************
 * Universal Functions 
 **************************************/
function isValidEmail(email) {
    var EmailRegex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return EmailRegex.test(email);
}


// Function for '.alpha_only, .alpha'
document.querySelectorAll('.alpha_only, .alpha').forEach(function (element) {
    element.addEventListener('keyup', function () {
        var node = this;
        node.value = node.value.replace(/[^a-z]/g, '');
    });

    element.addEventListener('blur', function () {
        var node = this;
        node.value = node.value.replace(/[^a-z]/g, '');
    });
});

// Function for '.alpha_space_only, .alpha_space'
document.querySelectorAll('.alpha_space_only, .alpha_space').forEach(function (element) {
    element.addEventListener('keyup', function () {
        var node = this;
        node.value = node.value.replace(/[^a-z ]/g, '');
    });

    element.addEventListener('blur', function () {
        var node = this;
        node.value = node.value.replace(/[^a-z ]/g, '');
    });
});

// Function for '.alpha_space_dash_only, .alpha-space'
document.querySelectorAll('.alpha_space_dash_only, .alpha-space').forEach(function (element) {
    element.addEventListener('keyup', function () {
        var node = this;
        node.value = node.value.replace(/[^a-z -]/g, '');
    });

    element.addEventListener('blur', function () {
        var node = this;
        node.value = node.value.replace(/[^a-z -]/g, '');
    });
});

// Function for '.numeric_only, .numeric, .numbers_only'
document.querySelectorAll('.numeric_only, .numeric, .numbers_only').forEach(function (element) {
    element.addEventListener('keyup', function () {
        var node = this;
        node.value = node.value.replace(/[^0-9]/g, '');
    });

    element.addEventListener('blur', function () {
        var node = this;
        node.value = node.value.replace(/[^0-9]/g, '');
    });
});

// Function for '.alpha_numeric_only, .alpha_numeric'
document.querySelectorAll('.alpha_numeric_only, .alpha_numeric').forEach(function (element) {
    element.addEventListener('keyup', function () {
        var node = this;
        node.value = node.value.replace(/[^a-z0-9]/g, '');
    });

    element.addEventListener('blur', function () {
        var node = this;
        node.value = node.value.replace(/[^a-z0-9]/g, '');
    });
});

// Function for '.alpha_numeric_dash, .no_special_chars'
document.querySelectorAll('.alpha_numeric_dash, .no_special_chars').forEach(function (element) {
    element.addEventListener('keyup', function () {
        var node = this;
        node.value = node.value.replace(/[^a-z0-9 -]/g, '');
    });

    element.addEventListener('blur', function () {
        var node = this;
        node.value = node.value.replace(/[^a-z0-9 -]/g, '');
    });
});


/**
SCOLL TO MIDDLE(Insted of all the way to top)
**/
function scroll_middle(id) {
    document.getElementById(id).scrollIntoView({
        behavior: 'auto',
        block: 'center',
        inline: 'center'
    });
}




/* *
*JAVASCRIPT: CLONE DIV WITH BUTTON CLICK

How to Use:
To use this function, you would call it and provide the class name of the element you want to clone
 as an argument. For example:
clone_html('elementToCloneClass');

What It Does:
This function clones the first element found in the document with the specified class (to_clone). It then appends the cloned element after the original in the DOM. Finally, it clears the value of the first input element within the cloned element and logs "cloned" to the console.

* */
function clone_html(to_clone) {
    var elementsToClone = document.getElementsByClassName(to_clone);

    if (elementsToClone.length > 0) {
        var elementToClone = elementsToClone[0];

        var newElement = elementToClone.cloneNode(true);
        elementToClone.parentNode.appendChild(newElement);
        console.log('Cloned:', to_clone);

        let theInputs = newElement.querySelectorAll('input');
        theInputs.forEach(input => input.value = "");
    } else {
        // console.error('No element found with class:', to_clone);
    }
}





/***
 * CHECK EMAIL IS VALID
 * **/
function ValidateEmail(input, response_div) {

    var validRegex = /^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$/;

    if (!input.value.match(validRegex)) {
        // alert("Invalid email address!");
        this.focus();
        jQuery(response_div).html("Invalid Email. Please provide a valid email address.");
        //jQuery(submit).attr("disabled", true);
        return false;
    } else {
        jQuery('#email_response').html("");
        //jQuery(submit).removeAttr('disabled');
    }

}



/***
 * CHECK CURRENT PAGE IS
 * **/
function current_page_is(the_page) {
    var currentURL = window.location.href;
    console.log('current URL is: ' + currentURL);
    if (the_page == currentURL) {
        return true;
    } else {
        return false;
    }
}

/***
* HACK FOR BS5 TO WP NAV MENU
* **/
document.querySelectorAll('.current-menu-item').forEach(function (element) {
    element.classList.add('active');
});


/***
 * AJAX FUNCTION FOR ONE RESPONSE
 * **/
function _AJAX_function_1(target, admin_ajax_url, action, type, data, data_type) {
    //let admin_ajax_url = siteAjax();
    // console.log(new Date());
    jQuery.ajax({
        url: admin_ajax_url + '?action=' + action,
        type: type,
        dataType: data_type,
        data: data,
        beforeSend: function (xhr) {
            // debugger;
            setTimeout(() => {
                jQuery(target).html('<div id="loading"> Please wait... </div>');
                jQuery(target).find('div').attr('id', 'loader').show();
                // jQuery('input').attr('disabled', 'disabled');
            }, 1000);
        },
    }).done(function (response) {

        if (response.status === 200) {
            // jQuery('input').attr('disabled', false);
            jQuery(target).html(response.result);
        } else {
            jQuery(target).html('<div class="error">' + response.result + "</div>");
        }
    }); //ajax done
}

/***
 * AJAX FUNCTION FOR TWO RESPONSES
 * **/
function _AJAX_function_2(target_1, target_2, admin_ajax_url, action, type, data, data_type) {
    //let admin_ajax_url = siteAjax();
    // console.log(new Date());
    jQuery.ajax({
        url: admin_ajax_url + '?action=' + action,
        type: type,
        dataType: data_type,
        data: data,
        beforeSend: function (xhr) {
            $(target_1).html('<div> Loading, Please wait...! </div>');
            $(target_1).find('div').attr('id', 'loader').show();
            $(target_2).html('<div> Loading, Please wait...! </div>');
            $(target_2).find('div').attr('id', 'loader').show();
        },
    }).done(function (response) {
        $(target_1).html(response.result_1);
        $(target_2).html(response.result_2);
        // console.log(new Date());
        // loader.hide();
    }); //ajax done
}

/***
 * AJAX FUNCTION FOR THREE RESPONSES
 * **/
function _AJAX_function_3(target_1, target_2, target_3, admin_ajax_url, action, type, data, data_type) {
    //let admin_ajax_url = siteAjax();
    // console.log(new Date());
    jQuery.ajax({
        url: admin_ajax_url + '?action=' + action,
        type: type,
        dataType: data_type,
        data: data,
        beforeSend: function (xhr) {
            $(target_1).html('<div> Loading, Please wait...! </div>');
            $(target_1).find('div').attr('id', 'loader').show();
            $(target_2).html('<div> Loading, Please wait...! </div>');
            $(target_2).find('div').attr('id', 'loader').show();
            $(target_3).html('<div> Loading, Please wait...! </div>');
            $(target_3).find('div').attr('id', 'loader').show();
        },
    }).done(function (response) {
        $(target_1).html(response.result_1);
        $(target_2).html(response.result_2);
        $(target_3).html(response.result_3);
        // console.log(new Date());
        // loader.hide();
    }); //ajax done
}

/* *
* AJAX FUNCTION TO INCLUDE PAGE
* */
/**
 * Fetches a page and updates the content of a specified section.
 *
 * @param {string} page - The URL of the page to fetch.
 * @param {string} section - The selector of the section to update.
 * @return {undefined} This function does not return a value.
 */
function include_page(page, section) {
    $.ajax({
        url: page,
        method: 'GET',
        dataType: 'html',
        success: function (data) {
            // Replace the content of the main section with the updated content
            $(section).html(data);
        },
        error: function (xhr, status, error) {
            console.error('Error fetching data:', error);
        }
    });
}

/**
 * cloneAndRemove - Sets up event listeners to clone and remove elements based on click events.
 *
 * @function
 * @name cloneAndRemove
 * @memberof global
 *
 * @description
 * This function initializes event listeners for click events on the document. When an element with
 * the class 'clone_trigger' is clicked, it clones its closest ancestor with the class
 * 'clone_remove_this' and inserts the clone after the original. Additionally, when an element with
 * the class 'remove_trigger' is clicked, it removes its closest ancestor with the class
 * 'clone_remove_this', but only if there is more than one such element in the document.
 */
(function cloneAndRemove() {
    // console.log('HELP:: parent: .clone_remove_this, add_remove: .clone_trigger, .remove_trigger');

    document.addEventListener('click', function (event) {
        var target = event.target;

        if (target.classList.contains('clone_trigger')) {
            var itsParent = findClosestParent(target, '.clone_remove_this');
            if (itsParent) {
                var clone = itsParent.cloneNode(true);
                itsParent.parentNode.insertBefore(clone, itsParent.nextSibling);
                clearInputValues(clone);
            }
        }

        if (target.classList.contains('remove_trigger')) {
            var itsParent = findClosestParent(target, '.clone_remove_this');
            if (itsParent && document.querySelectorAll('.clone_remove_this').length > 1) {
                itsParent.parentNode.removeChild(itsParent);
            }
        }
    });
});
    /***|Apply CSS Class 'active' to current menu item in Bootstrap5|***/
    document.addEventListener('DOMContentLoaded', function () {
        var currentItem = document.querySelector('li.current-menu-item');
        if (currentItem !== null) {
            currentItem.classList.add('active');
        }
    });

    /******
     * HELPER FUNCTIONS
     * *******/

    /***
     * WOOCOMMERCE SUPPORT FOR BOOTSTRAP
     * ***/
    function addClassToElement(selector, ...classNames) {
        var element = document.querySelector(selector);
        if (element) {
            console.log('Adding classes:', classNames, 'to element:', element);
            element.classList.add(...classNames);
            // console.log('Classes after addition:', element.classList);
        } else {
            // console.log('Element not found for selector:', selector);
        }
    }

    function addClassToElements(selector, ...classNames) {
        var elements = document.querySelectorAll(selector);
        if (elements.length > 0) {
            // console.log('Adding classes:', classNames, 'to elements:', elements);
            elements.forEach(function(element) {
                element.classList.add(...classNames);
            });
            // console.log('Classes after addition:', elements[0].classList); // Log classes of the first element
        } else {
            // console.log('No elements found for selector:', selector);
        }
    }
    
    document.addEventListener('DOMContentLoaded', function () {
        addClassToElement('.woocommerce #primary.content-area', 'container');
        addClassToElements('input[type="submit"]', 'btn', 'btn-primary');


        addClassToElement('.woocommerce-MyAccount-navigation ul', 'list-group');
        addClassToElements('.woocommerce-MyAccount-navigation li a', 'list-group-item');
        addClassToElements('input[type="text"]', 'form-control');
        addClassToElements('input[type="email"]', 'form-control');
        addClassToElements('input[type="password"]', 'form-control');
        addClassToElement('a.components-button.wc-block-components-button.wp-element-button.wc-block-cart__submit-button.contained', 'btn', 'btn-primary');
        
        addClassToElements('button[type="button"]', 'btn','btn-primary');
    });
