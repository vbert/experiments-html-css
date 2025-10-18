var rmf = {

    message: (text) => {
        const msgPopup = document.createElement('div');
        msgPopup.classList.add('message-window');
        msgPopup.innerHTML = text;
        document.body.appendChild(msgPopup);
        window.setTimeout(() => {
            msgPopup.remove();
        }, 1000);
    },

    clipboardCopy: (text) => {
        const input = document.createElement('textarea');
        input.value = text;
        document.body.appendChild(input);
        input.select();
        document.execCommand('copy');
        document.body.removeChild(input);
    },

    shareURL: (url, title, text = '') => {
        url = new URL(document.URL).origin + url;
        const shareData = { url, title, text };
        console.log(shareData);
        if (!navigator.canShare) {
            rmf.clipboardCopy(url);
            rmf.message('Link skopiowany do schowka!');
            return false;
        } else if (navigator.canShare(shareData)) {
            try {
                navigator.share(shareData).then(() => {
                    return true;
                });
            } catch (err) {
                rmf.clipboardCopy(url);
                rmf.message('Link skopiowany do schowka!');
                return false;
            }
        } else {
            rmf.clipboardCopy(url);
            rmf.message('Link skopiowany do schowka!');
            return false;
        }
    }
};


var isMobileMenuOpen = false
var lib_mobile_menu = {
    init: function () {
        document.querySelector('.medium-line_btn_menu').addEventListener('click', function () {
            isMobileMenuOpen == false ? lib_mobile_menu.open_menu() : lib_mobile_menu.close_menu()
        })

        document.querySelectorAll('.medium-line_nav a, .main-header_megamenu a').forEach(function (item) {
            item.addEventListener('click', function () {
                lib_mobile_menu.close_menu()
            })
        })
    },
    open_menu: function () {
        isMobileMenuOpen = true
        document.querySelector('.medium-line_btn_menu').classList.add('opened')
        document.querySelector('.medium-line_nav').classList.add('opened')
        document.querySelector('.main-header_megamenu').classList.add('opened')
    },
    close_menu: function () {
        isMobileMenuOpen = false
        document.querySelector('.medium-line_btn_menu').classList.remove('opened')
        document.querySelector('.medium-line_nav').classList.remove('opened')
        document.querySelector('.main-header_megamenu').classList.remove('opened')
    },
};

var lib_sliders = {
    init: function () {
        // Init slider Podcasts / Index
        if (document.querySelector('#owl-podcasts') != null) {
            $('#owl-podcasts').owlCarousel({
                navText: ['', ''],
                loop: true,
                margin: 25,
                nav: true,
                items: 3,
                dots: false,
                responsive: {
                    0: {
                        items: 2,
                        margin: 15,
                    },
                    769: {
                        items: 3,
                    },
                },
            })
        }

        // Init slider Podcasts / Sidebar
        if (document.querySelector('#owl-podcasts-sidebar') != null) {
            $('#owl-podcasts-sidebar').owlCarousel({
                navText: ['', ''],
                loop: true,
                margin: 15,
                nav: true,
                items: 2,
                dots: false,
                responsive: {
                    0: {
                        items: 2,
                        margin: 15,
                    },
                    769: {
                        items: 3,
                    },
                },
            })
        }
    },
};

var lib_sticky_header = {
    force_sticked: false,
    init: function () {

        if (window.navigator.userAgent.match(/Firefox\/([0-9]+)\./)) {
            $('a[href*="#"]').click(function (event) {
                var href = $(this.hash);
                if (href.length) {
                    event.preventDefault();
                    const y = href[0].getBoundingClientRect().top + window.pageYOffset - $('.main-header').height();
                    window.scrollTo({ top: y, behavior: 'smooth' });
                }
            });
        }

        $(".main-header").attr('data-height', $('.main-header').height());
        $(".main-header").sticky({ topSpacing: 0, zIndex: 99999 });

        $(".main-header").on('sticky-start', function () {
            $(".main-header").addClass('sticked');
        });

        $('.main-header').on('sticky-end', function () {
            if (!lib_sticky_header.force_sticked) {
                $(".main-header").removeClass('sticked');
                lib_sticky_header.update_sticky_height();
            }
        });

        $(window).on("resize", lib_sticky_header.update_sticky_height);
    },
    update_sticky_height: function () {
        if (!lib_sticky_header.force_sticked) {
            document.querySelector('#sticky-wrapper').style.height = (window.innerWidth > 1199) ? '116px' : '46px';
        } else {
            document.querySelector('#sticky-wrapper').style.height = '45px';
        }
    }
};

(function () {
    // Mega Menu
    const openMegaMenu = document.querySelector('.open-mega-menu');
    const megaMenu = document.querySelector('.main-header_megamenu');
    const openSubMenus = document.querySelectorAll('.open-sub-menu');

    openMegaMenu.addEventListener('mouseenter', function () {
        megaMenu.classList.add('d-block');
    });
    document.querySelector('.main-header').addEventListener('mouseleave', function () {
        megaMenu.classList.remove('d-block');
    });
    openSubMenus.forEach(s => {
        s.addEventListener('mouseenter', function () {
            megaMenu.classList.remove('d-block');
            s.querySelector('.submenu').classList.add('shown');
        });
        s.addEventListener('mouseleave', function () {
            s.querySelector('.submenu').classList.remove('shown');
        });
    });


    // Sticky Header
    lib_sticky_header.init();

    // Mobile Menu
    lib_mobile_menu.init();

    // Sliders
    lib_sliders.init();

    setTimeout(function () { if (typeof gtag != "undefined") gtag('event', 'mk_czas_spedzony_7s'); }, 7000);
})()
