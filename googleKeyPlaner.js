// requires
var utils = require('utils');

var fs = require('fs');

//Текущая директория скрипта
var currentFile = require('system').args[3];
var curFilePath = fs.absolute(currentFile).split('/');
curFilePath.pop();
var filePath = curFilePath.join('/');

var casper = require('casper').create({
    pageSettings: {
        webSecurityEnabled: false,
        loadImages: false,        // do not load images
        loadPlugins: false         // do not load NPAPI plugins (Flash, Silverlight, ...)
    },
    viewportSize: {width: 1024, height: 768},
    verbose: true,
    logLevel: "debug",
    //waitTimeout: 5000,
    stepTimeout: 200000,
    onStepTimeout: function () { //what to do if specific step timeout reaches.
        this.echo('----->EXIT FOR TIMEOUT STEP------');
        this.exit();
    }
});
var x = require('casper').selectXPath;

var email = '';
var passwd = '';
var phoneVer = '';
var key = '';
var country = '';

//login
casper.start();

//Получаем параметы из командной строки
if (casper.cli.has("email")) {
    email = casper.cli.get("email");
}

if (casper.cli.has("pass")) {
    passwd = casper.cli.get("pass");
}

if (casper.cli.has("key")) {
    key = casper.cli.get("key");
}

if (casper.cli.has("country")) {
    country = casper.cli.get("country");
}


casper.userAgent("Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/534.16 (KHTML, like Gecko) Chrome/10.0.648.204 Safari/534.16");

casper.open('https://accounts.google.com/ServiceLogin?service=adwords&continue=https://adwords.google.com/um/identity?hl%3Den_EN&hl=en_EN&ltmpl=signin&passive=0&skipvpage=true');

casper.then(function () {
    this.sendKeys('#Email', email);
});

casper.then(function () {
    this.click('input#next');
});

casper.then(function () {
    this.wait(2000, function () {
    });
});

casper.then(function () {
    this.sendKeys('#Passwd', passwd);
});

casper.then(function () {
    this.click('#signIn');
});

casper.wait(5000, function () {
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

        casper.wait(3000, function () {
        });
    }
});


//go to keyword planner
casper.thenOpen('https://adwords.google.com/ko/KeywordPlanner/Home?hl=en');
casper.waitForSelector('#gwt-debug-splash-panel-search-selection-input , div.spLd-t:nth-child(1) > div:nth-child(2) > div:nth-child(1) > div:nth-child(1)',
    function () {

    }, function () {
    }, 15000);


//Открываем слайдер
casper.then(function () {
    if (this.exists('#gwt-debug-splash-panel-search-selection-input')) {
        this.click('#gwt-debug-splash-panel-search-selection-input');
    } else {
        this.click('div.spLd-t:nth-child(1) > div:nth-child(2) > div:nth-child(1) > div:nth-child(1)');
    }
});

casper.wait(1000, function () {
});


//Заполняем поле для ключа
casper.thenEvaluate(function (key) {
    var elements = document.querySelectorAll('textarea#gwt-debug-keywords-text-area');
    var evt = document.createEvent('HTMLEvents');
    evt.initEvent('change', false, true);


    for (i = 0; i < elements.length; ++i) {
        elements[i].value = key;
        elements[i].dispatchEvent(evt);
    }
}, {key: key});
casper.wait(1000, function () {
});

//Заполняем страну
casper.then(function () {
    if (this.exists('#gwt-debug-splash-panel-form > div.spYb-k > div.spYb-d > div.spYb-j > div:nth-child(2) > div:nth-child(1) > div.spS-i')) {
        this.click('#gwt-debug-splash-panel-form > div.spYb-k > div.spYb-d > div.spYb-j > div:nth-child(2) > div:nth-child(1) > div.spS-i');
    } else if (this.exists('div.spLd-n:nth-child(3) > div:nth-child(2) > div:nth-child(1) > div:nth-child(3)')) {
        this.click('div.spLd-n:nth-child(3) > div:nth-child(2) > div:nth-child(1) > div:nth-child(3)');
    } else if (this.exists('#gwt-debug-splash-panel > div:nth-child(3) > div.spcc-i > div.spcc-f > div > div.spcc-n > div:nth-child(2) > div:nth-child(1) > div.spt-i > div > div.spt-n')) {
        this.click('#gwt-debug-splash-panel > div:nth-child(3) > div.spcc-i > div.spcc-f > div > div.spcc-n > div:nth-child(2) > div:nth-child(1) > div.spt-i > div > div.spt-n');
    } else {
        this.click('div.spbc-n:nth-child(3) > div:nth-child(2) > div:nth-child(1) > div:nth-child(3) > div:nth-child(2) > div:nth-child(2)');
    }
});

casper.then(function () {
    this.click('#gwt-debug-positive-targets-table > table > tbody > tr > td.aw-geopickerv2-bin-action-header > a');
});

casper.then(function () {
    this.click('#gwt-debug-advanced-map-link');
});

/*    this.capture('/home/arion/DAMP/'+key+'-beforCountryEnter.png');
 });*/

//casper.waitWhileVisible('.aw-geopickerv2-search-panel > div:nth-child(1) > div:nth-child(2) > input:nth-child(1)', function(){}, function(){}, 15000);

casper.wait(1000, function () {
});

casper.then(function () {
    this.sendKeys(".aw-geopickerv2-search-panel > div:nth-child(1) > div:nth-child(2) > input:nth-child(1)", 'usa', {keepFocus: true});
});

casper.then(function () {
    this.click('.aw-geopickerv2-search-panel > div:nth-child(1) > div:nth-child(3)');
});


casper.wait(2000, function () {
});

//casper.waitWhileVisible('div.aw-button-featured:nth-child(1)', function(){}, function(){}, 15000);

