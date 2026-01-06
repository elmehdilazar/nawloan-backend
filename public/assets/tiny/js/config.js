"use strict";
var base={
    defaultFontFamily:"Overpass, sans-serif",
    primaryColor:"#1b68ff",
    secondaryColor:"#4f4f4f",successColor:"#3ad29f",warningColor:"#ffc107",infoColor:"#17a2b8",dangerColor:"#dc3545",
    darkColor:"#343a40",lightColor:"#f2f3f6"},
    extend={primaryColorLight:tinycolor(base.primaryColor).lighten(10).toString(),
        primaryColorLighter:tinycolor(base.primaryColor).lighten(30).toString(),
        primaryColorDark:tinycolor(base.primaryColor).darken(10).toString(),
        primaryColorDarker:tinycolor(base.primaryColor).darken(30).toString()},
    chartColors=[base.primaryColor,base.successColor,"#6f42c1",extend.primaryColorLighter],
    colors={bodyColor:"#6c757d",headingColor:"#495057",borderColor:"#e9ecef",backgroundColor:"#f8f9fa",mutedColor:"#adb5bd",chartTheme:"light"},
    darkColor={bodyColor:"#adb5bd",headingColor:"#e9ecef",borderColor:"#212529",backgroundColor:"#495057",mutedColor:"#adb5bd",chartTheme:"dark"},
    lightColors=colors,
    curentTheme=localStorage.getItem("mode"),dark=document.querySelector("#darkTheme"),light=document.querySelector("#lightTheme"),
    switcher=document.querySelector("#modeSwitcher");
    function getCookie(name){var match=document.cookie.match(new RegExp("(^|;\\s*)"+name+"=([^;]+)"));return match?match[2]:null}
    function setCookie(mode){document.cookie="mode="+mode+"; path=/; max-age=31536000"}
    function applyTheme(mode){var isDark="dark"===mode;dark&&(dark.disabled=!isDark),light&&(light.disabled=isDark),colors=isDark?darkColor:lightColors,
        document.body&&(document.body.classList.toggle("theme-dark",isDark),document.body.classList.toggle("theme-light",!isDark),
            document.body.classList.toggle("dark",isDark),document.body.classList.toggle("light",!isDark),
            document.body.dataset.mode=isDark?"dark":"light"),
        document.documentElement&&document.documentElement.setAttribute("data-theme",isDark?"dark":"light"),
        switcher&&(switcher.dataset.mode=isDark?"dark":"light"),
        setCookie(isDark?"dark":"light");
        try{localStorage.setItem("mode",isDark?"dark":"light")}catch(e){}}
    function resolveInitialTheme(){if("dark"===curentTheme||"light"===curentTheme)return curentTheme;var cookieMode=getCookie("mode");if("dark"===cookieMode||"light"===cookieMode)return cookieMode;return document.body&&document.body.classList.contains("dark")?"dark":"light"}
    function modeSwitch(){var current=localStorage.getItem("mode"),next="dark"===current?"light":"dark";applyTheme(next)}
    applyTheme(resolveInitialTheme());
