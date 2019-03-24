var API = {};

API._ajax = function (method, url, data, fail, done, always) {
    $.ajax({
            method: method,
            beforeSend: function(request){
                if (method !== 'GET') {
                    // csrf protect
                    var dateObj = new Date();
                    dateObj.setTime(dateObj.getTime() + (30 * 1000)); // 30s
                    var expires = dateObj.toGMTString();
                    var cryptoObj = window.crypto || window.msCrypto;
                    var csrf_token = cryptoObj.getRandomValues(new Uint32Array(3));
                    document.cookie = "X-CSRF-Token=" + csrf_token + "; path=/; expires=" + expires;
                    request.setRequestHeader('X-CSRF-Token', csrf_token);
                }
            },
            url: '/api/v1' + url,
            data: data,
            dataType: "json"
        })
        .fail(function (jqxhr, textStatus) {
            if (jqxhr.status === 401) {
                window.location.assign('/login');
            }

            fail(jqxhr, textStatus);
        })
        .done(function (data, textStatus) {
            done(data, textStatus);
        })
        .always(function (data, textStatus) {
            always(data, textStatus);
        })
};

/**
 * console 提醒區別
 */
var ConsoleHint = function (data, text, suffix, bgcolor) {
    console.log(data);
    console.log("%c[ " + text + " " + suffix + " ]←───┘", "background-color: " + bgcolor + "; color: white");
};

API._register = function (data, callback) {
    ConsoleHint(data, "註冊結果", "POST", "slateGray");
    this._ajax("POST", "/auth/register", data,
        function (jqxhr) {
            ConsoleHint(jqxhr, "註冊結果失敗", "FAIL", "red");
            callback(jqxhr.responseJSON);
        }, function (data) {
            ConsoleHint(data, "註冊結果成功", "DONE", "green");
            callback(data);
        }, function (data) {
        });
};

API._login = function (data, callback) {
    ConsoleHint(data, "登入結果", "POST", "slateGray");
    this._ajax("POST", "/auth/login", data,
        function (jqxhr) {
            ConsoleHint(jqxhr, "登入結果失敗", "FAIL", "red");
            callback(jqxhr.responseJSON);
        }, function (data) {
            ConsoleHint(data, "登入結果成功", "DONE", "green");
            callback(data);
        }, function (data) {
        });
};


$(document).ready(function () {
    $('#registerBtn').click(function(){
        var data = {
            email:$('#email').val(),
            password:$('#password').val(),
            name:$('#name').val()
        };
        API._register(data, function(result){
            alert(result.message);
            if (result.success) {
                setTimeout(function(){
                    window.location.assign('/login')
                }, 2000);
            }
        });
    });

    $('#loginBtn').click(function(){
        var data = {
            email:$('#email').val(),
            password:$('#password').val()
        };
        API._login(data, function(result){
            if (result.success) {
                localStorage.setItem("name", result.value.name);
                window.location.assign('/');
            } else {
                alert(result.message);
            }
        });
    });

});
