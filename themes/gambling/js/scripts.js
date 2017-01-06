(function ($) {

    var $w = $(window);

    (function () {
        function preventWindowScroll(e) {
            e.preventDefault();
        }

        function touchmoveOnScrollable(e) {
            if (e.currentTarget.scrollHeight === e.currentTarget.offsetHeight) return;
            e.stopPropagation();
        }

        function touchstartOnScrollable(e) {
            if (e.currentTarget.scrollTop === 0) {
                e.currentTarget.scrollTop = 1;
            } else if (e.currentTarget.scrollHeight === e.currentTarget.scrollTop + e.currentTarget.offsetHeight) {
                e.currentTarget.scrollTop -= 1;
            }
        }

        var scrollableElements = '.filter-content .inner';
        window.disableScroll = function () {
            $('html').css('overflow-y', 'hidden');
            $(window).on('touchmove', preventWindowScroll);
            $('body')
                .on('touchmove', scrollableElements, touchmoveOnScrollable)
                .on('touchstart', scrollableElements, touchstartOnScrollable);
        };
        window.enableScroll = function () {
            $('html').css('overflow-y', '');
            $(window).off('touchmove', preventWindowScroll);
            $('body')
                .off('touchmove', scrollableElements, touchmoveOnScrollable)
                .off('touchstart', scrollableElements, touchstartOnScrollable);
        };
    })();


    function initSlider() {
        if ($w.width < 768) return;
        $('.slider').each(function () {
            var $slider = $(this);
            $slider.slick({
                arrows: true,
                dots: true,
                infinite: true,
                slidesToShow: 1
            })
        });
    }

    function initDropdown() {
        var isDropdownClick = false,
            $dropdowns = $('.dropdown');
        $(document).on('click', function () {
            if (!isDropdownClick) {
                $dropdowns.removeClass('opened');
            }
            isDropdownClick = false;
        });
        $dropdowns.each(function () {
            var $dropdown = $(this),
                $btn = $dropdown.find('.dropdown-btn'),
                $menu = $dropdown.find('.dropdown-menu'),
                hasActions = $dropdown.hasClass('dropdown--menu_actions');
            $btn.on('click', function () {
                isDropdownClick = true;
                if ($dropdown.hasClass('opened')) {
                    $dropdown.removeClass('opened');
                }
                else {
                    $dropdowns.removeClass('opened');
                    $dropdown.addClass('opened');
                }
            });
            if (hasActions) {
                $menu.on('click', function () {
                    isDropdownClick = true;
                })
            }
        });
    }

    function initSubmenu() {
        $('.submenu').each(function () {
            var $submenu = $(this);
        })
    }

    function initContact() {

    }


    function initFilter() {
        var $filter = $('.filter');
        if (!$filter.length) return;

        var $open = $filter.find('.filter-open'),
            $close = $filter.find('.filter-close'),
            isOpened = false;

        $open.on('click', function () {
            isOpened = true;
            $filter.addClass('opened');
            disableScroll();
        });

        $close.on('click', function () {
            isOpened = false;
            $filter.removeClass('opened');
            enableScroll();
        });

        $w.on('resize', function () {
            if ($w.width() >= 768 && isOpened) {
                $close.click();
            }
        });
    }


    function initSearch() {
        $('.search').each(function () {
            var $search = $(this),
                $inp = $search.find('input'),
                $clear = $search.find('.search-clear');

            $clear.on('click', function () {
                $inp.val('');
            });
        });
    }


    function initCasino() {
        $('.casino').each(function () {
            var $casino = $(this),
                $tabs = $casino.find('.casino-tabs div'),
                $contentItems = $casino.find('.casino-review, .casino-overview');
            $tabs.each(function (i) {
                $(this).on('click', function () {
                    if ($(this).hasClass('active')) return;
                    $tabs.removeClass('active');
                    $(this).addClass('active');
                    $contentItems.removeClass('active').eq(i).addClass('active');
                });
            });
        });
    }


    function initCountdown() {
        var $elements = $('[data-end]');
        if (!$elements.length) return;

        function leadingZero(num) {
            return ('0' + num).slice(-2);
        }

        function setText() {
            var $this = $(this),
                end = parseInt($this.data('end'));

            if (!end) return;

            var diff = end - Math.floor(Date.now() / 1000); // Difference in seconds
            diff = diff > 0 ? diff : 0;
            var days = Math.floor(diff / (24 * 60 * 60));
            diff = diff - days * 24 * 60 * 60;
            var hours = Math.floor(diff / (60 * 60));
            diff = diff - hours * 60 * 60;
            var minutes = Math.floor(diff / 60);
            diff = diff - minutes * 60;
            var seconds = diff;

            var text = '';

            if (days > 0) {
                text = leadingZero(days) + 'd ' + leadingZero(hours) + 'h ' + leadingZero(minutes) + 'm';
            }
            else {
                text = leadingZero(hours) + 'h ' + leadingZero(minutes) + 'm ' + leadingZero(seconds) + 's';
            }

            $this.text(text);

            // console.log(new Date(end * 1000));
            // console.log(days, hours, minutes, seconds);
        }

        $elements.each(setText);

        setInterval(function () {
            $elements.each(setText);
        }, 1000);
    }


    function initBackBtns() {
        var origin = window.location.origin,
            referrer = document.referrer;

        if (referrer.indexOf(origin) !== 0) return;

        $('.back_link').click(function(e) {
            e.preventDefault();
            window.history.back();
        });
    }


    $(function () {
        initSlider();
        initDropdown();
        initSubmenu();
        initContact();
        initFilter();
        initSearch();
        initCasino();
        initCountdown();
        initBackBtns();

        $('a[href="#"]').click(function(e) {
            e.preventDefault();
        });
    });

})(jQuery);