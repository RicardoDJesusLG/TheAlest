(function (w, d, s, o, f, js, fjs) {
    w[o] = w[o] || function () {
        (w[o].q = w[o].q || []).push(arguments);
    };

    js = d.createElement(s);
    fjs = d.getElementsByTagName(s)[0];

    js.id = o;
    js.src = f;
    js.async = 1;

    fjs.parentNode.insertBefore(js, fjs);
})(
    window,
    document,
    "script",
    "CanaryChatWidget",
    "https://static.cdn.canarytechnologies.com/dist/web-chat-loader.js"
);

CanaryChatWidget("init", {
    slug: "the-alest-hotel3",
    chat_button_bottom_offset: 20
}, "https://www.canarytechnologies.com");