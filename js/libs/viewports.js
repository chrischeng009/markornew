/**
 * 允许强制将设置倍数为 1
 * 移动适配解决方案
 */
const viewport = {
    isAndroid : navigator.userAgent.match(/android/gi),
    isIPhone : navigator.userAgent.match(/iphone/gi),
    isWeChat : navigator.userAgent.match(/MicroMessenger/gi),
    init: function(absDpr) {
        const doc = document;
        const rootEl = doc.documentElement;
        const header = doc.getElementsByTagName("head")[0];
        var viewport = doc.createElement("meta");
        var fontScale = doc.createElement("meta");
        var devicePixelRatio = window.devicePixelRatio;
        var dpr = null;
        var tid = null;
        var rDpr = null;

        if (typeof devicePixelRatio === "number") {
            if (devicePixelRatio >= 3) {
                rDpr = 3;
            } else if (devicePixelRatio >= 2) {
                rDpr = 2;
            } else {
                rDpr = 1;
            }
        } else {
            rDpr = absDpr || 1;
        }
        dpr = typeof absDpr === "number" ? +absDpr : (this.isIPhone ? rDpr : 1);
        const scale = 1 / dpr;
        const type = this.isIPhone ? "iphone" : (this.isAndroid ? "android" : "other");
        rootEl.setAttribute("data-dpr", dpr);
        rootEl.setAttribute("data-device-type", type);
        rootEl.classList.add(type + "-data-dir-" + rDpr);
        viewport.name = "viewport";
        viewport.content = "initial-scale=" + scale + ", maximum-scale=" + scale + ", minimum-scale=" + scale + ", user-scalable=no";
        header.appendChild(viewport);
        fontScale.name = "wap-font-scale";
        fontScale.content = "no";
        header.appendChild(fontScale);

        try{
            var vMin = doc.createElement("div");
            vMin.style.width = "1px";
            vMin.style.width = "1vmin";
            document.body.appendChild(vMin);
            if(vMin.offsetWidth > 1){
                document.body.removeChild(vMin);
                rootEl.style.fontSize = "10vmin";
                rootEl.style.fontSize = window.getComputedStyle(rootEl, null).fontSize;
                if(rootEl.style.fontSize.indexOf("px") > 0) return;
            }
        }catch(e){}

        const refreshRem = function(){
            var width = doc.documentElement.clientWidth;
            var height = doc.documentElement.clientHeight;
            if(width === 0 || !width){
                width = rootEl.getBoundingClientRect().width;
            }
            const rem = (Math.min(width, height) / 10);
            rootEl.style.fontSize = rem + "px";
        };

        window.addEventListener('resize', function() {
            clearTimeout(tid);
            tid = setTimeout(refreshRem, 300);
        }, false);
        window.addEventListener('pageshow', function(e) {
            if (e.persisted) {
                clearTimeout(tid);
                tid = setTimeout(refreshRem, 300);
            }
        }, false);

        refreshRem();
    },
    rem: function() {
        return parseFloat(window.getComputedStyle(document.documentElement, null).fontSize, 10);
    },
    px2rem: function(px) {
        return px / this.rem();
    },

    rem2px: function(rem) {
        return rem * that.rem();
    },
    curDpr: function() {
        return document.documentElement.hasAttribute("data-dpr") ? parseInt(document.documentElement.hasAttribute("data-dpr"), 10) : window.devicePixelRatio;
    },
    getDeviceType: function() {
        return this.isIPhone ? "iphone" : (this.isAndroid ? "android" : "other");
    }
};

viewport.init();


// 跳转页面
function jumpUrl(url){
    if(url) location.assign(url);
} 
