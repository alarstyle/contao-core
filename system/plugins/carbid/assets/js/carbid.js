/**
 * Carbid for Contao Open Source CMS
 *
 * Copyright (C) 2014 Alexander Stulnikov
 *
 * @package    Carbid
 * @link       https://github.com/alarstyle/contao-carbid
 * @license    http://opensource.org/licenses/MIT
 */

(function(Backend, Theme, Stylect, $) {

    var html = document.documentElement,
        base = document.getElementsByTagName('base')[0].getAttribute('href');

    var fontLoaded = false,
        pageLoaded = false;

    // Disable Contao core functions
    if (Backend) {
        Backend.addInteractiveHelp = function() {};
        Backend.getScrollOffset = function() {};
    }
    if (Theme) {
        Theme.hoverDiv = function(a,b) {};
    }

    /**
     * Adding custom classes to body tag
     */
    /*function addCustomClasses() {
        var strClasses = "";
        if ( window.self !== window.top ) {
            strClasses += " popup";
        }
        document.body.className = document.body.className + strClasses;
    }*/


    /**
     * Highlight element on mouse over action icon
     */
    function initElementActionsHighlight() {
        var elements, i;
        // metadata
        elements = document.querySelectorAll('#ctrl_meta li');
        for (i = 0; i < elements.length; i++) {
            var img = elements[i].getElementsByClassName('tl_metawizard_img')[0];
            if (img) {
                img.elementContainer = elements[i];
                img.setAttribute('onmouseover', 'Carbid.elementHighlight(this, "deleteAction")');
                img.setAttribute('onmouseout', 'Carbid.elementHighlight(this, "deleteAction", true)');
            }
        }
        // listing
        elements = document.querySelectorAll('.tl_listing_container tr, .tl_listing_container .tl_content, .tl_listing_container .tl_folder, .tl_listing_container .tl_file ');
        for (i = 0; i < elements.length; i++) {
            var anchor = elements[i].getElementsByClassName('delete')[0];
            if (anchor) {
                anchor.elementContainer = elements[i];
                anchor.setAttribute('onmouseover', 'Carbid.elementHighlight(this, "deleteAction")');
                anchor.setAttribute('onmouseout', 'Carbid.elementHighlight(this, "deleteAction", true)');
            }
        }
    }

    function initLogin() {
        if (!html.classList.contains('template-be_login')) return;
        var main = document.getElementById('main'),
            h2 = main.getElementsByTagName('h2')[0],
            h2IndexOfSeparator = h2.innerHTML.indexOf(':'),
            nameLabel = main.querySelector('label[for="username"]').innerHTML,
            passwordLabel = main.querySelector('label[for="password"]').innerHTML;
        if (h2IndexOfSeparator > 0) {
            h2.innerHTML = h2.innerHTML.slice(h2IndexOfSeparator+1) + '<span>' + h2.innerHTML.slice(0, h2IndexOfSeparator) + '</span>';
        }
        document.getElementById('username').setAttribute('placeholder', nameLabel);
        document.getElementById('password').setAttribute('placeholder', passwordLabel);
    }

    function initHeader() {
        var $header = $('#header'),
            $tmenu = $header.find('#tmenu'),
            $alertsContainer = $('<div class="tmenu-btn tmenu-alerts"></div>'),
            $mainContainer = $('<div class="tmenu-btn tmenu-main"></div>'),
            $alertDropdown = $('<div></div>'),
            $mainDropdown = $('<div></div>');
        $mainContainer.append('<span></span>').append( $mainDropdown.append( $tmenu.contents() ) );
        $alertsContainer.append('<span></span><b></b>').append( $alertDropdown.append( $header.find('.tl_permalert') ) );
        $tmenu.append($alertsContainer).append($mainContainer);
        $alertsContainer.find('> b').text($alertDropdown.children().length);
        return;

        if (!$alerts.length) {
            $alertContainer.addClass('hidden');
        }
        function openMenu() {
            closeAlerts();
            if ($tmenu.hasClass('opened')) {
                return;
            }
            $tmenu.addClass('opened');
            $(document).on('click', closeMenu);
        }
        function closeMenu() {
            $(document).off('click', closeMenu);
            $tmenu.removeClass('opened');
        }
        function openAlerts() {
            closeMenu();
            if ($alertContainer.hasClass('opened')) {
                return;
            }
            $alertContainer.addClass('opened');
            $(document).on('click', closeAlerts);
        }
        function closeAlerts() {
            $(document).off('click', closeAlerts);
            $alertContainer.removeClass('opened');
        }
        $alertContainer.add($tmenu).on('click', function(e) {
            e.stopPropagation();
        });
        $tmenuToggler.click(function(e) {
            e.stopPropagation();
            if ($tmenu.hasClass('opened')) {
                closeMenu();
            }
            else {
                openMenu();
            }
        });
        $alertToggler.click(function(e) {
            e.stopPropagation();
            if ($alertContainer.hasClass('opened')) {
                closeAlerts();
            }
            else {
                openAlerts();
            }
        });
    }

    function loadFonts() {

        function addFont() {
            var style = document.createElement('style');
            style.rel = 'stylesheet';
            style.textContent = localStorage.sourceOpenSans;
            document.head.appendChild(style);
            fontLoaded = true;
            showContentIfDone();
        }

        if (localStorage.sourceOpenSans) {
            addFont();
        } else {
            var fontUrl = isWoff2Supported() ? 'system/modules/carbid/assets/css/woff2.css' : 'system/modules/carbid/assets/css/woff.css';
            var request = new XMLHttpRequest();
            request.open('GET', fontUrl, true);

            request.onload = function() {
                if (request.status >= 200 && request.status < 400) {
                    localStorage.sourceOpenSans = request.responseText;
                    addFont();
                } else {
                    fontLoaded = true;
                    showContentIfDone();
                }
            };

            request.send();
        }
    }

    function styleCheckboxesAndRadios() {
        var checkboxes = document.querySelectorAll('input[type="checkbox"]:not([data-styled="1"]):not(.mw_enable)');
        for (var i = 0; i < checkboxes.length; i++) {
            checkboxes[i].setAttribute('data-styled', '1');
            checkboxes[i].insertAdjacentHTML('afterend', '<i></i>');
        }
        var radios = document.querySelectorAll('input[type="radio"]:not([data-styled="1"])');
        for (i = 0; i < radios.length; i++) {
            radios[i].setAttribute('data-styled', '1');
            radios[i].insertAdjacentHTML('afterend', '<i></i>');
        }
    }

    function addTitleAtribute() {
        var elements = document.querySelectorAll('p.tl_tip:not([title])');
        for (var i = 0; i < elements.length; i++) {
            elements[i].setAttribute('title', elements[i].innerHTML.replace('<em>','').replace('</em>',''));
        }
    }

    function sidebarScroll() {
        var sidebar = document.getElementById('tl_navigation');
        if (sidebar) {
            Ps.initialize(sidebar);
        }
    }

    function showContentIfDone() {
        if (pageLoaded && fontLoaded) {
            html.classList.add('page_loaded');
        }
    }

    window.addEvent('domready', function() {
        loadFonts();
        initLogin();
        styleCheckboxesAndRadios();
        addTitleAtribute();
        sidebarScroll();
        //addCustomClasses();
        //
        initHeader();
        //initScroll();
        //initElementActionsHighlight();
    });

    window.addEventListener('load', function() {
        pageLoaded = true;
        showContentIfDone();
    });

    // Fired with mootools
    window.addEvent('ajax_change', function() {
        addTitleAtribute();
        styleCheckboxesAndRadios();
    });

})(window.Backend, window.Theme, window.Stylect, window.jQuery);


