var SiteUrl = "http://192.168.3.250/shop";
var ApiUrl = "http://192.168.3.250/mobile";
var pagesize = 10;
var WapSiteUrl = "http://192.168.3.250/wap";
var IOSSiteUrl = "https://itunes.apple.com/us/app/shopnc-b2b2c/id879996267?l=zh&ls=1&mt=8";
var AndroidSiteUrl = "http://www.shopnc.net/download/app/AndroidShopNC2014Moblie.apk";

// auto url detection
(function() {
    var m = /^(https?:\/\/.+)\/wap/i.exec(location.href);
    if (m && m.length > 1) {
        SiteUrl = m[1] + '/shop';
        ApiUrl = m[1] + '/mobile';
        WapSiteUrl = m[1] + '/wap';
    }
})();
