Array.prototype.contains = function (needle) {
    for (i in this) {
        if (this[i] == needle) return true;
    }
    return false;
}

function urlRedict(url) {
    var tabs = ["/pages/index/index", "/pages/active_index/active_index", "/pages/business/business", "/pages/usercenter/usercenter"];
    if (tabs.contains(url)) {
        switchTab(url);
    } else {
        urlgo(url);
    }
}

function switchTab(url) {
    wx.miniProgram.switchTab({
        url: url, success: function () {
        },
        fail: function () {
        },
        complete: function () {
        }
    });
}

function urlgo(url) {
    wx.miniProgram.navigateTo({
        url: url,
        success: function () {
        },
        fail: function () {
        },
        complete: function () {
        }
    });
}

function postMessage(obj) {
    wx.miniProgram.postMessage({data: obj});
}
function share(title, img, url) {
    wx.miniProgram.postMessage({
        data: {
            "event": "share",
            "title": title,
            "img": img,
            "url": url
        }
    });
}

