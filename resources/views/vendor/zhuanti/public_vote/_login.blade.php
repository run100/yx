<script>
    $(function(){
        var path = $('#projPath').val();
        var loginCookieKey = 'js:'+ path.substring(1) + ':login';
        var isLogin = Cookies.get(loginCookieKey);
        if (isLogin != 1) {
            $.get(path + '/login_status', function (res) {
                if (res.code === 10014) {
                    location.href = path+'/login_start?redirectUrl='+encodeURIComponent(window.location.href);
                }
            });
        }
    })
</script>