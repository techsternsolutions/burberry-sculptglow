var isMobile = false;
$(function() {
    // best and thorough device detection
    if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent)
        || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) isMobile = true;

    //Block scrolling under menu and selfie modal
    $('.navbar, #selfie-modal').on('touchmove', function(e) {
        e.preventDefault();
    });

    var $selfie = $('#selfie-modal'), $form;

    $selfie.find('.btn-close').on('click', function(e) {
        $selfie.hide();
        $form.show();
        $('#selfie-form h2').remove();
        $('.crop-area').remove();
        e.preventDefault();
    });

    $('.action a').on('click', function(e) {
        $('#selfie_picture').click();
        e.preventDefault();
    });

    $selfie.on('change', '#selfie_picture', function(e) {
        $form = $(this).parents('form');

        var d = new FormData($form[0]);

        $selfie.addClass('waiting');
        $selfie.show();
        $.ajax({
            url: '/upload',
            type: "POST",
            dataType: 'html',
            data: d,
            processData: false,
            contentType: false
        }).done(function(r) {
            $form.hide().after(r);

            $('#crop-target').cropper({
                aspectRatio: 1,
                autoCropArea: 0.8,
                background: false,
                guides: true,
                highlight: false,
                dragCrop: false,
                zoomable: true,
                scaleable: true,
                moveable: true,
                cropBoxMovable: false,
                cropBoxResizable: false,
                minCropBoxWidth: 150,
                minCropBoxHeight: 150,
                preview: $('#crop-area-preview'),
                crop: function(e) {
                    $('#submission_submit').removeAttr('disabled');
                    $('#submission_path').val(JSON.stringify({
                        'source': $('#crop-target').attr('src'),
                        'x': e.x,
                        'y': e.y,
                        'w': e.width,
                        'h': e.height
                    }));
                }
            });

            //Make image width 100% of container
            $('#crop-target').on('built.cropper', function() {
                var imageData = $(this).cropper('getImageData');
                var windowWidth = $('.crop-area').outerWidth();

                zoomToRatio = windowWidth / imageData.naturalWidth;

                $(this).cropper('zoom', zoomToRatio);

                //Add scrollbar area for mobile
                var $scrollDivRight = $('<div class="mobile-scroll"></div>');
                var $scrollDivLeft = $('<div class="mobile-scroll left"></div>');
                $('.crop-area').append($scrollDivRight);
                $('.crop-area').prepend($scrollDivLeft);


                //Tap to continie 
                //Add tap-to-crop button if image is to big
                $formPosition = $('form[name=submission]').position();
                $windowHeight = $(window).height();
                $formHeight = $('form[name=submission]').height();

                if (($windowHeight - 200) < $formPosition.top) {
                    $('.tap-to-continue').removeClass('hidden');
                }

            });

            $selfie.removeClass('waiting');

            $('.tap-to-continue').on('click', function() {
                $("#selfie-modal").animate({ 'scrollTop' : $("#selfie-modal")[0].scrollHeight}, 500);
                $('.tap-to-continue').addClass('hidden');
            });
        });
    });
});

function initToolbar() {
    var $toolbar = $('.toolbar.top');
    var $leftArrow = $('.left-arrow');
    var $rightArrow = $('.right-arrow');

    function findByKey(key){
        return $toolbar.find('a[data-group="' + key + '"]:visible, a[data-key="' + key + '"]:visible').eq(0);
    }

    function getKey(link){
        return $(link).attr('data-group') || $(link).attr('data-key');
    }

    function refreshArrows($link) {
        if ($link.is(':not(.active)')){
            $link.trigger('click');
        }

        if ($link.nextAll('a:visible').length) {
            $rightArrow.show();
        } else {
            $rightArrow.hide();
        }

        if ($link.prevAll('a:visible').length) {
            $leftArrow.show();
        } else {
            $leftArrow.hide();
        }
    }

    function setToolbarActiveLink($link) {
        var key = getKey($link);
        if (key) {
            $toolbar.attr('data-pos', key);
            $link.trigger('click');
        }
        refreshArrows($link);
    }

    if (!$toolbar.data('inited')) {
        $toolbar.data('inited', true);
        $toolbar.find('a[data-key]').click(function() {
            $toolbar.attr('data-pos', getKey(this));
        });
        $leftArrow.click(function() {
            var pos = $toolbar.attr('data-pos');
            setToolbarActiveLink(findByKey(pos).prevAll('a:visible:eq(0)'));
        });

        $rightArrow.click(function() {
            var pos = $toolbar.attr('data-pos');
            setToolbarActiveLink(findByKey(pos).nextAll('a:visible:eq(0)'));
        });
    }
    refreshArrows(findByKey($toolbar.attr('data-pos')));
}

function openLink($link){
    $('#wait').show();
    $('#menu-button button').addClass('collapsed');
    $('#navbar').collapse('hide');
    setTimeout(function(){
        window.location.href = $link.attr('href');
    }, 300);

    $(window).on('unload', function(){
        $('#wait').hide();
    });
}

$(function disableNavLinks() {
    $('.large-nav a').click(function(){
        openLink($(this));
        return false;
    });
});

$(function initLikes() {
    $('.likes').click(function(){
        var $this = $(this);
        if ($this.is(':not(.liked)')){
            var $span = $this.find('span');
            $this.addClass('liked')
            $span.text(parseInt($span.text()) + 1);
        }
        return true;
    });
});

$(function hackTouch() {
    var touchOnMenuButton = false;
    var navLink = null;
    var $menuButton = $('#menu-button button');
    var $navbar = $('#navbar');
    var rest = false;

    function getTouchElement(event) {
        if (event.touches && event.touches.length) {
            event = event.touches[0];
        } else if (event.changedTouches && event.changedTouches.length) {
            event = event.changedTouches[0];
        }
        var element = document.elementFromPoint(event.clientX, event.clientY);
        return element;
    }

    function isMenuButton(element) {
        return $(element).closest('#menu-button').length > 0;
    }

    function isLargeNavElement(element) {
        return $(element).closest('a').closest('.large-nav').length > 0;
    }

    function touchStartListener(event) {
        var element = getTouchElement(event);
        if (!rest) {
            touchOnMenuButton = isMenuButton(element);
        }
        if (isLargeNavElement(element)){
            navLink = $(element).closest('a');
        }
    }

    var buttonTimer = null;
    function touchEndListener(event) {
        var element = getTouchElement(event);
        if (touchOnMenuButton && isMenuButton(element)) {
            touchOnMenuButton = false;
            rest = true;
            setTimeout(function() {
                rest = false;
            }, 500);
            $navbar.collapse('toggle');
            clearTimeout(buttonTimer);
            buttonTimer = setTimeout(function() {
                if ($navbar.attr('aria-expanded') === 'true') {
                    $menuButton.removeClass('collapsed');
                    $('.left-arrow, .right-arrow').css('visibility', 'hidden');
                } else {
                    $menuButton.addClass('collapsed');
                    $('.left-arrow, .right-arrow').css('visibility', 'visible');
                }
            }, 10);
        }

        if (isLargeNavElement(element)){
            var $link = $(element).closest('a');
            if (navLink[0] === $link[0]){
                openLink($link);
            }
        }

        navLink = null;
    }

    function clickListener(event) {
        touchStartListener(event);
        touchEndListener(event);
    }

    document.addEventListener('touchstart', touchStartListener);
    document.addEventListener('touchend', touchEndListener);
    document.addEventListener('click', clickListener);
});