casper.then(function () {
    this.click('div.aw-button-featured:nth-child(1)');
});

casper.wait(1000, function () {
});

casper.then(function () {
    this.click('#gwt-debug-save-button');
});

casper.wait(1000, function () {
});


casper.then(function () {
    this.click('.spt-l');
});

casper.wait(1000, function () {
});

//Устанавливаем Английский язык div.spcc-n:nth-child(5) > div:nth-child(2) > div:nth-child(2) > div:nth-child(3)
casper.then(function () {
    this.click('div.spcc-n:nth-child(5) > div:nth-child(2) > div:nth-child(2) > div:nth-child(3)');
});

casper.then(function () {
    if (this.exists('.spVc-c')) {
        this.click('.spVc-c');
    }
});

casper.then(function () {
    this.sendKeys("#gwt-debug-language-pill-input-box", 'English', {keepFocus: true});
});

casper.then(function () {
    this.click('div.spxb-e:nth-child(1)');
});

casper.then(function () {
    this.click('.spt-l');
});

//casper.waitWhileVisible('#gwt-uid-307 > div > div > div > div.spPc-a > div:nth-child(2) > div > div.aw-geopickerv2-search-box-wrapper > img', function(){});

//Включаем тесно связанные варианты
casper.then(function () {
    this.click('#gwt-debug-keyword-options-edit-div');
});

casper.then(function () {
    this.click('#gwt-debug-keyword-options-input-editor > div.spOb-h > div.spOb-e > div:nth-child(2) > div.spac-d');
});



//Нажимаем кнопку Поиска
casper.then(function () {
    this.click('#gwt-debug-search-button');
});
casper.then(function () {
    this.click('#gwt-debug-search-button');
});

//Ждем пока работает AJAX
casper.waitWhileSelector('#loadingStatus', function () {
    this.echo('----->AJAX Search Request finished');
});

//Переключаем вкладку
casper.then(function () {
    this.click('#gwt-debug-grouping-toggle-KEYWORD_IDEAS');
});


//Ждем пока работает AJAX
casper.waitWhileSelector('#loadingStatus', function () {
    this.echo('----->AJAX Get Keywords Request finished');
});

//Если нет файла для скачивания - выходим с кодом 100
casper.waitForText("No ideas were returned for your query", function () {
    casper.die('No KeyWords', 100);
}, function () {
});

casper.then(function () {
    this.capture('/home/arion/DAMP/' + key + '-Country.png');
});


//Ждем отображения кнопки "Скачать"
casper.waitUntilVisible(x('//*[@id="gwt-debug-search-download-button"]/div[2]/div[1]'), function () {
    this.echo('----->VISIBLE DOWNLOAD BUTTON');
}, function () {
    this.echo('!-----TIME OUT. DOWNLOAD BUTTON DONT VISIBLE-----!');

}, 10000);


//Жмем кнопку Скачать
casper.then(function () {
    this.click('#gwt-debug-search-download-button>div:nth-child(2)>div.ninja-button.goog-inline-block.goog-button.goog-button-base');
})

//Ждем всплывающее окно с кнопкой Скачать
casper.waitUntilVisible('#gwt-debug-download-button', function () {
    this.echo('----->VISIBLE DOWNLOAD BUTTON IN POPUP WINDOW');
}, function () {

    this.echo('!-----TIME OUT. DOWNLOAD BUTTON IN POPUP WINDOW DONT VISIBLE-----!');
    this.capture('/home/arion/DAMP/' + key + '-dnldBtn.png');
}, 10000);
casper.wait(1000, function () {
});

//Жмем кнопку Скачать
casper.then(function () {
    this.click('#gwt-debug-download-button');
})

var phantomJsCookiesToNetScapeString = function (cookies) {
    var string = "";
    string += "# Netscape HTTP Cookie File\n";
    string += "# http://curl.haxx.se/rfc/cookie_spec.html\n";
    for (var i = 0; i < cookies.length; i++) {
        cookie = cookies[i];
        string += cookie.domain + "\t" +
            'FALSE' + "\t" +
            cookie.path + "\t" +
            cookie.secure.toString().toUpperCase() + "\t" +
            ((cookie.expiry != undefined) ? cookie.expiry : "") + "\t" +
            cookie.name + "\t" +
            cookie.value + ((i == cookies.length - 1) ? "" : "\n");
    }
    return string;
};

//Ждем готовность ссылки на файл и сохраняем ее в файл
casper.waitUntilVisible('div[download-url]#gwt-debug-retrieve-download', function () {
    this.echo('----->WAIT DOWNLOAD URL...');
    var dnlURL = this.getElementAttribute('div[download-url]#gwt-debug-retrieve-download', 'download-url');
    this.echo('----------------------> DOWNLOAD URL: ' + dnlURL);
    this.echo('----------------------> DOWNLOAD URL IS PRESENT! START DOWLOAD...');

    if (casper.cli.has("key")) {
        var key = casper.cli.get("key");
    }
    ;

    file = filePath + "/tempFiles/dnld_" + key + ".txt";
    try {
        fs.write(file, dnlURL, 'w');
        this.echo('++++++Download URL WRITE Succes---> ');
    } catch (e) {
        this.echo(e);
    }

}, function () {
    this.echo("!-----TIME OUT. ERROR DOWNLOAD URL-----!")
}, 20000);

//Если есть запрос на postmessageRelay - останавливаем запрос
casper.on('resource.requested', function (requestData, request) {
    if (requestData.url.indexOf('/postmessageRelay?') > -1) {
        this.echo('!!!-------------->SKIPED: ' + requestData.url, 'info');
        request.abort();
    }
    ;
});


//Поехали!
casper.run();