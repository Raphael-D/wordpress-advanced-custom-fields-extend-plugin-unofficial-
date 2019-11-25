jQuery( window ).on( 'elementor/frontend/init', function() {

    elementorFrontend.hooks.addAction( 'frontend/element_ready/wts-gmap.default', function( $scope ) {
        map = new_map($scope.find('.eae-markers'));

        function new_map( $el ) {
            $wrapper = $scope.find('.eae-markers');
            var zoom = $wrapper.data('zoom');
            var $markers = $el.find('.marker');
            var styles = $wrapper.data('style');
            var prevent_scroll = $wrapper.data('scroll');
            // vars
            var args = {
                zoom		: zoom,
                center		: new google.maps.LatLng(0, 0),
                mapTypeId	: google.maps.MapTypeId.ROADMAP,
                styles		: styles
            };

            // create map
            var map = new google.maps.Map( $el[0], args);

            // add a markers reference
            map.markers = [];

            // add markers
            $markers.each(function(){
                add_marker( jQuery(this), map );
            });

            // center map
            center_map( map, zoom );

            // return
            return map;
        }

        function add_marker( $marker, map ) {
            var animate = $wrapper.data('animate');
            var info_window_onload = $wrapper.data('show-info-window-onload');
            //console.log(info_window_onload);
            $wrapper = $scope.find('.eae-markers');
            //alert($marker.attr('data-lat') + ' - '+ $marker.attr('data-lng'));
            var latlng = new google.maps.LatLng( $marker.attr('data-lat'), $marker.attr('data-lng') );

            icon_img = $marker.attr('data-icon');
            if(icon_img != ''){
                var icon = {
                    url : $marker.attr('data-icon'),
                    scaledSize: new google.maps.Size($marker.attr('data-icon-size'), $marker.attr('data-icon-size'))
                };

            }


            //var icon = $marker.attr('data-icon');

            // create marker
            var marker = new google.maps.Marker({
                position	: latlng,
                map			: map,
                icon        : icon,
                animation: google.maps.Animation.DROP
            });
            if(animate == 'animate-yes' && $marker.data('info-window') != 'yes'){
                marker.setAnimation(google.maps.Animation.BOUNCE);
            }
            if(animate == 'animate-yes'){
                google.maps.event.addListener(marker, 'click', function() {
                    marker.setAnimation(null);
                });
            }



            // add to array
            map.markers.push( marker );
            // if marker contains HTML, add it to an infoWindow

            if( $marker.html() )
            {
                // create info window
                var infowindow = new google.maps.InfoWindow({
                    content		: $marker.html()
                });

                // show info window when marker is clicked
                if($marker.data('info-window') == 'yes'){
                    infowindow.open(map, marker);
                }
                google.maps.event.addListener(marker, 'click', function() {
                    infowindow.open( map, marker );
                });

            }
            if(animate == 'animate-yes') {
                google.maps.event.addListener(infowindow, 'closeclick', function () {
                    marker.setAnimation(google.maps.Animation.BOUNCE);
                });
            }
        }

        function center_map( map, zoom ) {

            // vars
            var bounds = new google.maps.LatLngBounds();
            // loop through all markers and create bounds
            jQuery.each( map.markers, function( i, marker ){
                var latlng = new google.maps.LatLng( marker.position.lat(), marker.position.lng() );
                bounds.extend( latlng );
            });

            // only 1 marker?
            if( map.markers.length == 1 )
            {
                // set center of map
                map.setCenter( bounds.getCenter() );
                map.setZoom( zoom );
            }
            else
            {
                // fit to bounds
                map.fitBounds( bounds );
            }
        }
    });

    elementorFrontend.hooks.addAction( 'frontend/element_ready/global', function ( $scope ) {

        var eae_slides = [];
        var eae_slides_json = [];
        var eae_transition;
        var eae_animation;
        var eae_custom_overlay;
        var eae_overlay;
        var eae_cover;
        var eae_delay;
        var eae_timer;
        var slider_wrapper = $scope.children('.eae-section-bs').children('.eae-section-bs-inner');

        if (slider_wrapper && slider_wrapper.data('eae-bg-slider')) {

            slider_images = slider_wrapper.data('eae-bg-slider');
            eae_transition = slider_wrapper.data('eae-bg-slider-transition');
            eae_animation = slider_wrapper.data('eae-bg-slider-animation');
            eae_custom_overlay = slider_wrapper.data('eae-bg-custom-overlay');
            if (eae_custom_overlay == 'yes') {
                eae_overlay = eae_editor.plugin_url + 'assets/lib/vegas/overlays/' + slider_wrapper.data('eae-bg-slider-overlay');
            } else {
                if (slider_wrapper.data('eae-bg-slider-overlay')) {
                    eae_overlay = eae_editor.plugin_url + 'assets/lib/vegas/overlays/' + slider_wrapper.data('eae-bg-slider-overlay');
                } else {
                    eae_overlay = eae_editor.plugin_url + 'assets/lib/vegas/overlays/' + slider_wrapper.data('eae-bg-slider-overlay');
                }
            }

            eae_cover = slider_wrapper.data('eae-bg-slider-cover');
            eae_delay = slider_wrapper.data('eae-bs-slider-delay');
            eae_timer = slider_wrapper.data('eae-bs-slider-timer');

            if (typeof slider_images != 'undefined') {
                eae_slides = slider_images.split(",");

                jQuery.each(eae_slides, function (key, value) {
                    var slide = [];
                    slide.src = value;
                    eae_slides_json.push(slide);
                });

                slider_wrapper.vegas({
                    slides: eae_slides_json,
                    transition: eae_transition,
                    animation: eae_animation,
                    overlay: eae_overlay,
                    cover: eae_cover,
                    delay: eae_delay,
                    timer: eae_timer,
                    init: function () {
                        if (eae_custom_overlay == 'yes') {
                            var ob_vegas_overlay = slider_wrapper.children('.vegas-overlay');
                            ob_vegas_overlay.css('background-image', '');
                        }
                    }
                });

            }
        }
    });

});

