'use strict';

var fs = require('fs'),
    Horseman = require('node-horseman'),
    mysql = require('mysql'),
    parse = require('csv-parse'),
    path = require('path'),
    phantomjs = require('phantomjs-prebuilt'),
    program = require('commander');

var connection = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    database: 'yoastseo',
    charset: 'utf8mb4'
});

connection.connect();

// Declare our program and command line variables
program
    .version('1.0.0')
    .option('-f --file <file>', 'CSV file to parse.')
    .parse(process.argv);

/**
 * Looper function for URLs
 *
 * @param index
 * @param data
 */
function testAllPages(index, data) {
    if (data.length > index) {
        var promise = grabContent(data[index], index);
        promise.then(function () {
            testAllPages(++index, data);
            done++;
            if (done == data.length) {
                //console.log( headers );
                //console.log( output );

                connection.end();
            }
        });
    }
}

var done = 0;

// Declare our parser
var parser = parse({columns: true, delimiter: ','}, function (err, data) {
    testAllPages(0, data);
});

// Parse CSV file through our parser
fs.createReadStream(program.file).pipe(parser);

/**
 * Test featured snippet existence
 *
 * @param data Object
 */
function grabContent(data) {
    return new Promise(function (resolve) {

        var query = "SELECT COUNT(*) AS count FROM input WHERE url = " + connection.escape(data.url);
        connection.query(query, function (err, rows) {
            if (err) {
                console.error(err);
            }
            if (rows[0].count > 0) {
                console.log('[Already in DB] ' + data.url);
                resolve();
            } else {
                scrape(data).then(resolve);
            }
        });
    });
}

/**
 * Scrape the URL
 *
 * @param data
 * @returns {Promise}
 */
function scrape(data) {
    return new Promise(function (resolve) {
        // Instantiate our browser
        var horseman = new Horseman({
            injectJquery: true,
            phantomPath: phantomjs.path,
            timeout: 10000,
            loadImages: false
        });

        try {
            horseman
                .userAgent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_5) AppleWebKit/601.6.17 (KHTML, like Gecko) Version/9.1.1 Safari/601.6.17')
                .open(data.url)
                .waitForSelector(data.selector)
                .status()
                .then(function (status) {
                    if (Number(status) != 200) {
                        console.error("Couldn't load " + data.url);
                        resolve();
                        return horseman.close();
                    }
                })
                .html(data.selector)
                .then(function (paper) {
                    console.error(data.url);
                    var sql = "INSERT INTO input (url, content) VALUES ( ?, ? )";
                    var inserts = [data.url, paper];
                    var query = mysql.format(sql, inserts);
                    connection.query(query);
                    resolve();
                })
                .close()
        } catch (err) {
            console.error("Couldn't load " + data.url);
            resolve();
        }
    });
}
