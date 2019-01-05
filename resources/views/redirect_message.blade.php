<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    @if (!$is_alert && !$is_confirm)
    <meta http-equiv="refresh" content="{{(int)$delay}};url={{htmlentities($url)}}" />
    @endif
    <title>{{$title ?: '正在跳转...'}}</title>
</head>
<body>

@if (!$is_alert && !$is_confirm)
    <div style="position: absolute; top: 48%; left: 0; right: 0; text-align: center;">
        {{$msg}}
        @if ($delay > 0)
        (<span id="delay-countdown">{{$delay}}</span>)
        @endif
    </div>
@endif

<script type="text/javascript">
    @if ($is_alert)
    alert(@json($msg));
    location.href = @json($url);
    @elseif ($is_confirm)
    if (confirm(@json($msg))) {
        location.href = @json($url);
    } else {
        history.back();
    }
    @endif

    @if ($delay > 0)
    (function() {
        var delay = @json($delay);
        var $countdown = document.getElementById('delay-countdown');

        var startAt = new Date().valueOf();
        var handle = setInterval(function() {
            var diff = Math.floor((new Date().valueOf() - startAt) / 1000);
            $countdown.innerHTML = delay - diff;

            if (delay - diff <= 1) {
                clearInterval(handle);
            }
        }, 200);
    })();
    @endif
</script>

</body>
</html>