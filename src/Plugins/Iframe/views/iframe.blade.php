
@if($crud->options['iframe_preload'])
    <?php
    // Notice the other views that we're going to have the $url request response preloaded
    // in the iframe. Some browser may display unexpected behavior of the srcdoc
    // attribute and that's going to help the cross-browser compatibility.
    $GLOBALS['preloaded_by_iframe'] = true;
    ?>
    <iframe allowtransparency="yes" srcdoc="{{$crud->router->preload($url)}}" src="about:blank" frameborder="0" style="width:100%; display: block;" id="crud-iframe-{{$crud->id}}" scrolling="no"></iframe>
@else
    <iframe allowtransparency="yes" src="{{$url}}" frameborder="0" style="width:100%; display: block;" id="crud-iframe-{{$crud->id}}" scrolling="no"></iframe>
@endif


<script>
    function IsolatedIframe(selector) {
        if (typeof $ == 'undefined') {
            $ = jQuery;
        }
        this.iframe = $(selector);
        console.log('start', this.iframe.attr('id'));

        this.hash = {
            getLocation: function () {
                return location.hash.replace(/#/, '');
            },
            setLocation: function (newLocation) {
                location.hash = newLocation;
            }
        };

        this.run = function () {
            this.updateHashByLocation();

            this.changeLocation(this.hash.getLocation());

            var izolator = this;
            window.onhashchange = function () {
                izolator.changeLocation(izolator.hash.getLocation());
            };

            this.iframe.load(function() {
                setInterval(function() {
                    izolator.resizeIframe();
                }, 100);
            });
        };

        this.updateHashByLocation = function () {
            var izolator = this;
            this.iframe.load(function () {
                var path = izolator.getIframeLocation();
                @if($crud->options['iframe_follower'])
                if (path != 'srcdoc') {
                    izolator.hash.setLocation(path);
                }
                @endif

                if (path.match(/target=_parent/)) {
                    location = path;
                }

                izolator.resizeIframe();

                $(this).contents().find('body')[0].style.backgroundColor = getComputedStyle($(this).parent()[0]);
            });
        };

        this.resizeIframe = function () {
            var heightIframe = this.iframe.contents().find('.view').height() + {{$crud->app['config']['style.iframe_size_fix']}};
            this.iframe.css("height", heightIframe);
//            console.log("resize", this.iframe.attr('id'), heightIframe);
        };

        this.getIframeLocation = function () {
            return this.iframe.contents().get(0).location.pathname + this.iframe.contents().get(0).location.search;
        };

        this.changeLocation = function (new_location) {
            if (new_location) {
                if (new_location != this.getIframeLocation()) {
                    this.iframe.removeAttr('srcdoc');
                    this.iframe[0].src = new_location;
                }
            }
        };
    }


    var callback_{{$crud->id}} = function() {
        var izolator = new IsolatedIframe('#crud-iframe-{{$crud->id}}');
        izolator.run();
    };
    if(typeof jQuery == 'undefined') {
        var jq = document.createElement('script');
        jq.src = "https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js";
        jq.onload = function() {
            callback_{{$crud->id}}();
        };
        document.getElementsByTagName('head')[0].appendChild(jq);
    } else {
        callback_{{$crud->id}}();
    }

</script>