var Carbid = {

    /**
     *
     * @param {object}  el        The DOM element
     * @param {string}  className Class name which will be added or removed
     * @param {boolean} remove    If true - class name will be removed
     */
    elementHighlight: function(el, className, remove) {
        if (!el.elementContainer) {
            el.elementContainer = el.parentNode.parentNode;
        }
        if (!remove) {
            el.elementContainer.className = el.elementContainer.className + ' deleteAction';
        }
        else {
            el.elementContainer.className = el.elementContainer.className.replace(' deleteAction', '');
        }
    },


    /**
     * Key/value wizard
     *
     * @param {object} el      The DOM element
     * @param {string} command The command name
     * @param {string} id      The ID of the target element
     */
    keyValueWizard: function(el, command, id) {
        var table = $(id),
            tbody = table.getElement('tbody'),
            parent = $(el).getParent('tr'),
            rows = tbody.getChildren(),
            tabindex = tbody.get('data-tabindex'),
            input, childs, i, j;

        Backend.getScrollOffset();

        switch (command) {
            case 'copy':
                var tr = new Element('tr');
                childs = parent.getChildren();
                for (i=0; i<childs.length; i++) {
                    var next = childs[i].clone(true).inject(tr, 'bottom');
                    if (input = childs[i].getFirst('input')) {
                        next.getFirst().value = input.value;
                        if (next.getFirst().type == 'hidden') {
                            next.getFirst().value = '';
                        }
                    }
                }
                tr.inject(parent, 'after');
                break;
            case 'up':
                if (tr = parent.getPrevious('tr')) {
                    parent.inject(tr, 'before');
                } else {
                    parent.inject(tbody, 'bottom');
                }
                break;
            case 'down':
                if (tr = parent.getNext('tr')) {
                    parent.inject(tr, 'after');
                } else {
                    parent.inject(tbody, 'top');
                }
                break;
            case 'delete':
                if (rows.length > 1) {
                    parent.destroy();
                }
                break;
        }

        rows = tbody.getChildren();

        for (i=0; i<rows.length; i++) {
            childs = rows[i].getChildren();
            for (j=0; j<childs.length; j++) {
                if (input = childs[j].getFirst('input')) {
                    input.set('tabindex', tabindex++);
                    input.name = input.name.replace(/\[[0-9]+\]/g, '[' + i + ']')
                }
            }
        }

        new Sortables(tbody, {
            contstrain: true,
            opacity: 0.6,
            handle: '.drag-handle'
        });
    }

};