(function( $ ) {

    $(window).on('elementor/frontend/init', function () {

        var ab_image = function ($scope, $) {
            $scope.find('.eae-img-comp-container').imagesLoaded().done(function () {
                ab_style = $scope.find('.eae-img-comp-container').data("ab-style");
                slider_pos = $scope.find('.eae-img-comp-container').data("slider-pos");
                if (ab_style === "horizontal") {
                    horizontal($scope);
                } else {
                    vertical($scope);
                }
            });

            function horizontal($scope) {
                var x, i, start_pos;
                /*find all elements with an "overlay" class:*/
                x = $scope.find(".eae-img-comp-overlay");
                start_pos = x.width();
                start_pos = start_pos * slider_pos / 100;
                compareImages(x[0]);

                function compareImages(img) {
                    var slider, clicked = 0, w, h;
                    /*get the width and height of the img element*/
                    w = img.offsetWidth;
                    h = img.offsetHeight;
                    /*set the width of the img element to 50%:*/
                    img.style.width = start_pos + "px";
                    /*create slider:*/
                    slider = $scope.find(".eae-img-comp-slider");
                    slider = slider[0];
                    /*position the slider in the middle:*/
                    slider.style.top = (h / 2) - (slider.offsetHeight / 2) + "px";
                    slider.style.left = start_pos - (slider.offsetWidth / 2) + "px";
                    /*execute a function when the mouse button is pressed:*/
                    if (!$scope.hasClass('elementor-element-edit-mode')) {
                        slider.addEventListener("mousedown", slideReady);
                        //slider.addEventListener("mouseover", slideReady);
                        //img.addEventListener("mouseover", slideReady);

                        /*and another function when the mouse button is released:*/
                        window.addEventListener("mouseup", slideFinish);
                        //slider.addEventListener("mouseout", slideFinish);
                        //img.addEventListener("mouseout", slideFinish);
                        /*or touched (for touch screens:*/
                        slider.addEventListener("touchstart", slideReady);
                        /*and released (for touch screens:*/
                        window.addEventListener("touchstop", slideFinish);
                    }

                    function slideReady(e) {
                        /*prevent any other actions that may occur when moving over the image:*/
                        e.preventDefault();
                        /*the slider is now clicked and ready to move:*/
                        clicked = 1;
                        /*execute a function when the slider is moved:*/
                        window.addEventListener("mousemove", slideMove);
                        //window.addEventListener("mouseover", slideMove);
                        //window.addEventListener("touchmove", slideMove);
                        slider.addEventListener("touchmove", touchMoveaction);
                    }

                    function slideFinish() {
                        /*the slider is no longer clicked:*/
                        clicked = 0;
                    }

                    function slideMove(e) {
                        var pos;
                        /*if the slider is no longer clicked, exit this function:*/
                        if (clicked == 0) return false;
                        /*get the cursor's x position:*/
                        pos = getCursorPos(e);
                        /*prevent the slider from being positioned outside the image:*/
                        if (pos < 0) pos = 0;
                        if (pos > w) pos = w;
                        /*execute a function that will resize the overlay image according to the cursor:*/
                        slide(pos);
                    }

                    function touchMoveaction(e) {
                        var pos;
                        /*if the slider is no longer clicked, exit this function:*/
                        if (clicked == 0) return false;
                        /*get the cursor's x position:*/
                        pos = getTouchPos(e);

                        /*prevent the slider from being positioned outside the image:*/
                        if (pos < 0) pos = 0;
                        if (pos > w) pos = w;
                        /*execute a function that will resize the overlay image according to the cursor:*/
                        slide(pos);
                    }

                    function getTouchPos(e) {
                        var a, x = 0;
                        a = img.getBoundingClientRect();

                        /*calculate the cursor's x coordinate, relative to the image:*/
                        x = e.changedTouches[0].clientX - a.left;
                        return x;
                    }

                    function getCursorPos(e) {
                        var a, x = 0;
                        e = e || window.event;
                        /*get the x positions of the image:*/
                        a = img.getBoundingClientRect();
                        /*calculate the cursor's x coordinate, relative to the image:*/
                        x = e.pageX - a.left;

                        /*consider any page scrolling:*/
                        //x = x - window.pageXOffset;
                        return x;
                    }

                    function slide(x) {
                        /*resize the image:*/
                        img.style.width = x + "px";
                        /*position the slider:*/
                        slider.style.left = img.offsetWidth - (slider.offsetWidth / 2) + "px";
                    }
                }
            }

            function vertical($scope) {
                var x, i;
                /*find all elements with an "overlay" class:*/
                //x = document.getElementsByClassName("eae-img-comp-overlay");
                x = $scope.find(".eae-img-comp-overlay");
                start_pos = x.height();
                start_pos = start_pos * slider_pos / 100;
                compareImages(x[0]);

                function compareImages(img) {
                    var slider, img, clicked = 0, w, h;
                    /*get the width and height of the img element*/
                    w = img.offsetWidth;
                    h = img.offsetHeight;
                    /*set the width of the img element to 50%:*/
                    img.style.height = start_pos + "px";
                    /*create slider:*/
                    slider = $scope.find(".eae-img-comp-slider");
                    slider = slider[0];
                    /*position the slider in the middle:*/
                    slider.style.top = start_pos - (slider.offsetHeight / 2) + "px";
                    slider.style.left = (w / 2) - (slider.offsetWidth / 2) + "px";
                    /*execute a function when the mouse button is pressed:*/
                    if (!$scope.hasClass('elementor-element-edit-mode')) {
                        slider.addEventListener("mousedown", slideReady);
                        /*and another function when the mouse button is released:*/
                        window.addEventListener("mouseup", slideFinish);
                        /*or touched (for touch screens:*/
                        slider.addEventListener("touchstart", slideReady);
                        /*and released (for touch screens:*/
                        window.addEventListener("touchstop", slideFinish);
                    }

                    function slideReady(e) {
                        /*prevent any other actions that may occur when moving over the image:*/
                        e.preventDefault();
                        /*the slider is now clicked and ready to move:*/
                        clicked = 1;
                        /*execute a function when the slider is moved:*/
                        window.addEventListener("mousemove", slideMove);
                        slider.addEventListener("touchmove", touchMoveaction);
                    }

                    function slideFinish() {
                        /*the slider is no longer clicked:*/
                        clicked = 0;
                    }

                    function slideMove(e) {
                        var pos;
                        /*if the slider is no longer clicked, exit this function:*/
                        if (clicked == 0) return false;
                        /*get the cursor's x position:*/
                        pos = getCursorPos(e)
                        /*prevent the slider from being positioned outside the image:*/
                        if (pos < 0) pos = 0;
                        if (pos > h) pos = h;
                        /*execute a function that will resize the overlay image according to the cursor:*/
                        slide(pos);
                    }

                    function getCursorPos(e) {
                        var a, x = 0;
                        e = e || window.event;
                        /*get the x positions of the image:*/
                        a = img.getBoundingClientRect();
                        /*calculate the cursor's x coordinate, relative to the image:*/
                        x = e.pageY - a.top;
                        /*consider any page scrolling:*/
                        x = x - window.pageYOffset;

                        return x;
                    }

                    function touchMoveaction(e) {
                        var pos;
                        /*if the slider is no longer clicked, exit this function:*/
                        if (clicked == 0) return false;
                        /*get the cursor's x position:*/
                        pos = getTouchPos(e);

                        /*prevent the slider from being positioned outside the image:*/
                        if (pos < 0) pos = 0;
                        if (pos > h) pos = h;
                        /*execute a function that will resize the overlay image according to the cursor:*/
                        slide(pos);
                    }

                    function getTouchPos(e) {
                        var a, x = 0;
                        a = img.getBoundingClientRect();

                        /*calculate the cursor's x coordinate, relative to the image:*/
                        x = e.changedTouches[0].clientY - a.top;

                        //x = x - slider.offsetHeight;

                        return x;
                    }

                    function slide(x) {
                        /*resize the image:*/
                        img.style.height = x + "px";
                        /*position the slider:*/
                        slider.style.top = img.offsetHeight - (slider.offsetHeight / 2) + "px";
                    }
                }
            }
        };

        var ParticlesBG = function ($scope, $) {

            if ($scope.hasClass('eae-particle-yes')) {
                id = $scope.data('id');
                //console.lgo(id);
                element_type = $scope.data('element_type');
                pdata = $scope.data('eae-particle');
                pdata_wrapper = $scope.find('.eae-particle-wrapper').data('eae-pdata');
                if (typeof pdata != 'undefined' && pdata != '') {
                    if ($scope.find('.eae-section-bs').length > 0) {
                        $scope.find('.eae-section-bs').after('<div class="eae-particle-wrapper" id="eae-particle-' + id + '"></div>');
                        particlesJS('eae-particle-' + id, pdata);
                    } else {

                        if (element_type == 'column') {

                            $scope.find('.elementor-column-wrap').prepend('<div class="eae-particle-wrapper" id="eae-particle-' + id + '"></div>');
                        } else {
                            $scope.prepend('<div class="eae-particle-wrapper" id="eae-particle-' + id + '"></div>');
                        }

                        particlesJS('eae-particle-' + id, pdata);
                    }


                } else if (typeof pdata_wrapper != 'undefined' && pdata_wrapper != '') {

                    // $scope.prepend('<div class="eae-particle-wrapper" id="eae-particle-'+ id +'"></div>');
                    //console.log('calling particle js else', JSON.parse(pdata_wrapper));
                    if (element_type == 'column') {
                        $scope.find('.elementor-column-wrap').prepend('<div class="eae-particle-wrapper" id="eae-particle-' + id + '"></div>');
                    }
                    else{
                        $scope.prepend('<div class="eae-particle-wrapper" id="eae-particle-' + id + '"></div>');
                    }

                    particlesJS('eae-particle-' + id, JSON.parse(pdata_wrapper));
                }

            }

        };


        /*EAE Animated Gradient Background*/

        var AnimatedGradient = function ($scope, $) {


            if ($scope.hasClass('eae-animated-gradient-yes')) {
                id = $scope.data('id');
                //editMode    = elementorFrontend.isEditMode();
                //console.log(settings);
                color = $scope.data('color');
                angle = $scope.data('angle');
                var gradient_color = 'linear-gradient(' + angle + ',' + color + ')';
                heading = $scope.find('.elementor-heading-title');
                $scope.css('background-image', gradient_color);
                if($scope.hasClass('elementor-element-edit-mode'))
                {

                    color = $scope.find('.animated-gradient').data('color');
                    angle = $scope.find('.animated-gradient').data('angle');
                    gradient_color_editor = 'linear-gradient(' + angle + ',' + color + ')';
                    $scope.prepend('<div class="animated-gradient" style="background-image : ' + gradient_color_editor + ' "></div>');
                    //$scope.find('.animated-gradient').css('background-image', gradient_color_editor);
                    //$scope.find('.animated-gradient').css('background-color', 'red');
                }
                //$scope.css('position', 'relative');
                //$scope.css('background-color', 'black');

            }
        };

        var EaePopup = function ($scope, $) {
            $preview_modal = $scope.find('.eae-popup-wrapper').data('preview-modal');
            $close_btn = $scope.find('.eae-popup-wrapper').data('close-btn');

            $magnific = $scope.find('.eae-popup-link').eaePopup({
                type: 'inline',

                disableOn: 0,

                key: null,

                midClick: false,

                mainClass: 'eae-popup eae-popup-' + $scope.find('.eae-popup-link').data('id') + ' eae-wrap-' + $scope.find('.eae-popup-link').data('ctrl-id'),

                preloader: true,

                focus: '', // CSS selector of input to focus after popup is opened

                closeOnContentClick: false,

                closeOnBgClick: true,

                closeBtnInside: $scope.find('.eae-popup-wrapper').data('close-in-out'),

                showCloseBtn: true,

                enableEscapeKey: false,

                modal: false,

                alignTop: false,

                removalDelay: 0,

                prependTo: null,

                fixedContentPos: 'auto',

                fixedBgPos: 'auto',

                overflowY: 'auto',

                closeMarkup: '<i class="eae-close ' + $close_btn + '"> </i>',

                tClose: 'Close (Esc)',

                tLoading: 'Loading...',

                autoFocusLast: true
            });

            if ($preview_modal == 'yes') {
                if ($scope.hasClass('elementor-element-edit-mode')) {
                    $scope.find('.eae-popup-link').click();
                }
            }
        };


        var EAETestimonial = function ($scope, $) {
            if ($scope.find('.eae-grid-wrapper').hasClass('eae-masonry-yes')) {
                //console.log('grid');
                var grid = $scope.find('.eae-grid');
                var $grid_obj = grid.masonry({});
                $grid_obj.imagesLoaded().progress(function () {
                    $grid_obj.masonry('layout');
                });
            }
            if ($scope.find('.eae-layout-carousel').length) {
                outer_wrapper = $scope.find('.eae-swiper-outer-wrapper');
                wid = $scope.data('id');
                wclass = '.elementor-element-' + wid;
                var direction = outer_wrapper.data('direction');
                var speed = outer_wrapper.data('speed');
                var autoplay = outer_wrapper.data('autoplay')
                var duration = outer_wrapper.data('duration');
                //console.log(duration);
                var effect = outer_wrapper.data('effect');
                var space = outer_wrapper.data('space');
                var loop = outer_wrapper.data('loop');
                if (loop == 'yes') {
                    loop = true;
                }
                else {
                    loop = false;
                }
                var slides_per_view = outer_wrapper.data('slides-per-view');
                var slides_per_group = outer_wrapper.data('slides-per-group');
                var ptype = outer_wrapper.data('ptype');
                var navigation = outer_wrapper.data('navigation');
                var clickable = outer_wrapper.data('clickable');
                var keyboard = outer_wrapper.data('keyboard');
                var scrollbar = outer_wrapper.data('scrollbar');
                adata = {
                    direction: direction,
                    effect: effect,
                    spaceBetween: space.desktop,
                    loop: loop,
                    speed: speed,
                    slidesPerView: slides_per_view.desktop,
                    slidesPerGroup: slides_per_group.desktop,
                    observer: true,
                    mousewheel: {
                        invert: true,
                    },
                    breakpoints: {
                        1024: {
                            spaceBetween: space.tablet,
                            slidesPerView: slides_per_view.tablet,
                            slidesPerGroup: slides_per_group.tablet,
                        },
                        767: {
                            spaceBetween: space.mobile,
                            slidesPerView: slides_per_view.mobile,
                            slidesPerGroup: slides_per_group.mobile,
                        }
                    }
                };
                if (effect == 'fade') {
                    adata['fadeEffect'] = {
                        crossFade: false,
                    }
                }
                if (autoplay == 'yes') {
                    adata['autoplay'] = {
                        delay: duration,
                        disableOnInteraction: false,
                    };
                }
                else {
                    adata['autoplay'] = false;
                }
                if (navigation == 'yes') {
                    adata['navigation'] = {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev',
                    }
                }
                if (ptype != '') {
                    adata['pagination'] = {
                        el: '.swiper-pagination',
                        type: ptype,
                    }
                }
                if (ptype == 'bullets' && clickable == 'yes') {
                    adata['pagination'] = {
                        el: '.swiper-pagination',
                        clickable: true,
                        type: ptype,
                    }
                }
                if (scrollbar == 'yes') {
                    adata['scrollbar'] = {
                        el: '.swiper-scrollbar',
                        draggable: true,
                    }
                }
                if (keyboard == 'yes') {
                    adata['keyboard'] = {
                        enabled: true,
                        onlyInViewport: true,
                    }
                }
                if (loop == false) {
                    adata['autoplay'] = {
                        delay: duration,
                        stopOnLastSlide: true,
                        disableOnInteraction: false,
                    }
                }
                //console.log(adata);
                window.mswiper = new Swiper('.elementor-element-' + wid + ' .eae-swiper-outer-wrapper .swiper-container', adata);
                $('.elementor-element-' + wid + ' .eae-swiper-outer-wrapper .swiper-container').css('visibility', 'visible');
            }
        };

        /* Info Circle */
        var InfoCircleHandler = function ($scope, $) {

            $wrap_class = '.elementor-element-' + $scope.data('id');
            $angle = 0;

            function set_icon_mobile($wrap_class) {
                $icons = $(document).find($wrap_class).find('.eae-ic-icon-wrap');

                if (window.innerWidth < 767) {
                    $icons.each(function (index, value) {
                        $(value).css('top', $(value).height() / 2 + 8 + "px");
                        $(value).next('.eae-info-circle-item__content-wrap').css('padding-top', $(value).height() / 2 + 8 + "px");
                    });
                }
                else {
                    $icons.each(function (index, value) {
                        $(value).css('margin-left', $(value).outerWidth() * -.5);
                        $(value).css('margin-top', $(value).outerHeight() * -.5);
                        $a = arc_to_coords($angle);
                        $b = 360 / $icons.length;
                        $(value).css('left', $a.x + "%");
                        $(value).css('top', $a.y + "%");
                        $angle += $b;
                    });
                }
            }

            set_icon_mobile($scope);

            function arc_to_coords(angle) {
                angle = (angle - 90) * Math.PI / 180;

                return {
                    x: 50 + (45 * Math.cos(angle)),
                    y: 50 + (45 * Math.sin(angle))
                }
            }

            var timer = null;
            $autoplayDuration = $scope.find('.eae-info-circle').data('delay');

            function startSetInterval() {
                if ($scope.find('.eae-info-circle').data('autoplay') == 'yes') {
                    timer = setInterval(showDiv, $autoplayDuration);
                }
            }

            // start function on page load
            startSetInterval();

            // hover behaviour
            $scope.find('.eae-ic-icon-wrap').hover(function () {
                clearInterval(timer);
            }, function () {
                startSetInterval();
            });
            if ($scope.find('.eae-info-circle-item').length > 0) {
                $($scope.find('.eae-info-circle-item')[0]).addClass('eae-active');
            }

            $scope.find('.eae-ic-icon-wrap').on('click mouseenter', function () {
                $scope.find('.eae-info-circle-item').removeClass('eae-active');
                $(this).parent().addClass('eae-active');
            });

            function showDiv() {
                if ($scope.find('.eae-active').next().length > 0) {
                    $scope.find('.eae-active').next().addClass('eae-active').siblings().removeClass('eae-active');
                }
                else {
                    $scope.find('.eae-info-circle-item').eq(0).addClass('eae-active').siblings().removeClass('eae-active');
                }
            }

            window.addEventListener("resize", set_icon_mobile.bind(this, $wrap_class));
        };

        var TimelineHandler = function ($scope, $) {

            set_progress_bar();

            function set_progress_bar() {
                var pb = $scope.find(".eae-timline-progress-bar");
                var items = $scope.find(".eae-timeline-item");
                var tl = $scope.find(".eae-timeline");
                var h = $(tl).height();
                var last_offset = $(items).last().find('.eae-tl-icon-wrapper').offset().top - $(items[0]).parent().offset().top;
                var icon_width = $scope.find('.eae-tl-icon-wrapper');

                $(pb).css('top', $(items[0]).find('.eae-tl-icon-wrapper').offset().top - $(items[0]).parent().offset().top);
                $(pb).css('bottom', h - last_offset);
                $(pb).css('left', icon_width.eq(0)[0].offsetLeft + icon_width.eq(0).width() / 2);

                items.each(function (index, value) {
                    var waypoint = new Waypoint({
                        element: $(value),
                        handler: function (direction) {
                            if (direction == 'down') {
                                $(value).addClass('eae-tl-item-focused');
                            }
                            else {
                                $(value).removeClass('eae-tl-item-focused');
                            }
                        },
                        offset: 100,
                    })
                });
            }

            function progress_bar_increment() {
                var pb = $scope.find(".eae-timline-progress-bar");

                //jQuery(".eae-timline-progress-bar").css('top', $scope.find(".eae-timeline").offset().top + 50);
                $scope.find(".eae-pb-inner-line").css('height', $(window).scrollTop() - $scope.find(".eae-timeline").offset().top + 100);
                $scope.find(".eae-pb-inner-line").css('max-height', $scope.find(".eae-pb-inner-line").parent().height());
            }

            // listen for events
            //window.addEventListener("load", set_progress_bar);
            window.addEventListener("resize", set_progress_bar);
            window.addEventListener("scroll", progress_bar_increment);
        };

        function eaeSetCookie(cname, cvalue, exdays) {
            var d = new Date();
            d.setTime(d.getTime() + (exdays * 60 * 60 * 1000));

            //console.log('exp time',cookie_expire);
            //d.setTime(d.getTime() + ( exdays * 60 * 60 * 1000));
            var expires = "expires=" + d.toUTCString();
            document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";

        };

        function eaeGetCookie(cname) {
            var name = cname + "=";
            var decodedCookie = decodeURIComponent(document.cookie);
            var ca = decodedCookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') {
                    c = c.substring(1);
                }
                if (c.indexOf(name) == 0) {
                    return c.substring(name.length, c.length);
                }
            }
            return "";
        };

        var EgTimerSkin1 = function ($scope, $) {

            var countDownDate = $scope.find(".eae-evergreen-wrapper").data("egtime");
            var cookie_expire = $scope.find(".eae-evergreen-wrapper").data("egt-expire");
            var element_type = $scope.find(".eae-evergreen-wrapper").data("element-type");
            var element_id = "eae-" + $scope.find(".eae-evergreen-wrapper").data("id");
            var element_cookie_id = "eae-temp-" + $scope.find(".eae-evergreen-wrapper").data("id");
            var actions = $scope.find(".eae-evergreen-wrapper").data("actions");

            var unqId = $scope.find(".eae-evergreen-wrapper").data("unqid");

            var now = new Date().getTime();

            // for front end time get from cookie

            if (!$scope.hasClass('elementor-element-edit-mode')) {
                if (element_type === 'countdown') {
                    date1 = new Date(countDownDate);
                    countDownDate = date1.getTime();

                    var expires1 = "expires=" + date1.toUTCString();
                    document.cookie = element_cookie_id + "=" + date1.getTime() + ";" + expires1 + ";path=/";
                }
                else {
                    var first_load_value = eaeGetCookie(element_id);
                    var date1 = "";
                    if (first_load_value !== "") {
                        date1 = new Date(parseInt(first_load_value));
                        date1.setSeconds(date1.getSeconds() + $scope.find(".eae-evergreen-wrapper").data("egtime"));
                        countDownDate = date1.getTime();

                        var d2 = new Date(parseInt(first_load_value));
                        d2.setTime(d2.getTime() + (cookie_expire * 60 * 60 * 1000));
                        var expires2 = "expires=" + d2.toUTCString();
                        document.cookie = element_id + "=" + first_load_value + ";" + expires2 + ';path=/';


                        var d1 = new Date(parseInt(first_load_value));
                        d1.setTime(d1.getTime() + ($scope.find(".eae-evergreen-wrapper").data("egtime") * 1000));
                        var expires1 = "expires=" + d1.toUTCString();

                        //console.log('expire',expires);
                        if ((countDownDate - now) > 0) {
                            document.cookie = element_cookie_id + "=" + first_load_value + ";" + expires1 + ";path=/";
                        }
                    }
                    else {
                        //console.log('countdown date set cookie',countDownDate);
                        temp_date = countDownDate;
                        date1 = new Date();
                        date1.setSeconds(date1.getSeconds() + $scope.find(".eae-evergreen-wrapper").data("egtime"));
                        countDownDate = date1.getTime();
                        //console.log('countdown date set cookie',countDownDate);
                        eaeSetCookie(element_id, new Date().getTime(), cookie_expire);
                        //eaeSetCookie(element_cookie_id, new Date().getTime(), countDownDate);

                        var d = new Date();
                        d.setTime(d.getTime() + (temp_date * 1000));
                        var expires = "expires=" + d.toUTCString();
                        console.log('first load');
                        //console.log('expire',expires);

                        document.cookie = element_cookie_id + "=" + new Date().getTime() + ";" + expires + ";path=/";
                    }
                }
            }
            if (!$scope.hasClass('elementor-element-edit-mode')) {
                var distance = countDownDate - now;

                if (distance < 0) {

                    if (actions.length > 0) {
                        actions.forEach(function (value) {
                            if (value === 'redirect') {
                                $url = $scope.find(".eae-evergreen-wrapper").data("redirected-url");
                                if ($.trim($url) !== "") {
                                    window.location.href = $url1;
                                }
                            }
                            if (value === 'hide') {
                                if (!$scope.hasClass('elementor-element-edit-mode')) {
                                    $scope.find('#eaeclockdiv').css('display', 'none');
                                    $scope.find('.egt-title').css('display', 'none');
                                }
                            }
                            if (value === 'message') {
                                $scope.find('.eae-egt-message').css('display', 'block');
                            }
                            if (value === 'hide_parent') {
                                if (!$scope.hasClass('elementor-element-edit-mode')) {
                                    $p_secs = $scope.closest('section');
                                    $p_secs.css('display', 'none');
                                }
                            }
                        });
                    }

                    days = "00";
                    hours = "00";
                    minutes = "00";
                    seconds = "00";

                    $scope.find('.' + unqId).find('#eaedivDays').html(days);
                    $scope.find('.' + unqId).find('#eaedivHours').html(hours.slice(-2));
                    $scope.find('.' + unqId).find('#eaedivMinutes').html(minutes.slice(-2));
                    $scope.find('.' + unqId).find('#eaedivSeconds').html(seconds.slice(-2));
                    return;
                }
            }

            // For editor

            if ($scope.hasClass('elementor-element-edit-mode')) {

                if (element_type === 'countdown') {
                    date1 = new Date(countDownDate);
                    countDownDate = date1.getTime();
                }
                else {
                    date1 = new Date();
                    date1.setSeconds(date1.getSeconds() + $scope.find(".eae-evergreen-wrapper").data("egtime"));
                    countDownDate = date1.getTime();
                }
            }

            var y = setInterval(function () {
                //console.log('c date inner',countDownDate);
                // Get todays date and time

                var now = new Date().getTime();
                // Find the distance between now and the count down date

                var distance = countDownDate - now;

                //console.log('distance',distance);
                var days = 0;
                var hours = 0;
                var minutes = 0;
                var seconds = 0;
                if (distance > 0) {
                    // Time calculations for days, hours, minutes and seconds
                    days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    hours = "0" + Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    minutes = "0" + Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    seconds = "0" + Math.floor((distance % (1000 * 60)) / 1000);
                }
                else {
                    if (actions.length > 0) {
                        if (!$scope.hasClass('elementor-element-edit-mode')) {
                            actions.forEach(function (value) {
                                if (value === 'redirect') {
                                    $url1 = $scope.find(".eae-evergreen-wrapper").data("redirected-url");
                                    if ($.trim($url1) !== "") {
                                        window.location.href = $url1;
                                    }
                                }
                                if (value === 'hide') {
                                    $scope.find('#eaeclockdiv').css('display', 'none');
                                    $scope.find('.egt-title').css('display', 'none');
                                }
                                if (value === 'message') {
                                    $scope.find('.eae-egt-message').css('display', 'block');
                                }
                                if (value === 'hide_parent') {
                                    if (!$scope.hasClass('elementor-element-edit-mode')) {
                                        $p_secs = $scope.closest('section');
                                        $p_secs.css('display', 'none');
                                    }
                                }
                            });
                        }
                    }
                    clearInterval(y);
                    days = "0";
                    hours = "00";
                    minutes = "00";
                    seconds = "00";
                }

                if (days < 10) {
                    days = "0" + days;
                }
                $scope.find('.' + unqId).find('#eaedivDays').html(days);
                $scope.find('.' + unqId).find('#eaedivHours').html(hours.slice(-2));
                $scope.find('.' + unqId).find('#eaedivMinutes').html(minutes.slice(-2));
                $scope.find('.' + unqId).find('#eaedivSeconds').html(seconds.slice(-2));

            }, 1000);
        };

        var EgTimerSkin2 = function ($scope, $) {
            var countDownDate = $scope.find(".eae-evergreen-wrapper").data("egtime");
            var cookie_expire = $scope.find(".eae-evergreen-wrapper").data("egt-expire");
            var element_type = $scope.find(".eae-evergreen-wrapper").data("element-type");
            var element_id = "eae-" + $scope.find(".eae-evergreen-wrapper").data("id");
            var element_cookie_id = "eae-temp-" + $scope.find(".eae-evergreen-wrapper").data("id");
            var actions = $scope.find(".eae-evergreen-wrapper").data("actions");
            var unqId = $scope.find(".eae-evergreen-wrapper").data("unqid");

            var now = new Date().getTime();

            if (!$scope.hasClass('elementor-element-edit-mode')) {
                if (element_type === 'countdown') {
                    date1 = new Date(countDownDate);
                    countDownDate = date1.getTime();
                    var expires1 = "expires=" + date1.toUTCString();

                    document.cookie = element_cookie_id + "=" + date1.getTime() + ";" + expires1 + ";path=/";
                }
                else {
                    var first_load_value = eaeGetCookie(element_id);
                    var date1 = "";
                    if (first_load_value !== "") {
                        date1 = new Date(parseInt(first_load_value));
                        date1.setSeconds(date1.getSeconds() + $scope.find(".eae-evergreen-wrapper").data("egtime"));
                        countDownDate = date1.getTime();

                        var d2 = new Date(parseInt(first_load_value));
                        d2.setTime(d2.getTime() + (cookie_expire * 60 * 60 * 1000));
                        var expires2 = "expires=" + d2.toUTCString();
                        document.cookie = element_id + "=" + first_load_value + ";" + expires2 + ';path=/';


                        var d1 = new Date(parseInt(first_load_value));
                        d1.setTime(d1.getTime() + ($scope.find(".eae-evergreen-wrapper").data("egtime") * 1000));
                        var expires1 = "expires=" + d1.toUTCString();

                        //console.log('expire',expires);
                        if ((countDownDate - now) > 0) {
                            document.cookie = element_cookie_id + "=" + first_load_value + ";" + expires1 + ";path=/";
                        }
                    }
                    else {
                        temp_date = countDownDate;
                        date1 = new Date();
                        date1.setSeconds(date1.getSeconds() + $scope.find(".eae-evergreen-wrapper").data("egtime"));
                        countDownDate = date1.getTime();
                        //console.log('countdown date set cookie',countDownDate);
                        eaeSetCookie(element_id, new Date().getTime(), cookie_expire);
                        //eaeSetCookie(element_cookie_id, new Date().getTime(), countDownDate);

                        var d = new Date();
                        d.setTime(d.getTime() + (temp_date * 1000));
                        var expires = "expires=" + d.toUTCString();

                        document.cookie = element_cookie_id + "=" + new Date().getTime() + ";" + expires + ";path=/";
                    }
                }
            }
            if (!$scope.hasClass('elementor-element-edit-mode')) {
                var distance = countDownDate - now;
                if (distance < 0) {

                    if (actions.length > 0) {
                        actions.forEach(function (value) {
                            if (value === 'redirect') {
                                $url = $scope.find(".eae-evergreen-wrapper").data("redirected-url");
                                if ($.trim($url) !== "") {
                                    window.location.href = $url;
                                }
                            }
                            if (value === 'hide') {
                                $scope.find('.' + unqId).find('.timer-container').css('display', 'none');
                                $scope.find('.' + unqId).find('.egt-title').css('display', 'none');
                            }
                            if (value === 'message') {
                                $scope.find('.' + unqId).find('.eae-egt-message').css('display', 'block');
                            }
                            if (value === 'hide_parent') {
                                if (!$scope.hasClass('elementor-element-edit-mode')) {
                                    $p_secs = $scope.closest('section');
                                    $p_secs.css('display', 'none');
                                }
                            }
                        });
                    }


                    return;
                }
            }

            if ($scope.hasClass('elementor-element-edit-mode')) {
                if (element_type === 'countdown') {
                    date1 = new Date(countDownDate);
                    countDownDate = date1.getTime();
                }
                else {
                    date1 = new Date();
                    date1.setSeconds(date1.getSeconds() + $scope.find(".eae-evergreen-wrapper").data("egtime"));
                    countDownDate = date1.getTime();
                }
            }

            // Update the count down every 1 second
            var x = setInterval(function () {

                // Get todays date and time
                var now = new Date().getTime();

                // Find the distance between now and the count down date
                var distance = countDownDate - now;

                // Time calculations for days, hours, minutes and seconds
                var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                $scope.find('.' + unqId).find('#eaeulSec1').find('.flip-clock-active').removeClass('flip-clock-active');
                $scope.find('.' + unqId).find('#eaeulSec1').find('.flip-clock-before').removeClass('flip-clock-before');
                $scope.find('.' + unqId).find('#eaeulSec').find('.flip-clock-active').removeClass('flip-clock-active');
                $scope.find('.' + unqId).find('#eaeulSec').find('.flip-clock-before').removeClass('flip-clock-before');
                // If the count down is finished, write some text
                if (distance < 0) {
                    clearInterval(x);
                    if (actions.length > 0) {
                        actions.forEach(function (value) {
                            if (value === 'redirect') {
                                if (!$scope.hasClass('elementor-element-edit-mode')) {
                                    $url1 = $scope.find(".eae-evergreen-wrapper").data("redirected-url");
                                    if ($.trim($url1) !== "") {
                                        window.location.href = $url1;
                                    }
                                }
                            }
                            if (value === 'hide') {
                                if (!$scope.hasClass('elementor-element-edit-mode')) {
                                    $scope.find('.' + unqId).find('.timer-container').css('display', 'none');
                                    $scope.find('.' + unqId).find('.egt-title').css('display', 'none');
                                }
                            }
                            if (value === 'message') {
                                if (!$scope.hasClass('elementor-element-edit-mode')) {
                                    $scope.find('.' + unqId).find('.eae-egt-message').css('display', 'block');
                                }
                            }
                            if (value === 'hide_parent') {
                                if (!$scope.hasClass('elementor-element-edit-mode')) {
                                    $p_secs = $scope.closest('section');
                                    $p_secs.css('display', 'none');
                                }
                            }
                        });
                    }
                    //document.getElementById("demo").Html = "EXPIRED";
                    return;
                }
                if ($.trim(seconds).length === 2) {
                    //var x = parseInt($.trim(seconds).charAt(1)) - 1;
                    var a = "#eaeulSec1 li:eq( " + $.trim(seconds).charAt(1) + " )";
                    var b = "#eaeulSec li:eq( " + $.trim(seconds).charAt(0) + " )";

                    if ($scope.find('.' + unqId).find(a).next().length > 0) {
                        $scope.find('.' + unqId).find(a).addClass('flip-clock-active');
                        $scope.find('.' + unqId).find(a).next().addClass('flip-clock-before');
                    }
                    else {
                        $scope.find('.' + unqId).find('#eaeulSec1 li:last-child').addClass('flip-clock-active');
                        $scope.find('.' + unqId).find('#eaeulSec1 li:first-child').addClass('flip-clock-before');
                    }
                    if ($scope.find('.' + unqId).find(b).next().length > 0) {
                        $scope.find('.' + unqId).find(b).addClass('flip-clock-active');
                        $scope.find('.' + unqId).find(b).next().addClass('flip-clock-before');
                    }
                    else {
                        $scope.find('.' + unqId).find('#eaeulSec li:last-child').addClass('flip-clock-active');
                        $scope.find('.' + unqId).find('#eaeulSec li:first-child').addClass('flip-clock-before');
                    }
                }
                else {
                    //var x = parseInt($.trim(seconds).charAt(1)) - 1;
                    var a = "#eaeulSec1 li:eq( " + $.trim(seconds).charAt(0) + " )";
                    var b = "#eaeulSec li:eq( 0 )";

                    if ($scope.find('.' + unqId).find(a).next().length > 0) {
                        $scope.find('.' + unqId).find(a).addClass('flip-clock-active');
                        $scope.find('.' + unqId).find(a).next().addClass('flip-clock-before');
                    }
                    else {
                        $scope.find('.' + unqId).find('#eaeulSec1 li:last-child').addClass('flip-clock-active');
                        $scope.find('.' + unqId).find('#eaeulSec1 li:first-child').addClass('flip-clock-before');
                    }

                    if ($scope.find('.' + unqId).find(b).next().length > 0) {
                        $scope.find('.' + unqId).find(b).addClass('flip-clock-active');
                        $scope.find('.' + unqId).find(b).next().addClass('flip-clock-before');
                    }
                    else {
                        $scope.find('.' + unqId).find('#eaeulSec li:last-child').addClass('flip-clock-active');
                        $scope.find('.' + unqId).find('#eaeulSec li:first-child').addClass('flip-clock-before');
                    }
                }

                $scope.find('.' + unqId).find('#eaeulMin1').find('.flip-clock-active').removeClass('flip-clock-active');
                $scope.find('.' + unqId).find('#eaeulMin1').find('.flip-clock-before').removeClass('flip-clock-before');
                $scope.find('.' + unqId).find('#eaeulMin').find('.flip-clock-active').removeClass('flip-clock-active');
                $scope.find('.' + unqId).find('#eaeulMin').find('.flip-clock-before').removeClass('flip-clock-before');

                if ($.trim(minutes).length == 2) {
                    //var x = parseInt($.trim(seconds).charAt(1)) - 1;
                    var a = "#eaeulMin1 li:eq( " + $.trim(minutes).charAt(1) + " )";
                    var b = "#eaeulMin li:eq( " + $.trim(minutes).charAt(0) + " )";

                    if ($scope.find('.' + unqId).find(a).next().length > 0) {
                        $scope.find('.' + unqId).find(a).addClass('flip-clock-active');
                        $scope.find('.' + unqId).find(a).next().addClass('flip-clock-before');
                    }
                    else {
                        $scope.find('.' + unqId).find('#eaeulMin1 li:last-child').addClass('flip-clock-active');
                        $scope.find('.' + unqId).find('#eaeulMin1 li:first-child').addClass('flip-clock-before');
                    }
                    if ($scope.find('.' + unqId).find(b).next().length > 0) {
                        $scope.find('.' + unqId).find(b).addClass('flip-clock-active');
                        $scope.find('.' + unqId).find(b).next().addClass('flip-clock-before');
                    }
                    else {
                        $scope.find('.' + unqId).find('#eaeulMin li:last-child').addClass('flip-clock-active');
                        $scope.find('.' + unqId).find('#eaeulMin li:first-child').addClass('flip-clock-before');
                    }
                }
                else {
                    //var x = parseInt($.trim(seconds).charAt(1)) - 1;
                    var a = "#eaeulMin1 li:eq( " + $.trim(minutes).charAt(0) + " )";
                    var b = "#eaeulMin li:eq( 0 )";

                    if ($scope.find('.' + unqId).find(a).next().length > 0) {
                        $scope.find('.' + unqId).find(a).addClass('flip-clock-active');
                        $scope.find('.' + unqId).find(a).next().addClass('flip-clock-before');
                    }
                    else {
                        $scope.find('.' + unqId).find('#eaeulMin1 li:last-child').addClass('flip-clock-active');
                        $scope.find('.' + unqId).find('#eaeulMin1 li:first-child').addClass('flip-clock-before');
                    }

                    if ($scope.find('.' + unqId).find(b).next().length > 0) {
                        $scope.find('.' + unqId).find(b).addClass('flip-clock-active');
                        $scope.find('.' + unqId).find(b).next().addClass('flip-clock-before');
                    }
                    else {
                        $scope.find('.' + unqId).find('#eaeulMin li:last-child').addClass('flip-clock-active');
                        $scope.find('.' + unqId).find('#eaeulMin li:first-child').addClass('flip-clock-before');
                    }
                }

                $scope.find('.' + unqId).find('#eaeulHour1').find('.flip-clock-active').removeClass('flip-clock-active');
                $scope.find('.' + unqId).find('#eaeulHour1').find('.flip-clock-before').removeClass('flip-clock-before');
                $scope.find('.' + unqId).find('#eaeulHour').find('.flip-clock-active').removeClass('flip-clock-active');
                $scope.find('.' + unqId).find('#eaeulHour').find('.flip-clock-before').removeClass('flip-clock-before');

                if ($.trim(hours).length == 2) {
                    //var x = parseInt($.trim(seconds).charAt(1)) - 1;
                    var a = "#eaeulHour1 li:eq( " + $.trim(hours).charAt(1) + " )";
                    var b = "#eaeulHour li:eq( " + $.trim(hours).charAt(0) + " )";

                    if ($scope.find('.' + unqId).find(a).next().length > 0) {
                        $scope.find('.' + unqId).find(a).addClass('flip-clock-active');
                        $scope.find('.' + unqId).find(a).next().addClass('flip-clock-before');
                    }
                    else {
                        $scope.find('.' + unqId).find('#eaeulHour1 li:last-child').addClass('flip-clock-active');
                        $scope.find('.' + unqId).find('#eaeulHour1 li:first-child').addClass('flip-clock-before');
                    }
                    if ($scope.find('.' + unqId).find(b).next().length > 0) {
                        $scope.find('.' + unqId).find(b).addClass('flip-clock-active');
                        $scope.find('.' + unqId).find(b).next().addClass('flip-clock-before');
                    }
                    else {
                        $scope.find('.' + unqId).find('#eaeulHour li:last-child').addClass('flip-clock-active');
                        $scope.find('.' + unqId).find('#eaeulHour li:first-child').addClass('flip-clock-before');
                    }
                }
                else {
                    //var x = parseInt($.trim(seconds).charAt(1)) - 1;
                    var a = "#eaeulHour1 li:eq( " + $.trim(hours).charAt(0) + " )";
                    var b = "#eaeulHour li:eq( 0 )";

                    if ($scope.find('.' + unqId).find(a).next().length > 0) {
                        $scope.find('.' + unqId).find(a).addClass('flip-clock-active');
                        $scope.find('.' + unqId).find(a).next().addClass('flip-clock-before');
                    }
                    else {
                        $scope.find('.' + unqId).find('#eaeulHour1 li:last-child').addClass('flip-clock-active');
                        $scope.find('.' + unqId).find('#eaeulHour li:first-child').addClass('flip-clock-before');
                    }

                    if ($scope.find('.' + unqId).find(b).next().length > 0) {
                        $scope.find('.' + unqId).find(b).addClass('flip-clock-active');
                        $scope.find('.' + unqId).find(b).next().addClass('flip-clock-before');
                    }
                    else {
                        $scope.find('.' + unqId).find('#eaeulHour li:last-child').addClass('flip-clock-active');
                        $scope.find('.' + unqId).find('#eaeulHour li:first-child').addClass('flip-clock-before');
                    }
                }

                $scope.find('.' + unqId).find('#eaeulDay1').find('.flip-clock-active').removeClass('flip-clock-active');
                $scope.find('.' + unqId).find('#eaeulDay1').find('.flip-clock-before').removeClass('flip-clock-before');
                $scope.find('.' + unqId).find('#eaeulDay').find('.flip-clock-active').removeClass('flip-clock-active');
                $scope.find('.' + unqId).find('#eaeulDay').find('.flip-clock-before').removeClass('flip-clock-before');

                if ($.trim(days).length == 2) {
                    //var x = parseInt($.trim(seconds).charAt(1)) - 1;
                    var a = "#eaeulDay1 li:eq( " + $.trim(days).charAt(1) + " )";
                    var b = "#eaeulDay li:eq( " + $.trim(days).charAt(0) + " )";

                    if ($scope.find('.' + unqId).find(a).next().length > 0) {
                        $scope.find('.' + unqId).find(a).addClass('flip-clock-active');
                        $scope.find('.' + unqId).find(a).next().addClass('flip-clock-before');
                    }
                    else {
                        $scope.find('.' + unqId).find('#eaeulDay1 li:last-child').addClass('flip-clock-active');
                        $scope.find('.' + unqId).find('#eaeulDay1 li:first-child').addClass('flip-clock-before');
                    }
                    if ($scope.find('.' + unqId).find(b).next().length > 0) {
                        $scope.find('.' + unqId).find(b).addClass('flip-clock-active');
                        $scope.find('.' + unqId).find(b).next().addClass('flip-clock-before');
                    }
                    else {
                        $scope.find('.' + unqId).find('#eaeulDay li:last-child').addClass('flip-clock-active');
                        $scope.find('.' + unqId).find('#eaeulDay li:first-child').addClass('flip-clock-before');
                    }
                }
                else {
                    //var x = parseInt($.trim(seconds).charAt(1)) - 1;
                    var a = "#eaeulDay1 li:eq( " + $.trim(days).charAt(0) + " )";
                    var b = "#eaeulDay li:eq( 0 )";

                    if ($scope.find('.' + unqId).find(a).next().length > 0) {
                        $scope.find('.' + unqId).find(a).addClass('flip-clock-active');
                        $scope.find('.' + unqId).find(a).next().addClass('flip-clock-before');
                    }
                    else {
                        $scope.find('.' + unqId).find('#eaeulDay1 li:last-child').addClass('flip-clock-active');
                        $scope.find('.' + unqId).find('#eaeulDay li:first-child').addClass('flip-clock-before');
                    }

                    if ($scope.find('.' + unqId).find(b).next().length > 0) {
                        $scope.find('.' + unqId).find(b).addClass('flip-clock-active');
                        $scope.find('.' + unqId).find(b).next().addClass('flip-clock-before');
                    }
                    else {
                        $scope.find('.' + unqId).find('#eaeulDay li:last-child').addClass('flip-clock-active');
                        $scope.find('.' + unqId).find('#eaeulDay li:first-child').addClass('flip-clock-before');
                    }
                }

            }, 1000);
        };

        var EgTimerSkin3 = function ($scope, $) {
            var countDownDate = $scope.find(".eae-evergreen-wrapper").data("egtime");
            var cookie_expire = $scope.find(".eae-evergreen-wrapper").data("egt-expire");
            var element_type = $scope.find(".eae-evergreen-wrapper").data("element-type");
            var element_id = "eae-" + $scope.find(".eae-evergreen-wrapper").data("id");
            var element_cookie_id = "eae-temp-" + $scope.find(".eae-evergreen-wrapper").data("id");
            var actions = $scope.find(".eae-evergreen-wrapper").data("actions");
            var dayShow = $scope.find(".eae-evergreen-wrapper").data("days");
            var hourShow = $scope.find(".eae-evergreen-wrapper").data("hours");
            var minShow = $scope.find(".eae-evergreen-wrapper").data("mins");
            var secShow = $scope.find(".eae-evergreen-wrapper").data("seconds");
            var unqId = $scope.find(".eae-evergreen-wrapper").data("unqid");

            var now = new Date().getTime();

            // for front end time get from cookie

            if (!$scope.hasClass('elementor-element-edit-mode')) {
                if (element_type === 'countdown') {
                    date1 = new Date(countDownDate);
                    countDownDate = date1.getTime();

                    var expires1 = "expires=" + date1.toUTCString();
                    document.cookie = element_cookie_id + "=" + date1.getTime() + ";" + expires1 + ";path=/";

                }
                else {
                    var first_load_value = eaeGetCookie(element_id);
                    var date1 = "";
                    if (first_load_value !== "") {
                        date1 = new Date(parseInt(first_load_value));
                        date1.setSeconds(date1.getSeconds() + $scope.find(".eae-evergreen-wrapper").data("egtime"));
                        countDownDate = date1.getTime();

                        var d2 = new Date(parseInt(first_load_value));
                        d2.setTime(d2.getTime() + (cookie_expire * 60 * 60 * 1000));
                        var expires2 = "expires=" + d2.toUTCString();
                        document.cookie = element_id + "=" + first_load_value + ";" + expires2 + ';path=/';


                        var d1 = new Date(parseInt(first_load_value));
                        d1.setTime(d1.getTime() + ($scope.find(".eae-evergreen-wrapper").data("egtime") * 1000));
                        var expires1 = "expires=" + d1.toUTCString();

                        //console.log('expire',expires);
                        if ((countDownDate - now) > 0) {
                            document.cookie = element_cookie_id + "=" + first_load_value + ";" + expires1 + ";path=/";
                        }
                    }
                    else {
                        //console.log('countdown date set cookie',countDownDate);
                        temp_date = countDownDate;
                        date1 = new Date();
                        date1.setSeconds(date1.getSeconds() + $scope.find(".eae-evergreen-wrapper").data("egtime"));
                        countDownDate = date1.getTime();
                        //console.log('countdown date set cookie',countDownDate);
                        eaeSetCookie(element_id, new Date().getTime(), cookie_expire);
                        //eaeSetCookie(element_cookie_id, new Date().getTime(), countDownDate);

                        var d = new Date();
                        d.setTime(d.getTime() + (temp_date * 1000));
                        var expires = "expires=" + d.toUTCString();
                        //console.log('expire',expires);

                        document.cookie = element_cookie_id + "=" + new Date().getTime() + ";" + expires + ";path=/";
                    }
                }
            }
            if (!$scope.hasClass('elementor-element-edit-mode')) {
                var distance = updateTime(countDownDate);

                if (parseInt(distance.all) < 1) {
                    if (actions.length > 0) {
                        actions.forEach(function (value) {
                            if (value === 'redirect') {
                                if (!$scope.hasClass('elementor-element-edit-mode')) {
                                    $url = $scope.find(".eae-evergreen-wrapper").data("redirected-url");
                                    if ($url !== "") {
                                        window.location.href = $url;
                                    }
                                }
                            }
                            if (value === 'hide_parent') {
                                if (!$scope.hasClass('elementor-element-edit-mode')) {
                                    $p_secs = $scope.closest('section');
                                    $p_secs.css('display', 'none');
                                }
                            }
                            if (value === 'hide') {
                                $scope.find('#timer').css('display', 'none');
                                $scope.find('.egt-title').css('display', 'none');
                                $scope.find('.desc').css('display', 'none');
                            }
                            if (value === 'message') {
                                $scope.find('.eae-egt-message').css('display', 'block');
                            }
                        });

                        if (actions.length === 1) {
                            if (actions[0] === '' || actions[0] === 'message') {
                                var clock = $scope.find('.' + unqId).find('#timer')[0];
                                //clock.innerHTML = "<span class='egt-time eae-time-wrapper'>0</span><span class='egt-time eae-time-wrapper'>0</span><span class='egt-time eae-time-wrapper'>0</span><span class='egt-time eae-time-wrapper'>0</span>";
                                if (dayShow === 'yes') {
                                    clock.innerHTML = "<span class='egt-time eae-time-wrapper'><div>00</div></span>";
                                }
                                if (hourShow === 'yes') {
                                    if (dayShow === 'yes') {
                                        $(clock).append("<span class='egt-time eae-time-wrapper'><div>00</div></span>");
                                    }
                                    else {
                                        clock.innerHTML = "<span class='egt-time eae-time-wrapper'><div>00</div></span>";
                                    }
                                }
                                if (minShow === 'yes') {
                                    if (dayShow === 'yes' || hourShow === 'yes') {
                                        $(clock).append("<span class='egt-time eae-time-wrapper'><div>00</div></span>");
                                    }
                                    else {
                                        clock.innerHTML = "<span class='egt-time eae-time-wrapper'><div>00</div></span>";
                                    }
                                }
                                if (secShow === 'yes') {
                                    if (dayShow === 'yes' || hourShow === 'yes' || minShow === 'yes') {
                                        $(clock).append("<span class='egt-time eae-time-wrapper'><div>00</div></span>");
                                    }
                                    else {
                                        clock.innerHTML = "<span class='egt-time eae-time-wrapper'><div>00</div></span>";
                                    }
                                }
                            }
                        }
                    }

                    // set html for 000000


                    return;
                }
            }

            if ($scope.hasClass('elementor-element-edit-mode')) {
                if (element_type === 'countdown') {
                    date1 = new Date(countDownDate);
                    countDownDate = date1.getTime();
                }
                else {
                    date1 = new Date();
                    date1.setSeconds(date1.getSeconds() + $scope.find(".eae-evergreen-wrapper").data("egtime"));
                    countDownDate = date1.getTime();
                }
            }

            /* if (element_type === 'countdown') {
                 date1 = new Date(countDownDate);
                 countDownDate = date1.getTime();
             }
             else {
                 var first_load_value = eaeGetCookie(element_id);
                 var date1 = "";
                 if (first_load_value !== "") {
                     date1 = new Date(parseInt(first_load_value));
                     date1.setSeconds(date1.getSeconds() + countDownDate);
                     countDownDate = date1.getTime();
                 }
                 else {
                     date1 = new Date();
                     date1.setSeconds(date1.getSeconds() + countDownDate);
                     countDownDate = date1.getTime();
                     eaeSetCookie(element_id, new Date().getTime(), cookie_expire);
                 }
             }*/

            var timer = updateTime(countDownDate);

            if (timer.all > 1) {
                startTimer('timer', countDownDate);
            }

            function updateTime(endDate) {
                var time = countDownDate - new Date();

                return {
                    'days': Math.floor(time / (1000 * 60 * 60 * 24)),
                    'hours': "0" + Math.floor((time / (1000 * 60 * 60)) % 24),
                    'minutes': "0" + Math.floor((time / (1000 * 60)) % 60),
                    'seconds': "0" + Math.floor((time / 1000) % 60),
                    'all': time
                }

            }

            function animate(span) {
                span.classList.add('fade');
                setTimeout(function () {
                    span.classList.remove('fade');
                }, 700)
            }

            function startTimer(clockID, endDate) {

                var timeInt = setInterval(function () {
                    //var clock = document.getElementById(clockID);
                    var clock = $scope.find('.' + unqId).find('#timer')[0];
                    var timer = updateTime(countDownDate);

                    //clock.innerHTML = "<span class='egt-time eae-time-wrapper'><div>"+timer.days+"</div></span><span class='egt-time eae-time-wrapper'><div>"+timer.hours+" </div></span><span class='egt-time eae-time-wrapper'><div>"+timer.minutes+"</div></span><span class='egt-time eae-time-wrapper'><div>"+timer.seconds+"</div></span>";
                    if (dayShow === 'yes') {
                        if (timer.days < 10) {
                            timer.days = "0" + timer.days;
                        }
                        clock.innerHTML = "<span class='egt-time eae-time-wrapper'><div>" + timer.days + "</div></span>";
                    }
                    if (hourShow === 'yes') {
                        if (dayShow === 'yes') {
                            $(clock).append("<span class='egt-time eae-time-wrapper'><div>" + timer.hours.slice(-2) + "</div></span>");
                        }
                        else {
                            clock.innerHTML = "<span class='egt-time eae-time-wrapper'><div>" + timer.hours.slice(-2) + "</div></span>";
                        }
                    }
                    if (minShow === 'yes') {
                        if (dayShow === 'yes' || hourShow === 'yes') {
                            $(clock).append("<span class='egt-time eae-time-wrapper'><div>" + timer.minutes.slice(-2) + "</div></span>");
                        }
                        else {
                            clock.innerHTML = "<span class='egt-time eae-time-wrapper'><div>" + timer.minutes.slice(-2) + "</div></span>";
                        }
                    }
                    if (secShow === 'yes') {
                        if (dayShow === 'yes' || hourShow === 'yes' || minShow === 'yes') {
                            $(clock).append("<span class='egt-time eae-time-wrapper'><div>" + timer.seconds.slice(-2) + "</div></span>");
                        }
                        else {
                            clock.innerHTML = "<span class='egt-time eae-time-wrapper'><div>" + timer.seconds.slice(-2) + "</div></span>";
                        }
                    }
                    //console.log('d',dayShow,'h',hourShow,'m',minShow,'s',secShow);
                    // animate
                    var spans = clock.getElementsByTagName('span');
                    if (dayShow === 'yes') {
                        if (timer.hours == 59 && timer.minutes == 59 && timer.seconds == 59) animate(spans[0]);
                    }

                    if (hourShow === 'yes') {
                        if (dayShow === 'yes') {
                            if (timer.minutes == 59 && timer.seconds == 59) animate(spans[1]);
                        }
                        else {
                            if (timer.minutes == 59 && timer.seconds == 59) animate(spans[0]);
                        }
                    }

                    if (minShow === 'yes') {
                        if (dayShow === 'yes') {
                            if (hourShow === 'yes') {
                                if (timer.seconds == 59) animate(spans[2]);
                            }
                            else {
                                if (timer.seconds == 59) animate(spans[1]);
                            }
                        }
                        else {
                            if (hourShow === 'yes') {
                                if (timer.seconds == 59) animate(spans[1]);
                            }
                            else {
                                if (timer.seconds == 59) animate(spans[0]);
                            }
                        }
                    }
                    if (secShow === 'yes') {
                        if (dayShow === 'yes') {
                            if (hourShow === 'yes') {
                                if (minShow === 'yes') {
                                    animate(spans[3]);
                                }
                            }
                            else {
                                if (minShow === 'yes') {
                                    animate(spans[2]);
                                }
                                else {
                                    animate(spans[1]);
                                }
                            }
                        }
                        else {
                            if (hourShow === 'yes') {
                                if (minShow === 'yes') {
                                    animate(spans[2]);
                                }
                            }
                            else {
                                if (minShow === 'yes') {
                                    animate(spans[1]);
                                }
                                else {
                                    animate(spans[0]);
                                }
                            }
                        }
                    }

                    if (timer.all <= 1) {
                        clearInterval(timeInt);
                        //clock.innerHTML = "<span class='egt-time eae-time-wrapper'>0</span><span class='egt-time eae-time-wrapper'>0</span><span class='egt-time eae-time-wrapper'>0</span><span class='egt-time eae-time-wrapper'>0</span>";
                        if (dayShow === 'yes') {
                            clock.innerHTML = "<span class='egt-time eae-time-wrapper'><div>00</div></span>";
                        }
                        if (hourShow === 'yes') {
                            if (dayShow === 'yes') {
                                $(clock).append("<span class='egt-time eae-time-wrapper'><div>00</div></span>");
                            }
                            else {
                                clock.innerHTML = "<span class='egt-time eae-time-wrapper'><div>00</div></span>";
                            }
                        }
                        if (minShow === 'yes') {
                            if (dayShow === 'yes' || hourShow === 'yes') {
                                $(clock).append("<span class='egt-time eae-time-wrapper'><div>00</div></span>");
                            }
                            else {
                                clock.innerHTML = "<span class='egt-time eae-time-wrapper'><div>00</div></span>";
                            }
                        }
                        if (secShow === 'yes') {
                            if (dayShow === 'yes' || hourShow === 'yes' || minShow === 'yes') {
                                $(clock).append("<span class='egt-time eae-time-wrapper'><div>00</div></span>");
                            }
                            else {
                                clock.innerHTML = "<span class='egt-time eae-time-wrapper'><div>00</div></span>";
                            }
                        }

                        if (!$scope.hasClass('elementor-element-edit-mode')) {
                            if (actions.length > 0) {
                                actions.forEach(function (value) {
                                    if (value === 'redirect') {
                                        $url1 = $scope.find(".eae-evergreen-wrapper").data("redirected-url");
                                        if ($url1 !== "") {
                                            window.location.href = $url1;
                                        }
                                    }
                                    if (value === 'hide') {
                                        $scope.find('#timer').css('display', 'none');
                                        $scope.find('.egt-title').css('display', 'none');
                                        $scope.find('.desc').css('display', 'none');
                                    }
                                    if (value === 'message') {
                                        $scope.find('.eae-egt-message').css('display', 'block');
                                    }
                                    if (value === 'hide_parent') {
                                        $p_secs = $scope.closest('section');
                                        $p_secs.css('display', 'none');
                                    }
                                });
                            }
                        }
                    }
                }, 1000);
            }
        };

        var EgTimerSkin4 = function ($scope, $) {
            var countDownDate = $scope.find(".eae-evergreen-wrapper").data("egtime");
            var cookie_expire = $scope.find(".eae-evergreen-wrapper").data("egt-expire");
            var element_type = $scope.find(".eae-evergreen-wrapper").data("element-type");
            var element_id = "eae-" + $scope.find(".eae-evergreen-wrapper").data("id");
            var actions = $scope.find(".eae-evergreen-wrapper").data("actions");
            var dayShow = $scope.find(".eae-evergreen-wrapper").data("days");
            var hourShow = $scope.find(".eae-evergreen-wrapper").data("hours");
            var minShow = $scope.find(".eae-evergreen-wrapper").data("mins");
            var secShow = $scope.find(".eae-evergreen-wrapper").data("seconds");


            if (element_type === 'countdown') {
                date1 = new Date(countDownDate);
                countDownDate = date1.getTime();
                countDownDate = Math.floor((countDownDate - new Date()) / 1000);
            }
            else {
                var first_load_value = eaeGetCookie(element_id);
                var date1 = "";
                var cur_date = "";
                if (first_load_value !== "") {
                    date1 = new Date(parseInt(first_load_value));
                    cur_date = new Date().getTime();
                    date1 = cur_date - first_load_value;
                    date1 = date1 / 1000;
                    countDownDate = countDownDate - date1;
                }
                else {
                    //date1 = new Date();
                    //date1.setSeconds(date1.getSeconds() + countDownDate);
                    //console.log('date1 else',date1);
                    //countDownDate = countDownDate;
                    eaeSetCookie(element_id, new Date().getTime(), cookie_expire);
                }
            }

            C3Counter("counter", {startTime: countDownDate});

            function C3Counter(id, opt) {

                this.options = {
                    stepTime: 60, // not used
                    format: "dd:hh:mm:ss", // not used
                    startTime: "00:00:00:00",
                    digitImages: 1,
                    digitWidth: 30,
                    digitHeight: 44,
                    digitSlide: true,
                    digitSlideTime: 200,
                    digitImageHeight: 484,
                    digitAnimationHeight: 44,
                    timerEnd: function () {
                    },
                    image: "digits.png",
                    updateInterval: 1000

                };
                var s;
                if (typeof opt != "undefined") {
                    for (s in this.options) {
                        if (typeof opt[s] != "undefined") {
                            this.options[s] = opt[s];
                        }
                    }
                }
                if (String(options.startTime).indexOf(":") == -1) {
                    options.tempStartTime = options.startTime;
                } else {
                    //TODO - does not convert time with : to seconds to count
                    var td = new Date(options.startTime);
                }


                this.pad2 = function (number) {
                    return (number < 10 ? '0' : '') + number;
                }

                var timer = setInterval("this.updateCounter()", options.updateInterval);
                var startTime = new Date().getTime();
                var secNo = 0;
                var timerSingle = new Array();
                var dc = 0;
                var digits = new Array();
                var d = new Date();
                var lastTime = d.getTime();

                this.calculateTime = function () {
                    var tempTime = options.tempStartTime;

                    if (String(options.tempStartTime).indexOf(":") == -1) {
                        var seconds = Math.round(options.tempStartTime % 60);
                        options.tempStartTime = Math.floor(options.tempStartTime / 60);
                        var minutes = Math.round(options.tempStartTime % 60);
                        options.tempStartTime = Math.floor(options.tempStartTime / 60);
                        var hours = Math.round(options.tempStartTime % 24);
                        options.tempStartTime = Math.floor(options.tempStartTime / 24);
                        var days = Math.round(options.tempStartTime);
                        options.timeStr = this.pad2(days) + this.pad2(hours) + this.pad2(minutes) + this.pad2(seconds);
                    }

                    var currTime = new Date().getTime();
                    var diff = currTime - startTime;
                    if (seconds < 0 || minutes < 0 || hours < 0 || days < 0) {
                        options.timeStr = this.pad2(0) + this.pad2(0) + this.pad2(0) + this.pad2(0);
                    }
                    options.tempStartTime = options.startTime - Math.round(diff / 1000);
                };
                this.calculateTime();

                for (dc = 0; dc < 8; dc++) {
                    digits[dc] = {digit: this.options.timeStr.charAt(dc)};
                    /*if(dayShow !== 'yes'){
                        console.log('day no show',dc);
                        //dc = 3;
                        return true;
                    }
                    console.log('if out',dc);*/
                    $("#" + id).append("<div id='digit" + dc + "' style='position:relative;float:left;width:" + this.options.digitWidth + "px;height:" + this.options.digitHeight + "px;overflow:hidden;'><div class='digit' id='digit-bg" + dc + "' style='position:absolute; top:-" + digits[dc].digit * this.options.digitAnimationHeight + "px; width:" + this.options.digitWidth + "px; height:" + this.options.digitImageHeight + "px; '></div></div>");

                    if (dc % 2 == 1 && dc < 6) {
                        $("#" + id).append("<div class='digit-separator' style='float:left;'></div>");
                    }
                }

                $("#" + id).append("<div style='clear:both'></div>");

                this.animateDigits = function () {
                    for (var dc = 0; dc < 8; dc++) {
                        digits[dc].digitNext = Number(this.options.timeStr.charAt(dc));
                        digits[dc].digitNext = (digits[dc].digitNext + 10) % 10;
                        var no = dc;

                        if (digits[no].digit == 0) $("#digit-bg" + no).css("top", -this.options.digitImageHeight + this.options.digitHeight + "px");
                        if (digits[no].digit != digits[no].digitNext) {
                            $("#digit-bg" + no).animate({"top": -digits[no].digitNext * options.digitHeight + "px"}, options.digitSlideTime);
                            digits[no].digit = digits[no].digitNext;
                        }

                    }

                    var end = this.checkEnd();
                };

                this.checkEnd = function () {
                    for (var i = 0; i < digits.length; i++) {
                        if (digits[i].digit != 0) {
                            return false;
                        }
                    }
                    clearInterval(timer);
                    if (typeof(actions) !== 'undefined') {
                        actions.forEach(function (value) {
                            if (value.type === 'redirect') {
                                if (value.redirect_url !== "") {
                                    window.location.href = value.redirect_url;
                                }
                            }
                            if (value.type === 'hide') {
                                $scope.find('.eae-evergreen-wrapper').css('display', 'none');
                            }
                            if (value.type === 'message') {
                                $scope.find('.eae-egt-message').css('display', 'block');
                            }
                        });
                    }
                    this.options.timerEnd();
                    return true;
                };

                this.updateCounter = function () {
                    d = new Date();

                    if ((d.getTime() - lastTime) < (options.updateInterval - 50)) {
                        return;
                    }
                    lastTime = d.getTime();
                    this.calculateTime();
                    this.animateDigits();
                }

            }
        };

        var CompareTable = function ($scope, $) {
            $($scope.find(".eae-ct-heading")[0]).addClass("active");
            $scope.find("ul").on("click", "li", function () {

                var pos = $(this).index() + 2;
                $scope.find("tr").find('td:not(:eq(0))').hide();
                $scope.find('td:nth-child(' + pos + ')').css('display', 'table-cell');
                $scope.find("tr").find('th:not(:eq(0))').hide();
                $scope.find('li').removeClass('active');
                $(this).addClass('active');
            });

            // Initialize the media query
            var mediaQuery = window.matchMedia('(min-width: 767px)');

            // Add a listen event
            mediaQuery.addListener(doSomething);

            // Function to do something with the media query
            function doSomething(mediaQuery) {
                if (mediaQuery.matches) {
                    $scope.find('.sep').attr('colspan', 5);
                } else {
                    $scope.find('.sep').attr('colspan', 2);
                }
            }

            // On load
            doSomething(mediaQuery);
        };

        var ProgressBar = function ($scope, $) {
            $wrapper = $scope.find('.eae-progress-bar');
            var skill = $wrapper.attr('data-skill');
            var skill_value = $wrapper.attr('data-value');
            var skin = $wrapper.attr('data-skin');
            var skillELem = $wrapper.find('.eae-pb-bar-skill');
            var valueELem = $wrapper.find('.eae-pb-bar-value');
            var prgBar = $wrapper.find('.eae-pb-bar');
            var prgInner = $wrapper.find('.eae-pb-bar-inner');

            if(skin === 'skin1'){
                $(prgInner).attr("style" , "width : " +skill_value+ "%");
            }
            if(skin === 'skin2'){
                $(prgInner).attr("style" , "width : " +skill_value+ "%");
            }
            if(skin === 'skin3'){
                $(valueELem).addClass('eae-pb-bar-value--aligned-value');
                $(valueELem).attr("style" ,"left :" +skill_value+ "%");
                $(prgInner).attr("style" ,"width :" +skill_value+ "%");
            }
            if(skin === 'skin4'){
                $(valueELem).addClass('eae-pb-bar-value--aligned-value');
                $(valueELem).attr("style" ,"left :" +skill_value+ "%");
                $(prgInner).attr("style" ,"width :" +skill_value+ "%")
                $(prgBar).addClass('eae-pb-bar--no-overflow');
            }
            if(skin === 'skin5'){
                $(valueELem).addClass('eae-pb-bar-value--aligned-value');
                $(valueELem).attr("style" ,"left :" +skill_value+ "%");
                $(prgInner).attr("style" ,"width :" +skill_value+ "%")
            }

            $wrapper.each(function (index, value) {
                var waypoint = new Waypoint({
                    element: value,
                    skill_value : $(value).find('.eae-pb-bar-skill'),
                    valueElem : $(value).find('.eae-pb-bar-value'),
                    prgBar : $(value).find('.eae-pb-bar-bar'),
                    prgInner : $(value).find('.eae-pb-bar-inner'),
                    handler: function (direction) {
                        if (direction == 'down') {
                            if(!$(valueELem).hasClass('js-animated')){
                                $(valueELem).addClass('js-animated');
                            }
                            if(!$(prgInner).hasClass('js-animated')){
                                $(prgInner).addClass('js-animated');
                            }
                            if(!$(skillELem).hasClass('js-animated')) {
                                $(skillELem).addClass('js-animated');
                            }
                        }
                    },
                    offset: 'bottom-in-view',
                })
            });
        };
        elementorFrontend.hooks.addAction('frontend/element_ready/wts-ab-image.default', ab_image);
        elementorFrontend.hooks.addAction('frontend/element_ready/global', ParticlesBG);
        elementorFrontend.hooks.addAction('frontend/element_ready/global', AnimatedGradient);
        elementorFrontend.hooks.addAction('frontend/element_ready/wts-modal-popup.default', EaePopup);
        elementorFrontend.hooks.addAction('frontend/element_ready/wts-testimonial-slider.default', EAETestimonial);
        elementorFrontend.hooks.addAction('frontend/element_ready/eae-info-circle.skin1', InfoCircleHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/eae-info-circle.skin2', InfoCircleHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/eae-info-circle.skin3', InfoCircleHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/eae-info-circle.skin4', InfoCircleHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/eae-timeline.skin1', TimelineHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/eae-timeline.skin2', TimelineHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/eae-timeline.skin3', TimelineHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/eae-timeline.skin4', TimelineHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/eae-evergreen-timer.skin1', EgTimerSkin1);
        elementorFrontend.hooks.addAction('frontend/element_ready/eae-evergreen-timer.skin2', EgTimerSkin2);
        elementorFrontend.hooks.addAction('frontend/element_ready/eae-evergreen-timer.skin3', EgTimerSkin3);
        //elementorFrontend.hooks.addAction('frontend/element_ready/eae-evergreen-timer.skin4', EgTimerSkin4);
        elementorFrontend.hooks.addAction('frontend/element_ready/eae-comparisontable.default', CompareTable);
        elementorFrontend.hooks.addAction('frontend/element_ready/eae-progress-bar.skin1', ProgressBar);
        elementorFrontend.hooks.addAction('frontend/element_ready/eae-progress-bar.skin2', ProgressBar);
        elementorFrontend.hooks.addAction('frontend/element_ready/eae-progress-bar.skin3', ProgressBar);
        elementorFrontend.hooks.addAction('frontend/element_ready/eae-progress-bar.skin4', ProgressBar);
        elementorFrontend.hooks.addAction('frontend/element_ready/eae-progress-bar.skin5', ProgressBar);
    });

})(jQuery)