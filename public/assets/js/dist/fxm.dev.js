"use strict";

function loginThenUpdateFirebaseToken(idTarget) {
  messaging.requestPermission().then(function () {
    return messaging.getToken();
  }).then(function (token) {
    console.log(token);
    $(idTarget).val(token);
  })["catch"](function (err) {
    console.log('GET DEVICE TOKEN ERROR: ' + err);
  });
}
//# sourceMappingURL=fxm.dev.js.map