function isWoff2Supported() {
    if( !( "FontFace" in window ) ) {
        return false;
    }
    var f = new FontFace('t', 'url("data:application/font-woff2;base64,d09GMgABAAAAAAIkAAoAAAAABVwAAAHcAAEAAAAAAAAAAAAAAAAAAAAAAAAAAAAABlYAgloKLEoBNgIkAxgLDgAEIAWDcgc1G7IEyB6SJAFID5YA3nAHC6h4+H7s27nP1kTyOoQkGuJWtNGIJKYznRI3VEL7IaHq985ZUuKryZKcAtJsi5eULwUybm9KzajBBhywZ5ZwoJNuwDX5C/xBjvz5DbsoNsvG1NGQiqp0NMLZ7JlnW+5MaM3HwcHheUQeiVokekHkn/FRdefvJaTp2PczN+I1Sc3k9VuX51Tb0Tqqf1deVXGdJsDOhz0/EffMOPOzHNH06pYkDDjs+P8fb/z/8n9Iq8ITzWywkP6PBMMN9L/O7vY2FNoTAkp5PpD6g1nV9WmyQnM5uPpAMHR2fe06jbfvzPriekVTQxC6lpKr43oDtRZfCATl5OVAUKykqwm9o8R/kg37cxa6eZikS7cjK4aIwoyh6jOFplhFrz2b833G3Jii9AjDUiAZ9AxZtxdEYV6imvRF0+0Nej3wu6nPZrTLh81AVcV3kmMVdQj6Qbe9qetzbuDZ7vXOlRrqooFSxCv6SfrDICA6rnHZXQPVcUHJYGcoqa3jVH7ATrjWBNYYkEqF3RFpVIl0q2JvMOJd7/TyjXHw2NyAuJpNaEbz8RTEVtCbSH7JrwQQOqwGl7sTUOtdBZIY2DKqKlvOmPvUxJaURAZZcviTT0SKHCXqzwc=") format("woff2")', {});

    f.load()['catch'](function() {});

    return f.status == 'loading' || f.status == 'loaded';
}

function insertAfter(newNode, referenceNode) {
    referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
}