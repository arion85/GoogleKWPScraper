// requires
var utils = require('utils');
var fs = require('fs');

var currentFile = require('system').args[3];
var curFilePath = fs.absolute(currentFile).split('/');
curFilePath.pop();
var filePath = curFilePath.join('/');


var casper = require('casper').create({
    verbose: true,
    logLevel: "debug",
    waitTimeout: 5000,
    webSecurityEnabled: false,
    viewportSize: {width: 1024, height: 768},
});

var mouse = require("mouse").create(casper);

var email = '';
var passwd = '';
var phoneVer = '';

//login
casper.start();

if (casper.cli.has("email")) {
    email = casper.cli.get("email");
}
;
if (casper.cli.has("pass")) {
    passwd = casper.cli.get("pass");
}
;

//casper.echo(email);
casper.userAgent("Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/534.16 (KHTML, like Gecko) Chrome/10.0.648.204 Safari/534.16");

casper.thenOpen('https://accounts.google.com/ServiceLogin?service=adwords&continue=https://adwords.google.com/um/identity?hl%3Den_US&hl=en_US&ltmpl=signin&passive=0&skipvpage=true');

casper.thenEvaluate(function login(email, passwd) {
    document.querySelector('#Email').setAttribute('value', email);
    document.querySelector('#Passwd').setAttribute('value', passwd);
}, {email: email, passwd: passwd});

casper.then(function () {
    this.click('#signIn');
});

casper.wait(2000, function () {
});

casper.then(function () {
    // Если подтверждение логина - вводим телефон
    if (this.getCurrentUrl().indexOf("/LoginVerification?") !== -1) {

        casper.then(function () {
            this.click("input#PhoneVerificationChallenge");
            this.wait(1000, function () {
            });
        });

        casper.waitUntilVisible('input#phoneNumber', function () {
            casper.then(function () {
                if (casper.cli.has("verphone")) {
                    var phoneVer = casper.cli.raw.get("verphone");
                }
                ;
                this.sendKeys("input#phoneNumber", phoneVer, {keepFocus: true});
                this.page.sendEvent("keypress", this.page.event.key.Enter);
            });
        });

        casper.thenEvaluate(function () {
            var el = __utils__.findOne('input#phoneNumber');
            var evt = document.createEvent('HTMLEvents');
            evt.initEvent('change', false, true);
            el.dispatchEvent(evt);
        });

        casper.thenEvaluate(function () {
            __utils__.click('input#submitChallenge');
        });
    }
});

casper.then(function () {
    if (this.getCurrentUrl().indexOf("/um/Welcome/Home?") !== -1) {

        casper.waitWhileVisible('#loadingStatus', function () {
        });

        casper.waitUntilVisible('#gwt-debug-account-page > img:nth-child(4)', function () {
        });

        casper.waitWhileVisible('#loadingStatus', function () {
        });

        casper.waitWhileVisible('#loadingStatus', function () {
        });

        casper.then(function () {
            this.wait(3000, function () {
            });
        });

        casper.then(function () {
            this.click('#gwt-debug-country-selector');
        });

        if (casper.cli.has("country_id")) {
            var country_id = casper.cli.get("country_id");
        }
        ;

        casper.waitUntilVisible('#gwt-uid-' + country_id, function () {
        });

        casper.thenEvaluate(function (country_id) {

            var el = document.querySelector('#gwt-uid-' + country_id);

            var ev = document.createEvent("MouseEvent");
            ev.initMouseEvent(
                "mouseup",
                true /* bubble */, true /* cancelable */,
                window, null,
                0, 0, 0, 0, /* coordinates */
                false, false, false, false, /* modifier keys */
                0 /*left*/, null
            );
            el.dispatchEvent(ev);
        }, {country_id: country_id});

        casper.then(function () {
            this.wait(2000, function () {
            });
        });

        casper.waitWhileVisible('#loadingStatus', function () {
        });

        casper.then(function () {
            this.capture('/home/arion/DAMP/beforClick.png');
        });

        casper.then(function () {
            this.click('#gwt-debug-account-page-continue-button');
        });

        casper.wait(5000, function () {
        });

        casper.then(function () {
            this.capture('/home/arion/DAMP/beforClick.png');
        });

    }
});
casper.on("page.error", function (msg, trace) {
    this.echo("Error:    " + msg, "ERROR");
    this.echo("file:     " + trace[0].file, "WARNING");
    this.echo("line:     " + trace[0].line, "WARNING");
    this.echo("function: " + trace[0]["function"], "WARNING");
    errors.push(msg);
});

casper.on('resource.received', function (resource) {
    "use strict";
    if (resource.url.indexOf("/VerifiedPhoneInterstitial?") !== -1) {
        this.echo('VERIFIED PHONE');

        casper.then(function () {
            this.click('#send-code-cancel-button');
        });
    } else if (resource.url.indexOf("/CampaignMgmt?") !== -1) {
        fs.write(filePath + '/loginnerTemp/return.txt', '0', 'w');
        this.exit();
    } else if (resource.url.indexOf("/um/GetStarted/Home?") !== -1) {
        fs.write(filePath + '/loginnerTemp/return.txt', '0', 'w');
        this.exit();
    } else if (resource.url.indexOf("/um/Welcome/Home?") !== -1) {

    }
});

//Поехали!
casper.run();