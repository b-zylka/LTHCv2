$(document).ready(function() {
	$('#divRss').FeedEk({
            	FeedUrl: 'https://www.us-cert.gov/ncas/alerts.xml',
                MaxCount: 5,
                ShowDesc: false
                });
	$('#divTCRss').FeedEk({
                FeedUrl: 'http://feeds.feedburner.com/TechCrunch/',
                MaxCount: 5,
                ShowDesc: false
                });
	$('#divSCTimesRSS').FeedEk({
                FeedUrl: 'http://rssfeeds.sctimes.com/stcloud/news',
                MaxCount: 5,
                ShowDesc: false
                });
	$('#divMSRSS').FeedEk({
                FeedUrl: 'https://technet.microsoft.com/en-us/security/rss/bulletin',
                MaxCount: 5,
                ShowDesc: false
                });
});
