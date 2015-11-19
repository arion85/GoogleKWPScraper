/**
 * Created by arion on 01.10.15.
 */
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
        loadPlugins: false,         // do not load NPAPI plugins (Flash, Silverlight, ...)
    },
    viewportSize: {width: 1024, height: 768},
    verbose: true,
    logLevel: "debug",
    waitTimeout: 10000,
    stepTimeout: 200000,
    onStepTimeout: function () { //what to do if specific step timeout reaches.
        this.echo('----->EXIT FOR TIMEOUT STEP------');
        this.exit();
    }
});

var dnldUrl = '';

//login
casper.start();

//Получаем параметы из командной строки
if (casper.cli.has("dnldUrl")) {
    dnldUrl = casper.cli.get("dnldUrl");
}

if (casper.cli.has("key")) {
    var key = casper.cli.get("key");
}

file = filePath + "/tempGrab/stats_" + key + ".csv";

casper.on('remote.message', function (msg) {
    logConsole('***remote message caught***: ' + msg);
});

casper.userAgent("Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/534.16 (KHTML, like Gecko) Chrome/10.0.648.204 Safari/534.16");

casper.on('resource.received', function (resource) {
    //this.echo('---->RESOURCE URL: ' + document.domain);
});

casper.open('https://google.com');

casper.thenOpen(dnldUrl, function () {
    this.waitForResource(/\.csv/, function (resource) {
        var file;

        if (casper.cli.has("key")) {
            var key = casper.cli.get("key");
        }
        ;

        file = filePath + "/tempGrab/stats_" + key + ".csv";
        try {
            this.echo("Attempting to download file " + file);
            casper.download(resource.url, file);
        } catch (e) {
            this.echo(e);
        }

    });
});



//Поехали!
casper.run();