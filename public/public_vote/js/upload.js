/**
 * Created by staff on 2018/2/2.
 */
//升级目的：对多图上传通用下面代码即可。不用写多分class和复制多分这个js文件
$(".upload_btn").click(init);
function init() {
    var that = this;
    var inputfile = $(this).find(".input_file")[0];
    var u = new UploadPic();
    u.init({
        input: inputfile,
        callback: function (base64) {
            if ((this.fileSize / 1024).toFixed(2) > 3072) {
                alert("不能上传超过3M的图片！");
                $('.loading').next().remove();
                $('.loading').remove();
                return;
            }
            $(that).prev('.upload_btn.img').remove();
            var div = $('<div class="upload_btn img" style="background:url(' + base64 + ');background-size: cover;"></div>'),
                input1 = $('<input type="hidden" name="thumb" id="thumb"/>'),
                input2 = $('<input type="hidden" name="src" id="src"/>'),
                a = $('<a href="javascript:;" class="cBtn cBtnOn cBtn_pa cBtn_db">关闭</a>');
            a.click(function () {
                $(this).parent().remove();
                $(that).show();
            });
            div.append(input1).append(input2).append(a)
                .append('<div class="shade"><span></span></div>');
            $(that).before(div).hide();
            upload(this.noHead, (this.fileSize / 1024).toFixed(2), this.fileName, base64);
        },
        loading: function () {
        }
    });
}


function UploadPic() {
    this.sw = 0;
    this.sh = 0;
    this.tw = 0;
    this.th = 0;
    this.scale = 0;
    this.maxWidth = 0;
    this.maxHeight = 0;
    this.maxSize = 0;
    this.fileSize = 0;
    this.fileDate = null;
    this.fileType = '';
    this.fileName = '';
    this.input = null;
    this.canvas = null;
    this.mime = {};
    this.type = '';
    this.callback = function () {
    };
    this.loading = function () {
    };
    this.noHead = "";
}

UploadPic.prototype.init = function (options) {
    this.maxWidth = options.maxWidth || 800;
    this.maxHeight = options.maxHeight || 600;
    this.maxSize = options.maxSize || 3 * 1024 * 1024;
    this.input = options.input;
    this.mime = {'png': 'image/png', 'jpg': 'image/jpeg', 'jpeg': 'image/jpeg', 'bmp': 'image/bmp'};
    this.callback = options.callback || function () {
        };
    this.loading = options.loading || function () {
        };
    this._addEvent();
};
UploadPic.prototype._addEvent = function () {
    var _this = this;

    function tmpSelectFile(ev) {
        _this._handelSelectFile(ev);
    }

    this.input.addEventListener('change', tmpSelectFile, false);
};
UploadPic.prototype._handelSelectFile = function (ev) {
    var file = ev.target.files[0];
    this.type = file.type
    if (!this.type) {
        this.type = this.mime[file.name.match(/\.([^\.]+)$/i)[1]];
    }
    if (!/image.(png|jpg|jpeg|bmp)/.test(this.type)) {
        alert('选择的文件类型不是图片');
        return;
    }
    if (file.size > this.maxSize) {
        alert('选择文件大于' + this.maxSize / 1024 / 1024 + 'M，请重新选择');
        return;
    }
    this.fileName = file.name;
    this.fileSize = file.size;
    this.fileType = this.type;
    this.fileDate = file.lastModifiedDate;
    this._readImage(file);
};
UploadPic.prototype._readImage = function (file) {
    var _this = this;

    function tmpCreateImage(uri) {
        _this._createImage(uri);
    }

    this.loading();
    this._getURI(file, tmpCreateImage);
};
UploadPic.prototype._getURI = function (file, callback) {
    var reader = new FileReader();
    var _this = this;

    function tmpLoad() {
        var re = /^data:base64,/;
        var ret = this.result + '';
        if (re.test(ret)) ret = ret.replace(re, 'data:' + _this.mime[_this.fileType] + ';base64,');
        if (ret.indexOf(";base64,") >= 0) {
            var num = ret.indexOf(";base64,");
            num = parseInt(num) + 8;
            _this.noHead = ret.substring(num);
        }
        callback && callback(ret);
    }

    reader.onload = tmpLoad;
    reader.readAsDataURL(file);
    return false;
};
UploadPic.prototype._createImage = function (uri) {
    var img = new Image();
    var _this = this;

    function tmpLoad() {
        _this._drawImage(this);
    }

    img.onload = tmpLoad;
    img.src = uri;
};
UploadPic.prototype._drawImage = function (img, callback) {

    this.tw = img.width;
    this.th = img.height;
    this.sw = 100;
    this.sh = 100;
    this.scale = (this.tw / this.th).toFixed(2);
    if (this.sw > this.maxWidth) {
        this.sw = this.maxWidth;
        this.sh = Math.round(this.sw / this.scale);
    }
    if (this.sh > this.maxHeight) {
        this.sh = this.maxHeight;
        this.sw = Math.round(this.sh * this.scale);
    }
    this.canvas = document.createElement('canvas');
    var ctx = this.canvas.getContext('2d');
    this.canvas.width = this.sw;
    this.canvas.height = this.sh;
    ctx.drawImage(img, 0, 0, img.width, img.height, 0, 0, this.sw, this.sh);
    this.callback(this.canvas.toDataURL(this.type));
    ctx.clearRect(0, 0, this.tw, this.th);
    this.canvas.width = 0;
    this.canvas.height = 0;
    this.canvas = null;
};

function upload(base64, size, name, img) {
    function onprogress(evt) {
        var percent = Math.floor(evt.loaded * 100 / evt.total) + '%';
        console.log(percent);
        if (percent == '100%') {
            $('.shade').remove();
        }
        $('.shade span').text(percent);
    };
    var xhr_provider = function () {
        var xhr = jQuery.ajaxSettings.xhr();
        if (onprogress && xhr.upload) {
            xhr.upload.addEventListener('progress', onprogress, false);
        }
        return xhr;
    };

};
