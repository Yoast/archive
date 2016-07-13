'use strict';

var mysql = require('mysql'),
    yoastseo = require('yoastseo'),
    Jed = require('jed');

var connection = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    database: 'yoastseo',
    charset: 'utf8mb4'
});

var Paper = yoastseo.Paper;
var ContentAssessor = yoastseo.ContentAssessor;
var i18n = new Jed( {"domain": "js-text-analysis",
    "locale_data": {
        "js-text-analysis": {
            "": {}
        }
    }});
var contentAssessor = new ContentAssessor(i18n);

/**
 * Run YoastSEO on site
 *
 * @param row Object
 */
function Analyze(row) {
    try {
        console.error(row.url);
        var paper = new Paper(row.content);
        try {
            contentAssessor.assess(paper);
        } catch (err) {
            // console.error(paper);
            // Do nuttin'
        }
        var scores = contentAssessor.getValidResults();

        var headers = ['inputId'];
        var scoresOut = [row.id];
        scores.forEach(function (score) {
			// This overrides the 3-6-9 score specifically for the textSentenceLengthVariation
			if ( score._identifier == 'textSentenceLengthVariation' ) {
				score.score = score.text.match( /score is ([\d.]+)/ )[1];
			}
            if (score._identifier != '') {
                headers.push(score._identifier);
                scoresOut.push(score.score);
            }
        });

        headers.push('Total');
        var overall = contentAssessor.calculateOverallScore();
        scoresOut.push(overall);

        var sql = "INSERT INTO results ( ?? ) VALUES ( ? )";
        var inserts = [headers, scoresOut];
        var query = mysql.format(sql, inserts);

        connection.query(query, function (err) {
            if (err) {
                console.log('Query that errored:' + query);
                console.error(err);
            }
        });
    }
    catch (err) {
        console.log(err);
    }
}

var query = "SELECT input.* FROM input WHERE input.id NOT IN ( SELECT inputId FROM results )";

connection.query(query, function (err, rows) {
    if (err) {
        console.error(err);
        return;
    }
    for( var i = 0, row; row = rows[i]; i++ ) {
        Analyze( row );
    }
});


