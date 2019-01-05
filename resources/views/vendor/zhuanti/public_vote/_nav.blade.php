<div class="fix_bottom bottom_nav">
    <div class="flex_box">
            <a href="{{ $proj->path }}" class="item" ><i class="bottom_nav_1" ></i><span >活动首页</span></a>
            <a href="{{ $proj->path }}/reg" class="item" ><i class="bottom_nav_2"></i><span>我要报名</span></a>
            <a href="{{ $proj->path }}/rank" class="item" ><i class="bottom_nav_3"></i><span>活动排名</span></a>
            <a href="{{ $proj->path }}/rule" class="item" ><i class="bottom_nav_4"></i><span>活动规则</span></a>
    </div>
</div>
<script type="text/javascript">
    (function() {
        $('.bottom_nav a[href="'+location.pathname+'"]').addClass('current');
    })();
</script>