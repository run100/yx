<!--页码-->
<div class="mt25 mb25 page_box">
    <a target="_self" class="page_item" href="javascript:goto_page(1)">首页</a>
    <a target="_self" class="page_item" href="javascript:goto_page('prev')">上一页</a>
    <select class="PropelPagerForm pager">
        @for($i = 1; $i <= $pager->lastPage(); $i++)
            <option value="{{$i}}">{{$i}}</option>
        @endfor
    </select>
    <a target="_self" class="page_item" href="javascript:goto_page('next')">下一页</a>
    <a target="_self" class="page_item" href="javascript:goto_page(@jsonattr($pager->lastPage()))">末页</a>
</div>
<script type="text/javascript">
    (function () {
        var cur_page = @json($pager->currentPage());
        var max_page = @json($pager->lastPage());

        $('select.pager option[value="'+cur_page+'"]')
            .attr('selected', 'selected')

        $('select.pager').change(function() {
            goto_page(this.value);
        });

        window.goto_page = function(page) {
            if (page === 'next') {
                page = cur_page + 1;
            } else if (page === 'prev') {
                page = cur_page - 1;
            }

            if (page > max_page) {
                page = max_page;
            }

            if (page < 1) {
                page = 1;
            }

            var link = location.href;
            link = link.replace(/&?page=\d+/, '');
            if (link.match(/[?].+/)) {
                link += '&page=' + page;
            } else if (link.match(/[?]$/)) {
                link += 'page=' + page;
            } else {
                link += '?page=' + page;
            }

            location.href = link;
        };
    })();
</script>